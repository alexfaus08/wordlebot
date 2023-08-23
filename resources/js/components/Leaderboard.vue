<script setup lang="ts">
import {onMounted, ref} from 'vue';
import * as dayjs from 'dayjs';
import axios from 'axios';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const usersWithScores = ref();
const from = dayjs().subtract(1, 'week');
const to = dayjs();
const presetDates = ref([
    {
        label: 'Current Week',
        value: [dayjs().startOf('week').add(1, 'day').toString(), dayjs().toString()]
    },
    {
        label: 'This Month',
        value: [dayjs().startOf('month').toString(), dayjs().toString()]
    },
    {
        label: 'This Year',
        value: [dayjs().startOf('year').toString(), dayjs().toString()]
    }
]);

const dates = ref([from.toString(), to.toString()]);
const isLoading = ref(false);
async function getScores() {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/leaderboard', {
            params: {
                from: dayjs(dates.value[0]).format('MM/DD/YYYY'),
                to: dayjs(dates.value[1]).format('MM/DD/YYYY')
            }
        });

        usersWithScores.value = response.data.data;
    } catch (e) {
        console.error(e);
    } finally {
        isLoading.value = false;
    }
}

const handleDateSelection = async (modelData: string[]) => {
    dates.value = modelData;
    await getScores();
};

onMounted(() => {
    getScores();
});
</script>

<template>
  <div class="pb-20">
    <h3 class="p-2 text-xl font-bold text-green-300">
      Leaderboard
    </h3>
    <div class="rounded bg-slate-500 p-4">
      <VueDatePicker
        :model-value="dates"
        range
        :clearable="false"
        :preset-dates="presetDates"
        @update:model-value="handleDateSelection"
      />
      <table class="table">
        <thead>
          <tr>
            <td class="p-2 font-bold text-green-300">
              Name
            </td>
            <td class="p-2 font-bold text-green-300">
              Score
            </td>
          </tr>
        </thead>
        <tbody class="text-white">
          <tr
            v-for="(user, index) in usersWithScores"
            :key="user.id"
            :class="index % 2 === 0 ? '' : 'bg-slate-400'"
          >
            <td class="p-2">
              {{ user.name }}
            </td>
            <td class="p-2">
              {{ user.total }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>

</style>
