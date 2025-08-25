<template>
  <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative">
      <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-800" @click="$emit('close')">{{ $t('auto.key235') }}</button>
      <h3 class="text-lg font-bold mb-4">{{ $t('auto.key236') }}</h3>
      <form @submit.prevent="handleSave">
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">{{ $t('auto.key237') }}</label>
          <select v-model="event.type" class="border rounded px-2 py-1 w-full" required>
            <option value="">{{ $t('auto.key238') }}</option>
            <option value="goal">{{ $t('auto.key239') }}</option>
            <option value="yellow_card">{{ $t('auto.key240') }}</option>
            <option value="red_card">{{ $t('auto.key241') }}</option>
            <option value="substitution">{{ $t('auto.key242') }}</option>
            <option value="injury">{{ $t('auto.key243') }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">{{ $t('auto.key244') }}</label>
          <select v-model="event.team_id" class="border rounded px-2 py-1 w-full" required @change="onTeamChange">
            <option value="">{{ $t('auto.key245') }}</option>
            <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">{{ $t('auto.key246') }}</label>
          <select v-model="event.player_id" class="border rounded px-2 py-1 w-full" required :disabled="!event.team_id">
            <option value="">{{ $t('auto.key247') }}</option>
            <option v-for="player in filteredPlayers" :key="player.id" :value="player.id">
              {{ player.first_name }} {{ player.last_name }}
            </option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">{{ $t('auto.key248') }}</label>
          <input type="number" v-model.number="event.minute" min="1" max="120" class="border rounded px-2 py-1 w-full" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">{{ $t('auto.key249') }}</label>
          <input type="text" v-model="event.description" class="border rounded px-2 py-1 w-full" maxlength="500" />
        </div>
        <div class="flex justify-end gap-2">
          <button type="button" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400" @click="$emit('close')">{{ $t('auto.key250') }}</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">{{ $t('auto.key251') }}</button>
        </div>
        <div v-if="error" class="text-red-600 mt-2">{{ error }}</div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
  teams: { type: Array, required: true }, // [{id, name, players: [{id, first_name, last_name}]}]
  defaultTeamId: { type: [String, Number], default: '' },
  defaultType: { type: String, default: '' },
  defaultMinute: { type: Number, default: null },
  defaultPlayerId: { type: [String, Number], default: '' },
  defaultDescription: { type: String, default: '' },
});

const emit = defineEmits(['close', 'save']);

const event = ref({
  type: props.defaultType || '',
  team_id: props.defaultTeamId || '',
  player_id: props.defaultPlayerId || '',
  minute: props.defaultMinute || '',
  description: props.defaultDescription || '',
});

const error = ref('');

const filteredPlayers = computed(() => {
  const team = props.teams.find(t => t.id == event.value.team_id);
  return team ? team.players : [];
});

const onTeamChange = () => {
  event.value.player_id = '';
};

const handleSave = () => {
  error.value = '';
  if (!event.value.type || !event.value.team_id || !event.value.player_id || !event.value.minute) {
    error.value = 'Tous les champs obligatoires doivent être remplis.';
    return;
  }
  if (event.value.minute < 1 || event.value.minute > 120) {
    error.value = 'La minute doit être comprise entre 1 et 120.';
    return;
  }
  emit('save', { ...event.value });
  emit('close');
};

onMounted(() => {
  // Pré-remplir si valeurs par défaut
});
</script>

<style scoped>
.fixed {
  z-index: 2000;
}
</style> 