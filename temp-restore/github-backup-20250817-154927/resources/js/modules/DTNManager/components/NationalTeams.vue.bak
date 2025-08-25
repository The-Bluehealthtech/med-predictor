<template>
    <div>
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2"
        >
            <h2 class="text-xl font-bold text-blue-800">
                {{ $t("dtn.nationalTeams") }}
            </h2>
            <input
                v-model="search"
                type="text"
                :placeholder="$t('dtn.searchPlaceholder')"
                class="border rounded px-3 py-2 w-full md:w-64"
            />
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border rounded shadow">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="px-4 py-2 text-left">
                            {{ $t("dtn.category") }}
                        </th>
                        <th class="px-4 py-2 text-left">
                            {{ $t("dtn.gender") }}
                        </th>
                        <th class="px-4 py-2 text-left">
                            {{ $t("dtn.ageGroup") }}
                        </th>
                        <th class="px-4 py-2 text-left">
                            {{ $t("dtn.coach") }}
                        </th>
                        <th class="px-4 py-2 text-left">
                            {{ $t("dtn.actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="team in filteredTeams"
                        :key="team.id"
                        class="hover:bg-blue-50"
                    >
                        <td class="px-4 py-2">{{ team.category }}</td>
                        <td class="px-4 py-2">{{ team.gender }}</td>
                        <td class="px-4 py-2">{{ team.ageGroup }}</td>
                        <td class="px-4 py-2">{{ team.coach }}</td>
                        <td class="px-4 py-2">
                            <button
                                @click="showDetails(team)"
                                class="text-blue-700 hover:underline"
                            >
                                {{ $t("dtn.details") }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredTeams.length === 0">
                        <td colspan="5" class="text-center text-gray-400 py-6">
                            {{ $t("dtn.noTeams") }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Details Modal -->
        <div
            v-if="selectedTeam"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
        >
            <div
                class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg relative"
            >
                <button
                    @click="selectedTeam = null"
                    class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl"
                >
                    &times;
                </button>
                <h3 class="text-lg font-bold mb-2">
                    {{ selectedTeam.category }} - {{ selectedTeam.gender }}
                    {{ selectedTeam.ageGroup }}
                </h3>
                <div class="mb-2">
                    <span class="font-semibold">{{ $t("dtn.coach") }}:</span>
                    {{ selectedTeam.coach }}
                </div>
                <div class="mb-2">
                    <span class="font-semibold">{{ $t("dtn.players") }}:</span>
                </div>
                <ul class="list-disc ml-6 text-sm">
                    <li v-for="player in selectedTeam.players" :key="player.id">
                        {{ player.name }} ({{ player.position }})
                    </li>
                </ul>
                <div class="mt-4 text-right">
                    <button
                        @click="selectedTeam = null"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                        {{ $t("dtn.close") }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NationalTeams",
    data() {
        return {
            search: "",
            selectedTeam: null,
            teams: [
                {
                    id: 1,
                    category: "Senior",
                    gender: "Men",
                    ageGroup: "A",
                    coach: "Jean Dupont",
                    players: [
                        { id: 1, name: "Ali Ben Youssef", position: "Forward" },
                        {
                            id: 2,
                            name: "Karim Slimani",
                            position: "Midfielder",
                        },
                        {
                            id: 3,
                            name: "Yassine Trabelsi",
                            position: "Defender",
                        },
                    ],
                },
                {
                    id: 2,
                    category: "U20",
                    gender: "Women",
                    ageGroup: "U20",
                    coach: "Sophie Martin",
                    players: [
                        { id: 4, name: "Leila Haddad", position: "Goalkeeper" },
                        { id: 5, name: "Nadia Kacem", position: "Defender" },
                    ],
                },
                {
                    id: 3,
                    category: "Futsal",
                    gender: "Men",
                    ageGroup: "A",
                    coach: "Mohamed Salah",
                    players: [
                        { id: 6, name: "Omar Jaziri", position: "Pivot" },
                        { id: 7, name: "Rami Gharbi", position: "Winger" },
                    ],
                },
            ],
        };
    },
    computed: {
        filteredTeams() {
            const s = this.search.trim().toLowerCase();
            if (!s) return this.teams;
            return this.teams.filter(
                (team) =>
                    team.category.toLowerCase().includes(s) ||
                    team.gender.toLowerCase().includes(s) ||
                    team.ageGroup.toLowerCase().includes(s) ||
                    team.coach.toLowerCase().includes(s)
            );
        },
    },
    methods: {
        showDetails(team) {
            this.selectedTeam = team;
        },
    },
};
</script>
