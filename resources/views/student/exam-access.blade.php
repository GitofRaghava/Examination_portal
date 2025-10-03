<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->name }} - Examination Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-8 text-center">
            <div class="mb-4">
                <svg class="mx-auto h-16 w-16 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-2">{{ $exam->name }}</h1>
            <p class="text-blue-100">Online Examination</p>
        </div>

        <!-- Exam Info -->
        <div class="p-8">
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Exam Information</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">📝 Total Marks:</span>
                        <span class="font-semibold text-gray-800">{{ $exam->total_marks }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">⏱️ Duration:</span>
                        <span class="font-semibold text-gray-800">{{ $exam->duration_minutes }} minutes</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">📊 Status:</span>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-3 text-gray-800">📋 Instructions</h3>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>• Fill in your details accurately before starting</li>
                        <li>• You cannot pause the exam once started</li>
                        <li>• Your answers are automatically saved</li>
                        <li>• Submit before time expires to avoid data loss</li>
                        <li>• Ensure stable internet connection throughout</li>
                    </ul>
                </div>
            </div>

            <!-- Error Messages -->
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
            @endif

            <!-- Student Details Form -->
<form action="{{ route('student.exam.register', $exam->uuid) }}" method="POST" class="space-y-5">
            @csrf

            <!-- Hidden Exam ID -->
            <input type="hidden" name="exam_id" value="{{ $exam->id }}">

            <!-- Candidate Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                <input type="text" name="candidate_name" required 
                       value="{{ old('candidate_name') }}"
                       placeholder="Enter your full name"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('candidate_name') border-red-500 @enderror">
                @error('candidate_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Candidate Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                <input type="email" name="candidate_email" required 
                       value="{{ old('candidate_email') }}"
                       placeholder="Enter your email address"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('candidate_email') border-red-500 @enderror">
                @error('candidate_email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Candidate Contact (Optional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number (Optional)</label>
                <input type="text" name="candidate_contact" 
                       value="{{ old('candidate_contact') }}"
                       placeholder="Enter your contact number"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('candidate_contact') border-red-500 @enderror">
                @error('candidate_contact')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Candidate City (Optional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">City (Optional)</label>
                <input type="text" name="candidate_city" 
                       value="{{ old('candidate_city') }}"
                       placeholder="Enter your city"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('candidate_city') border-red-500 @enderror">
                @error('candidate_city')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Consent Checkbox -->
            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                <input type="checkbox" id="consent" name="consent" required 
                       class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="consent" class="text-sm text-gray-700">
                    I understand the exam instructions and agree to follow the guidelines. I confirm that the information provided is accurate.
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                🚀 Register & Get OTP
            </button>
        </form>
        </div>
    </div>

    <!-- Warning Modal for Back Button -->
    <div id="back-warning" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl p-8 max-w-md mx-4">
            <div class="text-center">
                <div class="mb-4">
                    <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Are you sure?</h3>
                <p class="text-gray-600 mb-6">Going back will exit the exam. You'll need to start over.</p>
                <div class="flex gap-3">
                    <button onclick="hideBackWarning()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Stay Here
                    </button>
                    <button onclick="goBack()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Exit Exam
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent back button during exam
        history.pushState(null, document.title, location.href);
        window.addEventListener('popstate', function (event) {
            showBackWarning();
        });

        function showBackWarning() {
            document.getElementById('back-warning').classList.remove('hidden');
        }

        function hideBackWarning() {
            document.getElementById('back-warning').classList.add('hidden');
            history.pushState(null, document.title, location.href);
        }

        function goBack() {
            window.history.back();
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.querySelector('input[name="student_name"]').value.trim();
            const email = document.querySelector('input[name="student_email"]').value.trim();
            const consent = document.querySelector('input[name="consent"]').checked;

            if (!name || !email || !consent) {
                e.preventDefault();
                alert('Please fill in all required fields and agree to the terms.');
                return;
            }

            // Show loading state
            const button = document.querySelector('button[type="submit"]');
            button.disabled = true;
            button.innerHTML = '🔄 Starting Exam...';
        });
    </script>

</body>
</html>