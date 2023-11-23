<script setup lang="ts">
import dayjs from 'dayjs';

defineProps<{
    name: string,
    scores: array
}>();

function reformatWordle(input: string): string {
    const lines = input.split(' ');
    const header = lines.slice(0, 3).join(' ');
    const scoreBlocks = lines.slice(4).join('\n');

    return header + '\n\n' + scoreBlocks;
}
</script>

<template>
  <div class="container mx-auto flex flex-col items-center gap-16 p-16">
    <h2 class="text-3xl font-bold">
      What did the
      <span class="text-primary">
        {{ name }}
      </span>
      score today?
    </h2>
    <div class="mx-auto grid w-fit grid-cols-2 items-start gap-x-16 gap-y-8 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="score in scores"
        :key="score.id"
        class="chat chat-start"
      >
        <div class="chat-header pl-2">
          {{ score.user.name }}
          <time class="text-xs opacity-50">{{ dayjs(score.created_at).format('hh:mm A') }}</time>
        </div>
        <div class="chat-bubble whitespace-pre py-4 pl-4 pr-8">
          {{ reformatWordle(score.full_score) }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
