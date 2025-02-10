<template>
  <div>
    <v-container fluid>
      <div class="d-flex justify-space-between align-center">
        <page-title-with-action-buttons :title="{ show: true, text: 'Participant Answers' }"
          :backButton="{ show: true, text: 'Back' }" />

        <ExportModal page="3" :participantId="[route.params.id, route.query.summaryId]"
          :participantName="quizDetails.participantName" :summaryId="route.query.summaryId"
          :quizSummaryName="quizDetails.quizName" />

      </div>

      <div>


        <v-col class="mb-5">
          <v-card>
            <v-container>
              <div class="d-flex">
                <v-col>
                  <div class="mb-3">
                    <small>Name</small>
                    <h3 @click="redirectToQuizHistory" class="clickable-style ">
                      <v-tooltip location="top" text="Go to Participant's Quiz History">
                        <template v-slot:activator="{ props }">
                          <span v-bind="props">{{ quizDetails.participantName }}</span>
                        </template>
                      </v-tooltip>
                    </h3>
                  </div>
                  <div class="mb-3">
                    <small>Email Address</small>
                    <p v-if="quizDetails.participantEmail">{{
                      quizDetails.participantEmail }}</p>
                    <p v-else>N/A</p>
                  </div>
                  <div class="mb-3">
                    <small>Contact Number</small>
                    <p>{{ quizDetails.participantContactNumber || 'N/A' }}</p>
                  </div>
                  <div>
                    <small>Age</small>
                    <p>{{ quizDetails.participantAge || 'N/A' }}</p>
                  </div>
                </v-col>

                <v-card color="#f0f0f0" class="border-0 w-50 px-2" elevation="0">
                  <v-col>
                    <div class="mb-3">
                      <small>Quiz Name</small>
                      <h3>{{ quizDetails.quizName }}</h3>
                    </div>
                    <div class="mb-3">
                      <small>Score</small>
                      <h3 class="text-green-darken-1">{{ quizDetails.totalPoints || '0' }}/{{
                        maxPoints || '0'
                      }}</h3>
                    </div>
                    <div>
                      <small>Result</small>
                      <h3>{{ quizDetails.result?.header || 'TBD' }}</h3>
                    </div>
                  </v-col>
                </v-card>
              </div>
            </v-container>
          </v-card>
        </v-col>

        <v-col v-if="loading" class="d-flex justify-center">
          <LottieAnimation :animationData="customLoader" height="100" width="100" />
        </v-col>

        <div v-if="!loading">
          <v-col v-for="(question, index) in quizDetails.questions" :key="question.id">
            <v-card>
              <v-container class="pa-7">
                <v-row justify="space-between">
                  <div>
                    <span class="question-type-container text-caption px-3">
                      {{ question.questionType }}
                    </span>
                    <span class="text-caption">
                      Question {{ index + 1 }} out of {{ quizDetails.questions.length }}
                    </span>
                  </div>
                  <div>
                    <span>{{ question.points !== null && question.points !== undefined ?
                      question.points
                      : 0
                      }} Points</span>
                  </div>
                </v-row>
                <v-row class="mt-7">
                  <p class="text-caption">Question</p>
                </v-row>
                <v-row class="mt-3">
                  <p class="text-h6">{{ question.text }}</p>
                </v-row>
                <v-row v-if="question.image" class="mt-5">
                  <v-card height="150" width="100%" max-width="200">
                    <v-img :src="question.image" height="100%" width="100%" cover></v-img>
                  </v-card>
                </v-row>
                <v-row class="mt-7 mb-n2">
                  <p class="text-caption">Answer(s)</p>
                </v-row>
                <v-row class="mt-3">
                  <div v-for="(answer, index) in question.answer" :key="index" class="w-100">
                    <v-card class="rounded-lg mb-2 me-3 py-2 px-5 text-wrap" min-width="50px" variant="tonal">
                      {{ answer }}
                    </v-card>
                  </div>
                </v-row>
              </v-container>
            </v-card>
          </v-col>
        </div>

      </div>
    </v-container>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import customLoader from "@/json/loader.json";

const router = useRouter();
const route = useRoute();
const loading = ref(false);

const summaryId = ref(route.query.summaryId);

const quizDetails = ref({
  participantName: "",
  quizName: "",
  totalPoints: 0,
  questions: [],
});


const maxPoints = computed(() => {
  return quizDetails.value.questions.length * 5;
});

const redirectToQuizHistory = () => {
  router.push(`/participant/${quizDetails.value.participantID}/quizzes`);
};

const fetchQuizSummary = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/quiz-summary/${summaryId.value}`);

    quizDetails.value = {
      participantID: response.data.participant.id,
      participantName: response.data.participant.full_name,
      participantEmail: response.data.participant.email,
      participantContactNumber: response.data.participant.contact_number,
      participantAge: response.data.participant.age,
      quizName: response.data.quiz.name,
      totalPoints: response.data.quiz.score,
      result: response.data.result || 'TBD',
      questions: response.data.quiz.questions.map((q) => {
        const answersArray = q.answers ? Object.values(q.answers) : [];

        return {
          id: q.id,
          text: q.text,
          answer: answersArray,
          points: answersArray.reduce((total, answer) => {
            const choice = q.choices.find(c => c.choice_text === answer);
            return total + (choice?.points || 0);
          }, 0),
          questionType: q.question_type?.type || "Unknown",
          image: q.image,
        };
      }),
    };
  } catch (error) {
    console.error("Error fetching quiz summary:", error);
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  router.back();
};

onMounted(() => {
  fetchQuizSummary();
});
</script>

<style scoped>
.question-type-container {
  display: inline-block;
  padding: 2px 4px;
  font-weight: 500;
  border-radius: 10px;
  background-color: #e3f2fd;
  color: #1976d2;
  margin-right: 8px;
  pointer-events: none;
}

.question-image {
  max-width: 100%;
  max-height: 100px;
  border-radius: 4px;
  object-fit: contain;
}

.clickable-style {
  color: #1774FD;
  cursor: pointer;
  text-decoration: underline;
}
</style>