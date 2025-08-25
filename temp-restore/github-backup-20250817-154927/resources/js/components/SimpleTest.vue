<template>
    <div class="simple-test">
        <h1
            style="
                color: var(--fifa-blue-primary);
                font-size: 2rem;
                margin: 2rem 0;
            "
        >
            🎉 Application FIFA Vue.js Fonctionnelle !
        </h1>

        <div
            class="test-content"
            style="
                background: white;
                padding: 2rem;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            "
        >
            <h2 style="color: var(--fifa-gray-800); margin-bottom: 1rem">
                Test de l'Application
            </h2>

            <p style="color: var(--fifa-gray-600); margin-bottom: 1rem">
                Si vous voyez ce message, l'application Vue.js fonctionne
                correctement !
            </p>

            <div style="display: flex; gap: 1rem; margin-top: 2rem">
                <button
                    @click="testClick"
                    style="
                        background: var(--fifa-blue-primary);
                        color: white;
                        padding: 0.5rem 1rem;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                    "
                >
                    Test Click ({{ clickCount }})
                </button>

                <button
                    @click="testNavigation"
                    style="
                        background: var(--fifa-success);
                        color: white;
                        padding: 0.5rem 1rem;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                    "
                >
                    Test Navigation
                </button>
            </div>

            <div
                v-if="message"
                style="
                    margin-top: 1rem;
                    padding: 1rem;
                    background: var(--fifa-success-light);
                    color: var(--fifa-success);
                    border-radius: 4px;
                "
            >
                {{ message }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "SimpleTest",
    data() {
        return {
            clickCount: 0,
            message: "",
        };
    },
    methods: {
        testClick() {
            this.clickCount++;
            this.message = `Click testé ${this.clickCount} fois !`;
        },
        testNavigation() {
            this.$router.push("/test");
            this.message = "Navigation vers /test réussie !";
        },
    },
    mounted() {
        console.log("SimpleTest component mounted successfully!");
        this.message = "Composant monté avec succès !";
    },
};
</script>

<style scoped>
.simple-test {
    min-height: 100vh;
    background: linear-gradient(
        135deg,
        var(--fifa-blue-primary) 0%,
        var(--fifa-blue-secondary) 100%
    );
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.test-content {
    max-width: 600px;
    width: 100%;
}
</style>
