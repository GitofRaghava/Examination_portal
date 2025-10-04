<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exam;
use App\Models\Question;
use App\Services\QuestionSelector;

class AssignQuestionsToExam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:assign-questions {exam_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign questions to an exam using intelligent selection with subject filtering';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $examId = $this->argument('exam_id');
        
        try {
            $exam = Exam::findOrFail($examId);
            
            $this->info('📝 Assigning questions to: ' . $exam->name);
            $this->line('🎯 Target marks: ' . $exam->total_marks);
            
            // Check available questions
            $totalQuestions = Question::where('status', 'active')->count();
            $this->line('📚 Available questions: ' . $totalQuestions);
            
            if ($totalQuestions === 0) {
                $this->error('❌ No active questions found in database');
                $this->line('💡 Please import questions first using: php artisan csv:import');
                return 1;
            }
            
            // Use QuestionSelector to intelligently select questions
            $questionSelector = new QuestionSelector();
            
            // Add filters based on exam name/subject
            $filters = [];
            $examName = strtolower($exam->name);
            
            // Enhanced subject detection from exam name
            if (str_contains($examName, 'python')) {
                $filters['tags'] = 'python';
                $this->line('🐍 Detected Python exam - filtering for Python questions only');
            } elseif (str_contains($examName, 'php')) {
                $filters['tags'] = 'php';
                $this->line('🐘 Detected PHP exam - filtering for PHP questions only');
            } elseif (str_contains($examName, 'node') || str_contains($examName, 'nodejs')) {
                $filters['tags'] = 'nodejs';
                $this->line('🟢 Detected Node.js exam - filtering for Node.js questions only');
            } elseif (str_contains($examName, 'javascript') || str_contains($examName, 'js')) {
                $filters['tags'] = 'javascript';
                $this->line('🟡 Detected JavaScript exam - filtering for JavaScript questions only');
            } elseif (str_contains($examName, 'java') && !str_contains($examName, 'javascript')) {
                $filters['tags'] = 'java';
                $this->line('☕ Detected Java exam - filtering for Java questions only');
            } elseif (str_contains($examName, 'sql') || str_contains($examName, 'database')) {
                $filters['tags'] = 'sql';
                $this->line('🗄️ Detected SQL/Database exam - filtering for SQL questions only');
            } elseif (str_contains($examName, 'c++') || str_contains($examName, 'dsa') || str_contains($examName, 'data structures')) {
                $filters['tags'] = 'dsa';
                $this->line('⚡ Detected C++/DSA exam - filtering for DSA questions only');
            } else {
                $this->line('🔍 No specific subject detected - using all available questions');
            }
            
            $result = $questionSelector->selectQuestions($exam->total_marks, $filters);
            
            if (!$result['success'] || empty($result['questions'])) {
                $this->error('❌ No questions could be selected for this exam');
                $this->line('Reason: ' . ($result['message'] ?? 'Unknown error'));
                
                if (!empty($filters['tags'])) {
                    $this->line('');
                    $this->line('💡 SOLUTION: Import a CSV file containing ' . strtoupper($filters['tags']) . ' questions first');
                    $this->line('   The exam "' . $exam->name . '" requires ' . $filters['tags'] . '-specific questions');
                    $this->line('   Currently no ' . $filters['tags'] . ' questions exist in the database');
                    $this->line('');
                    $this->line('📝 Steps to fix:');
                    $this->line('   1. Create/prepare a CSV file with ' . $filters['tags'] . ' questions');
                    $this->line('   2. Import it via: Admin Dashboard → Import Questions');
                    $this->line('   3. Re-run: php artisan exam:assign-questions ' . $exam->id);
                }
                
                return 1;
            }
            
            $selectedQuestions = $result['questions'];
            
            // Clear existing questions from exam first
            $exam->questions()->detach();
            
            // Assign selected questions to the exam
            foreach ($selectedQuestions as $index => $questionData) {
                $exam->questions()->attach($questionData['id'], [
                    'order_position' => $index + 1
                ]);
            }
            
            $this->info('✅ Questions assigned successfully!');
            $this->line('📊 Total questions: ' . count($selectedQuestions));
            $this->line('🎯 Total marks: ' . $result['total_marks']);
            $this->line('🧠 Algorithm used: ' . $result['algorithm_used']);
            $this->line('📝 Exam: ' . $exam->name);
            $this->line('🔗 UUID: ' . $exam->uuid);
            
            // Display selection metadata
            if (isset($result['selection_metadata'])) {
                $metadata = $result['selection_metadata'];
                $this->line('');
                $this->line('📈 Selection Details:');
                $this->line('   • Avg marks per question: ' . $metadata['avg_marks_per_question']);
                
                if (isset($metadata['difficulty_distribution'])) {
                    $diff = $metadata['difficulty_distribution'];
                    $this->line('   • Easy questions: ' . ($diff['easy'] ?? 0));
                    $this->line('   • Medium questions: ' . ($diff['medium'] ?? 0));
                    $this->line('   • Hard questions: ' . ($diff['hard'] ?? 0));
                }
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}