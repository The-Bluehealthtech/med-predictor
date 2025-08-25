<template>
  <div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">{{ $t('auto.key63') }}</h1>
      <button @click="goBack" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
        Retour
      </button>
    </div>
    <div v-if="loading" class="text-gray-500">{{ $t('auto.key64') }}</div>
    <div v-else>
      <table class="min-w-full bg-white border rounded shadow">
        <thead>
          <tr>
            <th class="px-4 py-2">{{ $t('auto.key65') }}</th>
            <th class="px-4 py-2">{{ $t('auto.key66') }}</th>
            <th class="px-4 py-2">{{ $t('auto.key67') }}</th>
            <th class="px-4 py-2">{{ $t('auto.key68') }}</th>
            <th class="px-4 py-2">{{ $t('auto.key69') }}</th>
            <th class="px-4 py-2">{{ $t('auto.key70') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="match in matches" :key="match.id">
            <td class="px-4 py-2">{{ formatDate(match.match_date) }}</td>
            <td class="px-4 py-2">{{ match.competition?.name }}</td>
            <td class="px-4 py-2">{{ match.home_team?.name }}</td>
            <td class="px-4 py-2">{{ match.away_team?.name }}</td>
            <td class="px-4 py-2">
              <span :class="statusColor(match.status)">{{ statusLabel(match.status) }}</span>
            </td>
            <td class="px-4 py-2">
              <button class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700" @click="openMatchSheet(match.id)">
                Feuille de match
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="error" class="text-red-600 mt-4">{{ error }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const matches = ref([]);
const loading = ref(true);
const error = ref('');

const fetchMatches = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await axios.get('/api/referee/matches');
    matches.value = response.data.data;
  } catch (e) {
    error.value = e.response?.data?.message || 'Erreur lors du chargement des matchs';
  } finally {
    loading.value = false;
  }
};

const openMatchSheet = (matchId) => {
  router.push({ name: 'referee-match-sheet', params: { id: matchId } });
};

const goBack = () => {
  router.go(-1);
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleString('fr-FR');
};

const statusLabel = (status) => {
  switch (status) {
    case 'upcoming': return 'À venir';
    case 'active': return 'En cours';
    case 'completed': return 'Terminé';
    case 'cancelled': return 'Annulé';
    default: return status;
  }
};

const statusColor = (status) => {
  switch (status) {
    case 'upcoming': return 'text-blue-600';
    case 'active': return 'text-green-600';
    case 'completed': return 'text-gray-600';
    case 'cancelled': return 'text-red-600';
    default: return '';
  }
};

onMounted(fetchMatches);
</script>

<style scoped>
.container {
  max-width: 900px;
}
</style> 