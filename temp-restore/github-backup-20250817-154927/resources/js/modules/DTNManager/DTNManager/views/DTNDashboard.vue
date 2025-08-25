<template>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold mb-4">{{ $t('auto.key55') }}</h1>
        <NationalTeams />
        <InternationalSelections />
        <ExpatsClubSync />
        <MedicalClubInterface />
        <TechnicalPlanning />
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
};
</script>
