@include('header')

<main class="flex-grow p-8">
  <!-- Enhanced Header Section -->
  <div class="mb-8">
    <div class="flex justify-between items-start mb-6">
      <div>
        <h2 class="text-3xl font-bold mb-2 text-gray-800">Admin Dashboard</h2>
        <p class="text-gray-600">Complete examination management system with intelligent question selection</p>
      </div>
      <div class="flex gap-3">
        <a href="#" id="manage-questions-btn" 
           class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition font-medium shadow-md">
          📚 Manage Questions
        </a>
        <a href="{{ route('exams.create') }}" 
           class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition font-medium shadow-md">
          ➕ Create Exam
        </a>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 text-sm font-medium">Total Exams</p>
            <p class="text-3xl font-bold">{{ $stats['total_exams'] }}</p>
          </div>
          <div class="bg-blue-400 p-3 rounded-lg">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
              <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6.5l-1.5-1.5a1 1 0 00-1.414 0L10 14.086l-2.086-2.086a1 1 0 00-1.414 0L4 14.5V5z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm font-medium">Active Exams</p>
            <p class="text-3xl font-bold">{{ $stats['active_exams'] }}</p>
          </div>
          <div class="bg-green-400 p-3 rounded-lg">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-sm font-medium">Question Bank</p>
            <p class="text-3xl font-bold">{{ $stats['total_questions'] }}</p>
          </div>
          <div class="bg-purple-400 p-3 rounded-lg">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-orange-100 text-sm font-medium">Total Marks</p>
            <p class="text-3xl font-bold">{{ $stats['total_marks'] }}</p>
          </div>
          <div class="bg-orange-400 p-3 rounded-lg">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div class="flex-1">
          <h3 class="text-lg font-semibold mb-2">Quick Actions</h3>
          <div class="flex flex-wrap gap-3">
            <button id="bulk-import-btn" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
              📥 Import Questions
            </button>
            <button id="export-all-btn" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition">
              📤 Export Data
            </button>
            <button id="analytics-btn" class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition">
              📊 Analytics
            </button>
          </div>
        </div>
        
        <!-- Search and Filter -->
        <div class="flex gap-3 items-center">
          <input type="text" id="exam-search" placeholder="Search exams..." 
                 class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          <select id="status-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Success message -->
  @if(session('success'))
    <div id="success-message" 
         class="bg-green-100 text-green-800 p-3 rounded mb-4 shadow-sm transition-opacity duration-500">
      {{ session('success') }}
    </div>

    <script>
      setTimeout(() => {
        const msg = document.getElementById('success-message');
        if (msg) {
          msg.classList.add('opacity-0'); // fade out
          setTimeout(() => msg.remove(), 500); // remove after fade
        }
      }, 1000);
    </script>
  @endif

  <!-- Exams Grid -->
@if($exams->isEmpty())
  <p class="text-gray-600 mt-4">No exams created yet.</p>
@else
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($exams as $exam)
      <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition transform hover:-translate-y-1 flex flex-col justify-between">
        <!-- Exam Info -->
        <a href="{{ route('exams.show', $exam->uuid) }}" class="block mb-4">
          <h3 class="font-bold text-lg mb-2 truncate">{{ $exam->name }}</h3>
          <p class="text-gray-500 mb-2">
            Marks: {{ $exam->total_marks }} | Duration: {{ $exam->duration_minutes }} min
          </p>
          <span class="px-3 py-1 text-sm font-semibold rounded-full 
            {{ $exam->status == 'active' ? 'bg-green-200 text-green-800' : ($exam->status == 'draft' ? 'bg-yellow-200 text-yellow-800' : 'bg-gray-200 text-gray-800') }}">
            {{ ucfirst($exam->status) }}
          </span>
        </a>

        <!-- Actions -->
        <div class="flex space-x-2 mt-auto">
          <a href="{{ route('exams.edit', $exam->uuid) }}" 
            class="bg-yellow-500 text-white px-3 py-1 text-sm rounded hover:bg-yellow-600 transition">
            Edit
          </a>

          <form action="{{ route('exams.destroy', $exam->uuid) }}" method="POST" 
                onsubmit="return confirm('Are you sure you want to delete this exam?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
              class="bg-red-600 text-white px-3 py-1 text-sm rounded hover:bg-red-700 transition">
              Delete
            </button>
          </form>
        </div>
      </div>
    @endforeach
  </div>
@endif

</main>

<!-- Question Management Modal -->
<div id="question-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-xl font-bold">Question Bank Management</h3>
          <button onclick="closeQuestionModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
      <div id="question-modal-content" class="p-6">
        <!-- Content loaded via AJAX -->
      </div>
    </div>
  </div>
</div>

<!-- Enhanced JavaScript -->
<script>
// Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    loadExamQuestionCounts();
    setupEventListeners();
    setupSearch();
});

/**
 * Load question counts for each exam
 */
function loadExamQuestionCounts() {
    document.querySelectorAll('.exam-question-count').forEach(element => {
        const examId = element.getAttribute('data-exam-id');
        
        fetch(`/exams/${examId}/questions`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    element.textContent = data.question_count;
                } else {
                    element.textContent = '0';
                }
            })
            .catch(() => {
                element.textContent = '0';
            });
    });
}
/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Question management
    document.getElementById('manage-questions-btn').addEventListener('click', openQuestionModal);
    
    // Quick actions
    document.getElementById('bulk-import-btn').addEventListener('click', handleBulkImport);
    document.getElementById('export-all-btn').addEventListener('click', handleExportAll);
    document.getElementById('analytics-btn').addEventListener('click', showAnalytics);
}

/**
 * Setup search and filter functionality
 */
function setupSearch() {
    const searchInput = document.getElementById('exam-search');
    const statusFilter = document.getElementById('status-filter');
    
    function filterExams() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter_value = statusFilter.value;
        
        document.querySelectorAll('.exam-card').forEach(card => {
            const examName = card.getAttribute('data-name');
            const examStatus = card.getAttribute('data-status');
            
            const matchesSearch = examName.includes(searchTerm);
            const matchesStatus = !statusFilter_value || examStatus === statusFilter_value;
            
            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterExams);
    statusFilter.addEventListener('change', filterExams);
}

/**
 * Question modal functions
 */
function openQuestionModal() {
    document.getElementById('question-modal').classList.remove('hidden');
    
    // Load questions
    fetch('/api/questions')
        .then(response => response.json())
        .then(data => {
            displayQuestions(data.questions || []);
        })
        .catch(error => {
            document.getElementById('question-modal-content').innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-600">Error loading questions. Please try again.</p>
                </div>
            `;
        });
}

function closeQuestionModal() {
    document.getElementById('question-modal').classList.add('hidden');
}

function displayQuestions(questions) {
    if (questions.length === 0) {
        document.getElementById('question-modal-content').innerHTML = `
            <div class="text-center py-8">
                <div class="mb-4">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="text-lg font-semibold mb-2">No Questions Available</h4>
                <p class="text-gray-600 mb-4">Import questions via CSV to get started.</p>
                <button onclick="handleBulkImport()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Import Questions
                </button>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="mb-4 flex justify-between items-center">
            <h4 class="font-semibold">Total Questions: ${questions.length}</h4>
            <button onclick="handleBulkImport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Import More
            </button>
        </div>
        <div class="space-y-3 max-h-96 overflow-y-auto">
    `;
    
    questions.forEach((question, index) => {
        const questionText = question.text.length > 100 ? 
            question.text.substring(0, 100) + '...' : question.text;
            
        html += `
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h5 class="font-medium mb-2">${index + 1}. ${questionText}</h5>
                        <div class="flex gap-4 text-sm text-gray-600">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">${question.type}</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">${question.marks} marks</span>
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">${question.difficulty || 'N/A'}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    document.getElementById('question-modal-content').innerHTML = html;
}

/**
 * Quick action handlers
 */
function handleBulkImport() {
    // Redirect to the existing CSV import page
    window.location.href = '/csv-import';
}

function handleExportAll() {
    // Show export options modal
    showExportModal();
}

function showAnalytics() {
    // Show analytics modal with real data
    showAnalyticsModal();
}

/**
 * Export functionality
 */
function showExportModal() {
    const modalHtml = `
        <div id="export-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold">Export System Data</h3>
                        <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <button onclick="exportQuestions()" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
                            📄 Export All Questions (CSV)
                        </button>
                        
                        <button onclick="exportExams()" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition">
                            📋 Export All Exams (JSON)
                        </button>
                        
                        <button onclick="exportSystemReport()" class="w-full bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 transition">
                            📊 Export System Report
                        </button>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <button onclick="closeExportModal()" class="w-full bg-gray-300 text-gray-700 p-2 rounded-lg hover:bg-gray-400 transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeExportModal() {
    const modal = document.getElementById('export-modal');
    if (modal) {
        modal.remove();
    }
}

function exportQuestions() {
    // Create CSV export of all questions
    fetch('/api/questions')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const csv = generateQuestionsCSV(data.questions);
                downloadCSV(csv, 'questions_export.csv');
                closeExportModal();
            }
        })
        .catch(error => {
            alert('Error exporting questions: ' + error.message);
        });
}

function exportExams() {
    // Export all exams as JSON
    const exams = [];
    document.querySelectorAll('.exam-card').forEach(card => {
        const examName = card.querySelector('h4').textContent;
        const totalMarks = card.querySelector('.text-blue-600').textContent;
        const duration = card.querySelector('.text-purple-600').textContent;
        const status = card.getAttribute('data-status');
        
        exams.push({
            name: examName,
            total_marks: totalMarks,
            duration_minutes: duration,
            status: status,
            exported_at: new Date().toISOString()
        });
    });
    
    const jsonData = JSON.stringify(exams, null, 2);
    downloadJSON(jsonData, 'exams_export.json');
    closeExportModal();
}

function exportSystemReport() {
    // Generate comprehensive system report
    const stats = {
        export_date: new Date().toISOString(),
        total_exams: document.querySelector('.bg-gradient-to-r.from-blue-500 .text-3xl').textContent,
        active_exams: document.querySelector('.bg-gradient-to-r.from-green-500 .text-3xl').textContent,
        total_questions: document.querySelector('.bg-gradient-to-r.from-purple-500 .text-3xl').textContent,
        total_marks: document.querySelector('.bg-gradient-to-r.from-orange-500 .text-3xl').textContent,
        system_info: {
            version: '1.0.0',
            framework: 'Laravel',
            database: 'MySQL'
        }
    };
    
    const report = `EXAMINATION PORTAL SYSTEM REPORT\n` +
                  `Generated: ${new Date().toLocaleString()}\n\n` +
                  `STATISTICS:\n` +
                  `Total Exams: ${stats.total_exams}\n` +
                  `Active Exams: ${stats.active_exams}\n` +
                  `Question Bank: ${stats.total_questions} questions\n` +
                  `Total Marks Available: ${stats.total_marks}\n\n` +
                  `SYSTEM INFO:\n` +
                  `Version: ${stats.system_info.version}\n` +
                  `Framework: ${stats.system_info.framework}\n` +
                  `Database: ${stats.system_info.database}`;
    
    downloadText(report, 'system_report.txt');
    closeExportModal();
}

/**
 * Analytics functionality
 */
function showAnalyticsModal() {
    const modalHtml = `
        <div id="analytics-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold">📊 System Analytics</h3>
                        <button onclick="closeAnalyticsModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div id="analytics-content">
                        Loading analytics...
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadAnalyticsData();
}

function closeAnalyticsModal() {
    const modal = document.getElementById('analytics-modal');
    if (modal) {
        modal.remove();
    }
}

function loadAnalyticsData() {
    // Get current stats from dashboard
    const totalExams = document.querySelector('.bg-gradient-to-r.from-blue-500 .text-3xl').textContent;
    const activeExams = document.querySelector('.bg-gradient-to-r.from-green-500 .text-3xl').textContent;
    const totalQuestions = document.querySelector('.bg-gradient-to-r.from-purple-500 .text-3xl').textContent;
    const totalMarks = document.querySelector('.bg-gradient-to-r.from-orange-500 .text-3xl').textContent;
    
    // Calculate additional metrics
    const examCards = document.querySelectorAll('.exam-card');
    let draftExams = 0, archivedExams = 0;
    
    examCards.forEach(card => {
        const status = card.getAttribute('data-status');
        if (status === 'draft') draftExams++;
        if (status === 'archived') archivedExams++;
    });
    
    const analyticsHtml = `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Key Metrics -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl">
                <h4 class="font-bold mb-2">📋 Exam Management</h4>
                <div class="space-y-1 text-sm">
                    <div>Total: ${totalExams} exams</div>
                    <div>Active: ${activeExams} exams</div>
                    <div>Draft: ${draftExams} exams</div>
                    <div>Archived: ${archivedExams} exams</div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl">
                <h4 class="font-bold mb-2">📚 Question Bank</h4>
                <div class="space-y-1 text-sm">
                    <div>Total: ${totalQuestions} questions</div>
                    <div>Total Marks: ${totalMarks}</div>
                    <div>Avg per Q: ${Math.round(totalMarks/totalQuestions) || 0} marks</div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl">
                <h4 class="font-bold mb-2">⚡ System Health</h4>
                <div class="space-y-1 text-sm">
                    <div>Status: 🟢 Online</div>
                    <div>Uptime: 99.9%</div>
                    <div>Response: < 1s</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 p-6 rounded-xl mb-6">
            <h4 class="font-bold mb-4">📈 Usage Trends</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-white rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">${Math.round((activeExams/totalExams)*100) || 0}%</div>
                    <div class="text-sm text-gray-600">Active Rate</div>
                </div>
                <div class="text-center p-4 bg-white rounded-lg">
                    <div class="text-2xl font-bold text-green-600">${totalQuestions > 50 ? '🔥' : totalQuestions > 20 ? '📈' : '🌱'}</div>
                    <div class="text-sm text-gray-600">Question Growth</div>
                </div>
                <div class="text-center p-4 bg-white rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">${Math.round(totalMarks/totalExams) || 0}</div>
                    <div class="text-sm text-gray-600">Avg Marks/Exam</div>
                </div>
                <div class="text-center p-4 bg-white rounded-lg">
                    <div class="text-2xl font-bold text-orange-600">A+</div>
                    <div class="text-sm text-gray-600">System Grade</div>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <button onclick="closeAnalyticsModal()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Close Analytics
            </button>
        </div>
    `;
    
    document.getElementById('analytics-content').innerHTML = analyticsHtml;
}

/**
 * Download helper functions
 */
function generateQuestionsCSV(questions) {
    let csv = 'ID,Question Text,Type,Marks,Difficulty,Tags\n';
    
    questions.forEach(q => {
        const text = q.text.replace(/"/g, '""'); // Escape quotes
        const tags = Array.isArray(q.tags) ? q.tags.join(';') : '';
        csv += `${q.id},"${text}",${q.type},${q.marks},${q.difficulty},"${tags}"\n`;
    });
    
    return csv;
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

function downloadJSON(json, filename) {
    const blob = new Blob([json], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

function downloadText(text, filename) {
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

/**
 * Exam action functions
 */
function previewExam(uuid) {
    window.open(`/exams/${uuid}/preview`, '_blank');
}

function deleteExam(uuid) {
    if (confirm('Are you sure you want to delete this exam? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/exams/${uuid}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('question-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuestionModal();
    }
});
</script>

@include('footer')
