<template>
    <div>
        <h2>{{ $t('auto.key419') }}</h2>
        <div>
            <p>Joueur : {{ license.playerName }}</p>
            <p>Saison : {{ license.season }}</p>
            <p>Statut : {{ license.status }}</p>
            <p>{{ $t('auto.key420') }}</p>
            <ul>
                <li v-for="doc in license.documents" :key="doc">{{ doc }}</li>
            </ul>
        </div>
        <button @click="approveLicense">{{ $t('auto.key421') }}</button>
        <button @click="openRejectModal">{{ $t('auto.key422') }}</button>
        <button @click="openRequestInfoModal">{{ $t('auto.key423') }}</button>
        <!-- Modales pour rejet/demande info -->
    </div>
</template>

<script setup>
import { ref } from "vue";
const license = ref({}); // À charger via API ou props
function approveLicense() {
    // Appel API pour approuver
}
function openRejectModal() {
    // Ouvre modale pour motif de rejet
}
function openRequestInfoModal() {
    // Ouvre modale pour motif info
}
</script>
