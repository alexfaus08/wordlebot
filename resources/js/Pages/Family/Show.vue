<script setup lang="ts">
import dayjs from 'dayjs';

defineProps<{
    name: string,
    scores: array
}>();
</script>

<template>
  <div class="container mx-auto flex flex-col items-center gap-16 p-16">
    {{ name }}
    <div class="mx-auto grid w-fit grid-cols-3 gap-x-16 gap-y-8">
      <div
        v-for="score in scores"
        :key="score.id"
        class="chat chat-start"
      >
        <div class="chat-header">
          {{ score.user.name }}
          <time class="text-xs opacity-50">{{ dayjs(score.created_at).format('hh:mm A') }}</time>
        </div>
        <div class="chat-bubble whitespace-pre">
          {{ score.full_score }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
