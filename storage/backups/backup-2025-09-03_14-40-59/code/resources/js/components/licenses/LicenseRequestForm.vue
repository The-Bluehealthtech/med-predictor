<template>
    <div class="modal">
        <h3>
            Demande de licence pour {{ player.first_name }}
            {{ player.last_name }}
        </h3>
        <form @submit.prevent="submitRequest">
            <label>{{ $t('auto.key424') }}</label>
            <input v-model="season" required />
            <label>{{ $t('auto.key425') }}</label>
            <input type="file" multiple @change="handleFiles" />
            <button type="submit">{{ $t('auto.key426') }}</button>
            <button type="button" @click="$emit('close')">{{ $t('auto.key427') }}</button>
        </form>
        <div v-if="licenseStatus">
            <p>Statut actuel : {{ licenseStatus }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, toRefs } from "vue";
const props = defineProps({ player: Object });
const season = ref("");
const licenseStatus = ref("");
const documents = ref([]);

function handleFiles(event) {
    documents.value = Array.from(event.target.files);
}

function submitRequest() {
    // Appel API pour soumettre la demande
    // ...
}
</script>
