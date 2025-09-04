<template>
    <a :href="link" class="block">
        <div
            class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 p-6 h-full border border-gray-100 hover:border-blue-200 group"
        >
            <div class="flex flex-col items-center text-center h-full">
                <div
                    class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300"
                >
                    {{ icon }}
                </div>
                <h3
                    class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300"
                >
                    {{ title }}
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed flex-grow">
                    {{ description }}
                </p>
                <div
                    class="mt-4 text-blue-600 group-hover:text-blue-700 transition-colors duration-300"
                >
                    <span class="text-sm font-medium">{{ $t('auto.key121') }}</span>
                    <svg
                        class="inline-block w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        ></path>
                    </svg>
                </div>
            </div>
        </div>
    </a>
</template>

<script>
export default {
    name: "ModuleCard",
    props: {
        title: {
            type: String,
            required: true,
        },
        description: {
            type: String,
            required: true,
        },
        icon: {
            type: String,
            required: true,
        },
        link: {
            type: String,
            required: true,
        },
    },
};
</script>

<style scoped>
/* Additional custom styles if needed */
</style>
