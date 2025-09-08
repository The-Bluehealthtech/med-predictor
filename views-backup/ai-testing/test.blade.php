@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">AI Testing - Simple Test</h1>
        
        <!-- Debug Info -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">Debug Information</h3>
            <div class="text-sm text-yellow-700">
                <p>CSRF Token: <span id="csrfToken">{{ csrf_token() }}</span></p>
                <p>Current URL: <span id="currentUrl">{{ url()->current() }}</span></p>
                <p>JavaScript Status: <span id="jsStatus">Checking...</span></p>
            </div>
        </div>

        <!-- Simple Test Buttons -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Buttons</h3>
                <div class="space-y-4">
                    <button id="testButton1" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Test Button 1
                    </button>
                    <button id="testButton2" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        Test Button 2
                    </button>
                    <button id="testButton3" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        Test Button 3
                    </button>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div id="testResults" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Results</h3>
                <div id="resultsContent" class="text-gray-600">
                    Click a test button to see results...
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Test page loaded');
    
    // Update debug info
    document.getElementById('jsStatus').textContent = 'JavaScript Loaded';
    
    const resultsContent = document.getElementById('resultsContent');

    function updateResults(message) {
        console.log('Updating results:', message);
        resultsContent.innerHTML = `
            <div class="p-4 bg-gray-50 rounded">
                <p class="text-sm text-gray-700">${message}</p>
                <p class="text-xs text-gray-500 mt-2">Timestamp: ${new Date().toLocaleString()}</p>
            </div>
        `;
    }

    // Test Button 1
    document.getElementById('testButton1').addEventListener('click', function() {
        console.log('Test Button 1 clicked');
        updateResults('Test Button 1 was clicked successfully! JavaScript is working.');
    });

    // Test Button 2
    document.getElementById('testButton2').addEventListener('click', function() {
        console.log('Test Button 2 clicked');
        updateResults('Test Button 2 was clicked successfully! Event listeners are working.');
    });

    // Test Button 3
    document.getElementById('testButton3').addEventListener('click', function() {
        console.log('Test Button 3 clicked');
        updateResults('Test Button 3 was clicked successfully! All JavaScript functionality is working.');
    });

    console.log('Test event listeners attached');
});
</script>
@endsection 