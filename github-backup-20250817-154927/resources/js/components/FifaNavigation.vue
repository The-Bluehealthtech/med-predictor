<template>
    <nav class="fifa-nav" :class="{ 'fifa-nav--collapsed': isCollapsed }">
        <!-- Logo Section -->
        <div class="fifa-nav__logo">
            <div class="fifa-nav__logo-container">
                <div class="fifa-nav__logo-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="fifa-nav__logo-svg"
                    >
                        <path
                            d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
                        />
                    </svg>
                </div>
                <div class="fifa-nav__logo-text" v-show="!isCollapsed">
                    <span class="fifa-nav__logo-title">{{ $t('auto.key227') }}</span>
                    <span class="fifa-nav__logo-subtitle"
                        >{{ $t('auto.key228') }}</span
                    >
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="fifa-nav__menu">
            <ul class="fifa-nav__list">
                <li
                    v-for="item in menuItems"
                    :key="item.id"
                    class="fifa-nav__item"
                >
                    <router-link
                        :to="item.path"
                        class="fifa-nav__link"
                        :class="{
                            'fifa-nav__link--active':
                                currentRoute === item.path,
                        }"
                        @click="handleNavigation(item)"
                    >
                        <div class="fifa-nav__link-icon">
                            <component :is="item.icon" />
                        </div>
                        <span
                            class="fifa-nav__link-text"
                            v-show="!isCollapsed"
                            >{{ item.label }}</span
                        >
                        <div
                            class="fifa-nav__link-badge"
                            v-if="item.badge && !isCollapsed"
                        >
                            <span class="fifa-badge fifa-badge-primary">{{
                                item.badge
                            }}</span>
                        </div>
                    </router-link>
                </li>
            </ul>
        </div>

        <!-- User Section -->
        <div class="fifa-nav__user">
            <div class="fifa-nav__user-profile" @click="toggleUserMenu">
                <div class="fifa-nav__user-avatar">
                    <img :src="userAvatar" :alt="userName" />
                </div>
                <div class="fifa-nav__user-info" v-show="!isCollapsed">
                    <span class="fifa-nav__user-name">{{ userName }}</span>
                    <span class="fifa-nav__user-role">{{ userRole }}</span>
                </div>
                <div class="fifa-nav__user-arrow" v-show="!isCollapsed">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            </div>

            <!-- User Dropdown Menu -->
            <div class="fifa-nav__user-menu" v-if="showUserMenu">
                <div class="fifa-nav__user-menu-item" @click="handleProfile">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span>{{ $t('auto.key229') }}</span>
                </div>
                <div class="fifa-nav__user-menu-item" @click="handleSettings">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span>{{ $t('auto.key230') }}</span>
                </div>
                <div class="fifa-nav__user-menu-divider"></div>
                <div
                    class="fifa-nav__user-menu-item fifa-nav__user-menu-item--danger"
                    @click="handleLogout"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span>{{ $t('auto.key231') }}</span>
                </div>
            </div>
        </div>

        <!-- Collapse Toggle -->
        <button class="fifa-nav__toggle" @click="toggleCollapse">
            <svg
                viewBox="0 0 20 20"
                fill="currentColor"
                :class="{ 'fifa-nav__toggle-icon--rotated': isCollapsed }"
            >
                <path
                    fill-rule="evenodd"
                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                    clip-rule="evenodd"
                />
            </svg>
        </button>
    </nav>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter, useRoute } from "vue-router";

// Icon Components
const DashboardIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
  </svg>`,
};

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

const SettingsIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
  </svg>`,
};

const TestIcon = {
    template: `<svg viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
  </svg>`,
};

export default {
    name: "FifaNavigation",
    components: {
        DashboardIcon,
        TestIcon,
        PlayersIcon,
        MedicalIcon,
        AnalyticsIcon,
        SettingsIcon,
    },
    setup() {
        const router = useRouter();
        const route = useRoute();

        const isCollapsed = ref(false);
        const showUserMenu = ref(false);

        const currentRoute = computed(() => route.path);

        const menuItems = ref([
            {
                id: "dashboard",
                label: "Dashboard",
                path: "/dashboard",
                icon: "DashboardIcon",
                badge: null,
            },
            {
                id: "players",
                label: "Players",
                path: "/players",
                icon: "PlayersIcon",
                badge: "12",
            },
            {
                id: "medical",
                label: "Medical",
                path: "/medical",
                icon: "MedicalIcon",
                badge: "3",
            },
            {
                id: "analytics",
                label: "Analytics",
                path: "/analytics",
                icon: "AnalyticsIcon",
                badge: null,
            },
            {
                id: "settings",
                label: "Settings",
                path: "/settings",
                icon: "SettingsIcon",
                badge: null,
            },
            {
                id: "test",
                label: "Test FIFA",
                path: "/test",
                icon: "TestIcon",
                badge: "NEW",
            },
        ]);

        const userAvatar = ref(
            "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face"
        );
        const userName = ref("John Doe");
        const userRole = ref("Club Manager");

        const toggleCollapse = () => {
            isCollapsed.value = !isCollapsed.value;
            localStorage.setItem("fifa-nav-collapsed", isCollapsed.value);
        };

        const toggleUserMenu = () => {
            showUserMenu.value = !showUserMenu.value;
        };

        const handleNavigation = (item) => {
            router.push(item.path);
        };

        const handleProfile = () => {
            router.push("/profile");
            showUserMenu.value = false;
        };

        const handleSettings = () => {
            router.push("/settings");
            showUserMenu.value = false;
        };

        const handleLogout = () => {
            // Handle logout logic
            console.log("Logout clicked");
            showUserMenu.value = false;
        };

        const handleClickOutside = (event) => {
            if (!event.target.closest(".fifa-nav__user")) {
                showUserMenu.value = false;
            }
        };

        onMounted(() => {
            const savedState = localStorage.getItem("fifa-nav-collapsed");
            if (savedState !== null) {
                isCollapsed.value = JSON.parse(savedState);
            }

            document.addEventListener("click", handleClickOutside);
        });

        onUnmounted(() => {
            document.removeEventListener("click", handleClickOutside);
        });

        return {
            isCollapsed,
            showUserMenu,
            currentRoute,
            menuItems,
            userAvatar,
            userName,
            userRole,
            toggleCollapse,
            toggleUserMenu,
            handleNavigation,
            handleProfile,
            handleSettings,
            handleLogout,
        };
    },
};
</script>

<style scoped>
.fifa-nav {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 280px;
    background: var(--fifa-white);
    border-right: 1px solid var(--fifa-gray-200);
    display: flex;
    flex-direction: column;
    transition: width var(--fifa-transition-normal);
    z-index: var(--fifa-z-fixed);
    box-shadow: var(--fifa-shadow-lg);
}

.fifa-nav--collapsed {
    width: 80px;
}

/* Logo Section */
.fifa-nav__logo {
    padding: var(--fifa-spacing-lg);
    border-bottom: 1px solid var(--fifa-gray-200);
    background: linear-gradient(
        135deg,
        var(--fifa-blue-primary) 0%,
        var(--fifa-blue-secondary) 100%
    );
}

.fifa-nav__logo-container {
    display: flex;
    align-items: center;
    gap: var(--fifa-spacing-md);
}

.fifa-nav__logo-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--fifa-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.fifa-nav__logo-svg {
    width: 24px;
    height: 24px;
    color: var(--fifa-white);
}

.fifa-nav__logo-text {
    display: flex;
    flex-direction: column;
    color: var(--fifa-white);
}

.fifa-nav__logo-title {
    font-size: var(--fifa-text-xl);
    font-weight: var(--fifa-font-weight-bold);
    line-height: 1.2;
}

.fifa-nav__logo-subtitle {
    font-size: var(--fifa-text-xs);
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Navigation Menu */
.fifa-nav__menu {
    flex: 1;
    padding: var(--fifa-spacing-lg) 0;
    overflow-y: auto;
}

.fifa-nav__list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.fifa-nav__item {
    margin: 0;
}

.fifa-nav__link {
    display: flex;
    align-items: center;
    padding: var(--fifa-spacing-md) var(--fifa-spacing-lg);
    color: var(--fifa-gray-600);
    text-decoration: none;
    transition: all var(--fifa-transition-normal);
    position: relative;
    gap: var(--fifa-spacing-md);
}

.fifa-nav__link:hover {
    background: var(--fifa-gray-50);
    color: var(--fifa-blue-primary);
}

.fifa-nav__link--active {
    background: var(--fifa-blue-light);
    color: var(--fifa-blue-primary);
    border-right: 3px solid var(--fifa-blue-primary);
}

.fifa-nav__link-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.fifa-nav__link-text {
    font-weight: var(--fifa-font-weight-medium);
    white-space: nowrap;
}

.fifa-nav__link-badge {
    margin-left: auto;
}

/* User Section */
.fifa-nav__user {
    padding: var(--fifa-spacing-lg);
    border-top: 1px solid var(--fifa-gray-200);
    position: relative;
}

.fifa-nav__user-profile {
    display: flex;
    align-items: center;
    gap: var(--fifa-spacing-md);
    cursor: pointer;
    padding: var(--fifa-spacing-sm);
    border-radius: var(--fifa-radius-md);
    transition: background var(--fifa-transition-normal);
}

.fifa-nav__user-profile:hover {
    background: var(--fifa-gray-50);
}

.fifa-nav__user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.fifa-nav__user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.fifa-nav__user-info {
    flex: 1;
    min-width: 0;
}

.fifa-nav__user-name {
    display: block;
    font-weight: var(--fifa-font-weight-medium);
    color: var(--fifa-gray-800);
    font-size: var(--fifa-text-sm);
    line-height: 1.2;
}

.fifa-nav__user-role {
    display: block;
    font-size: var(--fifa-text-xs);
    color: var(--fifa-gray-500);
    line-height: 1.2;
}

.fifa-nav__user-arrow {
    width: 16px;
    height: 16px;
    color: var(--fifa-gray-400);
    transition: transform var(--fifa-transition-normal);
}

.fifa-nav__user-menu {
    position: absolute;
    bottom: 100%;
    left: var(--fifa-spacing-lg);
    right: var(--fifa-spacing-lg);
    background: var(--fifa-white);
    border: 1px solid var(--fifa-gray-200);
    border-radius: var(--fifa-radius-lg);
    box-shadow: var(--fifa-shadow-xl);
    padding: var(--fifa-spacing-sm);
    margin-bottom: var(--fifa-spacing-sm);
    z-index: var(--fifa-z-dropdown);
}

.fifa-nav__user-menu-item {
    display: flex;
    align-items: center;
    gap: var(--fifa-spacing-sm);
    padding: var(--fifa-spacing-sm);
    border-radius: var(--fifa-radius-md);
    cursor: pointer;
    transition: background var(--fifa-transition-normal);
    font-size: var(--fifa-text-sm);
    color: var(--fifa-gray-700);
}

.fifa-nav__user-menu-item:hover {
    background: var(--fifa-gray-50);
}

.fifa-nav__user-menu-item svg {
    width: 16px;
    height: 16px;
}

.fifa-nav__user-menu-item--danger {
    color: var(--fifa-error);
}

.fifa-nav__user-menu-item--danger:hover {
    background: var(--fifa-error-light);
}

.fifa-nav__user-menu-divider {
    height: 1px;
    background: var(--fifa-gray-200);
    margin: var(--fifa-spacing-sm) 0;
}

/* Toggle Button */
.fifa-nav__toggle {
    position: absolute;
    top: var(--fifa-spacing-lg);
    right: -12px;
    width: 24px;
    height: 24px;
    background: var(--fifa-white);
    border: 1px solid var(--fifa-gray-200);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--fifa-transition-normal);
    box-shadow: var(--fifa-shadow-md);
}

.fifa-nav__toggle:hover {
    box-shadow: var(--fifa-shadow-lg);
    transform: scale(1.1);
}

.fifa-nav__toggle svg {
    width: 12px;
    height: 12px;
    color: var(--fifa-gray-600);
    transition: transform var(--fifa-transition-normal);
}

.fifa-nav__toggle-icon--rotated {
    transform: rotate(180deg);
}

/* Responsive */
@media (max-width: 768px) {
    .fifa-nav {
        transform: translateX(-100%);
    }

    .fifa-nav--mobile-open {
        transform: translateX(0);
    }
}
</style>
