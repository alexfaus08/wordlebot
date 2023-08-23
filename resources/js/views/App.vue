<script setup lang="ts">
import TodayScoreboard from '../components/TodayScoreboard.vue';
import Leaderboard from '../components/Leaderboard.vue';
</script>

<template>
  <div class="min-w-screen flex min-h-screen flex-col items-center bg-slate-600">
    <div class="my-10 text-4xl font-bold text-green-300">
      Family Scores
    </div>
    <TodayScoreboard />
    <Leaderboard />
  </div>
</template>
