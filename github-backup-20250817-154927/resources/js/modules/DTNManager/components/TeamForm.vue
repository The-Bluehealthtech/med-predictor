<template>
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ $t("dtn.teamForm.title") }}
            </h2>
        </div>
        <div class="p-6">
            <p class="text-gray-600">
                {{ $t("dtn.teamForm.inProgress") }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

// Reactive data
const teamData = ref({});

// Methods
const saveTeam = async () => {
    console.log($t("dtn.teamForm.save"));
};

// Lifecycle
onMounted(() => {
    // Initialisation
});
</script>

<style scoped>
/* Styles spécifiques au composant TeamForm */
</style>
