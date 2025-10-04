<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class CSVImportController extends Controller
{
    /**
     * Display the CSV import form
     */
    public function showImportForm()
    {
        return view('admin.csv-import');
    }

    /**
     * Process CSV file and import questions
     */
    public function import(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            // Read CSV file
            $csvData = array_map('str_getcsv', file($path));
            $header = array_shift($csvData); // Remove header row

            // to check how many qns were successfully imported, skipped, errors
            $importStats = [
                'total_rows' => count($csvData),
                'imported' => 0,  
                'skipped' => 0,   
                'errors' => []
            ];

            DB::beginTransaction(); //if anything goes wrong, rollback

            foreach ($csvData as $rowIndex => $row) {
                try {
                    $questionData = $this->parseCSVRow($header, $row);
                    
                    if ($this->importQuestion($questionData)) { // returns true if imported, false if duplicate
                        $importStats['imported']++;
                    } else {
                        $importStats['skipped']++;
                    }
                    
                } catch (Exception $e) { 
                    $importStats['errors'][] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                    $importStats['skipped']++; //if any row fails, count as skipped
                }
            }

            DB::commit(); //save all changes

            return redirect()->back()->with([
                'success' => 'CSV Import completed successfully!',
                'import_stats' => $importStats
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['csv_file' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Parse CSV row into question data structure
     */
    private function parseCSVRow($header, $row)
    {
        $data = array_combine($header, $row); // combine header and row to associative array
        
        // Required fields validation
        $required = ['question_text', 'type', 'marks']; // question_text, type, marks are mandatory
        foreach ($required as $field) {
            if (empty($data[$field])) { // check if field is missing or empty
                throw new Exception("Missing required field: {$field}");
            }
        }

        // Parse options for MCQ questions
        $options = [ ];
        if (in_array($data['type'], ['mcq_single', 'mcq_multiple'])) { // MCQ types for options
            for ($i = 1; $i <= 6; $i++) { // Support up to 6 options
                $optionKey = "option_{$i}"; // option key
                $correctKey = "correct_{$i}"; // correct answer key

                if (!empty($data[$optionKey])) { // option text is present for this index
                    $options[] = [ // add option to options array
                        'text' => trim($data[$optionKey]),
                        'is_correct' => !empty($data[$correctKey]) && 
                                      strtolower($data[$correctKey]) === 'true' // correct if marked 'true'
                    ];
                }
            }
            
            if (empty($options)) {
                throw new Exception("MCQ question must have at least one option");
            }
        }

        return [ // return parsed question data
            'text' => trim($data['question_text']),
            'type' => $data['type'],
            'marks' => (int) $data['marks'],
            'difficulty' => $data['difficulty'] ?? 'medium',
            'tags' => $data['tags'] ?? '',
            'status' => $data['status'] ?? 'active',
            'options' => $options
        ];
    }

    /**
     * Import a single question with its options
     */
    private function importQuestion($questionData)
    {
        // Generate import hash to prevent duplicates
        $hashData = $questionData['text'] . '|' . 
                   implode('|', array_column($questionData['options'], 'text')) . '|' . 
                   $questionData['marks'];
        $importHash = hash('sha256', $hashData); // unique hash for this question sha256 is for security

        // Check if question already exists
        if (DB::table('questions')->where('import_hash', $importHash)->exists()) {
            return false; // Skip duplicate
        }

        // Insert question
        $questionId = DB::table('questions')->insertGetId([
            'text' => $questionData['text'],
            'type' => $questionData['type'],
            'marks' => $questionData['marks'],
            'difficulty' => $questionData['difficulty'],
            'tags' => $questionData['tags'],
            'status' => $questionData['status'],
            'import_hash' => $importHash,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insert options for MCQ questions
        if (!empty($questionData['options'])) {
            $optionsToInsert = [];
            foreach ($questionData['options'] as $option) {
                $optionsToInsert[] = [
                    'question_id' => $questionId,
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            DB::table('question_options')->insert($optionsToInsert);
        }

        return true; // Successfully imported
    }
}