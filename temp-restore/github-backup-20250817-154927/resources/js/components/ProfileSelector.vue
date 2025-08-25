<template>
    <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto"
    >
        <div
            v-for="profile in profiles"
            :key="profile.type"
            class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer border-2 border-transparent"
            :class="profile.border"
            @click="selectProfile(profile.type)"
        >
            <div class="p-8 text-center">
                <div
                    :class="
                        profile.bg +
                        ' w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4'
                    "
                >
                    <span class="text-3xl">{{ profile.icon }}</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    {{ profile.label }}
                </h3>
                <p class="text-gray-600 mb-6">{{ profile.desc }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ProfileSelector",
    data() {
        return {
            profiles: [
                {
                    type: "player",
                    label: "Player",
                    icon: "⚽",
                    desc: "Access your personal dashboard, health records, and match statistics",
                    border: "hover:border-blue-500",
                    bg: "bg-blue-100",
                },
                {
                    type: "club_staff",
                    label: "Club Staff",
                    icon: "🏆",
                    desc: "Manage club operations, player registrations, and team administration",
                    border: "hover:border-green-500",
                    bg: "bg-green-100",
                },
                {
                    type: "association_staff",
                    label: "Association Staff",
                    icon: "🏛️",
                    desc: "Oversee competitions, manage registrations, and coordinate activities",
                    border: "hover:border-purple-500",
                    bg: "bg-purple-100",
                },
                {
                    type: "medical_staff",
                    label: "Medical Staff",
                    icon: "🏥",
                    desc: "Access health records, medical assessments, and injury tracking",
                    border: "hover:border-red-500",
                    bg: "bg-red-100",
                },
                {
                    type: "referee",
                    label: "Referee",
                    icon: "⚖️",
                    desc: "Manage match assignments, create reports, and track performance",
                    border: "hover:border-yellow-500",
                    bg: "bg-yellow-100",
                },
                {
                    type: "system_admin",
                    label: "System Administrator",
                    icon: "⚙️",
                    desc: "Full system access, user management, and platform configuration",
                    border: "hover:border-gray-500",
                    bg: "bg-gray-100",
                },
            ],
        };
    },
    methods: {
        selectProfile(profileType) {
            sessionStorage.setItem("selectedProfileType", profileType);
            const footballType =
                sessionStorage.getItem("selectedFootballType") || "11aside";
            // Option 1: login/password hardcoded for demo (à adapter)
            const login = sessionStorage.getItem("login") || "";
            const password = sessionStorage.getItem("password") || "";
            const loginUrl = new URL("/login", window.location.origin);
            loginUrl.searchParams.set("association", "england");
            loginUrl.searchParams.set("access_type", profileType);
            loginUrl.searchParams.set("football_type", footballType);
            if (login) loginUrl.searchParams.set("login", login);
            if (password) loginUrl.searchParams.set("password", password);
            window.location.href = loginUrl.toString();
        },
    },
    mounted() {
        console.log("Vue ProfileSelector mounted!");
    },
};
</script>
