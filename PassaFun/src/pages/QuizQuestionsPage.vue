<template>
  <PageContainer class="select-none">
    <PageHeader
      :to="{ path: `/${currentQuizUID}`, query: { preview: route.query.preview } }"
      class="relative z-10"
      :currentQuestionIndex="currentQuestionIndex"
    />

    <div class="flex flex-col place-items-center justify-start h-[100%] px-4 pb-4">
      <div
        class="flex flex-col gap-1 w-full items-center justify-center font-roboto sm:bg-transparent relative z-10"
      >
        <div class="flex place-items-center justify-start w-full">
          <ProgressBar
            :currentQuestionIndex="currentQuestionIndex"
            :totalQuestions="totalQuestions"
          />
        </div>
      </div>

      <div class="w-full">
        <transition name="fade" mode="out-in">
          <component
            v-if="currentQuestion"
            :is="getQuestionComponent(currentQuestion.question_type.type)"
            :key="currentQuestionIndex"
            :question="currentQuestion"
            :disabled="!isAnswerSelected"
            @update:response="updateUserResponse"
          />
        </transition>
      </div>

      <div
        class="absolute bottom-0 left-0 right-0 flex justify-between space-x-2 w-full px-4 pb-2"
        :style="
          quizTheme.background_type === 'color'
            ? { backgroundColor: quizTheme.background_value }
            : ''
        "
      >
        <Button
          :boxShadowColor="currentQuestionIndex === 0 ? '#CFCFCF' : quizTheme.accent_color"
          backgroundColor="transparent"
          :boxShadowSize="currentQuestionIndex === 0 ? '1px' : '2px'"
          :boxShadowBottom="currentQuestionIndex === 0 ? '1px' : '2px'"
          :borderColor="currentQuestionIndex === 0 ? '#CFCFCF' : quizTheme.accent_color"
          :disabled="currentQuestionIndex === 0"
          :textColor="currentQuestionIndex === 0 ? '#CFCFCF' : quizTheme.main_color"
          @click="goToPreviousQuestion"
          class="rounded-md text-lg w-[50%] font-nunito flex place-items-center justify-center text-center font-bold"
        >
          <ChevronLeft size="24" class="-ml-2" />
          <span>Previous</span>
        </Button>

        <Button
          :boxShadowColor="isAnswerSelected ? quizTheme.accent_color : 'transparent'"
          :backgroundColor="isAnswerSelected ? quizTheme.button_color : '#d1d5db'"
          :boxShadowBottom="isAnswerSelected ? '2px' : '0'"
          :borderColor="isAnswerSelected ? quizTheme.accent_color : '#CFCFCF'"
          :disabled="!isAnswerSelected"
          @click="handleQuizAction"
          class="rounded-md text-lg w-[50%] flex place-items-center justify-center font-nunito font-bold"
        >
          <span>{{ actionButtonText }}</span>
          <ChevronRight color="white " size="24" class="-mr-2" />
        </Button>
      </div>
    </div>
  </PageContainer>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useQuizzesStore } from '@/stores/quizzesStore';
import PageContainer from '@/components/PageContainer.vue';
import ProgressBar from '@/components/quiz/ProgressBar.vue';
import Button from '@/components/ui/Button.vue';

import SingleSelectQuestionLayout from '@/layout/SingleSelectQuestionLayout.vue';
import MultipleSelectQuestionLayout from '@/layout/MultipleSelectQuestionLayout.vue';
import OpenEndedQuestionLayout from '@/layout/OpenEndedQuestionLayout.vue';
import RatingScaleQuestionLayout from '@/layout/RatingScaleQuestionLayout.vue';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import { vibrate } from '@/utils/vibrationButton';
import { removeQuizInSessionStorage } from '@/utils/sessionStorage';
import { debounce } from 'lodash';

const store = useQuizzesStore();
const { quizTheme, questionsContent, currentQuizUID, quizId, quizDetail, isQuizPublished, isQuizNewlyFetched } =
  storeToRefs(store);

const router = useRouter();
const userResponses = ref({});

const currentQuestionIndex = ref(0);

const route = useRoute();

const currentQuestion = computed(() => {
  return questionsContent.value[currentQuestionIndex.value] || null;
});

const totalQuestions = computed(() => {
  return questionsContent.value.length;
});

const isLastQuestion = computed(() => {
  return currentQuestionIndex.value === questionsContent.value.length - 1;
});

const actionButtonText = computed(() => {
  return isLastQuestion.value ? 'Finish' : 'Next';
});

const checkQuizPublishStatusDebounced = debounce(() => {
  if (!store.isPreviewMode) {
    store.checkQuizPublishStatus();
  }
}, 400);

const goToPreviousQuestion = () => {
  handleVibration();
  checkQuizPublishStatusDebounced();
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--;
    updateRouteParameter();
  }
};

const goToNextQuestion = () => {
  if (!isLastQuestion.value) {
    currentQuestionIndex.value++;
    updateRouteParameter();
  }
};

const getSessionUserResponses = () => {
  return JSON.parse(sessionStorage.getItem(`user_responses_${route.params.quizId}`)) || {};
};

const handleVibration = () => {
  vibrate(40);
};

const updateUserResponse = (response) => {
  userResponses.value[currentQuestionIndex.value] = response;
  sessionStorage.setItem(
    `user_responses_${route.params.quizId}`,
    JSON.stringify({
      quiz_id: quizId.value,
      answers: userResponses.value,
    })
  );
};

const isAnswerSelected = computed(() => {
  userResponses.value[currentQuestionIndex.value];

  const response =
    getSessionUserResponses()?.answers?.[currentQuestionIndex.value]?.choice_id ??
    getSessionUserResponses()?.answers?.[currentQuestionIndex.value]?.open_ended_response;

  return Array.isArray(response) ? response.length > 0 : Boolean(response);
});

const getQuestionComponent = (type) => {
  if (!type) return null;
  const questionComponents = {
    'Single Select Choice': SingleSelectQuestionLayout,
    'True/False': SingleSelectQuestionLayout,
    'Would You Rather': SingleSelectQuestionLayout,
    'Image Choice': SingleSelectQuestionLayout,
    'Text Choice': SingleSelectQuestionLayout,
    'Open Ended': OpenEndedQuestionLayout,
    'Rating Scale': RatingScaleQuestionLayout,
    'Multiple Select Choice': MultipleSelectQuestionLayout,
  };

  return questionComponents[type] || null;
};

const navigateToResults = () => {
  router.push({ name: 'get-results-page', query: { preview: route.query.preview } });
};

const handleQuizAction = async () => {
  handleVibration();
  if (isLastQuestion.value) {
    sessionStorage.setItem(`quiz_status_${route.params.quizId}`, `completed`);
    navigateToResults();
  } else {
    checkQuizPublishStatusDebounced();
    goToNextQuestion();
  }
};

const currentQuestionId = computed(() => route.params.questionId);

const initializeQuestionBasedOnRouteParams = async () => {
  if (isNaN(currentQuestionId.value)) {
    currentQuestionIndex.value = questionsContent.value.length - 1;
    updateRouteParameter(true);
  } else {
    currentQuestionIndex.value = parseInt(currentQuestionId.value) - 1;
  }
};

const updateRouteParameter = (replace = false) => {
  const routeConfig = {
    name: 'questions-page',
    params: {
      quizId: route.params.quizId,
      questionId: currentQuestionIndex.value + 1,
    },
    query: route.query,
  };
  
  if (replace) {
    router.replace(routeConfig);
  } else {
    router.push(routeConfig);
  }
};

const redirectIfQuizIsUnpublished = () => {
  if (!isQuizPublished.value) {
    removeQuizInSessionStorage(
      `quiz_${route.params.quizId}`,
      `getResult_${route.params.quizId}`,
      `user_responses_${route.params.quizId}`,
      `quiz_status_${route.params.quizId}`
    );
    router.push('/unavailable-quiz');
  }
};

watch(
  () => isQuizPublished.value,
  () => {
    redirectIfQuizIsUnpublished();
  }
);

watch(
  () => route.params,
  async (newParams, oldParams) => {
    if (route.name === 'questions-page') {
      if (newParams.questionId) {
        const newIndex = parseInt(newParams.questionId) - 1;
        if (!isNaN(newIndex) && newIndex >= 0 && newIndex < questionsContent.value.length) {
          currentQuestionIndex.value = newIndex;
        }
      }

      if (newParams.quizId !== quizId.value) {
        if (!quizDetail.value.id) {
          await store.getQuizzesData();
        }
        await initializeQuestionBasedOnRouteParams();
      }
    }
  },
  { immediate: true }
);

onMounted(() => {
  if (route.query.preview === 'true') {
    store.setPreviewMode(true);
  }
  if (!store.isPreviewMode) {
    if (quizId.value) {
      store.checkQuizPublishStatus();
    }
    if (!isQuizPublished.value) {
      redirectIfQuizIsUnpublished();
    }
  }
  if (!isQuizNewlyFetched.value) {
    store.getQuizzesData();
  }
  initializeQuestionBasedOnRouteParams();
});
</script>
