<template>
    <div class="fifa-sync-dashboard">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">
                FIFA Integration Dashboard
            </h1>
            <p class="text-gray-600 mb-6">
                Manage data synchronization with FIFA API
            </p>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-12">
                <div
                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"
                ></div>
                <span class="ml-3 text-gray-600"
                    >{{ $t('auto.key211') }}</span
                >
            </div>

            <!-- Dashboard Content -->
            <div v-else>
                <!-- Dashboard Status -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Dashboard Status
                    </h3>
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
                    >
                        <div class="bg-green-100 p-4 rounded-lg">
                            <p class="text-sm font-medium text-green-800">
                                Connection Status
                            </p>
                            <p class="text-2xl font-semibold text-green-900">
                                {{ connectionStatus }}
                            </p>
                        </div>
                        <div class="bg-blue-100 p-4 rounded-lg">
                            <p class="text-sm font-medium text-blue-800">
                                Last Sync
                            </p>
                            <p class="text-2xl font-semibold text-blue-900">
                                {{ lastSyncTime }}
                            </p>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg">
                            <p class="text-sm font-medium text-yellow-800">
                                Pending Conflicts
                            </p>
                            <p class="text-2xl font-semibold text-yellow-900">
                                {{ pendingConflicts }}
                            </p>
                        </div>
                        <div class="bg-red-100 p-4 rounded-lg">
                            <p class="text-sm font-medium text-red-800">
                                Recent Errors
                            </p>
                            <p class="text-2xl font-semibold text-red-900">
                                {{ recentErrors }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sync Controls -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Synchronization Controls
                    </h3>
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4"
                    >
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >{{ $t('auto.key212') }}</label
                            >
                            <select
                                v-model="syncType"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="all">{{ $t('auto.key213') }}</option>
                                <option value="players">{{ $t('auto.key214') }}</option>
                                <option value="clubs">{{ $t('auto.key215') }}</option>
                                <option value="associations">
                                    Associations Only
                                </option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >{{ $t('auto.key216') }}</label
                            >
                            <input
                                v-model="batchSize"
                                type="number"
                                min="10"
                                max="100"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >{{ $t('auto.key217') }}</label
                            >
                            <input
                                v-model="filters"
                                type="text"
                                placeholder='{"country": "France"}'
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div class="flex items-end">
                            <button
                                @click="startSync"
                                :disabled="syncing"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ syncing ? "Syncing..." : "Start Sync" }}
                            </button>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <button
                            @click="testConnection"
                            :disabled="testing"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 disabled:opacity-50"
                        >
                            {{ testing ? "Testing..." : "Test Connection" }}
                        </button>
                        <button
                            @click="dryRun"
                            :disabled="dryRunning"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 disabled:opacity-50"
                        >
                            {{ dryRunning ? "Running..." : "Dry Run" }}
                        </button>
                        <button
                            @click="clearCache"
                            :disabled="clearing"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 disabled:opacity-50"
                        >
                            {{ clearing ? "Clearing..." : "Clear Cache" }}
                        </button>
                    </div>
                </div>

                <!-- Entity Statistics -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Players
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $t('auto.key218') }}</span>
                                <span class="font-semibold">{{
                                    stats.players.total
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $t('auto.key219') }}</span>
                                <span class="font-semibold text-green-600">{{
                                    stats.players.synced
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $t('auto.key220') }}</span>
                                <span class="font-semibold"
                                    >{{ stats.players.syncRate }}%</span
                                >
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Clubs
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $t('auto.key221') }}</span>
                                <span class="font-semibold">{{
                                    stats.clubs.total
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $t('auto.key222') }}</span>
                                <span class="font-semibold text-green-600">{{
                                    stats.clubs.synced
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $t('auto.key223') }}</span>
                                <span class="font-semibold"
                                    >{{ stats.clubs.syncRate }}%</span
                                >
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Associations
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600"
                                    >{{ $t('auto.key224') }}</span
                                >
                                <span class="font-semibold">{{
                                    stats.associations.total
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $t('auto.key225') }}</span>
                                <span class="font-semibold text-green-600">{{
                                    stats.associations.synced
                                }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $t('auto.key226') }}</span>
                                <span class="font-semibold"
                                    >{{ stats.associations.syncRate }}%</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Recent Activity
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="activity in recentActivity"
                            :key="activity.id"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                        >
                            <div class="flex items-center">
                                <div
                                    :class="getActivityIcon(activity.type)"
                                    class="w-8 h-8 rounded-full flex items-center justify-center mr-3"
                                >
                                    <span class="text-white text-sm">{{
                                        getActivityIconText(activity.type)
                                    }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ activity.description }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ activity.timestamp }}
                                    </p>
                                </div>
                            </div>
                            <span
                                :class="getActivityStatusClass(activity.status)"
                                class="px-2 py-1 rounded-full text-xs font-medium"
                            >
                                {{ activity.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import axios from "axios";

export default {
    name: "FifaSyncDashboard",
    setup() {
        const loading = ref(true);
        const syncing = ref(false);
        const testing = ref(false);
        const dryRunning = ref(false);
        const clearing = ref(false);

        const connectionStatus = ref("Unknown");
        const lastSyncTime = ref("Never");
        const pendingConflicts = ref(0);
        const recentErrors = ref(0);

        const syncType = ref("all");
        const batchSize = ref(50);
        const filters = ref("");

        const stats = ref({
            players: { total: 0, synced: 0, syncRate: 0 },
            clubs: { total: 0, synced: 0, syncRate: 0 },
            associations: { total: 0, synced: 0, syncRate: 0 },
        });

        const recentActivity = ref([]);

        // Configure axios for CSRF
        const configureAxios = () => {
            axios.defaults.headers.common["X-Requested-With"] =
                "XMLHttpRequest";
            axios.defaults.withCredentials = true;

            const token = document.head.querySelector(
                'meta[name="csrf-token"]'
            );
            if (token) {
                axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
            }
        };

        const loadDashboardData = async () => {
            try {
                loading.value = true;
                configureAxios();

                const response = await axios.get("/fifa/statistics/api");
                const data = response.data;

                console.log("Dashboard data received:", data);

                connectionStatus.value = data.connection_status || "Connected";
                lastSyncTime.value = data.last_sync || "Never";
                pendingConflicts.value = data.pending_conflicts || 0;
                recentErrors.value = data.recent_errors || 0;

                stats.value = {
                    players: {
                        total: data.players?.total || 0,
                        synced: data.players?.synced || 0,
                        syncRate: data.players?.sync_rate || 0,
                    },
                    clubs: {
                        total: data.clubs?.total || 0,
                        synced: data.clubs?.synced || 0,
                        syncRate: data.clubs?.sync_rate || 0,
                    },
                    associations: {
                        total: data.associations?.total || 0,
                        synced: data.associations?.synced || 0,
                        syncRate: data.associations?.sync_rate || 0,
                    },
                };

                recentActivity.value = data.recent_activity || [];
            } catch (error) {
                console.error("Error loading dashboard data:", error);
                connectionStatus.value = "Error";
                recentErrors.value = 1;
            } finally {
                loading.value = false;
            }
        };

        const startSync = async () => {
            try {
                syncing.value = true;
                configureAxios();

                const response = await axios.post("/fifa/sync-players", {
                    type: syncType.value,
                    batch_size: batchSize.value,
                    filters: filters.value ? JSON.parse(filters.value) : {},
                });

                console.log("Sync response:", response.data);

                if (response.data.success) {
                    await loadDashboardData(); // Reload data after sync
                }
            } catch (error) {
                console.error("Error starting sync:", error);
                alert(
                    "Sync failed: " +
                        (error.response?.data?.message || error.message)
                );
            } finally {
                syncing.value = false;
            }
        };

        const testConnection = async () => {
            try {
                testing.value = true;
                configureAxios();

                const response = await axios.get("/fifa/connectivity/status");
                connectionStatus.value = response.data.connected
                    ? "Connected"
                    : "Disconnected";
            } catch (error) {
                console.error("Error testing connection:", error);
                connectionStatus.value = "Error";
            } finally {
                testing.value = false;
            }
        };

        const dryRun = async () => {
            try {
                dryRunning.value = true;
                configureAxios();

                await axios.post("/fifa/sync-players", {
                    type: syncType.value,
                    batch_size: batchSize.value,
                    filters: filters.value ? JSON.parse(filters.value) : {},
                    dry_run: true,
                });
            } catch (error) {
                console.error("Error running dry run:", error);
            } finally {
                dryRunning.value = false;
            }
        };

        const clearCache = async () => {
            try {
                clearing.value = true;
                configureAxios();

                await axios.post("/fifa/clear-caches");
                await loadDashboardData(); // Reload data after clearing cache
            } catch (error) {
                console.error("Error clearing cache:", error);
            } finally {
                clearing.value = false;
            }
        };

        const getActivityIcon = (type) => {
            switch (type) {
                case "success":
                    return "bg-green-500";
                case "error":
                    return "bg-red-500";
                case "warning":
                    return "bg-yellow-500";
                default:
                    return "bg-gray-500";
            }
        };

        const getActivityIconText = (type) => {
            switch (type) {
                case "success":
                    return "✓";
                case "error":
                    return "✗";
                case "warning":
                    return "!";
                default:
                    return "•";
            }
        };

        const getActivityStatusClass = (status) => {
            switch (status) {
                case "completed":
                    return "bg-green-100 text-green-800";
                case "failed":
                    return "bg-red-100 text-red-800";
                case "pending":
                    return "bg-yellow-100 text-yellow-800";
                default:
                    return "bg-gray-100 text-gray-800";
            }
        };

        onMounted(() => {
            loadDashboardData();
        });

        return {
            loading,
            syncing,
            testing,
            dryRunning,
            clearing,
            connectionStatus,
            lastSyncTime,
            pendingConflicts,
            recentErrors,
            syncType,
            batchSize,
            filters,
            stats,
            recentActivity,
            startSync,
            testConnection,
            dryRun,
            clearCache,
            getActivityIcon,
            getActivityIconText,
            getActivityStatusClass,
        };
    },
};
</script>

<style scoped>
.fifa-sync-dashboard {
    /* @apply p-6; */
}
</style>
