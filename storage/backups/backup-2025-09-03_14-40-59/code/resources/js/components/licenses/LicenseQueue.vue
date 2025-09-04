<template>
    <div>
        <h2>{{ $t('auto.key428') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ $t('auto.key429') }}</th>
                    <th>{{ $t('auto.key430') }}</th>
                    <th>{{ $t('auto.key431') }}</th>
                    <th>{{ $t('auto.key432') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="license in licenses" :key="license.id">
                    <td>{{ license.playerName }}</td>
                    <td>{{ license.season }}</td>
                    <td>{{ license.status }}</td>
                    <td>
                        <button @click="reviewLicense(license)">
                            Examiner
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref } from "vue";
const licenses = ref([]); // À charger via API
function reviewLicense(license) {
    // Rediriger vers LicenseReview.vue
}
</script>
