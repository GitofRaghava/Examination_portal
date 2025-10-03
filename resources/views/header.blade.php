<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Examination Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
  <!-- Navbar -->
  <header class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center shadow">
    <h1 class="text-xl font-bold">Examination Portal</h1>
    <div class="flex items-center gap-4">
      <span>Welcome, Admin</span>
      <a href="{{ route('logout') }}" 
         class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Logout</a>
    </div>
  </header>
