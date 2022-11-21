<script setup lang="ts">
import {ref, reactive, onMounted, computed} from 'vue';
import axios from 'axios';
import Score from '../types/Score';

const isLoading = ref(true);

const todaysScores = reactive<Score[]>([]);
const hasNoScores = computed(() => todaysScores.length === 0);
async function getTodaysScores(): Promise<void> {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/score/today');
        Object.assign(todaysScores, response.data.data);
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
}

let currentPlace = 0;
let previousScore: null | number = null;
function getCurrentPlace(score: Score): string {
    if (score.value !== previousScore) {
        currentPlace += 1;
    }
    previousScore = score.value;
    return currentPlace + nth(currentPlace);
}
function nth(place: number): string {
    return ['st', 'nd', 'rd'][((place+90)%100-10)%10-1]||'th';
}

onMounted(() => {
    getTodaysScores();
});
</script>

<template>
  <div class="text-green-100">
    <div class="text-xl font-bold text-green-300">
      Today's Scoreboard
    </div>
    <div
      v-if="!hasNoScores && !isLoading"
      class="text-white"
    >
      <div
        v-for="score in todaysScores"
        :key="score.id"
        class="m-2 flex w-full flex-row rounded bg-slate-500 p-2"
      >
        <div class="basis-1/4">
          {{ getCurrentPlace(score) }}
        </div>
        <div class="mx-2 basis-1/2">
          {{ score.user_name }}
        </div>
        <div class="basis-1/4">
          {{ score.value === 7 ? 'X' : score.value }} / 6
        </div>
      </div>
    </div>
    <div v-else-if="isLoading">
      Loading...
    </div>
    <div v-else>
      No scores posted yet today.
    </div>
  </div>
</template>
