<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $exam->name }} - Take Exam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<<div class="max-w-4xl mx-auto mt-10 bg-white p-8 shadow-2xl rounded-xl border border-gray-100">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-2 border-b pb-2">{{ $exam->name }}</h1>
    <p class="mb-4 text-gray-500 italic">{{ $exam->description }}</p>
    <div class="flex justify-between items-center mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="font-bold text-lg text-blue-700">Duration: <span class="text-blue-900">{{ $exam->duration_minutes }} minutes</span></p>
        <p id="timer" class="text-2xl text-red-700 font-extrabold tracking-wide bg-red-100 px-3 py-1 rounded-full shadow-inner"></p>
    </div>

    <form id="examForm" method="POST" action="{{ route('exam.submit', $exam->uuid) }}">
        @csrf

        @foreach($questions as $q)
            <div class="mb-8 p-6 border-2 border-gray-100 rounded-xl bg-gray-50 hover:shadow-md transition duration-300">
                <p class="font-bold text-xl text-gray-800 mb-3 border-b-2 border-gray-200 pb-2">
                    <span class="text-indigo-600 mr-2">{{ $loop->iteration }}.</span> {{ $q->text }}
                </p>

                {{-- Descriptive Question --}}
                @if($q->type === 'descriptive')
                    <textarea 
                        name="answers[{{ $q->id }}][answer_text]" 
                        rows="4" 
                        class="w-full mt-3 border border-gray-300 rounded-lg p-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm"
                        placeholder="Type your detailed answer here..."></textarea>
                @endif

                {{-- Multiple Choice Questions --}}
                @if(in_array($q->type, ['mcq_single', 'mcq_multiple']))
                    <div class="mt-4 space-y-3">
                        @foreach($q->options as $option)
                            <label class="flex items-start p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-indigo-50 transition duration-150">
                                <input 
                                    type="{{ $q->type === 'mcq_single' ? 'radio' : 'checkbox' }}" 
                                    name="answers[{{ $q->id }}][chosen_option_ids]{{ $q->type === 'mcq_single' ? '' : '[]' }}" 
                                    value="{{ $option->id }}" 
                                    class="mt-1 mr-3 h-5 w-5 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                >
                                <span class="text-gray-700">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        <button id="submitBtn" type="submit" class="w-full px-4 py-3 bg-indigo-600 text-white font-semibold text-lg rounded-lg shadow-xl hover:bg-indigo-700 transition duration-300 transform hover:scale-[1.01] focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
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
        let timeLeft = durationMs / 1000;
        const timerEl = document.getElementById('timer');

        countdownInterval = setInterval(() => {
            const mins = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            timerEl.textContent = `Time left: ${mins}:${secs < 10 ? '0' : ''}${secs}`;
            timeLeft--;

            if (timeLeft < 0) {
                clearInterval(countdownInterval);
                document.getElementById('examForm').dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }, 1000);
    }

    // Disable right-click / copy-paste
    function disableInteractions() {
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && ['c', 'v', 'x', 'u'].includes(e.key.toLowerCase())) e.preventDefault();
        });
        window.addEventListener('beforeunload', e => { e.preventDefault(); e.returnValue = ''; });
    }

    // Start recording
    async function startRecording() {
        try {
            // Camera (with audio)
            const cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            document.getElementById('cameraPreview').srcObject = cameraStream;

            // Screen (video only - audio may cause issues)
            const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
            document.getElementById('screenPreview').srcObject = screenStream;

            // Camera recorder
            cameraRecorder = new MediaRecorder(cameraStream, { mimeType: 'video/webm; codecs=vp9' });
            cameraRecorder.ondataavailable = e => { if (e.data.size > 0) cameraChunks.push(e.data); };
            cameraRecorder.start(1000);

            // Screen recorder
            screenRecorder = new MediaRecorder(screenStream, { mimeType: 'video/webm; codecs=vp9' });
            screenRecorder.ondataavailable = e => { if (e.data.size > 0) screenChunks.push(e.data); };
            screenRecorder.start(1000);

            console.log("✅ Recording started: Camera + Screen");

            startTimer();
            disableInteractions();

            // If screen share ends early
            screenStream.getVideoTracks()[0].addEventListener('ended', () => {
                console.warn("⚠️ Screen sharing stopped early!");
                stopRecorder(screenRecorder, screenChunks);
            });

        } catch (err) {
            alert('Error starting proctoring: ' + err.message);
        }
    }

    // Stop a recorder
    function stopRecorder(recorder, chunks) {
        return new Promise(resolve => {
            if (!recorder || recorder.state === 'inactive') return resolve();
            recorder.onstop = () => {
                console.log("🛑 Recorder stopped:", recorder.mimeType, "Chunks:", chunks.length);
                resolve();
            };
            recorder.stop();
        });
    }

    // Stop all recording
    async function stopRecording() {
        clearInterval(countdownInterval);
        await stopRecorder(cameraRecorder, cameraChunks);
        await stopRecorder(screenRecorder, screenChunks);
        await uploadVideos();
    }

    // Upload videos
    async function uploadVideos() {
        const cameraBlob = new Blob(cameraChunks, { type: 'video/webm' });
        const screenBlob = new Blob(screenChunks, { type: 'video/webm' });

        console.log("🎥 Camera Blob size:", cameraBlob.size, "chunks:", cameraChunks.length);
        console.log("🖥️ Screen Blob size:", screenBlob.size, "chunks:", screenChunks.length);

        if (cameraBlob.size === 0 && screenBlob.size === 0) {
            console.error("❌ No video data to upload");
            return;
        }

        const formData = new FormData();
        if (cameraBlob.size > 0) formData.append('camera_video', cameraBlob, 'camera.webm');
        if (screenBlob.size > 0) formData.append('screen_video', screenBlob, 'screen.webm');

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
            console.log("📤 Proctor upload response:", data);

            if (!data.success) {
                console.error("❌ Upload failed:", data.message);
            } else {
                console.log("✅ Videos uploaded successfully!");
            }
        } catch (err) {
            console.error("❌ Video upload failed:", err);
        }
    }

    // Ensure form submits only after videos uploaded
    document.getElementById('examForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        console.log("⏹️ Stopping recordings & uploading before submit...");
        await stopRecording();
        e.target.submit();
    });

    // Start on page load
    window.onload = () => startRecording();
</script>


</body>
</html>
