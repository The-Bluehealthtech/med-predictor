<template>
    <div
        style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: green;
            color: white;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 48px;
            font-weight: bold;
            text-align: center;
        "
    >
        <h1>{{ $t('auto.key315') }}</h1>
        <p>{{ $t('auto.key316') }}</p>
        <p>Compteur: {{ count }}</p>
        <button
            @click="count++"
            style="
                background: white;
                color: green;
                padding: 30px;
                border: none;
                font-size: 32px;
                cursor: pointer;
                margin: 30px;
                border-radius: 10px;
            "
        >
            CLICK ME
        </button>
        <p style="font-size: 24px; margin-top: 30px">
            Timestamp: {{ timestamp }}
        </p>
    </div>
</template>

<script>
export default {
    name: "BasicTest",
    data() {
        return {
            count: 0,
            timestamp: new Date().toLocaleTimeString(),
        };
    },
    mounted() {
        console.log("🟢 BasicTest component mounted!");
        alert("Vue.js fonctionne ! Composant BasicTest monté.");

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
