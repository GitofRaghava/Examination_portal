<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $exam->name }} - Take Exam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="max-w-3xl mx-auto mt-10 bg-white p-6 shadow rounded-lg">
    <h1 class="text-2xl font-bold mb-2">{{ $exam->name }}</h1>
    <p class="mb-2 text-gray-600">{{ $exam->description }}</p>
    <p class="mb-4 font-semibold">Duration: {{ $exam->duration_minutes }} minutes</p>
    <p id="timer" class="mb-6 text-red-600 font-bold"></p>

    <form id="examForm" method="POST" action="{{ route('exam.submit', $exam->uuid) }}">
        @csrf

        @foreach($questions as $q)
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <p class="font-semibold">{{ $loop->iteration }}. {{ $q->text }}</p>

                {{-- Descriptive Question --}}
                @if($q->type === 'descriptive')
                    <textarea 
                        name="answers[{{ $q->id }}][answer_text]" 
                        rows="3" 
                        class="w-full mt-2 border rounded p-2"
                        placeholder="Type your answer here..."></textarea>
                @endif

                {{-- Multiple Choice Questions --}}
                @if(in_array($q->type, ['mcq_single', 'mcq_multiple']))
                    <div class="mt-2 space-y-2">
                        @foreach($q->options as $option)
                            <label class="flex items-center">
                                <input 
                                    type="{{ $q->type === 'mcq_single' ? 'radio' : 'checkbox' }}" 
                                    name="answers[{{ $q->id }}][chosen_option_ids]{{ $q->type === 'mcq_single' ? '' : '[]' }}" 
                                    value="{{ $option->id }}" 
                                    class="mr-2"
                                >
                                {{ $option->option_text }}
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        <button id="submitBtn" type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            Submit Exam
        </button>
    </form>
</div>

<!-- Hidden video previews (optional for debugging) -->
<video id="cameraPreview" autoplay muted style="width:200px; display:none;"></video>
<video id="screenPreview" autoplay style="width:200px; display:none;"></video>

<script>
    const durationMinutes = Number('{{ $exam->duration_minutes }}');
    const durationMs = durationMinutes * 60 * 1000;

    let cameraRecorder, screenRecorder;
    let cameraChunks = [], screenChunks = [];
    let countdownInterval;

    // Timer countdown
    function startTimer() {
        let timeLeft = durationMs / 1000; // seconds
        const timerEl = document.getElementById('timer');

        countdownInterval = setInterval(() => {
            const mins = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            timerEl.textContent = `Time left: ${mins}:${secs < 10 ? '0' : ''}${secs}`;
            timeLeft--;
            if (timeLeft < 0) {
                clearInterval(countdownInterval);
                document.getElementById('examForm').dispatchEvent(new Event('submit', {cancelable: true}));
            }
        }, 1000);
    }

    // Disable right-click / copy-paste
    function disableInteractions() {
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && ['c','v','x','u'].includes(e.key.toLowerCase())) e.preventDefault();
        });
        window.addEventListener('beforeunload', e => { e.preventDefault(); e.returnValue = ''; });
    }

    // Start recording
    async function startRecording() {
        try {
            const cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            document.getElementById('cameraPreview').srcObject = cameraStream;

            const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: true });
            document.getElementById('screenPreview').srcObject = screenStream;

            // Camera recorder
            cameraRecorder = new MediaRecorder(cameraStream);
            cameraRecorder.ondataavailable = e => cameraChunks.push(e.data);
            cameraRecorder.start(1000);

            // Screen recorder
            screenRecorder = new MediaRecorder(screenStream);
            screenRecorder.ondataavailable = e => screenChunks.push(e.data);
            screenRecorder.start(1000);

            startTimer();
            disableInteractions();

            // Stop if screen share ends
            screenStream.getVideoTracks()[0].addEventListener('ended', () => stopRecording());
        } catch (err) {
            alert('Error starting proctoring: ' + err.message);
        }
    }

    // Stop a recorder and wait for full data
    function stopRecorder(recorder) {
        return new Promise(resolve => {
            if (!recorder || recorder.state === 'inactive') return resolve();
            recorder.onstop = () => resolve();
            recorder.stop();
        });
    }

    // Stop all recording and upload
    async function stopRecording() {
        clearInterval(countdownInterval);

        // Wait until both recorders finish
        await stopRecorder(cameraRecorder);
        await stopRecorder(screenRecorder);

        await uploadVideos();
    }

    // Upload videos
    async function uploadVideos() {
        if (cameraChunks.length === 0 && screenChunks.length === 0) {
            console.warn("No video data to upload");
            return;
        }

        const cameraBlob = new Blob(cameraChunks, { type: 'video/webm' });
        const screenBlob = new Blob(screenChunks, { type: 'video/webm' });
        console.log('Camera Blob size:', cameraBlob.size);
        console.log('Screen Blob size:', screenBlob.size);


        const formData = new FormData();
        formData.append('camera_video', cameraBlob, 'camera.webm');
        formData.append('screen_video', screenBlob, 'screen.webm');

        // Include exam_id and student_id
        const studentId = '{{ $studentSession["student_id"] ?? auth()->user()->id }}';
    formData.append('student_id', studentId);
    formData.append('exam_id', '{{ $exam->id }}');

        try {
            const res = await fetch("{{ route('upload.proctor.videos', ['uuid' => $exam->uuid]) }}", {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    body: formData
});
            const data = await res.json();
            console.log("Proctor upload response:", data);
        } catch (err) {
            console.error("Video upload failed:", err);
        }
    }

    // Intercept exam form submit
    document.getElementById('examForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        await stopRecording(); // wait for upload
        e.target.submit();     // submit answers after upload
    });

    // Start on page load
    window.onload = () => startRecording();
</script>


</body>
</html>
