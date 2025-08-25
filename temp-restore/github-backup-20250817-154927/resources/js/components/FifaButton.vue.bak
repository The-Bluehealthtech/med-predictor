<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        class="fifa-btn"
        :class="[
            `fifa-btn--${variant}`,
            `fifa-btn--${size}`,
            {
                'fifa-btn--loading': loading,
                'fifa-btn--full-width': fullWidth,
                'fifa-btn--rounded': rounded,
            },
        ]"
        @click="handleClick"
    >
        <!-- Loading Spinner -->
        <div v-if="loading" class="fifa-btn__loading">
            <div class="fifa-btn__spinner"></div>
        </div>

        <!-- Icon Left -->
        <svg
            v-if="iconLeft && !loading"
            viewBox="0 0 20 20"
            fill="currentColor"
            class="fifa-btn__icon fifa-btn__icon--left"
        >
            <component :is="iconLeft" />
        </svg>

        <!-- Content -->
        <span v-if="!loading" class="fifa-btn__content">
            <slot>{{ text }}</slot>
        </span>

        <!-- Icon Right -->
        <svg
            v-if="iconRight && !loading"
            viewBox="0 0 20 20"
            fill="currentColor"
            class="fifa-btn__icon fifa-btn__icon--right"
        >
            <component :is="iconRight" />
        </svg>
    </button>
</template>

<script>
export default {
    name: "FifaButton",
    props: {
        type: {
            type: String,
            default: "button",
            validator: (value) => ["button", "submit", "reset"].includes(value),
        },
        variant: {
            type: String,
            default: "primary",
            validator: (value) =>
                [
                    "primary",
                    "secondary",
                    "success",
                    "warning",
                    "error",
                    "ghost",
                    "outline",
                ].includes(value),
        },
        size: {
            type: String,
            default: "md",
            validator: (value) =>
                ["xs", "sm", "md", "lg", "xl"].includes(value),
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        loading: {
            type: Boolean,
            default: false,
        },
        fullWidth: {
            type: Boolean,
            default: false,
        },
        rounded: {
            type: Boolean,
            default: false,
        },
        text: {
            type: String,
            default: "",
        },
        iconLeft: {
            type: [String, Object],
            default: null,
        },
        iconRight: {
            type: [String, Object],
            default: null,
        },
    },
    emits: ["click"],
    setup(props, { emit }) {
        const handleClick = (event) => {
            if (!props.disabled && !props.loading) {
                emit("click", event);
            }
        };

        return {
            handleClick,
        };
    },
};
</script>

<style scoped>
.fifa-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--fifa-spacing-xs);
    font-family: var(--fifa-font-family);
    font-weight: var(--fifa-font-weight-medium);
    text-decoration: none;
    border: 1px solid transparent;
    border-radius: var(--fifa-radius-md);
    cursor: pointer;
    transition: all var(--fifa-transition-normal);
    position: relative;
    white-space: nowrap;
    user-select: none;
    outline: none;
}

.fifa-btn:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.fifa-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

/* Sizes */
.fifa-btn--xs {
    padding: var(--fifa-spacing-xs) var(--fifa-spacing-sm);
    font-size: var(--fifa-text-xs);
    min-height: 28px;
}

.fifa-btn--sm {
    padding: var(--fifa-spacing-sm) var(--fifa-spacing-md);
    font-size: var(--fifa-text-sm);
    min-height: 32px;
}

.fifa-btn--md {
    padding: var(--fifa-spacing-md) var(--fifa-spacing-lg);
    font-size: var(--fifa-text-sm);
    min-height: 40px;
}

.fifa-btn--lg {
    padding: var(--fifa-spacing-lg) var(--fifa-spacing-xl);
    font-size: var(--fifa-text-base);
    min-height: 48px;
}

.fifa-btn--xl {
    padding: var(--fifa-spacing-xl) var(--fifa-spacing-2xl);
    font-size: var(--fifa-text-lg);
    min-height: 56px;
}

/* Variants */
.fifa-btn--primary {
    background: var(--fifa-blue-primary);
    color: var(--fifa-white);
    border-color: var(--fifa-blue-primary);
}

.fifa-btn--primary:hover:not(:disabled) {
    background: var(--fifa-blue-secondary);
    border-color: var(--fifa-blue-secondary);
    transform: translateY(-1px);
    box-shadow: var(--fifa-shadow-md);
}

.fifa-btn--secondary {
    background: var(--fifa-gray-100);
    color: var(--fifa-gray-700);
    border-color: var(--fifa-gray-300);
}

.fifa-btn--secondary:hover:not(:disabled) {
    background: var(--fifa-gray-200);
    border-color: var(--fifa-gray-400);
    color: var(--fifa-gray-800);
}

.fifa-btn--success {
    background: var(--fifa-success);
    color: var(--fifa-white);
    border-color: var(--fifa-success);
}

.fifa-btn--success:hover:not(:disabled) {
    background: #059669;
    border-color: #059669;
    transform: translateY(-1px);
    box-shadow: var(--fifa-shadow-md);
}

.fifa-btn--warning {
    background: var(--fifa-warning);
    color: var(--fifa-white);
    border-color: var(--fifa-warning);
}

.fifa-btn--warning:hover:not(:disabled) {
    background: #d97706;
    border-color: #d97706;
    transform: translateY(-1px);
    box-shadow: var(--fifa-shadow-md);
}

.fifa-btn--error {
    background: var(--fifa-error);
    color: var(--fifa-white);
    border-color: var(--fifa-error);
}

.fifa-btn--error:hover:not(:disabled) {
    background: #dc2626;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: var(--fifa-shadow-md);
}

.fifa-btn--ghost {
    background: transparent;
    color: var(--fifa-gray-600);
    border-color: transparent;
}

.fifa-btn--ghost:hover:not(:disabled) {
    background: var(--fifa-gray-100);
    color: var(--fifa-gray-800);
}

.fifa-btn--outline {
    background: transparent;
    color: var(--fifa-blue-primary);
    border-color: var(--fifa-blue-primary);
}

.fifa-btn--outline:hover:not(:disabled) {
    background: var(--fifa-blue-primary);
    color: var(--fifa-white);
    transform: translateY(-1px);
    box-shadow: var(--fifa-shadow-md);
}

/* States */
.fifa-btn--loading {
    pointer-events: none;
}

.fifa-btn--full-width {
    width: 100%;
}

.fifa-btn--rounded {
    border-radius: 9999px;
}

/* Icons */
.fifa-btn__icon {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.fifa-btn--xs .fifa-btn__icon {
    width: 12px;
    height: 12px;
}

.fifa-btn--sm .fifa-btn__icon {
    width: 14px;
    height: 14px;
}

.fifa-btn--lg .fifa-btn__icon {
    width: 18px;
    height: 18px;
}

.fifa-btn--xl .fifa-btn__icon {
    width: 20px;
    height: 20px;
}

.fifa-btn__icon--left {
    margin-right: var(--fifa-spacing-xs);
}

.fifa-btn__icon--right {
    margin-left: var(--fifa-spacing-xs);
}

/* Loading State */
.fifa-btn__loading {
    display: flex;
    align-items: center;
    justify-content: center;
}

.fifa-btn__spinner {
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: fifa-spin 1s linear infinite;
}

.fifa-btn--xs .fifa-btn__spinner {
    width: 12px;
    height: 12px;
    border-width: 1.5px;
}

.fifa-btn--sm .fifa-btn__spinner {
    width: 14px;
    height: 14px;
    border-width: 1.5px;
}

.fifa-btn--lg .fifa-btn__spinner {
    width: 18px;
    height: 18px;
    border-width: 2px;
}

.fifa-btn--xl .fifa-btn__spinner {
    width: 20px;
    height: 20px;
    border-width: 2px;
}

@keyframes fifa-spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* Content */
.fifa-btn__content {
    display: flex;
    align-items: center;
    gap: var(--fifa-spacing-xs);
}

/* Responsive */
@media (max-width: 768px) {
    .fifa-btn--full-width {
        width: 100%;
    }

    .fifa-btn--lg {
        padding: var(--fifa-spacing-md) var(--fifa-spacing-lg);
        font-size: var(--fifa-text-sm);
        min-height: 44px;
    }

    .fifa-btn--xl {
        padding: var(--fifa-spacing-lg) var(--fifa-spacing-xl);
        font-size: var(--fifa-text-base);
        min-height: 48px;
    }
}
</style>
