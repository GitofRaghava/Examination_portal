<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Unavailable - Examination Portal</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Use Inter font for a professional, clean look */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            /* Stronger, more focused error gradient */
            background-image: linear-gradient(to bottom right, #fef2f2, #fffbeb); 
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    
    <!-- Main Card Container -->
    <div class="max-w-md w-full mx-auto transform transition duration-500 hover:shadow-2xl">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            
            <!-- Header (Bold Red) -->
            <div class="bg-gradient-to-r from-red-600 to-red-800 text-white p-8 text-center">
                <div class="mb-4">
                    <!-- Alert Icon -->
                    <svg class="mx-auto h-14 w-14 text-white opacity-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold mb-1">Access Denied</h1>
                <p class="text-red-200 font-medium">Examination Not Available</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                
                <!-- Primary Error Message -->
                <div class="text-center mb-8">
                    <div class="bg-red-50 border border-red-300 rounded-xl p-6 mb-6 shadow-inner">
                        <h2 class="text-2xl font-bold text-red-800 mb-3">🚫 Critical Error</h2>
                        <p class="text-red-700 text-lg font-medium">
                            {{ $message }}
                        </p>
                    </div>

                    @if(isset($exam) && $exam)
                        <!-- Exam Info (if available) -->
                        <div class="bg-gray-50 rounded-xl p-5 mb-8 border border-gray-200">
                            <h3 class="font-bold text-lg text-gray-800 mb-3 border-b pb-2">Exam Details</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Name:</span>
                                    <span class="font-semibold text-gray-800">{{ $exam->name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Duration:</span>
                                    <span class="font-semibold text-gray-800">{{ $exam->duration_minutes }} min</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Total Marks:</span>
                                    <span class="font-semibold text-gray-800">{{ $exam->total_marks }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-extrabold px-3 py-1 rounded-full text-xs 
                                        {{ $exam->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Guidance & Next Steps -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2 text-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 21h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span> What to do next:
                    </h3>
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-xl shadow-sm">
                        <ul class="space-y-3 text-gray-700 text-base list-disc pl-5">
                            <li>**Verify the Link:** Double-check the URL provided by your instructor or institution.</li>
                            <li>**Contact Support:** If the link is correct, please contact your exam administrator immediately.</li>
                            <li>**Wait:** If the exam status is currently **Draft** or **Inactive**, the administrator has not yet opened the examination.</li>
                            <li>**Refresh:** You can try refreshing the page in case of a temporary connection issue.</li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4">
                    <button onclick="location.reload()" 
                            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-xl font-extrabold text-lg 
                                   hover:bg-indigo-700 transition duration-200 shadow-md transform hover:scale-[1.005]">
                        <span class="flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356-2A8.001 8.001 0 004.582 17.5l-1.558 1.558m15.356-2l-1.558-1.558M20 10v5h.582" /></svg>
                            Refresh Page
                        </span>
                    </button>
                    
                    <button onclick="history.back()" 
                            class="w-full bg-gray-200 text-gray-700 py-3 px-6 rounded-xl hover:bg-gray-300 transition font-semibold">
                        ← Return to Previous Page
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-gray-500 text-xs">
                For urgent assistance, please contact technical support.
            </p>
        </div>
    </div>
    
    <script>
        // Simple client-side animation for the refresh icon (purely aesthetic)
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes spin-slow {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .animate-spin-slow {
                animation: spin-slow 2s linear infinite;
            }
        `;
        document.head.appendChild(style);
    </script>

</body>
</html>
