<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Services\QuestionSelector;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ExamController extends Controller
{
    // Show all exams in dashboard with enhanced statistics
    public function index()
    {
        $user = session('user'); // get user from session

        if (!$user) {
            // Redirect to login if not found in session
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $exams = Exam::latest()->get(); // get all exams
        
        // Calculate dashboard statistics
        $stats = [
            'total_exams' => Exam::count(),
            'active_exams' => Exam::where('status', 'active')->count(),
            'total_questions' => DB::table('questions')->where('status', 'active')->count(),
            'total_marks' => DB::table('questions')->where('status', 'active')->sum('marks')
        ];
        
        return view('index', compact('exams', 'user', 'stats'));
    }

    // Show form to create a new exam
    public function create()
    {
        return view('exams.create');
    }

    // Store newly created exam
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'status' => 'required|in:draft,active,archived'
        ]);

        Exam::create($request->all());

        return redirect()->route('dashboard')->with('success', 'Exam created successfully!');
    }
     
    // Show the form to edit an exam
public function edit($uuid)
{
    $exam = Exam::where('uuid', $uuid)->firstOrFail();
    return view('exams.edit', compact('exam'));
}

// Update the exam
public function update(Request $request, $uuid)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'total_marks' => 'required|integer|min:1',
        'duration_minutes' => 'required|integer|min:1',
        'status' => 'required|in:draft,active,archived'
    ]);

    $exam = Exam::where('uuid', $uuid)->firstOrFail();
    $exam->update($request->all());

    return redirect()->route('dashboard')->with('success', 'Exam updated successfully!');
}


    // Show exam detail (page to import CSV)
    public function show($uuid)
    {
        $exam = Exam::where('uuid', $uuid)->firstOrFail(); // <--- rename to $exam
        return view('exams.show', compact('exam'));        // pass as $exam, not $exams
    }

    // Delete exam
    public function destroy($uuid)
    {
    $exam = Exam::where('uuid', $uuid)->firstOrFail();
    $exam->delete();

    return redirect()->route('dashboard')->with('success', 'Exam deleted successfully!');
    }

    // NEW STEP 4 METHODS - EXAM MANAGEMENT

    /**
     * Regenerate exam questions using QuestionSelector service
     *CORE method that powers the "Regenerate Questions" button
     * 
     * @param string $uuid Exam UUID
     * @param Request $request Optional filters for question selection
     * @return JsonResponse
     */
    public function regenerate(string $uuid, Request $request): JsonResponse
    {
        try {
            $exam = Exam::where('uuid', $uuid)->firstOrFail();
            
            // Initialize QuestionSelector service
            $selector = new QuestionSelector();
            
            // Get filters from request (optional)
            $filters = [];
            if ($request->has('difficulty')) { // easy, medium, hard
                $filters['difficulty'] = $request->input('difficulty');
            }
            if ($request->has('tags')) { // comma-separated tags
                $filters['tags'] = explode(',', $request->input('tags'));
            }
            if ($request->has('max_marks')) { // maximum marks per question
                $filters['max_marks'] = (int) $request->input('max_marks');
            }
            
            // Use QuestionSelector to find optimal questions
            $selectionResult = $selector->selectQuestions($exam->total_marks, $filters); // returns array with 'success', 'questions', 'question_count', 'total_marks', etc.
            
            if (!$selectionResult['success']) { // failed to find suitable questions
                return response()->json([ 
                    'success' => false,
                    'message' => 'Could not find suitable questions for this exam.',
                    'details' => $selectionResult // 
                ], 400);
            }
            
            // Clear existing questions from exam
            DB::table('exam_questions')->where('exam_id', $exam->id)->delete();
            
            // Add newly selected questions to exam
            $examQuestions = []; 
            foreach ($selectionResult['questions'] as $index => $question) {
                $examQuestions[] = [ // Prepare data for insertion
                    'exam_id' => $exam->id,
                    'question_id' => $question['id'],
                    'order_position' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            DB::table('exam_questions')->insert($examQuestions); // Bulk insert
            
            // Return success response with details
            return response()->json([
                'success' => true,
                'message' => 'Questions regenerated successfully!',
                'data' => [
                    'question_count' => $selectionResult['question_count'], // total number of questions selected
                    'total_marks' => $selectionResult['total_marks'], // total marks of selected questions
                    'algorithm_used' => $selectionResult['algorithm_used'], // algorithm used for selection
                    'difficulty_distribution' => $selectionResult['selection_metadata']['difficulty_distribution'], // distribution of question difficulties
                    'questions' => $this->formatQuestionsForResponse($selectionResult['questions']) // formatted questions
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error regenerating questions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current exam questions (AJAX endpoint for loading questions)
     * Powers the questions display in the exam show page
     * 
     * @param string $uuid Exam UUID
     * @return JsonResponse
     */
    public function getQuestions(string $uuid): JsonResponse
    {
        try {
            $exam = Exam::where('uuid', $uuid)->firstOrFail(); // get exam by UUID
            
            // Get questions for this exam with their options
            $questions = DB::table('exam_questions') // get questions for this exam
                ->join('questions', 'exam_questions.question_id', '=', 'questions.id') // join to get question details
                ->where('exam_questions.exam_id', $exam->id) // filter by exam ID
                ->select( // select relevant fields
                    'questions.*',
                    'exam_questions.order_position'
                )
                ->orderBy('exam_questions.order_position')
                ->get();
            
            // Get options for MCQ questions
            $questionsWithOptions = [];
            foreach ($questions as $question) {
                $questionArray = (array) $question; // convert to array
                
                // Get options if MCQ
                if (in_array($question->type, ['mcq_single', 'mcq_multiple'])) { // MCQ types
                    $options = DB::table('question_options') 
                        ->where('question_id', $question->id) // filter by question ID
                        ->select('id', 'option_text', 'is_correct') // select relevant fields
                        ->get();
                    $questionArray['options'] = $options->toArray();
                } else {
                    $questionArray['options'] = [];
                }
                
                $questionsWithOptions[] = $questionArray;
            }
            
            return response()->json([ 
                'success' => true,
                'questions' => $questionsWithOptions,
                'total_marks' => array_sum(array_column($questionsWithOptions, 'marks')),
                'question_count' => count($questionsWithOptions)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading questions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attach a specific question to exam (Manual question addition)
     * 
     * @param string $uuid Exam UUID
     * @param Request $request Contains question_id
     * @return JsonResponse
     */
    public function attachQuestion(string $uuid, Request $request): JsonResponse
    {
        try {
            $exam = Exam::where('uuid', $uuid)->firstOrFail(); 
            
            $request->validate([ // validate input
                'question_id' => 'required|integer|exists:questions,id'
            ]);
            
            $questionId = $request->input('question_id');
            
            // Check if question already attached
            $exists = DB::table('exam_questions')
                ->where('exam_id', $exam->id) // filter by exam ID
                ->where('question_id', $questionId) // check if this question is already linked to the exam
                ->exists(); // returns true/false
            
            if ($exists) { 
                return response()->json([
                    'success' => false,
                    'message' => 'Question is already attached to this exam.'
                ], 400);
            }
            
            // Get question details
            $question = DB::table('questions')->where('id', $questionId)->first();
            
            // Check if adding this question would exceed total marks
            $currentTotal = DB::table('exam_questions')
                ->join('questions', 'exam_questions.question_id', '=', 'questions.id')
                ->where('exam_questions.exam_id', $exam->id)
                ->sum('questions.marks');
            
            if ($currentTotal + $question->marks > $exam->total_marks) {
                return response()->json([
                    'success' => false,
                    'message' => "Adding this question would exceed exam total marks ({$exam->total_marks}). Current total: {$currentTotal}, Question marks: {$question->marks}" 
                ], 400);
            }
            
            // Get next order position
            $nextPosition = DB::table('exam_questions')
                ->where('exam_id', $exam->id)
                ->max('order_position') + 1;
            
            // Attach question
            DB::table('exam_questions')->insert([
                'exam_id' => $exam->id,
                'question_id' => $questionId,
                'order_position' => $nextPosition,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Question attached successfully!',
                'question' => $question,
                'new_total_marks' => $currentTotal + $question->marks
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error attaching question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detach question from exam (Remove specific question)
     * 
     * @param string $uuid Exam UUID
     * @param int $questionId Question ID to remove
     * @return JsonResponse
     */
    public function detachQuestion(string $uuid, int $questionId): JsonResponse
    {
        try {
            $exam = Exam::where('uuid', $uuid)->firstOrFail();
            
            // Check if question is attached
            $examQuestion = DB::table('exam_questions')
                ->where('exam_id', $exam->id)
                ->where('question_id', $questionId)
                ->first();
            
            if (!$examQuestion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question is not attached to this exam.'
                ], 400);
            }
            
            // Remove question
            DB::table('exam_questions')
                ->where('exam_id', $exam->id)
                ->where('question_id', $questionId)
                ->delete();
            
            // Reorder remaining questions
            $remainingQuestions = DB::table('exam_questions')
                ->where('exam_id', $exam->id)
                ->orderBy('order_position')
                ->get();
            
            foreach ($remainingQuestions as $index => $q) {
                DB::table('exam_questions')
                    ->where('id', $q->id)
                    ->update(['order_position' => $index + 1]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Question removed successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview exam as students will see it
     * 
     * @param string $uuid Exam UUID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function preview(string $uuid)
    {
        try {
            $exam = Exam::where('uuid', $uuid)->firstOrFail();
            
            // Get exam questions with options
            $questions = DB::table('exam_questions')
                ->join('questions', 'exam_questions.question_id', '=', 'questions.id')
                ->where('exam_questions.exam_id', $exam->id)
                ->select(
                    'questions.*',
                    'exam_questions.order_position'
                )
                ->orderBy('exam_questions.order_position')
                ->get();
            
            // Load options for each question
            foreach ($questions as $question) {
                if (in_array($question->type, ['mcq_single', 'mcq_multiple'])) {
                    $question->options = DB::table('question_options')
                        ->where('question_id', $question->id)
                        ->select('id', 'option_text')  // Hide is_correct in preview
                        ->get();
                }
            }
            
            return view('exams.preview', compact('exam', 'questions'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading exam preview: ' . $e->getMessage());
        }
    }

    /**
     * Export exam to PDF/Word format
     * 
     * @param string $uuid Exam UUID
     * @param Request $request Format preference
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function export(string $uuid, Request $request)
    {
        try {
            $exam = Exam::where('uuid', $uuid)->firstOrFail();
            $format = $request->input('format', 'pdf'); // Default to PDF
            
            // Get exam questions
            $questions = DB::table('exam_questions')
                ->join('questions', 'exam_questions.question_id', '=', 'questions.id')
                ->where('exam_questions.exam_id', $exam->id)
                ->select('questions.*', 'exam_questions.order_position')
                ->orderBy('exam_questions.order_position')
                ->get();
            
            // Load options for MCQ questions
            foreach ($questions as $question) {
                if (in_array($question->type, ['mcq_single', 'mcq_multiple'])) {
                    $question->options = DB::table('question_options')
                        ->where('question_id', $question->id)
                        ->get();
                }
            }
            
            // For now, return simple text format (PDF generation can be added later)
            $content = $this->generateExamText($exam, $questions);
            
            return response($content)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="' . $exam->name . '_export.txt"');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting exam: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint to get all questions for admin management
     * Powers the Question Management modal in Step 5 dashboard
     * 
     * @return JsonResponse
     */
    public function getAllQuestions(): JsonResponse
    {
        try {
            $questions = DB::table('questions')
                ->where('status', 'active')
                ->select('id', 'text', 'type', 'marks', 'difficulty', 'tags', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Format for API response
            $formattedQuestions = $questions->map(function($question) {
                return [
                    'id' => $question->id,
                    'text' => $question->text,
                    'type' => str_replace('_', ' ', ucfirst($question->type)),
                    'marks' => $question->marks,
                    'difficulty' => $question->difficulty ?: 'Not specified',
                    'tags' => $question->tags ? explode(',', $question->tags) : [],
                    'created_at' => \Carbon\Carbon::parse($question->created_at)->diffForHumans()
                ];
            });
            
            return response()->json([
                'success' => true,
                'questions' => $formattedQuestions,
                'total_count' => $questions->count(),
                'statistics' => [
                    'mcq_single' => DB::table('questions')->where('type', 'mcq_single')->where('status', 'active')->count(),
                    'mcq_multiple' => DB::table('questions')->where('type', 'mcq_multiple')->where('status', 'active')->count(),
                    'descriptive' => DB::table('questions')->where('type', 'descriptive')->where('status', 'active')->count(),
                    'total_marks' => DB::table('questions')->where('status', 'active')->sum('marks'),
                    'difficulties' => [
                        'easy' => DB::table('questions')->where('difficulty', 'easy')->where('status', 'active')->count(),
                        'medium' => DB::table('questions')->where('difficulty', 'medium')->where('status', 'active')->count(),
                        'hard' => DB::table('questions')->where('difficulty', 'hard')->where('status', 'active')->count(),
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading questions: ' . $e->getMessage()
            ], 500);
        }
    }

    
    // HELPER METHODS

    /**
     * Format questions for JSON response
     */
    private function formatQuestionsForResponse(array $questions): array
    {
        return array_map(function($question) {
            return [
                'id' => $question['id'],
                'text' => substr($question['text'], 0, 100) . (strlen($question['text']) > 100 ? '...' : ''),
                'marks' => $question['marks'],
                'difficulty' => $question['difficulty'],
                'type' => $question['type']
            ];
        }, $questions);
    }

    /**
     * Generate text format export
     */
    private function generateExamText(object $exam, $questions): string
    {
        $text = "EXAM EXPORT\n"; // Header
        $text .= "===================\n\n"; // Separator
        $text .= "Exam Name: {$exam->name}\n"; // Exam name
        $text .= "Total Marks: {$exam->total_marks}\n"; // Total marks
        $text .= "Duration: {$exam->duration_minutes} minutes\n";// Duration
        $text .= "Status: {$exam->status}\n"; // Status
        $text .= "Export Date: " . now()->format('Y-m-d H:i:s') . "\n\n"; // Export date
        $text .= "QUESTIONS\n"; // Questions header
        $text .= "===================\n\n";
        
        foreach ($questions as $index => $question) { // list questions
            $text .= "Question " . ($index + 1) . " ({$question->marks} marks)\n";
            $text .= "Difficulty: {$question->difficulty}\n";
            $text .= "Type: {$question->type}\n\n";
            $text .= $question->text . "\n\n";
            
            if (isset($question->options) && $question->options) { // if MCQ with options
                $text .= "Options:\n";
                foreach ($question->options as $optIndex => $option) {
                    $letter = chr(65 + $optIndex); // A, B, C, D...
                    $correct = $option->is_correct ? " [CORRECT]" : "";
                    $text .= "{$letter}) {$option->option_text}{$correct}\n";
                }
                $text .= "\n";
            }
            
            $text .= str_repeat("-", 50) . "\n\n"; // Separator between questions
        }
        
        return $text;
    }
}
