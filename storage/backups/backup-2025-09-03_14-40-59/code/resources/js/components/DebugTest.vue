<template>
    <div
        style="
            background: red;
            color: white;
            padding: 20px;
            font-size: 24px;
            text-align: center;
        "
    >
        <h1>{{ $t('auto.key284') }}</h1>
        <p>{{ $t('auto.key285') }}</p>
        <p>Compteur: {{ count }}</p>
        <button
            @click="count++"
            style="
                background: white;
                color: red;
                padding: 10px;
                border: none;
                font-size: 18px;
                cursor: pointer;
            "
        >
            CLICK ME
        </button>
    </div>
</template>

<script>
export default {
    name: "DebugTest",
    data() {
        return {
            count: 0,
        };
    },
    mounted() {
        console.log("🔴 DebugTest component mounted!");
        alert("Vue.js fonctionne ! Composant DebugTest monté.");
    },
};
</script>
