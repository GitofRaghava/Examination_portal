<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure OTP Verification</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom font import for better look */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-image: linear-gradient(to bottom right, #eef2ff, #f3e8ff); /* Subtle, modern gradient */
        }
        /* Hide the number input controls (arrows) */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield; /* Firefox */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <!-- Main Card Container -->
    <div class="bg-white p-8 md:p-10 rounded-3xl shadow-2xl w-full max-w-sm border border-gray-100 transform hover:shadow-3xl transition duration-500 ease-in-out">
        
        <div class="text-center">
            <!-- Icon/Visual Element Placeholder -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 mb-6">
                <!-- Lock Icon SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            
            <h1 class="text-3xl font-extrabold text-gray-800 mb-2">Verification Code</h1>
            <p class="text-gray-500 mb-8 text-sm">Please enter the 6-digit code sent to your email/phone.</p>
        </div>

        <!-- Session Message Handling (Blade) -->
        @if(session('success'))
            <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg text-center font-medium" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg text-center font-medium" role="alert">
                {{ session('error') }}
            </div>
        @endif
        
        <form id="otpForm" method="POST" action="{{ route('verify-otp') }}">
            @csrf
            
            <!-- Hidden field to hold the combined 6-digit OTP value for submission -->
            <input type="hidden" name="otp" id="hiddenOtpInput">

            <!-- OTP Input Fields Container - Now supporting 6 digits -->
            <div class="flex justify-center space-x-2 sm:space-x-2 mb-8">
                <!-- Digit 1 -->
                <input type="number" id="otp-1" data-index="0" maxlength="1" required 
                       class="otp-input w-10 h-12 sm:w-11 sm:h-14 text-center text-xl font-semibold border-2 border-gray-300 rounded-xl focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition duration-150 p-1"
                       pattern="\d" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                <!-- Digit 2 -->
                <input type="number" id="otp-2" data-index="1" maxlength="1" required 
                       class="otp-input w-10 h-12 sm:w-11 sm:h-14 text-center text-xl font-semibold border-2 border-gray-300 rounded-xl focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition duration-150 p-1"
                       pattern="\d" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                <!-- Digit 3 -->
                <input type="number" id="otp-3" data-index="2" maxlength="1" required 
                       class="otp-input w-10 h-12 sm:w-11 sm:h-14 text-center text-xl font-semibold border-2 border-gray-300 rounded-xl focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition duration-150 p-1"
                       pattern="\d" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                <!-- Digit 4 -->
                <input type="number" id="otp-4" data-index="3" maxlength="1" required 
                       class="otp-input w-10 h-12 sm:w-11 sm:h-14 text-center text-xl font-semibold border-2 border-gray-300 rounded-xl focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition duration-150 p-1"
                       pattern="\d" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                <!-- Digit 5 -->
                <input type="number" id="otp-5" data-index="4" maxlength="1" required 
                       class="otp-input w-10 h-12 sm:w-11 sm:h-14 text-center text-xl font-semibold border-2 border-gray-300 rounded-xl focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition duration-150 p-1"
                       pattern="\d" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                <!-- Digit 6 -->
                <input type="number" id="otp-6" data-index="5" maxlength="1" required 
                       class="otp-input w-10 h-12 sm:w-11 sm:h-14 text-center text-xl font-semibold border-2 border-gray-300 rounded-xl focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition duration-150 p-1"
                       pattern="\d" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitButton" 
                    class="w-full bg-purple-600 text-white p-3 rounded-xl font-bold text-lg 
                           hover:bg-purple-700 transition duration-300 
                           focus:outline-none focus:ring-4 focus:ring-purple-500 focus:ring-opacity-50 shadow-md hover:shadow-lg">
                Verify Code
            </button>
        </form>

        <!-- Resend OTP Link -->
        <div class="mt-6 text-center text-sm">
            <p class="text-gray-500">
                Didn't receive the code? 
                <a href="#" class="text-purple-600 hover:text-purple-800 font-semibold transition">
                    Resend OTP
                </a>
            </p>
        </div>
    </div>
    
    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const form = document.getElementById('otpForm');
        const hiddenInput = document.getElementById('hiddenOtpInput');

        // 1. Focus on the first input when the page loads
        window.addEventListener('load', () => {
            if (inputs.length > 0) {
                inputs[0].focus();
            }
        });

        inputs.forEach((input, index) => {
            // 2. Handle input: Move focus forward on successful entry
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                if (value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            // 3. Handle Keydown: Move focus backward on backspace
            input.addEventListener('keydown', (e) => {
                // If backspace is pressed and the current field is empty, move to previous field
                if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // 4. Handle Paste Event: Distribute pasted code across inputs
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                // Get the pasted data (assuming it's a string of digits)
                const pasteData = (e.clipboardData || window.clipboardData)
                    // The .slice(0, inputs.length) ensures it only takes the required 6 digits
                    .getData('text').trim().slice(0, inputs.length); 
                
                // Distribute characters to inputs
                pasteData.split('').forEach((char, i) => {
                    if (inputs[i]) {
                        inputs[i].value = char;
                    }
                });

                // Focus on the next input or submit if full
                const nextIndex = Math.min(pasteData.length, inputs.length - 1);
                inputs[nextIndex].focus();
                
                // Manually trigger form preparation after paste
                prepareFormSubmission();
            });
        });

        // 5. Form Submission Logic: Combine the inputs into the hidden field
        const prepareFormSubmission = () => {
            let combinedOtp = '';
            inputs.forEach(input => {
                combinedOtp += input.value;
            });
            // Set the value of the hidden input
            hiddenInput.value = combinedOtp;

            // Simple validation: check if all 6 fields are filled
            if (combinedOtp.length !== inputs.length) {
                console.error("Please enter the complete 6-digit OTP code.");
                return false; 
            }
            return true;
        }

        form.addEventListener('submit', (e) => {
            if (!prepareFormSubmission()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
