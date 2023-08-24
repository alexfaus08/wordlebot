<script setup lang="ts">
import {computed, onMounted, ref} from 'vue';
import dayjs from 'dayjs';
import axios from 'axios';
import VueDatePicker from '@vuepic/vue-datepicker';

const usersWithScores = ref();
const from = dayjs().startOf('week').add(1, 'day');
const to = dayjs().subtract(1, 'day');
const presetDates = ref([
    {
        label: 'Current Week',
        value: [dayjs().startOf('week').add(1, 'day').toString(), dayjs().subtract(1, 'day').toString()]
    },
    {
        label: 'This Month',
        value: [dayjs().startOf('month').toString(), dayjs().subtract(1, 'day').toString()]
    },
    {
        label: 'This Year',
        value: [dayjs().startOf('year').toString(), dayjs().subtract(1, 'day').toString()]
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

const showTodayScoreWarning = computed<boolean>(() => {
    return dayjs(dates.value[1]).format('MM/DD/YYYY') === dayjs().format('MM/DD/YYYY');
});

onMounted(() => {
    getScores();
});
</script>

<template>
  <div class="mt-5 flex flex-col items-center pb-20">
    <h3 class="p-2 text-xl font-bold text-green-300">
      Leaderboard
    </h3>
    <div
      v-if="showTodayScoreWarning"
      class="mb-2 w-3/4 rounded border-2 border-yellow-500 bg-yellow-100 p-2 text-sm text-yellow-700"
    >
      Warning: leaderboard may become inaccurate if all players have not played today.
    </div>
    <div class="w-fit rounded bg-slate-500 p-4">
      <VueDatePicker
        :model-value="dates"
        range
        :clearable="false"
        :preset-dates="presetDates"
        @update:model-value="handleDateSelection"
      />
      <table class="table w-full">
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

<style>

</style>
