<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Postural Chart</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div id="app" class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-8">Test Postural Chart</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Test Vue.js</h2>
            <p class="mb-4">Message: @{{ message }}</p>
            <button @click="message = 'Vue.js fonctionne !'" class="bg-blue-500 text-white px-4 py-2 rounded">
                Cliquer ici
            </button>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4">Test SVG</h2>
            <div class="border border-gray-300 p-4">
                <svg width="200" height="300" class="border border-gray-400">
                    <!-- Simple body outline -->
                    <rect x="50" y="20" width="100" height="200" fill="none" stroke="black" stroke-width="2"/>
                    <!-- Head -->
                    <circle cx="100" cy="30" r="15" fill="none" stroke="black" stroke-width="2"/>
                    <!-- Arms -->
                    <line x1="50" y1="80" x2="30" y2="120" stroke="black" stroke-width="2"/>
                    <line x1="150" y1="80" x2="170" y2="120" stroke="black" stroke-width="2"/>
                    <!-- Legs -->
                    <line x1="80" y1="220" x2="70" y2="280" stroke="black" stroke-width="2"/>
                    <line x1="120" y1="220" x2="130" y2="280" stroke="black" stroke-width="2"/>
                </svg>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        
        createApp({
            data() {
                return {
                    message: 'Vue.js est chargé'
                }
            }
        }).mount('#app');
    </script>
</body>
</html> 