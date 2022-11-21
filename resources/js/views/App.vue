<script setup lang="ts">
import TodayScoreboard from '../components/TodayScoreboard.vue';
</script>

<template>
  <div class="flex h-screen w-screen flex-col items-center bg-slate-600">
    <div class="my-10 text-4xl font-bold text-green-300">
      Family Scores
    </div>
    <TodayScoreboard />
  </div>
</template>
