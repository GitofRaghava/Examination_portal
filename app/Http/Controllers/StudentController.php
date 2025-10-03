<?php

namespace App\Http\Controllers;
use App\Models\Exam;
use App\Models\Student;
use App\Models\ProctorRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Mail\WelcomeEmail;
use App\Models\student_answer;
use Illuminate\Support\Facades\Log;
use App\Models\ProctorVideo;

class StudentController extends Controller
{
    public function examAccess(string $uuid)
    {
        try {
            $exam = Exam::where('uuid', $uuid)->firstOrFail();
            
            // Check if exam is active
            if ($exam->status !== 'active') {
                return view('student.exam-not-available', [
                    'message' => 'This exam is not currently available.',
                    'exam' => $exam
                ]);
            }
            if (session()->has('student_email')) {
                $student = Student::where('email', session('student_email'))
                            ->where('exam_id', $exam->id)
                            ->first();

        if ($student) {
            // Student already registered → redirect to take exam
            return redirect()->route('student.exam.take', $exam->uuid);
        }
    }
            // Check if student already has a session for this exam
            $studentId = session("student_exam_{$uuid}");
            if ($studentId) {
                return redirect()->route('student.exam.take', $uuid);
            }
            
            return view('student.exam-access', compact('exam'));
            
        } catch (\Exception $e) {
            return view('student.exam-not-available', [
                'message' => 'Exam not found or has been removed.',
                'exam' => null
            ]);
        }
    }
    
    
     public function register(Request $request)
    {
        // ✅ Validate student input
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'candidate_name' => 'required|string|max:255',
            'candidate_email' => 'required|email',
            'candidate_contact' => 'nullable|string|max:15',
            'candidate_city' => 'nullable|string|max:255',
        ]);

        // ✅ Generate OTP
        $otp = rand(100000, 999999);
        $existing = Student::where('exam_id', $request->exam_id)
                   ->where('candidate_email', $request->candidate_email)
                   ->first();
if ($existing) {
    $existing->delete();
}

        // ✅ Create student record
        $student = Student::create([
            'exam_id' => $request->exam_id,
            'candidate_name' => $request->candidate_name,
            'candidate_email' => $request->candidate_email,
            'candidate_contact' => $request->candidate_contact,
            'candidate_city' => $request->candidate_city,
        ]);

        // ✅ Store student ID in session for OTP verification
        session(['student_email' => $student->email]);
        Session::put('otp', $otp);
        Session::put('student_id', $student->id);

        // ✅ Get exam UUID for session key
        $exam = Exam::find($request->exam_id);
        $uuid = $exam ? $exam->uuid : null;

        // ✅ Create student session for exam
        $studentSession = [
            'name' => $student->candidate_name,
            'email' => $student->candidate_email,
            'student_id' => $student->id,
            'exam_uuid' => $uuid,
            'start_time' => now()->toDateTimeString(),
            'answers' => [],
            'current_question' => 1
        ];

        if ($uuid) {
            session(["student_exam_{$uuid}" => $studentSession]);
        }

        // ✅ Prepare email details
        $studentDetails = [
            'name' => $student->candidate_name,
            'email' => $student->candidate_email,
            'city' => $student->candidate_city,
            'contact' => $student->candidate_contact,
            'otp' => $otp,
        ];

        // ✅ Send OTP mail
        Mail::to($student->candidate_email)->send(new WelcomeEmail($studentDetails));

        // ✅ Redirect to OTP verification page
        return redirect()->route('verify-otp')
                         ->with('success', 'OTP has been sent to your email!');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $storedOtp = Session::get('otp');

        if ($request->otp == $storedOtp) {
            $studentId = Session::get('student_id');
            $student = Student::find($studentId);

            if ($student instanceof \Illuminate\Contracts\Auth\Authenticatable) {
            // Clear OTP and student_id, but keep exam_id
            Session::forget(['otp', 'student_id']);
                return back()->with('error', 'Student not found or invalid.');
            }

            // Clear OTP and user_id, but keep exam_id
            Session::forget(['otp', 'student_id']);

            // Get exam UUID from student record
            $exam = Exam::find($student->exam_id);
            if (!$exam) {
                return back()->with('error', 'Exam not found.');
            }
            return redirect()->route('student.exam.take', $exam->uuid)->with('success', 'Verified! Start your exam.');
        } else {
            return back()->with('error', 'Invalid OTP. Try again.');
        }
    }
     public function showVerifyForm()
    {
        return view('verify-otp');  // We'll create this view
    }
    public function takeExam(string $uuid)
    {
        try {
            // Check if student has valid session
            $studentSession = session("student_exam_{$uuid}");
            if (!$studentSession) {
                return redirect()->route('student.exam.access', $uuid)
                    ->with('error', 'Please enter your details to start the exam.');
            }
            
            $exam = Exam::where('uuid', $uuid)->firstOrFail();
            
            // TEMPORARY: Disable time check for debugging
            $startTime = \Carbon\Carbon::parse($studentSession['start_time']);
            $currentTime = now();
            
            // Debug: Log time information but don't fail the exam yet
            Log::info("Exam Time Check (DEBUG MODE)", [
                'exam_uuid' => $uuid,
                'start_time' => $startTime->toDateTimeString(),
                'current_time' => $currentTime->toDateTimeString(),
                'duration_minutes' => $exam->duration_minutes,
                'raw_start_time' => $studentSession['start_time']
            ]);
            
            // Calculate elapsed time for logging only
            if ($currentTime->greaterThanOrEqualTo($startTime)) {
                $timeElapsedSeconds = $startTime->diffInSeconds($currentTime);
                $timeElapsedMinutes = floor($timeElapsedSeconds / 60);
                
                Log::info("Time Calculation (DEBUG MODE)", [
                    'elapsed_seconds' => $timeElapsedSeconds,
                    'elapsed_minutes' => $timeElapsedMinutes,
                    'exam_duration' => $exam->duration_minutes,
                    'would_expire' => $timeElapsedMinutes >= $exam->duration_minutes
                ]);
            } else {
                Log::warning("Current time is before start time!", [
                    'start_time' => $startTime->toDateTimeString(),
                    'current_time' => $currentTime->toDateTimeString()
                ]);
            }
            
            // DISABLED FOR DEBUGGING: Don't auto-submit the exam
            // if ($timeElapsedMinutes >= $exam->duration_minutes) {
            //     return redirect()->route('student.exam.submit', $uuid)
            //         ->with('info', 'Exam time has expired. Your answers have been submitted.');
            // }
            
            // Get exam questions
            $questions = $this->getExamQuestions($exam->id);
            
            return view('student.take-exam', compact('exam', 'questions', 'studentSession'));
            
        } catch (\Exception $e) {
            return redirect()->route('student.exam.access', $uuid)
                ->with('error', 'Error loading exam. Please try again.');
        }
    }
    
public function submitExam(Request $request, $uuid)
{
    $studentSession = session("student_exam_{$uuid}");
    $exam = Exam::where('uuid', $uuid)->firstOrFail();
    $examId = $exam->id;

    // ✅ Make sure we always have student_id
    $studentId = $studentSession['student_id'] ?? Session::get('student_id');

    if (!$studentId) {
        return redirect()->route('student.exam.access', $uuid)
            ->with('error', 'Student session expired. Please re-register.');
    }

    // Get all exam questions
    $allQuestions = DB::table('exam_questions')
        ->where('exam_id', $examId)
        ->pluck('question_id')
        ->toArray();

    $submittedAnswers = $request->input('answers', []);
    $finalAnswers = [];

    foreach ($allQuestions as $questionId) {
        $answerData = $submittedAnswers[$questionId] ?? [];
        $answerText = $answerData['answer_text'] ?? null;
        $chosenOptionIds = $answerData['chosen_option_ids'] ?? null;

        if (is_array($chosenOptionIds)) {
            $chosenOptionIds = json_encode($chosenOptionIds);
        }

        // ✅ Save only if student_id exists
        student_answer::updateOrCreate(
            [
                'exam_id' => $examId,
                'question_id' => $questionId,
                'student_id' => $studentId,
            ],
            [
                'answer_text' => $answerText,
                'chosen_option_ids' => $chosenOptionIds
            ]
        );

        $finalAnswers[$questionId] = [
            'answer_text' => $answerText,
            'chosen_option_ids' => $answerData['chosen_option_ids'] ?? []
        ];
    }

    // Store submission summary
    $summary = [
        'exam_name' => $exam->title,
        'student_name' => $studentSession['name'] ?? 'N/A',
        'student_email' => $studentSession['email'] ?? 'N/A',
        'submitted_at' => now()->toDateTimeString(),
        'total_questions' => count($allQuestions),
        'attempted' => count(array_filter($finalAnswers, function ($ans) {
            return !empty($ans['answer_text']) || !empty($ans['chosen_option_ids']);
        })),
        'unattempted' => count($allQuestions) - count(array_filter($finalAnswers, function ($ans) {
            return !empty($ans['answer_text']) || !empty($ans['chosen_option_ids']);
        })),
        'answers' => $finalAnswers
    ];

    // ✅ clear session + delete student now if you want
    Session::forget(["student_exam_{$uuid}", 'student_id', 'student_email']);
    Student::where('id', $studentId)->delete();

    return view('student.exam-submitted', [
        'summary' => $summary
    ]);
}

public function examSubmitted($uuid)
{
    $summary = session("exam_summary_{$uuid}");

    // Pass $uuid to the view
    return view('student.exam-submitted', [
        'uuid' => $uuid,
        'summary' => $summary,
    ]);
}


//     public function uploadProctorVideos(Request $request)
// {

//     Log::info('UploadProctorVideos called', [
//         'student_id' => $request->student_id,
//         'exam_id' => $request->exam_id,
//         'camera_present' => $request->hasFile('camera_video'),
//         'screen_present' => $request->hasFile('screen_video'),
//         'camera_size' => $request->file('camera_video')?->getSize(),
//         'screen_size' => $request->file('screen_video')?->getSize(),
//     ]);
//     $request->validate([
//         'camera_video' => 'required|file|mimes:webm,mp4|max:512000', // 500 MB
// 'screen_video' => 'required|file|mimes:webm,mp4|max:512000',
//         'exam_id' => 'required|exists:exams,id',
//         'student_id' => 'required|exists:students,id',
//     ]);

//     $cameraPath = $request->file('camera_video')->store('proctor_videos/camera', 'public');
//     $screenPath = $request->file('screen_video')->store('proctor_videos/screen', 'public');

//     $record = ProctorRecord::create([
//         'student_id' => $request->student_id,
//         'exam_id' => $request->exam_id,
//         'camera_video_path' => $cameraPath,
//         'screen_video_path' => $screenPath,
//     ]);

//      Log::info("Proctoring uploaded", [
//         'student_id' => $record->student_id,
//         'exam_id' => $record->exam_id,
//         'camera_path' => $cameraPath,
//         'screen_path' => $screenPath,
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Proctoring videos uploaded successfully!',
//     ]);
// }
public function uploadProctorVideos(Request $request)
{
    $request->validate([
        'camera_video' => 'nullable|mimes:webm,mp4,mov|max:51200',
        'screen_video' => 'nullable|mimes:webm,mp4,mov|max:51200',
        'student_id' => 'required|exists:students,id',
        'exam_id' => 'required|exists:exams,id',
    ]);

    $uploaded = [];

    $videos = [
        'camera_video' => 'camera',
        'screen_video' => 'screen',
    ];

    foreach ($videos as $inputName => $type) {
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);

            // Ensure folder exists
            $destination = storage_path('app/public/proctor_videos/');
            if (!file_exists($destination)) {
                mkdir($destination, 0775, true);
            }

            $filename = time() . '_' . $inputName . '.' . $file->getClientOriginalExtension();
            $fullPath = $destination . $filename;

            try {
                $file->move($destination, $filename);

                // ✅ Double check file actually exists and is not empty
                if (!file_exists($fullPath) || filesize($fullPath) === 0) {
                    throw new \Exception("File not saved or empty for $type video");
                }

                // ✅ Save DB record only if file is valid
                $video = ProctorVideo::create([
                    'student_id' => $request->student_id,
                    'exam_id' => $request->exam_id,
                    'type' => $type,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);

                $uploaded[] = $video;

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => "Failed to save $type video: " . $e->getMessage()
                ], 500);
            }
        }
    }

    return response()->json([
        'success' => true,
        'uploaded' => $uploaded
    ]);
}



    private function getExamQuestions(int $examId)
    {
        $questions = DB::table('exam_questions')
            ->leftJoin('questions', 'exam_questions.question_id', '=', 'questions.id')
            ->where('exam_questions.exam_id', $examId)
            ->select(
                'questions.*',
                'exam_questions.order_position'
            )
            ->orderBy('exam_questions.order_position')
            ->get();
        
        // Load options for MCQ questions
        foreach ($questions as $question) {
            if (in_array($question->type, ['mcq_single', 'mcq_multiple'])) {
                $question->options = DB::table('question_options')
                    ->where('question_id', $question->id)
                    ->select('id', 'option_text')  // Don't include is_correct
                    ->get();
            }
        }
        
        return $questions;
    }
    
}