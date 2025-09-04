<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <!-- Logo -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                  <span class="text-white font-bold text-xl">{{ $t('auto.key160') }}</span>
                </div>
                <div class="ml-4">
                  <h1 class="text-xl font-bold text-gray-900">{{ $t('auto.key161') }}</h1>
                  <p class="text-sm text-gray-600">{{ $t('auto.key162') }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Navigation -->
          <nav class="hidden md:flex items-center space-x-6">
            <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors duration-200 font-medium">{{ $t('auto.key163') }}</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors duration-200 font-medium">{{ $t('auto.key164') }}</a>
            <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors duration-200 font-medium">{{ $t('auto.key165') }}</a>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200">
              Contact Sales
            </button>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main>
      <!-- Hero Section -->
      <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 bg-blue-100 border border-blue-200 rounded-full text-blue-800 text-sm font-medium mb-8">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              Trusted by 500+ Football Associations Worldwide
            </div>
            
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
              Choose Your
              <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Football Format
              </span>
            </h1>
            
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
              Select your preferred football format to access tailored management tools, analytics, and compliance features designed specifically for your sport.
            </p>
          </div>

          <!-- Football Type Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
            <!-- 11-a-side Football -->
            <div 
              @click="selectFootballType('11aside')"
              class="group bg-white rounded-2xl p-8 cursor-pointer transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-gray-200 hover:border-blue-300"
            >
              <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                <span class="text-3xl">{{ $t('auto.key166') }}</span>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $t('auto.key167') }}</h3>
              <p class="text-gray-600 leading-relaxed mb-6">
                Traditional football format with comprehensive player management, match tracking, and performance analytics.
              </p>
              <div class="flex items-center text-blue-600 group-hover:text-blue-700 transition-colors duration-200">
                <span class="font-semibold">{{ $t('auto.key168') }}</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
              </div>
            </div>

            <!-- Women's Football -->
            <div 
              @click="selectFootballType('womens')"
              class="group bg-white rounded-2xl p-8 cursor-pointer transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-gray-200 hover:border-pink-300"
            >
              <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                <span class="text-3xl">{{ $t('auto.key169') }}</span>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $t('auto.key170') }}</h3>
              <p class="text-gray-600 leading-relaxed mb-6">
                Dedicated platform for women's football with specialized health monitoring and performance tracking.
              </p>
              <div class="flex items-center text-pink-600 group-hover:text-pink-700 transition-colors duration-200">
                <span class="font-semibold">{{ $t('auto.key171') }}</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
              </div>
            </div>

            <!-- Futsal -->
            <div 
              @click="selectFootballType('futsal')"
              class="group bg-white rounded-2xl p-8 cursor-pointer transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-gray-200 hover:border-green-300"
            >
              <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                <span class="text-3xl">{{ $t('auto.key172') }}</span>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $t('auto.key173') }}</h3>
              <p class="text-gray-600 leading-relaxed mb-6">
                Indoor football format with fast-paced action tracking, skill development, and tactical analysis.
              </p>
              <div class="flex items-center text-green-600 group-hover:text-green-700 transition-colors duration-200">
                <span class="font-semibold">{{ $t('auto.key174') }}</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
              </div>
            </div>

            <!-- Beach Soccer -->
            <div 
              @click="selectFootballType('beach')"
              class="group bg-white rounded-2xl p-8 cursor-pointer transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border border-gray-200 hover:border-yellow-300"
            >
              <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                <span class="text-3xl">{{ $t('auto.key175') }}</span>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $t('auto.key176') }}</h3>
              <p class="text-gray-600 leading-relaxed mb-6">
                Beach football with specialized tracking for sand conditions, fitness monitoring, and tournament management.
              </p>
              <div class="flex items-center text-yellow-600 group-hover:text-yellow-700 transition-colors duration-200">
                <span class="font-semibold">{{ $t('auto.key177') }}</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ $t('auto.key178') }}</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
              Comprehensive football management solution with advanced analytics, health monitoring, and compliance tools.
            </p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $t('auto.key179') }}</h3>
              <p class="text-gray-600">{{ $t('auto.key180') }}</p>
            </div>
            
            <div class="text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $t('auto.key181') }}</h3>
              <p class="text-gray-600">{{ $t('auto.key182') }}</p>
            </div>
            
            <div class="text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $t('auto.key183') }}</h3>
              <p class="text-gray-600">{{ $t('auto.key184') }}</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                <span class="text-white font-bold text-lg">{{ $t('auto.key185') }}</span>
              </div>
              <span class="ml-3 text-gray-900 font-semibold">{{ $t('auto.key186') }}</span>
            </div>
            <p class="text-gray-600 text-sm">
              Enterprise sports management platform for modern football organizations.
            </p>
          </div>
          
          <div>
            <h4 class="text-gray-900 font-semibold mb-4">{{ $t('auto.key187') }}</h4>
            <ul class="space-y-2 text-gray-600 text-sm">
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key188') }}</a></li>
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key189') }}</a></li>
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key190') }}</a></li>
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key191') }}</a></li>
            </ul>
          </div>
          
          <div>
            <h4 class="text-gray-900 font-semibold mb-4">{{ $t('auto.key192') }}</h4>
            <ul class="space-y-2 text-gray-600 text-sm">
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key193') }}</a></li>
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key194') }}</a></li>
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key195') }}</a></li>
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key196') }}</a></li>
            </ul>
          </div>
          
          <div>
            <h4 class="text-gray-900 font-semibold mb-4">{{ $t('auto.key197') }}</h4>
            <ul class="space-y-2 text-gray-600 text-sm">
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key198') }}</a></li>
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key199') }}</a></li>
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key200') }}</a></li>
              <li><a href="#" class="hover:text-gray-900 transition-colors">{{ $t('auto.key201') }}</a></li>
            </ul>
          </div>
        </div>
        
        <div class="border-t border-gray-200 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
          <p class="text-gray-600 text-sm">
            © 2024 FIT Platform. All rights reserved.
          </p>
          <div class="flex space-x-6 mt-4 md:mt-0">
            <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useFootballTypeStore } from '../stores/footballTypeStore'

const footballTypeStore = useFootballTypeStore()
const selectedType = ref(null)

const selectFootballType = (type) => {
  footballTypeStore.setFootballType(type)
  
  // Redirect to profile selector with football type parameter
  window.location.href = `/profile-selector?footballType=${type}`
}
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style> 