<template>
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                Planification Technique
            </h2>
        </div>
        <div class="p-6">
            <p class="text-gray-600">
                Composant TechnicalPlanning - En cours de développement
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

// Reactive data
const planningData = ref([]);

// Methods
const loadPlanningData = async () => {
    // Simulation de données
    planningData.value = [];
};

// Lifecycle
onMounted(() => {
    loadPlanningData();
});
</script>

<style scoped>
/* Styles spécifiques au composant TechnicalPlanning */
</style>
