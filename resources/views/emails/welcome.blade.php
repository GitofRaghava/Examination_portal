<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Quiz App</title>
</head>
<body class="bg-gray-100 font-sans">
    <div class="max-w-lg mx-auto mt-10 bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        
        <!-- Header -->
        <div class="bg-purple-600 text-white text-center py-4">
            <h1 class="text-2xl font-bold">Welcome to Examination_Portal 🎉</h1>
        </div>

        <!-- Body -->
        <div class="p-6">
            <p class="text-gray-700 text-base mb-4">
                Hello <span class="font-semibold">{{ $studentDetails['name'] }}</span>,
            </p>

            <p class="text-gray-600 mb-4">
                Thank you for registering with us. Here are your details:
            </p>

            <ul class="list-disc list-inside text-gray-700 space-y-2 mb-6">
                <li><strong>Username (Full Name):</strong> {{ $studentDetails['name'] }}</li>
                <li><strong>Email:</strong> {{ $studentDetails['email'] }}</li>
                <li><strong>City:</strong> {{ $studentDetails['city'] }}</li>
                <li><strong>Mobile Number:</strong> {{ $studentDetails['contact'] }}</li>
                <li><strong>Your OTP:</strong> <span class="text-purple-600 font-bold">{{ $studentDetails['otp'] }}</span> 
                    <span class="text-sm text-gray-500">(valid for 5 minutes)</span>
                </li>
            </ul>

            <p class="text-gray-700 mb-6">
                Please enter this OTP on the verification screen to start your exam.
            </p>

            <div class="text-center">
                <a href="{{ url('/verify') }}" 
                   class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Verify Now
                </a>
            </div>

            <p class="text-gray-500 text-sm mt-6">
                If you didn’t register, you can safely ignore this email.
            </p>
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 text-gray-600 text-center py-3 text-sm">
            Thanks,<br>
            <span class="font-semibold">Quiz App Team</span>
        </div>
    </div>
</body>
</html>
