<template>
  <v-card class="mt-6" elevation="2">
    <v-card-title class="d-flex align-center px-6 py-4">
      <v-icon icon="mdi-trophy" color="warning" class="me-2" />
      <span class="text-h6">Top Active Quizzes</span>
    </v-card-title>

    <v-table density="comfortable">
      <thead>
        <tr>
          <th class="text-center" width="70">Rank</th>
          <th width="100"></th>
          <th>Quiz Name</th>
          <th class="text-center" width="100">Participants</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(quiz, index) in quizzes" :key="quiz.id" class="quiz-row" @click="goToQuizDetails(quiz)"
          style="cursor: pointer">
          <td class="text-center">
            <v-chip :color="getRankColor(index)" :variant="index < 3 ? 'flat' : 'outlined'" class="font-weight-medium"
              size="small">
              #{{ index + 1 }}
            </v-chip>
          </td>
          <td class="pa-2">
            <v-img :src="quiz.thumbnail_url || '/default-quiz-image.jpg'" height="60" width="80" cover
              class="rounded-lg" />
          </td>
          <td class="text-subtitle-2 font-weight-medium text-wrap">{{ quiz.name }}</td>
          <td class="text-center">
            <v-chip color="success" variant="tonal" size="small" class="font-weight-medium">
              {{ quiz.participant_quiz_summaries_count }}
            </v-chip>
          </td>
        </tr>
      </tbody>
    </v-table>
  </v-card>
</template>

<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();

const getRankColor = (index) => {
  const colors = ['warning', 'grey-lighten-1', 'brown-lighten-1'];
  return index < 3 ? colors[index] : 'grey-lighten-3';
};

defineProps({
  quizzes: {
    type: Array,
    default: () => []
  }
});

const goToQuizDetails = (quiz) => {
  router.push(`/quiz/details/${quiz.id}`);
};
</script>

<style scoped>
.quiz-row {
  transition: background-color 0.2s ease;
}

.quiz-row:hover {
  background-color: #f0f0f0;
}

.v-table {
  background: transparent !important;
}
</style>
