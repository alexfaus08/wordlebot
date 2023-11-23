<script setup lang="ts">
defineProps<{
    name: string,
    scores: array
}>();
</script>

<template>
  <div>
    {{ name }}
    <pre>
    {{ scores[0].full_score }}
      </pre>
  </div>
</template>

<style scoped>

</style>
