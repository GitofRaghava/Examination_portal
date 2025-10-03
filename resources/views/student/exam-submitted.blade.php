<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Submitted</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Use Inter font for a professional, clean look */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fc;
        }
        .data-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body class="min-h-screen p-4 sm:p-8">

<div class="max-w-4xl mx-auto mt-10 mb-10 bg-white p-8 shadow-2xl rounded-2xl border border-green-200">
    <div class="text-center mb-8">
        <!-- Success Icon -->
        <svg class="mx-auto h-16 w-16 text-green-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h1 class="text-4xl font-extrabold text-green-700 tracking-tight">
            Submission Confirmed!
        </h1>
        <p class="text-lg text-gray-500 mt-2">Your responses have been successfully recorded and sent.</p>
    </div>

    <!-- Session Success Message -->
    @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 text-green-700 font-semibold rounded-xl border border-green-200 shadow-inner text-center">
            {{ session('success') }}
        </div>
    @endif


    <!-- Student and Exam Details -->
    <h2 class="text-2xl font-bold text-gray-700 mb-5 border-b pb-2">Candidate & Exam Details</h2>
    <div class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
        <div class="data-card">
            <strong class="text-gray-800 flex items-center"><span class="mr-2 text-indigo-500">📝</span> Exam Name:</strong>
            <span class="font-semibold text-indigo-700">{{ $summary['exam_name'] ?? 'Exam' }}</span>
        </div>
        <div class="data-card">
            <strong class="text-gray-800 flex items-center"><span class="mr-2 text-indigo-500">🧑‍🎓</span> Student Name:</strong>
            <span class="font-semibold">{{ $summary['student_name'] ?? 'N/A' }}</span>
        </div>
        <div class="data-card">
            <strong class="text-gray-800 flex items-center"><span class="mr-2 text-indigo-500">✉️</span> Email:</strong>
            <span class="font-medium truncate">{{ $summary['student_email'] ?? 'N/A' }}</span>
        </div>
        <div class="data-card">
            <strong class="text-gray-800 flex items-center"><span class="mr-2 text-indigo-500">🕒</span> Submitted At:</strong>
            <span class="font-mono text-sm">{{ $summary['submitted_at'] ?? now()->toDateTimeString() }}</span>
        </div>
    </div>

    <!-- Summary Statistics -->
    <h2 class="text-2xl font-bold text-gray-700 mb-5 border-b pb-2">Assessment Summary</h2>
    <div class="mb-12 grid grid-cols-3 gap-6 text-center">
        <!-- Total -->
        <div class="p-6 bg-indigo-50 border-b-4 border-indigo-600 rounded-xl shadow-lg transform hover:scale-[1.02] transition duration-200">
            <p class="text-5xl font-extrabold text-indigo-800">{{ $summary['total_questions'] ?? 0 }}</p>
            <p class="text-base font-semibold text-indigo-700 mt-2">Total Questions</p>
        </div>
        <!-- Attempted -->
        <div class="p-6 bg-green-50 border-b-4 border-green-600 rounded-xl shadow-lg transform hover:scale-[1.02] transition duration-200">
            <p class="text-5xl font-extrabold text-green-700">{{ $summary['attempted'] ?? 0 }}</p>
            <p class="text-base font-semibold text-green-700 mt-2">Attempted</p>
        </div>
        <!-- Unattempted -->
        <div class="p-6 bg-red-50 border-b-4 border-red-600 rounded-xl shadow-lg transform hover:scale-[1.02] transition duration-200">
            <p class="text-5xl font-extrabold text-red-700">{{ $summary['unattempted'] ?? 0 }}</p>
            <p class="text-base font-semibold text-red-700 mt-2">Unattempted</p>
        </div>
    </div>

<!-- Review of Responses -->
@if(!empty($summary['answers']))
    <h2 class="text-2xl font-bold text-gray-700 mb-6 border-b pb-2">Review of Your Responses</h2>
    <div class="space-y-6">
        @foreach($summary['answers'] as $questionId => $ans)
            @php
                // These models are assumed to be loaded and accessible via the Blade context, 
                // though for production, complex data lookups should be done in the controller.
                // We keep the Blade logic simple for presentation purposes.
                $question = \App\Models\Question::find($questionId);
                $chosenOptionTexts = [];
                $isAttempted = !empty($ans['answer_text']) || !empty($ans['chosen_option_ids']);

                if(!empty($ans['chosen_option_ids'])) {
                    // Ensures $ans['chosen_option_ids'] is an array for whereIn
                    $optionIds = is_array($ans['chosen_option_ids']) ? $ans['chosen_option_ids'] : [$ans['chosen_option_ids']]; 
                    $options = \App\Models\QuestionOption::whereIn('id', $optionIds)->pluck('option_text')->toArray();
                    $chosenOptionTexts = $options;
                }
            @endphp
            
            <div class="p-6 rounded-xl bg-gray-50 shadow-md transition duration-200 
                        {{ $isAttempted ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500' }}">
                
                <!-- Question Text -->
                <p class="font-bold text-xl text-gray-800 mb-3">
                    <span class="text-indigo-600 mr-2">{{ $loop->iteration }}.</span> 
                    {{ $question->text ?? 'Question not found' }}
                </p>
                
                <!-- Answer Content -->
                @if($isAttempted)
                    <div class="mt-3 p-4 bg-white border border-gray-200 rounded-lg text-gray-700">
                        <p class="font-semibold text-sm text-gray-500 mb-2 border-b pb-1">Your Response:</p>
                        
                        @if(!empty($ans['answer_text']))
                            <!-- Descriptive Answer -->
                            <p class="italic whitespace-pre-wrap text-base">{{ $ans['answer_text'] }}</p>
                        @endif

                        @if(!empty($chosenOptionTexts))
                            <!-- Selected Options -->
                            <ul class="list-disc ml-5 mt-2 space-y-1">
                                @foreach($chosenOptionTexts as $text)
                                    <li class="text-green-700 font-medium text-base">{{ $text }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @else
                    <!-- Not Attempted Message -->
                    <div class="mt-3 p-4 bg-red-100 border border-red-300 rounded-lg">
                        <p class="text-red-700 font-extrabold text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            This question was **NOT** attempted.
                        </p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

</div>

<!-- Sticky Footer -->
<div class="sticky bottom-0 mt-8 p-4 text-center bg-white shadow-inner border-t border-gray-100 w-full">
    <div class="max-w-4xl mx-auto">
        <p class="text-gray-500 text-sm font-medium">
            Your responses have been successfully recorded and are awaiting final evaluation by the administrator.
        </p>
        <p class="text-xs text-gray-400 mt-1">
            Do not refresh or navigate away from this page unless instructed.
        </p>
    </div>
</div>

</body>
</html>
