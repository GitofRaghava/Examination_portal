<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Submitted</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="max-w-4xl mx-auto mt-10 bg-white p-6 shadow rounded-lg">
    <h1 class="text-3xl font-bold mb-4 text-green-600">✅ Exam Submitted Successfully!</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    @php
    use App\Models\Question;
    use App\Models\QuestionOption;

    // $studentSession is already passed from controller
    $examName = $studentSession['exam_name'] ?? 'Exam';
    $studentName = $studentSession['name'] ?? 'N/A';
    $studentEmail = $studentSession['email'] ?? 'N/A';
    $submittedAt = $studentSession['submitted_at'] ?? now()->toDateTimeString();
    $answers = $studentSession['answers'] ?? [];

   $totalQuestions = count($answers);
$attemptedQuestions = 0;

foreach($answers as $ans) {
    $hasText = isset($ans['answer_text']) && trim($ans['answer_text']) !== '';
    $hasOptions = isset($ans['chosen_option_ids']) && count((array)$ans['chosen_option_ids']) > 0;

    if ($hasText || $hasOptions) {
        $attemptedQuestions++;
    }
}
@endphp

    <div class="mb-6">
        <p><strong>Exam Name:</strong> {{ $examName }}</p>
        <p><strong>Student Name:</strong> {{ $studentName }}</p>
        <p><strong>Email:</strong> {{ $studentEmail }}</p>
        <p><strong>Submitted At:</strong> {{ $submittedAt }}</p>
    </div>

    <div class="mb-6 p-4 bg-gray-50 rounded">
        <p><strong>Total Questions:</strong> {{ $totalQuestions }}</p>
        <p><strong>Attempted Questions:</strong> {{ $attemptedQuestions }}</p>
        <p><strong>Unattempted Questions:</strong> {{ $totalQuestions - $attemptedQuestions }}</p>
    </div>

    <h2 class="text-2xl font-semibold mb-4">Your Answers:</h2>

    <div class="space-y-4">
        @foreach($answers as $questionId => $ans)
            @php
                $question = Question::find($questionId);
                $chosenOptionTexts = [];
                if(!empty($ans['chosen_option_ids'])) {
                    $options = QuestionOption::whereIn('id', (array)$ans['chosen_option_ids'])->pluck('option_text')->toArray();
                    $chosenOptionTexts = $options;
                }
            @endphp
            <div class="p-4 border rounded bg-gray-50">
                <p class="font-semibold">Question {{ $loop->iteration }}: {{ $question->text ?? 'Question not found' }}</p>
                
                @if(!empty($ans['answer_text']))
                    <p class="mt-1"><em>Descriptive Answer:</em> {{ $ans['answer_text'] }}</p>
                @endif

                @if(!empty($chosenOptionTexts))
                    <p class="mt-1"><em>Selected Options:</em> {{ implode(', ', $chosenOptionTexts) }}</p>
                @endif

                @if(empty($ans['answer_text']) && empty($chosenOptionTexts))
                    <p class="mt-1 text-red-500"><em>Not attempted</em></p>
                @endif
            </div>
        @endforeach
    </div>

</div>

</body>
</html>
