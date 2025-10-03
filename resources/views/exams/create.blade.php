@include('header')
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Exam</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
  
  <!-- Main Content Wrapper -->
  <main class="flex-grow flex items-center justify-center px-4">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg">
      <h2 class="text-2xl font-bold mb-6">Create New Exam</h2>

      @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
          <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('exams.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block mb-1 font-medium">Exam Name</label>
          <input type="text" name="name" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
          <label class="block mb-1 font-medium">Total Marks</label>
          <input type="number" name="total_marks" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
          <label class="block mb-1 font-medium">Duration (Minutes)</label>
          <input type="number" name="duration_minutes" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div>
          <label class="block mb-1 font-medium">Status</label>
          <select name="status" class="w-full border px-3 py-2 rounded">
            <option value="draft">Draft</option>
            <option value="active">Active</option>
          </select>
        </div>

        <div class="flex justify-end space-x-2">
          <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</a>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Create Exam
          </button>
        </div>
      </form>
    </div>
  </main>

  @include('footer')
</body>
</html>
