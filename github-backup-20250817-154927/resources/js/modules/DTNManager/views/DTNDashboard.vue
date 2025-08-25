<template>
    <div class="max-w-5xl mx-auto py-8 px-4">
        <h1 class="text-3xl font-bold mb-6 text-blue-900">
            DTN Manager Dashboard
        </h1>
        <div class="mb-6 flex flex-wrap gap-2">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                :class="[
                    'px-4 py-2 rounded-lg font-semibold transition',
                    activeTab === tab.key
                        ? 'bg-blue-700 text-white'
                        : 'bg-blue-100 text-blue-700 hover:bg-blue-200',
                ]"
            >
                {{ tab.label }}
            </button>
        </div>
        <div class="bg-white rounded-xl shadow p-6 min-h-[300px]">
            <component :is="activeComponent" />
        </div>
    </div>
</template>

<script>
import NationalTeams from "../components/NationalTeams.vue";
import InternationalSelections from "../components/InternationalSelections.vue";
import ExpatsClubSync from "../components/ExpatsClubSync.vue";
import MedicalClubInterface from "../components/MedicalClubInterface.vue";
import TechnicalPlanning from "../components/TechnicalPlanning.vue";

export default {
    name: "DTNDashboard",
    components: {
        NationalTeams,
        InternationalSelections,
        ExpatsClubSync,
        MedicalClubInterface,
        TechnicalPlanning,
    },
    data() {
        return {
            tabs: [
                {
                    key: "national",
                    label: "National Teams",
                    component: "NationalTeams",
                },
                {
                    key: "selections",
                    label: "International Selections",
                    component: "InternationalSelections",
                },
                {
                    key: "expats",
                    label: "Expats Club Sync",
                    component: "ExpatsClubSync",
                },
                {
                    key: "medical",
                    label: "Medical Club Interface",
                    component: "MedicalClubInterface",
                },
                {
                    key: "planning",
                    label: "Technical Planning",
                    component: "TechnicalPlanning",
                },
            ],
            activeTab: "national",
        };
    },
    computed: {
        activeComponent() {
            const tab = this.tabs.find((t) => t.key === this.activeTab);
            return tab ? tab.component : "NationalTeams";
        },
    },
};
</script>

<style scoped>
.dtn-dashboard {
    padding: 20px;
    font-family: Arial, sans-serif;
}

.main-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #dee2e6;
}

.stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #007bff;
    margin: 10px 0;
}
</style>
