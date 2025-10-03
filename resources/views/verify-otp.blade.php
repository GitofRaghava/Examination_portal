<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h1 class="text-2xl font-bold mb-6 text-center">Enter OTP</h1>

        @if(session('success'))
            <p class="text-green-500 mb-4 text-center">{{ session('success') }}</p>
        @endif
        @if(session('error'))
            <p class="text-red-500 mb-4 text-center">{{ session('error') }}</p>
        @endif

        <form method="POST" action="{{ route('verify-otp') }}">
            @csrf
            <label class="block mb-2 font-medium">OTP:</label>
            <input type="number" name="otp" required class="w-full p-2 border rounded mb-4 focus:outline-none focus:ring-2 focus:ring-purple-500">
            
            <button type="submit" class="w-full bg-purple-600 text-white p-2 rounded hover:bg-purple-700 transition">Verify</button>
        </form>
    </div>
</body>
</html>
