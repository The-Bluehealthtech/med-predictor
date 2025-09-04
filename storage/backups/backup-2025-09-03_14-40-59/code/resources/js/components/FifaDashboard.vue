<template>
    <div class="fifa-dashboard">
        <!-- Navigation -->
        <FifaNavigation />

        <!-- Main Content -->
        <main
            class="fifa-dashboard__main"
            :class="{ 'fifa-dashboard__main--collapsed': isNavCollapsed }"
        >
            <!-- Header -->
            <header class="fifa-dashboard__header">
                <div class="fifa-dashboard__header-content">
                    <div class="fifa-dashboard__header-left">
                        <h1 class="fifa-dashboard__title">{{ pageTitle }}</h1>
                        <p class="fifa-dashboard__subtitle">
                            {{ pageSubtitle }}
                        </p>
                    </div>

                    <div class="fifa-dashboard__header-right">
                        <!-- Quick Actions -->
                        <div class="fifa-dashboard__quick-actions">
                            <button
                                class="fifa-btn fifa-btn-secondary"
                                @click="handleRefresh"
                            >
                                <svg
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="fifa-btn__icon"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Refresh
                            </button>

                            <button
                                class="fifa-btn fifa-btn-primary"
                                @click="handleNewPlayer"
                            >
                                <svg
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="fifa-btn__icon"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                New Player
                            </button>
                        </div>

                        <!-- Notifications -->
                        <div class="fifa-dashboard__notifications">
                            <button
                                class="fifa-dashboard__notification-btn"
                                @click="toggleNotifications"
                            >
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"
                                    />
                                </svg>
                                <span
                                    class="fifa-dashboard__notification-badge"
                                    v-if="notificationCount > 0"
                                >
                                    {{ notificationCount }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="fifa-dashboard__content">
                <!-- Stats Cards -->
                <section class="fifa-dashboard__stats">
                    <div class="fifa-dashboard__stats-grid">
                        <div
                            v-for="stat in stats"
                            :key="stat.id"
                            class="fifa-card fifa-dashboard__stat-card"
                            :class="`fifa-dashboard__stat-card--${stat.type}`"
                        >
                            <div class="fifa-dashboard__stat-content">
                                <div
                                    class="fifa-dashboard__stat-icon"
                                    :class="`fifa-dashboard__stat-icon--${stat.type}`"
                                >
                                    <component :is="stat.icon" />
                                </div>
                                <div class="fifa-dashboard__stat-info">
                                    <h3 class="fifa-dashboard__stat-value">
                                        {{ stat.value }}
                                    </h3>
                                    <p class="fifa-dashboard__stat-label">
                                        {{ stat.label }}
                                    </p>
                                    <div
                                        class="fifa-dashboard__stat-change"
                                        v-if="stat.change"
                                    >
                                        <span
                                            :class="`fifa-dashboard__stat-change--${stat.change.type}`"
                                        >
                                            {{ stat.change.value }}
                                        </span>
                                        <span
                                            class="fifa-dashboard__stat-change-period"
                                            >{{ $t('auto.key232') }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Main Content Area -->
                <section class="fifa-dashboard__main-content">
                    <div class="fifa-dashboard__content-grid">
                        <!-- Recent Activity -->
                        <div class="fifa-card fifa-dashboard__activity-card">
                            <div class="fifa-card-header">
                                <h2 class="fifa-card-title">{{ $t('auto.key233') }}</h2>
                                <button
                                    class="fifa-btn fifa-btn-secondary fifa-btn--sm"
                                >
                                    View All
                                </button>
                            </div>
                            <div class="fifa-card-body">
                                <div class="fifa-dashboard__activity-list">
                                    <div
                                        v-for="activity in recentActivities"
                                        :key="activity.id"
                                        class="fifa-dashboard__activity-item"
                                    >
                                        <div
                                            class="fifa-dashboard__activity-icon"
                                            :class="`fifa-dashboard__activity-icon--${activity.type}`"
                                        >
                                            <component :is="activity.icon" />
                                        </div>
                                        <div
                                            class="fifa-dashboard__activity-content"
                                        >
                                            <p
                                                class="fifa-dashboard__activity-text"
                                            >
                                                {{ activity.text }}
                                            </p>
                                            <span
                                                class="fifa-dashboard__activity-time"
                                                >{{ activity.time }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Chart -->
                        <div class="fifa-card fifa-dashboard__chart-card">
                            <div class="fifa-card-header">
                                <h2 class="fifa-card-title">
                                    Performance Overview
                                </h2>
                                <div class="fifa-dashboard__chart-controls">
                                    <button
                                        v-for="period in chartPeriods"
                                        :key="period.value"
                                        class="fifa-btn fifa-btn--sm"
                                        :class="
                                            selectedPeriod === period.value
                                                ? 'fifa-btn-primary'
                                                : 'fifa-btn-secondary'
                                        "
                                        @click="selectedPeriod = period.value"
                                    >
                                        {{ period.label }}
                                    </button>
                                </div>
                            </div>
                            <div class="fifa-card-body">
                                <div class="fifa-dashboard__chart-placeholder">
                                    <svg
                                        viewBox="0 0 400 200"
                                        class="fifa-dashboard__chart-svg"
                                    >
                                        <!-- Sample chart path -->
                                        <path
                                            d="M0,200 L50,150 L100,180 L150,120 L200,140 L250,80 L300,100 L350,60 L400,40"
                                            fill="none"
                                            stroke="var(--fifa-blue-secondary)"
                                            stroke-width="3"
                                        />
                                        <circle
                                            cx="50"
                                            cy="150"
                                            r="4"
                                            fill="var(--fifa-blue-secondary)"
                                        />
                                        <circle
                                            cx="100"
                                            cy="180"
                                            r="4"
                                            fill="var(--fifa-blue-secondary)"
                                        />
                                        <circle
                                            cx="150"
                                            cy="120"
                                            r="4"
                                            fill="var(--fifa-blue-secondary)"
                                        />
                                        <circle
                                            cx="200"
                                            cy="140"
                                            r="4"
                                            fill="var(--fifa-blue-secondary)"
                                        />
                                        <circle
                                            cx="250"
                                            cy="80"
                                            r="4"
                                            fill="var(--fifa-blue-secondary)"
                                        />
                                        <circle
                                            cx="300"
                                            cy="100"
                                            r="4"
                                            fill="var(--fifa-blue-secondary)"
                                        />
                                        <circle
                                            cx="350"
                                            cy="60"
                                            r="4"
                                            fill="var(--fifa-blue-secondary)"
                                        />
                                        <circle
                                            cx="400"
                                            cy="40"
                                            r="4"
                                            fill="var(--fifa-blue-secondary)"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import FifaNavigation from "./FifaNavigation.vue";

// Icon Components
const PlayersIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path d="M9 12l3-3m0 0l3 3m-3-3v9m-9-9a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
  </svg>`,
};

const MedicalIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
  </svg>`,
};

const AnalyticsIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
  </svg>`,
};

const TrophyIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
  </svg>`,
};

const ActivityIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
  </svg>`,
};

const UserIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
  </svg>`,
};

const CheckIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
  </svg>`,
};

export default {
    name: "FifaDashboard",
    components: {
        FifaNavigation,
        PlayersIcon,
        MedicalIcon,
        AnalyticsIcon,
        TrophyIcon,
        ActivityIcon,
        UserIcon,
        CheckIcon,
    },
    setup() {
        const route = useRoute();
        const isNavCollapsed = ref(false);
        const selectedPeriod = ref("week");
        const notificationCount = ref(3);

        const pageTitle = computed(() => {
            const titles = {
                "/dashboard": "Dashboard",
                "/players": "Players Management",
                "/medical": "Medical Module",
                "/analytics": "Analytics & Reports",
            };
            return titles[route.path] || "Dashboard";
        });

        const pageSubtitle = computed(() => {
            const subtitles = {
                "/dashboard": "Overview of your football operations",
                "/players": "Manage player registrations and profiles",
                "/medical": "Health monitoring and medical assessments",
                "/analytics": "Performance insights and reporting",
            };
            return (
                subtitles[route.path] || "Overview of your football operations"
            );
        });

        const stats = ref([
            {
                id: 1,
                type: "primary",
                label: "Total Players",
                value: "24",
                icon: "PlayersIcon",
                change: { type: "positive", value: "+12%" },
            },
            {
                id: 2,
                type: "success",
                label: "Active Licenses",
                value: "18",
                icon: "TrophyIcon",
                change: { type: "positive", value: "+8%" },
            },
            {
                id: 3,
                type: "warning",
                label: "Medical Alerts",
                value: "3",
                icon: "MedicalIcon",
                change: { type: "negative", value: "-2" },
            },
            {
                id: 4,
                type: "info",
                label: "Performance Score",
                value: "87%",
                icon: "AnalyticsIcon",
                change: { type: "positive", value: "+5%" },
            },
        ]);

        const recentActivities = ref([
            {
                id: 1,
                type: "success",
                text: "New player registration: John Smith",
                time: "2 minutes ago",
                icon: "UserIcon",
            },
            {
                id: 2,
                type: "info",
                text: "Medical assessment completed for Player #15",
                time: "15 minutes ago",
                icon: "CheckIcon",
            },
            {
                id: 3,
                type: "warning",
                text: "License renewal reminder for 5 players",
                time: "1 hour ago",
                icon: "ActivityIcon",
            },
            {
                id: 4,
                type: "success",
                text: "Performance report generated successfully",
                time: "2 hours ago",
                icon: "AnalyticsIcon",
            },
        ]);

        const chartPeriods = ref([
            { label: "Week", value: "week" },
            { label: "Month", value: "month" },
            { label: "Quarter", value: "quarter" },
        ]);

        const handleRefresh = () => {
            // Refresh data logic
            console.log("Refreshing dashboard data...");
        };

        const handleNewPlayer = () => {
            // New player logic
            console.log("Opening new player form...");
        };

        const toggleNotifications = () => {
            // Toggle notifications panel
            console.log("Toggling notifications...");
        };

        onMounted(() => {
            // Listen for navigation collapse state
            const handleStorageChange = () => {
                const savedState = localStorage.getItem("fifa-nav-collapsed");
                if (savedState !== null) {
                    isNavCollapsed.value = JSON.parse(savedState);
                }
            };

            handleStorageChange();
            window.addEventListener("storage", handleStorageChange);
        });

        return {
            isNavCollapsed,
            selectedPeriod,
            notificationCount,
            pageTitle,
            pageSubtitle,
            stats,
            recentActivities,
            chartPeriods,
            handleRefresh,
            handleNewPlayer,
            toggleNotifications,
        };
    },
};
</script>

<style scoped>
.fifa-dashboard {
    display: flex;
    min-height: 100vh;
    background: var(--fifa-gray-50);
}

.fifa-dashboard__main {
    flex: 1;
    margin-left: 280px;
    transition: margin-left var(--fifa-transition-normal);
}

.fifa-dashboard__main--collapsed {
    margin-left: 80px;
}

/* Header */
.fifa-dashboard__header {
    background: var(--fifa-white);
    border-bottom: 1px solid var(--fifa-gray-200);
    padding: var(--fifa-spacing-lg) var(--fifa-spacing-xl);
    box-shadow: var(--fifa-shadow-sm);
}

.fifa-dashboard__header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1400px;
    margin: 0 auto;
}

.fifa-dashboard__title {
    font-size: var(--fifa-text-2xl);
    font-weight: var(--fifa-font-weight-bold);
    color: var(--fifa-gray-900);
    margin: 0 0 var(--fifa-spacing-xs) 0;
}

.fifa-dashboard__subtitle {
    font-size: var(--fifa-text-base);
    color: var(--fifa-gray-600);
    margin: 0;
}

.fifa-dashboard__header-right {
    display: flex;
    align-items: center;
    gap: var(--fifa-spacing-lg);
}

.fifa-dashboard__quick-actions {
    display: flex;
    gap: var(--fifa-spacing-md);
}

.fifa-btn__icon {
    width: 16px;
    height: 16px;
    margin-right: var(--fifa-spacing-xs);
}

.fifa-btn--sm {
    padding: var(--fifa-spacing-xs) var(--fifa-spacing-md);
    font-size: var(--fifa-text-xs);
}

.fifa-dashboard__notifications {
    position: relative;
}

.fifa-dashboard__notification-btn {
    position: relative;
    background: none;
    border: none;
    padding: var(--fifa-spacing-sm);
    border-radius: var(--fifa-radius-md);
    cursor: pointer;
    transition: background var(--fifa-transition-normal);
}

.fifa-dashboard__notification-btn:hover {
    background: var(--fifa-gray-100);
}

.fifa-dashboard__notification-btn svg {
    width: 20px;
    height: 20px;
    color: var(--fifa-gray-600);
}

.fifa-dashboard__notification-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: var(--fifa-error);
    color: var(--fifa-white);
    font-size: var(--fifa-text-xs);
    font-weight: var(--fifa-font-weight-bold);
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

/* Content */
.fifa-dashboard__content {
    padding: var(--fifa-spacing-xl);
    max-width: 1400px;
    margin: 0 auto;
}

/* Stats Section */
.fifa-dashboard__stats {
    margin-bottom: var(--fifa-spacing-2xl);
}

.fifa-dashboard__stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--fifa-spacing-lg);
}

.fifa-dashboard__stat-card {
    padding: var(--fifa-spacing-lg);
    border-left: 4px solid transparent;
}

.fifa-dashboard__stat-card--primary {
    border-left-color: var(--fifa-blue-primary);
}

.fifa-dashboard__stat-card--success {
    border-left-color: var(--fifa-success);
}

.fifa-dashboard__stat-card--warning {
    border-left-color: var(--fifa-warning);
}

.fifa-dashboard__stat-card--info {
    border-left-color: var(--fifa-blue-secondary);
}

.fifa-dashboard__stat-content {
    display: flex;
    align-items: center;
    gap: var(--fifa-spacing-lg);
}

.fifa-dashboard__stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--fifa-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.fifa-dashboard__stat-icon--primary {
    background: var(--fifa-blue-light);
    color: var(--fifa-blue-primary);
}

.fifa-dashboard__stat-icon--success {
    background: var(--fifa-success-light);
    color: var(--fifa-success);
}

.fifa-dashboard__stat-icon--warning {
    background: var(--fifa-warning-light);
    color: var(--fifa-warning);
}

.fifa-dashboard__stat-icon--info {
    background: var(--fifa-blue-light);
    color: var(--fifa-blue-secondary);
}

.fifa-dashboard__stat-icon svg {
    width: 24px;
    height: 24px;
}

.fifa-dashboard__stat-info {
    flex: 1;
}

.fifa-dashboard__stat-value {
    font-size: var(--fifa-text-2xl);
    font-weight: var(--fifa-font-weight-bold);
    color: var(--fifa-gray-900);
    margin: 0 0 var(--fifa-spacing-xs) 0;
}

.fifa-dashboard__stat-label {
    font-size: var(--fifa-text-sm);
    color: var(--fifa-gray-600);
    margin: 0 0 var(--fifa-spacing-xs) 0;
}

.fifa-dashboard__stat-change {
    display: flex;
    align-items: center;
    gap: var(--fifa-spacing-xs);
}

.fifa-dashboard__stat-change--positive {
    color: var(--fifa-success);
    font-weight: var(--fifa-font-weight-medium);
}

.fifa-dashboard__stat-change--negative {
    color: var(--fifa-error);
    font-weight: var(--fifa-font-weight-medium);
}

.fifa-dashboard__stat-change-period {
    font-size: var(--fifa-text-xs);
    color: var(--fifa-gray-500);
}

/* Main Content */
.fifa-dashboard__content-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: var(--fifa-spacing-xl);
}

.fifa-card-title {
    font-size: var(--fifa-text-lg);
    font-weight: var(--fifa-font-weight-semibold);
    color: var(--fifa-gray-900);
    margin: 0;
}

/* Activity Section */
.fifa-dashboard__activity-list {
    space-y: var(--fifa-spacing-md);
}

.fifa-dashboard__activity-item {
    display: flex;
    align-items: flex-start;
    gap: var(--fifa-spacing-md);
    padding: var(--fifa-spacing-md) 0;
    border-bottom: 1px solid var(--fifa-gray-100);
}

.fifa-dashboard__activity-item:last-child {
    border-bottom: none;
}

.fifa-dashboard__activity-icon {
    width: 32px;
    height: 32px;
    border-radius: var(--fifa-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.fifa-dashboard__activity-icon--success {
    background: var(--fifa-success-light);
    color: var(--fifa-success);
}

.fifa-dashboard__activity-icon--info {
    background: var(--fifa-blue-light);
    color: var(--fifa-blue-secondary);
}

.fifa-dashboard__activity-icon--warning {
    background: var(--fifa-warning-light);
    color: var(--fifa-warning);
}

.fifa-dashboard__activity-icon svg {
    width: 16px;
    height: 16px;
}

.fifa-dashboard__activity-content {
    flex: 1;
}

.fifa-dashboard__activity-text {
    font-size: var(--fifa-text-sm);
    color: var(--fifa-gray-800);
    margin: 0 0 var(--fifa-spacing-xs) 0;
    line-height: 1.4;
}

.fifa-dashboard__activity-time {
    font-size: var(--fifa-text-xs);
    color: var(--fifa-gray-500);
}

/* Chart Section */
.fifa-dashboard__chart-controls {
    display: flex;
    gap: var(--fifa-spacing-xs);
}

.fifa-dashboard__chart-placeholder {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--fifa-gray-50);
    border-radius: var(--fifa-radius-md);
}

.fifa-dashboard__chart-svg {
    width: 100%;
    height: 100%;
    max-width: 400px;
}

/* Responsive */
@media (max-width: 1024px) {
    .fifa-dashboard__content-grid {
        grid-template-columns: 1fr;
    }

    .fifa-dashboard__stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    }
}

@media (max-width: 768px) {
    .fifa-dashboard__main {
        margin-left: 0;
    }

    .fifa-dashboard__main--collapsed {
        margin-left: 0;
    }

    .fifa-dashboard__header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--fifa-spacing-lg);
    }

    .fifa-dashboard__header-right {
        width: 100%;
        justify-content: space-between;
    }

    .fifa-dashboard__content {
        padding: var(--fifa-spacing-lg);
    }

    .fifa-dashboard__stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
