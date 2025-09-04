<template>
    <div>
        <h2>{{ $t('auto.key433') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ $t('auto.key434') }}</th>
                    <th>{{ $t('auto.key435') }}</th>
                    <th>{{ $t('auto.key436') }}</th>
                    <th>{{ $t('auto.key437') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="player in players" :key="player.id">
                    <td>{{ player.first_name }} {{ player.last_name }}</td>
                    <td>{{ player.birth_date }}</td>
                    <td>{{ player.licenseStatus }}</td>
                    <td>
                        <button @click="openLicenseModal(player)">
                            {{
                                player.hasLicense
                                    ? "Voir la licence"
                                    : "Demander une licence"
                            }}
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <LicenseRequestForm
            v-if="showLicenseModal"
            :player="selectedPlayer"
            @close="showLicenseModal = false"
        />
    </div>
</template>

<script setup>
import { ref } from "vue";
import LicenseRequestForm from "./LicenseRequestForm.vue";

const players = ref([]); // À charger via API
const showLicenseModal = ref(false);
const selectedPlayer = ref(null);

function openLicenseModal(player) {
    selectedPlayer.value = player;
    showLicenseModal.value = true;
}
</script>
