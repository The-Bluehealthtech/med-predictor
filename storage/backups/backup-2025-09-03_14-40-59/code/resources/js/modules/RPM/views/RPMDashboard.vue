<template>
    <div class="rpm-dashboard">
        <div class="debug-banner">
            <h1>{{ $t('auto.key3') }}</h1>
            <p>{{ $t('auto.key4') }}</p>
        </div>

        <div class="main-content">
            <h2>{{ $t('auto.key5') }}</h2>
            <p>{{ $t('auto.key6') }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "RPMDashboard",
    mounted() {
        console.log("🎯 RPMDashboard component mounted!");
    },
};
</script>

<style scoped>
.rpm-dashboard {
    padding: 20px;
}

.debug-banner {
    background: linear-gradient(45deg, #0066ff, #00ccff);
    color: white;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    text-align: center;
    border: 3px solid yellow;
}

.main-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
}
</style>
