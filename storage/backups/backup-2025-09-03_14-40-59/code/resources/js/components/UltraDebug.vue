<template>
    <div
        style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: red;
            color: white;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 32px;
            font-weight: bold;
        "
    >
        <h1>{{ $t('auto.key61') }}</h1>
        <p>{{ $t('auto.key62') }}</p>
        <p>Compteur: {{ count }}</p>
        <button
            @click="count++"
            style="
                background: white;
                color: red;
                padding: 20px;
                border: none;
                font-size: 24px;
                cursor: pointer;
                margin: 20px;
            "
        >
            CLICK ME
        </button>
        <p style="font-size: 16px; margin-top: 20px">
            Timestamp: {{ timestamp }}
        </p>
    </div>
</template>

<script>
export default {
    name: "UltraDebug",
    data() {
        return {
            count: 0,
            timestamp: new Date().toLocaleTimeString(),
        };
    },
    mounted() {
        console.log("🔴 UltraDebug component mounted!");
        alert("Vue.js fonctionne ! Composant UltraDebug monté.");

        // Forcer la suppression de l'écran de chargement
        const loadingScreen = document.getElementById("loading-screen");
        if (loadingScreen) {
            loadingScreen.style.display = "none";
            console.log("Écran de chargement masqué");
        }
    },
    updated() {
        this.timestamp = new Date().toLocaleTimeString();
    },
};
</script>
