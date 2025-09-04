<template>
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                {{$t('dtn.expats_club_sync.title')}}
            </h2>
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold" @click="syncExpats">
                {{$t('dtn.expats_club_sync.sync_button')}}
            </button>
        </div>
        <div class="p-6">
            <template v-if="expatsData.length > 0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{$t('dtn.expats_club_sync.club_name')}}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{$t('dtn.expats_club_sync.country')}}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{$t('dtn.expats_club_sync.players_count')}}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{$t('dtn.expats_club_sync.last_sync')}}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{$t('dtn.expats_club_sync.actions')}}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="club in expatsData" :key="club.id">
                            <td class="px-6 py-4 whitespace-nowrap">{{ club.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ club.country }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ club.players_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ club.last_sync }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-semibold" @click="syncClub(club)">
                                    {{$t('dtn.expats_club_sync.sync_now')}}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
            <template v-else>
                <p class="text-gray-600">
                    {{$t('dtn.expats_club_sync.no_data')}}
                </p>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

// Simulation de données d'expatriés
const expatsData = ref([]);

const loadExpatsData = async () => {
    expatsData.value = [
        { id: 1, name: 'Paris FC Expat', country: 'France', players_count: 5, last_sync: '2024-07-20 14:32' },
        { id: 2, name: 'London United', country: 'England', players_count: 3, last_sync: '2024-07-19 09:15' },
        { id: 3, name: 'FC Barcelona Expats', country: 'Spain', players_count: 7, last_sync: '2024-07-18 17:50' },
    ];
};

const syncExpats = () => {
    alert(t('dtn.expats_club_sync.sync_all_alert'));
};

const syncClub = (club) => {
    alert(t('dtn.expats_club_sync.sync_club_alert', { club: club.name }));
};

onMounted(() => {
    loadExpatsData();
});
</script>

<style scoped>
/* Styles spécifiques au composant ExpatsClubSync */
</style>
