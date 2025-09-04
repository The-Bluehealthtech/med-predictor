<template>
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between">
                    <!-- Left: FIT Logo -->
                    <div class="flex items-center">
                        <img
                            src="https://via.placeholder.com/120x40/1e40af/ffffff?text=FIT"
                            alt="FIT Logo"
                            class="h-10 w-auto"
                        />
                    </div>

                    <!-- Center: Title and Subtext -->
                    <div class="flex flex-col items-center text-center">
                        <h1 class="text-2xl font-bold text-gray-800 mb-1">
                            Welcome to FIT – Football Intelligence
                        </h1>
                        <p class="text-sm text-gray-600 italic">
                            {{ footballType }}
                        </p>
                    </div>

                    <!-- Right: The Blue HealthTech Logo -->
                    <div class="flex items-center">
                        <img
                            src="https://via.placeholder.com/120x40/059669/ffffff?text=TBHC"
                            alt="The Blue HealthTech Logo"
                            class="h-10 w-auto"
                        />
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow px-6 py-8">
            <div class="max-w-7xl mx-auto">
                <!-- Module Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <ModuleCard
                        v-for="module in modules"
                        :key="module.title"
                        :title="module.title"
                        :description="module.description"
                        :icon="module.icon"
                        :link="module.link"
                    />
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-50 border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div
                    class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0"
                >
                    <div
                        class="flex flex-wrap justify-center md:justify-start space-x-6 text-sm"
                    >
                        <a
                            href="https://www.fifa.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-gray-600 hover:text-gray-800 transition-colors duration-200"
                        >
                            FIFA
                        </a>
                        <a
                            href="https://www.tbhc.uk"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-gray-600 hover:text-gray-800 transition-colors duration-200"
                        >
                            The Blue HealthTech
                        </a>
                        <a
                            href="#"
                            class="text-gray-600 hover:text-gray-800 transition-colors duration-200"
                        >
                            Language
                        </a>
                        <a
                            href="/admin/login"
                            class="text-gray-600 hover:text-gray-800 transition-colors duration-200"
                        >
                            Admin Login
                        </a>
                        <a
                            href="/support"
                            class="text-gray-600 hover:text-gray-800 transition-colors duration-200"
                        >
                            Support
                        </a>
                    </div>
                    <div class="text-sm text-gray-500">
                        © 2024 FIT – Football Intelligence & Tracking. Powered
                        by The Blue HealthTech.
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
import ModuleCard from "./ModuleCard.vue";

export default {
    name: "FitDashboard",
    components: {
        ModuleCard,
    },
    props: {
        footballType: {
            type: String,
            default: "11-a-side Football",
        },
    },
    data() {
        return {
            modules: [
                {
                    title: "Medical Module",
                    description:
                        "Health and performance data tracking, medical assessments, and fitness monitoring for players and staff.",
                    icon: "🏥",
                    link: "/health-records",
                },
                {
                    title: "Licensing Module",
                    description:
                        "Player registrations, license management, approvals, and compliance tracking for all stakeholders.",
                    icon: "📋",
                    link: "/licenses",
                },
                {
                    title: "Competitions",
                    description:
                        "Tournament management, match scheduling, results tracking, and competition administration.",
                    icon: "🏆",
                    link: "/competitions",
                },
                {
                    title: "Analytics & Reports",
                    description:
                        "Statistical analysis, performance metrics, and comprehensive reporting tools.",
                    icon: "📊",
                    link: "/performances",
                },
            ],
        };
    },
};
</script>

<style scoped>
/* Additional custom styles if needed */
</style>
