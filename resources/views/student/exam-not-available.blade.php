<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Not Available - Examination Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-50 to-orange-50 min-h-screen flex items-center justify-center">
    
    <div class="max-w-lg mx-4">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-8 text-center">
                <div class="mb-4">
                    <svg class="mx-auto h-16 w-16 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold mb-2">⚠️ Exam Not Available</h1>
                <p class="text-red-100">Unable to access the examination</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                
                <!-- Error Message -->
                <div class="text-center mb-8">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                        <h2 class="text-xl font-semibold text-red-800 mb-3">Access Denied</h2>
                        <p class="text-red-700 text-lg">{{ $message }}</p>
                    </div>

                    @if($exam)
                        <!-- Exam Info (if available) -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                            <h3 class="font-semibold text-gray-800 mb-3">📋 Exam Information</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Exam Name:</span>
                                    <span class="font-medium">{{ $exam->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-medium 
                                        {{ $exam->status === 'active' ? 'text-green-600' : 
                                           ($exam->status === 'draft' ? 'text-yellow-600' : 'text-gray-600') }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Marks:</span>
                                    <span class="font-medium">{{ $exam->total_marks }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Duration:</span>
                                    <span class="font-medium">{{ $exam->duration_minutes }} minutes</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Possible Reasons -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">🤔 Possible Reasons:</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <span class="text-red-500 mr-3">•</span>
                            <span>The exam has not been activated yet</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-3">•</span>
                            <span>The exam has been archived or closed</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-3">•</span>
                            <span>The exam link is invalid or has expired</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-3">•</span>
                            <span>You may have already completed this exam</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-3">•</span>
                            <span>The exam has been removed by the administrator</span>
                        </li>
                    </ul>
                </div>

                <!-- What to do -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">💡 What can you do?</h3>
                    <ul class="space-y-2 text-blue-700 text-sm">
                        <li>• Double-check the exam link with your instructor</li>
                        <li>• Contact your exam administrator for assistance</li>
                        <li>• Wait if the exam hasn't been activated yet</li>
                        <li>• Refresh the page and try again</li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="text-center space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">📞 Need Help?</h4>
                        <p class="text-gray-600 text-sm">
                            Contact your examination administrator or technical support team for assistance.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button onclick="location.reload()" 
                                class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-semibold">
                            🔄 Refresh Page
                        </button>
                        
                        <button onclick="history.back()" 
                                class="w-full bg-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-400 transition">
                            ← Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Help -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 text-sm">
                Powered by <strong>Examination Portal</strong> 
                <span class="mx-2">•</span> 
                {{ now()->year }}
            </p>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds if exam is in draft status
        @if($exam && $exam->status === 'draft')
        setInterval(function() {
            location.reload();
        }, 30000);
        @endif

        // Helpful keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                // Allow refresh
                return true;
            }
            if (e.key === 'Escape') {
                history.back();
            }
        });
    </script>

</body>
</html>