<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->name }} - Examination Portal</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Use Inter font for a modern look */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    
    <!-- Main Card Container -->
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full mx-auto transform hover:shadow-3xl transition duration-500 ease-in-out border border-gray-200">
        
        <!-- Header Section -->
        <div class="bg-gradient-to-br from-blue-600 to-purple-700 text-white p-10 rounded-t-3xl text-center">
            <div class="mb-4">
                <!-- Academic Icon (Book/Graduation Cap) -->
                <svg class="mx-auto h-14 w-14 text-white opacity-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.05v-9.176l-.683 1.151l-.683-1.151V20.05a11.95 11.95 0 00-6.822-2.583A12.083 12.083 0 015.84 10.578L12 14z" />
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold mb-1">{{ $exam->name }}</h1>
            <p class="text-blue-200 text-sm font-medium">Secure Online Examination Portal</p>
        </div>

        <!-- Content Area -->
        <div class="p-8 md:p-10">
            <!-- Exam Info -->
            <div class="mb-8 p-4 border border-blue-100 bg-blue-50 rounded-xl">
                <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2 border-blue-200">Examination Summary</h2>
                
                <div class="space-y-3">
                    <!-- Total Marks -->
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                        <span class="text-gray-600 flex items-center"><span class="mr-2 text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </span> Total Marks:</span>
                        <span class="font-bold text-lg text-blue-700">{{ $exam->total_marks }}</span>
                    </div>
                    
                    <!-- Duration -->
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                        <span class="text-gray-600 flex items-center"><span class="mr-2 text-purple-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span> Duration:</span>
                        <span class="font-bold text-lg text-purple-700">{{ $exam->duration_minutes }} min</span>
                    </div>
                    
                    <!-- Status -->
                    <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm">
                        <span class="text-gray-600 flex items-center"><span class="mr-2 text-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </span> Status:</span>
                        <span class="inline-flex px-3 py-1 text-sm font-bold rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mb-8">
                <h3 class="text-lg font-bold mb-3 text-gray-800 flex items-center">
                    <span class="mr-2 text-yellow-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </span> Key Instructions
                </h3>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-xl shadow-inner">
                    <ul class="text-sm text-gray-700 space-y-2 list-disc pl-5">
                        <li>Fill in your details accurately to receive the **6-digit OTP**.</li>
                        <li>**Proctoring is active**: Do not switch tabs or leave the browser window after starting.</li>
                        <li>The exam timer **cannot be paused** once started.</li>
                        <li>Ensure you have a **stable internet connection** before proceeding.</li>
                    </ul>
                </div>
            </div>

            <!-- Error Messages -->
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-500 text-red-700 px-4 py-3 rounded-lg font-medium">
                    <strong>Attention:</strong> {{ session('error') }}
                </div>
            @endif

            <!-- Student Details Form -->
            <form action="{{ route('student.exam.register', $exam->uuid) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Hidden Exam ID -->
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">

                <!-- Candidate Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2" for="candidate_name">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="candidate_name" id="candidate_name" required 
                            value="{{ old('candidate_name') }}"
                            placeholder="Enter your full name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition @error('candidate_name') border-red-500 ring-red-100 @enderror">
                    @error('candidate_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Candidate Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2" for="candidate_email">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="candidate_email" id="candidate_email" required 
                            value="{{ old('candidate_email') }}"
                            placeholder="e.g., jane.doe@example.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition @error('candidate_email') border-red-500 ring-red-100 @enderror">
                    @error('candidate_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact and City in a single row for desktop, stacked on mobile -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Candidate Contact (Optional) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="candidate_contact">Contact Number (Optional)</label>
                        <input type="text" name="candidate_contact" id="candidate_contact"
                                value="{{ old('candidate_contact') }}"
                                placeholder="Enter your contact number"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition @error('candidate_contact') border-red-500 ring-red-100 @enderror">
                        @error('candidate_contact')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Candidate City (Optional) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2" for="candidate_city">City (Optional)</label>
                        <input type="text" name="candidate_city" id="candidate_city"
                                value="{{ old('candidate_city') }}"
                                placeholder="Enter your city"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition @error('candidate_city') border-red-500 ring-red-100 @enderror">
                        @error('candidate_city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Consent Checkbox -->
                <div class="flex items-start space-x-3 p-4 bg-purple-50 border border-purple-200 rounded-xl">
                    <input type="checkbox" id="consent" name="consent" required 
                            class="mt-1 h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded-md cursor-pointer">
                    <label for="consent" class="text-sm text-gray-800 leading-relaxed">
                        I have read and agree to the **examination instructions** and confirm that the information provided is accurate.
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-xl font-extrabold text-lg 
                                hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-[1.01] shadow-xl">
                    <span class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        Register & Send OTP
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- Removed intrusive back-button warning JS logic for better pre-exam UX -->
    
    <script>
        // Simple form submission feedback
        document.querySelector('form').addEventListener('submit', function(e) {
            const consent = document.getElementById('consent').checked;
            
            if (!consent) {
                // Since alert() is disabled, we'll use client-side visual feedback (like border-red-500 on form) 
                // but the Laravel validation is the primary guard.
                e.preventDefault();
                document.getElementById('consent').focus();
                // Add a temporary visual indicator if possible without alert
                console.error('Please agree to the terms and conditions.');
                return;
            }

            // Show loading state and disable button
            const button = e.submitter; // Get the button that was clicked
            button.disabled = true;
            button.innerHTML = 'Sending OTP... Please wait ⏳';
        });
    </script>
</body>
</html>
