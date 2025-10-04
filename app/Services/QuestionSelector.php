<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * QuestionSelector Service
 * 
 * Intelligent question selection engine that uses multiple algorithms
 * to find optimal question combinations for exam creation.
 * 
 * Algorithm Strategy:
 * 1. Greedy Randomized Search (Fast & Random)
 * 2. Dynamic Programming (Exact & Optimal) 
 * 3. Best-Fit Fallback (When exact impossible)
 */
class QuestionSelector
{
    /**
     * Select questions for an exam using intelligent algorithms
     * 
     * @param int $targetMarks - Desired total marks for the exam
     * @param array $filters - Additional filters (difficulty, tags, etc.)
     * @return array - Selected questions with metadata
     */
    public function selectQuestions(int $targetMarks, array $filters = []): array
    {
        // Step 1: Get available questions from database
        $availableQuestions = $this->getAvailableQuestions($filters);
        
        if ($availableQuestions->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No questions available for selection',
                'questions' => [],
                'total_marks' => 0,
                'algorithm_used' => 'none'
            ];
        }
        
        // Step 2: Try algorithms in order of preference
        
        // Algorithm 1 Greedy Randomized Search (Fast & gives variety)
        $result = $this->greedyRandomizedSearch($availableQuestions, $targetMarks);
        if ($result['success'] && $result['total_marks'] === $targetMarks) {
            $result['algorithm_used'] = 'greedy_randomized';
            return $result;
        }
        
        // Algorithm 2 Dynamic Programming (Precise, finds exact match)
        $result = $this->dynamicProgrammingSelection($availableQuestions, $targetMarks);
        if ($result['success'] && $result['total_marks'] === $targetMarks) {
            $result['algorithm_used'] = 'dynamic_programming';
            return $result;
        }
        
        // Algorithm 3 Best-Fit Fallback (Get as close as possible)
        $result = $this->bestFitSelection($availableQuestions, $targetMarks);
        $result['algorithm_used'] = 'best_fit_fallback';
        return $result;
    }
    
    /**
     * Get available questions from database with filters
     */
    private function getAvailableQuestions(array $filters = []): Collection
    {
        $query = DB::table('questions')
            ->where('status', 'active')  // Only active questions
            ->select('id', 'text', 'marks', 'difficulty', 'tags', 'type');
        
        // Apply filters if provided
        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }
        
        if (!empty($filters['tags'])) {
            $subject = strtolower($filters['tags']);
            $query->where(function($q) use ($subject) {
                // Exact tag matching first (most precise)
                $q->where('tags', 'LIKE', '%' . $subject . '%');
                
                // Subject-specific text matching to avoid cross-contamination
                if ($subject === 'python') {
                    $q->orWhere('text', 'LIKE', '%python%')
                      ->orWhere('text', 'LIKE', '%Python%');
                } elseif ($subject === 'php') {
                    $q->orWhere('text', 'LIKE', '%PHP%')
                      ->orWhere('text', 'LIKE', '%php%');
                } elseif ($subject === 'nodejs') {
                    $q->orWhere('text', 'LIKE', '%node%')
                      ->orWhere('text', 'LIKE', '%Node%')
                      ->orWhere('text', 'LIKE', '%nodejs%')
                      ->orWhere('text', 'LIKE', '%Node.js%');
                } elseif ($subject === 'javascript') {
                    $q->orWhere('text', 'LIKE', '%javascript%')
                      ->orWhere('text', 'LIKE', '%JavaScript%')
                      ->orWhere('text', 'LIKE', '%JS %');
                } elseif ($subject === 'java') {
                    $q->orWhere(function($subQ) {
                        $subQ->where('text', 'LIKE', '%java%')
                             ->where('text', 'NOT LIKE', '%javascript%')
                             ->where('text', 'NOT LIKE', '%JavaScript%');
                    });
                } elseif ($subject === 'sql') {
                    $q->orWhere('text', 'LIKE', '%SQL%')
                      ->orWhere('text', 'LIKE', '%sql%')
                      ->orWhere('text', 'LIKE', '%database%')
                      ->orWhere('text', 'LIKE', '%Database%');
                } elseif ($subject === 'dsa') {
                    $q->orWhere('text', 'LIKE', '%C++%')
                      ->orWhere('text', 'LIKE', '%c++%')
                      ->orWhere('text', 'LIKE', '%DSA%')
                      ->orWhere('text', 'LIKE', '%dsa%')
                      ->orWhere('text', 'LIKE', '%data structure%')
                      ->orWhere('text', 'LIKE', '%algorithm%')
                      ->orWhere('text', 'LIKE', '%Algorithm%');
                }
            });
        }
        
        if (!empty($filters['max_marks'])) {
            $query->where('marks', '<=', $filters['max_marks']);
        }
        
        if (!empty($filters['min_marks'])) {
            $query->where('marks', '>=', $filters['min_marks']);
        }
        
        return collect($query->get());
    }
    
    // Placeholder methods - we'll implement these step by step
    /**
     * Algorithm 1: Greedy Randomized Search
     * 
     * Strategy: 
     * - Randomly shuffle questions to add variety
     * - Greedily select questions that fit within remaining marks
     * - Multiple attempts with different random orders
     * - Fast execution, good for creating varied exams
     * 
     * @param Collection $questions Available questions
     * @param int $targetMarks Target total marks
     * @return array Selection result
     */
    private function greedyRandomizedSearch(Collection $questions, int $targetMarks): array
    {
        $bestSelection = [];
        $bestTotal = 0;
        $maxAttempts = 10; // Try 10 different random arrangements
        
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            // Shuffle questions for randomness and variety
            $shuffledQuestions = $questions->shuffle();
            
            $selected = [];
            $currentTotal = 0;
            $usedIds = [];
            
            foreach ($shuffledQuestions as $question) {
                $questionArray = (array) $question;
                
                // Skip if already used
                if (in_array($questionArray['id'], $usedIds)) {
                    continue;
                }
                
                // Check if adding this question would exceed target
                if ($currentTotal + $questionArray['marks'] <= $targetMarks) {
                    $selected[] = $questionArray;
                    $currentTotal += $questionArray['marks'];
                    $usedIds[] = $questionArray['id'];
                    
                    // Perfect match found!
                    if ($currentTotal === $targetMarks) {
                        return $this->formatResult($selected, 'greedy_randomized');
                    }
                }
            }
            
            // Keep track of best attempt (closest to target)
            if ($currentTotal > $bestTotal && $currentTotal <= $targetMarks) {
                $bestSelection = $selected;
                $bestTotal = $currentTotal;
            }
        }
        
        // Return best attempt if we found something reasonable
        if ($bestTotal >= $targetMarks * 0.8) { // At least 80% of target
            return $this->formatResult($bestSelection, 'greedy_randomized');
        }
        
        return ['success' => false, 'total_marks' => $bestTotal, 'questions' => $bestSelection];
    }
    
    /**
     * Algorithm 2: Dynamic Programming Selection (Knapsack Problem)
     * 
     * Strategy:
     * - Mathematical approach to find EXACT solution
     * - Uses knapsack algorithm to find optimal combination
     * - Guarantees best possible result if solution exists
     * - Slower but more precise than greedy approach
     * 
     * @param Collection $questions Available questions  
     * @param int $targetMarks Target total marks
     * @return array Selection result
     */
    private function dynamicProgrammingSelection(Collection $questions, int $targetMarks): array
    {
        $questionArray = $questions->values()->toArray();
        $n = count($questionArray);
        
        if ($n === 0 || $targetMarks <= 0) {
            return ['success' => false, 'total_marks' => 0, 'questions' => []];
        }
        
        // DP table: dp[i][w] = true if we can achieve weight w using first i items
        $dp = [];
        for ($i = 0; $i <= $n; $i++) {
            $dp[$i] = array_fill(0, $targetMarks + 1, false);
        }
        
        // Base case: 0 marks always achievable with 0 questions
        for ($i = 0; $i <= $n; $i++) {
            $dp[$i][0] = true;
        }
        
        // Fill DP table
        for ($i = 1; $i <= $n; $i++) {
            $currentQuestion = (array) $questionArray[$i - 1];
            $marks = (int) $currentQuestion['marks'];
            
            for ($w = 0; $w <= $targetMarks; $w++) {
                // Option 1: Don't take current question
                $dp[$i][$w] = $dp[$i - 1][$w];
                
                // Option 2: Take current question (if it fits)
                if ($marks <= $w && $dp[$i - 1][$w - $marks]) {
                    $dp[$i][$w] = true;
                }
            }
        }
        
        // Check if target is achievable
        if (!$dp[$n][$targetMarks]) {
            return ['success' => false, 'total_marks' => 0, 'questions' => []];
        }
        
        // Backtrack to find which questions were selected
        $selected = [];
        $w = $targetMarks;
        
        for ($i = $n; $i > 0 && $w > 0; $i--) {
            // If dp[i][w] != dp[i-1][w], then this question was included
            if ($w >= 0 && !$dp[$i - 1][$w]) {
                $question = (array) $questionArray[$i - 1];
                $selected[] = $question;
                $w -= (int) $question['marks'];
            }
        }
        
        return $this->formatResult(array_reverse($selected), 'dynamic_programming');
    }
    
    /**
     * Algorithm 3: Best-Fit Fallback Selection
     * 
     * Strategy:
     * - When exact match impossible, get as close as possible
     * - Prioritizes getting close to target rather than exact match
     * - Uses largest-first approach to minimize number of questions
     * - Always returns something useful (never complete failure)
     * 
     * @param Collection $questions Available questions
     * @param int $targetMarks Target total marks
     * @return array Selection result
     */
    private function bestFitSelection(Collection $questions, int $targetMarks): array
    {
        if ($questions->isEmpty()) {
            return ['success' => false, 'total_marks' => 0, 'questions' => []];
        }
        
        // Sort by marks descending (largest first for efficiency)
        $sortedQuestions = $questions->sortByDesc('marks')->values();
        
        $selected = [];
        $currentTotal = 0;
        $usedIds = [];
        
        // Strategy 1: Try to get as close as possible without exceeding
        foreach ($sortedQuestions as $question) {
            $questionArray = (array) $question;
            $marks = (int) $questionArray['marks'];
            
            if (in_array($questionArray['id'], $usedIds)) {
                continue;
            }
            
            // Add if it doesn't exceed target
            if ($currentTotal + $marks <= $targetMarks) {
                $selected[] = $questionArray;
                $currentTotal += $marks;
                $usedIds[] = $questionArray['id'];
                
                // Perfect match!
                if ($currentTotal === $targetMarks) {
                    return $this->formatResult($selected, 'best_fit_exact');
                }
            }
        }
        
        // Strategy 2: If we're still far from target, try smaller questions
        if ($currentTotal < $targetMarks * 0.7) { // Less than 70% of target
            $remainingQuestions = $sortedQuestions->whereNotIn('id', $usedIds);
            
            foreach ($remainingQuestions->sortBy('marks') as $question) {
                $questionArray = (array) $question;
                $marks = (int) $questionArray['marks'];
                
                if ($currentTotal + $marks <= $targetMarks) {
                    $selected[] = $questionArray;
                    $currentTotal += $marks;
                    $usedIds[] = $questionArray['id'];
                }
            }
        }
        
        // Strategy 3: If we have room and nothing fits, add one question that slightly exceeds
        if (empty($selected) || $currentTotal < $targetMarks * 0.5) {
            $availableQuestions = $sortedQuestions->whereNotIn('id', $usedIds);
            
            if ($availableQuestions->isNotEmpty()) {
                // Find smallest question that gets us closer
                $bestQuestion = null;
                $bestDifference = PHP_INT_MAX;
                
                foreach ($availableQuestions as $question) {
                    $questionArray = (array) $question;
                    $marks = (int) $questionArray['marks'];
                    $newTotal = $currentTotal + $marks;
                    
                    // Calculate how close this gets us to target
                    $difference = abs($targetMarks - $newTotal);
                    
                    if ($difference < $bestDifference) {
                        $bestDifference = $difference;
                        $bestQuestion = $questionArray;
                    }
                }
                
                if ($bestQuestion) {
                    $selected[] = $bestQuestion;
                    $currentTotal += (int) $bestQuestion['marks'];
                }
            }
        }
        
        $algorithm = ($currentTotal === $targetMarks) ? 'best_fit_exact' : 'best_fit_approximate';
        return $this->formatResult($selected, $algorithm);
    }
    
    /**
     * Validate and format final result
     */
    private function formatResult(array $selectedQuestions, string $algorithm): array
    {
        $totalMarks = array_sum(array_column($selectedQuestions, 'marks'));
        
        return [
            'success' => !empty($selectedQuestions),
            'questions' => $selectedQuestions,
            'total_marks' => $totalMarks,
            'question_count' => count($selectedQuestions),
            'algorithm_used' => $algorithm,
            'selection_metadata' => [
                'avg_marks_per_question' => $totalMarks > 0 ? round($totalMarks / count($selectedQuestions), 2) : 0,
                'difficulty_distribution' => $this->getDifficultyDistribution($selectedQuestions),
                'type_distribution' => $this->getTypeDistribution($selectedQuestions)
            ]
        ];
    }
    
    /**
     * Get difficulty distribution for analytics
     */
    private function getDifficultyDistribution(array $questions): array
    {
        $distribution = ['easy' => 0, 'medium' => 0, 'hard' => 0];
        foreach ($questions as $question) {
            if (isset($distribution[$question['difficulty']])) {
                $distribution[$question['difficulty']]++;
            }
        }
        return $distribution;
    }
    
    /**
     * Get question type distribution
     */
    private function getTypeDistribution(array $questions): array
    {
        $distribution = [];
        foreach ($questions as $question) {
            $type = $question['type'] ?? 'unknown';
            $distribution[$type] = ($distribution[$type] ?? 0) + 1;
        }
        return $distribution;
    }
}