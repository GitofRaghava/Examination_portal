<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Secure Examination Portal</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Use Inter font for a professional, clean look */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            /* Vibrant but professional background gradient */
            background-image: linear-gradient(to bottom right, #eef2ff, #f3e8ff); 
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <!-- Main Card Container -->
    <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 w-full max-w-md border border-gray-100 transform transition duration-500 hover:shadow-3xl">
        
        <!-- Header & Icon -->
        <div class="text-center mb-8">
            <!-- Lock Icon in a colored circle -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            
            <h1 class="text-3xl font-extrabold text-gray-800 mb-1">Admin Access</h1>
            <p class="text-gray-500 text-sm">Secure Examination Portal</p>
        </div>

        <!-- Error Messages Display -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-xl mb-6 shadow-inner">
                <h2 class="font-bold text-base mb-1">Login Error</h2>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="loginForm" action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Email Input Group -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <!-- Email Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.5H12" />
                        </svg>
                    </div>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition duration-150">
                </div>
            </div>

            <!-- Password Input Group -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <!-- Password Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v2m-4-2v2m8-2v2" />
                        </svg>
                    </div>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition duration-150">
                </div>
            </div>

            <!-- Login Button -->
            <button type="submit" id="submitButton"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-extrabold text-lg 
                           hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-[1.01] shadow-lg">
                <span class="flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3v-10a3 3 0 013-3h5a3 3 0 013 3v1" />
                    </svg>
                    Sign In
                </span>
            </button>
        </form>

        <p class="text-center text-gray-500 mt-6 text-sm">
            Access restricted to authorized personnel.
        </p>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('submitButton');
            
            // Prevent double submission and show loading state
            button.disabled = true;
            button.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Authenticating...</span>';
        });
    </script>
</body>
</html>
