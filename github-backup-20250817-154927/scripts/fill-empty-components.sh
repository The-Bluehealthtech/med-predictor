#!/bin/bash

# Script pour remplir les composants vides avec du contenu de base

# Fonction pour créer le contenu de base d'un composant Vue
create_vue_content() {
    local filename=$1
    local component_name=$(basename "$filename" .vue)
    
    cat > "$filename" << EOF
<template>
  <div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-xl font-semibold text-gray-800">$component_name</h2>
    </div>
    <div class="p-6">
      <p class="text-gray-600">Composant $component_name - En cours de développement</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// Reactive data
const data = ref({})

// Methods
const loadData = async () => {
  // Simulation de données
  data.value = {}
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>

<style scoped>
/* Styles spécifiques au composant $component_name */
</style>
EOF
}

# Liste des fichiers vides à remplir
files=(
  "resources/js/modules/DTNManager/components/TeamForm.vue"
  "resources/js/modules/DTNManager/components/TeamDetail.vue"
  "resources/js/modules/DTNManager/components/SelectionForm.vue"
  "resources/js/modules/DTNManager/components/SelectionDetail.vue"
  "resources/js/modules/DTNManager/components/SelectionPlayers.vue"
  "resources/js/modules/DTNManager/components/ExpatDetail.vue"
  "resources/js/modules/DTNManager/components/PlayerMedicalData.vue"
  "resources/js/modules/DTNManager/components/DTNReports.vue"
  "resources/js/modules/DTNManager/components/DTNSettings.vue"
  "resources/js/modules/RPM/components/SessionsList.vue"
  "resources/js/modules/RPM/components/SessionDetail.vue"
  "resources/js/modules/RPM/components/MatchesList.vue"
  "resources/js/modules/RPM/components/MatchDetail.vue"
  "resources/js/modules/RPM/components/PlayerLoadDetail.vue"
  "resources/js/modules/RPM/components/SessionAttendance.vue"
  "resources/js/modules/RPM/components/RPMReports.vue"
  "resources/js/modules/RPM/components/RPMSettings.vue"
  "resources/js/modules/RPM/components/RPMSync.vue"
)

# Remplir chaque fichier
for file in "${files[@]}"; do
  if [ -f "$file" ] && [ ! -s "$file" ]; then
    echo "Remplissage de $file"
    create_vue_content "$file"
  fi
done

echo "Tous les composants vides ont été remplis avec du contenu de base." 