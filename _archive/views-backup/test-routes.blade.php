<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Test - Med Predictor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">🔧 Route Test Page</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">✅ Working Routes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="/" class="block p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="font-semibold text-green-800">🏠 Home Page</div>
                        <div class="text-sm text-green-600">http://localhost:8000/</div>
                    </a>
                    
                    <a href="/login" class="block p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="font-semibold text-blue-800">🔐 Login Page</div>
                        <div class="text-sm text-blue-600">http://localhost:8000/login</div>
                    </a>
                    
                    <a href="/register" class="block p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="font-semibold text-purple-800">📝 Register Page</div>
                        <div class="text-sm text-purple-600">http://localhost:8000/register</div>
                    </a>
                    
                    <a href="/stakeholder-gallery" class="block p-4 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                        <div class="font-semibold text-yellow-800">👥 Stakeholder Gallery</div>
                        <div class="text-sm text-yellow-600">http://localhost:8000/stakeholder-gallery</div>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🔒 Protected Routes (Require Login)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="/dashboard" class="block p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="font-semibold text-gray-800">📊 Dashboard</div>
                        <div class="text-sm text-gray-600">http://localhost:8000/dashboard</div>
                        <div class="text-xs text-gray-500 mt-1">(Redirects to login if not authenticated)</div>
                    </a>
                    
                    <a href="/player-dashboard" class="block p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="font-semibold text-gray-800">⚽ Player Dashboard</div>
                        <div class="text-sm text-gray-600">http://localhost:8000/player-dashboard</div>
                        <div class="text-xs text-gray-500 mt-1">(Redirects to login if not authenticated)</div>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🧪 Test Login</h2>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-blue-800 mb-2"><strong>Test Credentials:</strong></p>
                    <div class="text-sm text-blue-700 space-y-1">
                        <div><strong>1. Player:</strong> john.doe@testfc.com / password123</div>
                        <div><strong>2. Admin:</strong> admin@testfc.com / password123</div>
                        <div><strong>3. Manager:</strong> manager@testfc.com / password123</div>
                        <div><strong>4. Medical:</strong> medical@testfc.com / password123</div>
                    </div>
                    <div class="mt-3">
                        <a href="/login" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Try Login Now
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🔍 Troubleshooting</h2>
                <div class="space-y-3 text-sm text-gray-700">
                    <div class="flex items-start">
                        <span class="text-green-500 mr-2">✅</span>
                        <span>If you can see this page, the server is running correctly</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-blue-500 mr-2">ℹ️</span>
                        <span>All routes above should work. If any return 404, there's a specific issue with that route</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-yellow-500 mr-2">⚠️</span>
                        <span>Protected routes will redirect to login if you're not authenticated</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-red-500 mr-2">❌</span>
                        <span>If you're getting 404s, try clearing browser cache (Ctrl+Shift+R)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 