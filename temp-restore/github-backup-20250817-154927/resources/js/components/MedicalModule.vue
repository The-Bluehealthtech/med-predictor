<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="text-center">
      <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $t('auto.key122') }}</h1>
      <p class="text-gray-600 mb-6">{{ $t('auto.key123') }}</p>
      <router-link to="/" class="text-blue-600 hover:text-blue-800 underline">
        ← Back to Dashboard
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MedicalModule'
};
</script> 