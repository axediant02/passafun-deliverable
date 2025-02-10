import { ref, watch } from 'vue';
import axios from 'axios';
import { defineStore } from 'pinia';
import { useRoute, useRouter } from 'vue-router';

export const useQuizzesStore = defineStore('quizzesStore', () => {
  const quizId = ref(null);
  const quizTheme = ref({});
  const landingPage = ref({});
  const isQuizNewlyFetched = ref(false);
  const mechanicsPageContent = ref({});
  const questionsContent = ref([]);
  const participantSubmissionPage = ref({});
  const quizDetail = ref({});
  const isLoading = ref(true);
  const error = ref(null);
  const isQuizPublished = ref(true);
  const isPreviewMode = ref(false);

  const route = useRoute();
  const router = useRouter();
  const currentQuizUID = ref(route.params.quizId);

  const getQuizzesData = async () => {
    if (quizDetail.value.id && quizDetail.value.id === quizId.value) {
      return;
    }

    isLoading.value = true;
    error.value = null;

    if (!currentQuizUID.value) {
      error.value = 'Quiz not found.';
      isLoading.value = false;
      return;
    }

    try {
      const response = await axios.get(`/api/quizzes/uid/${currentQuizUID.value}`);
      quizId.value = response.data.id;
      quizDetail.value = response.data;
      quizTheme.value = response.data.theme;
      landingPage.value = response.data.landing_page;
      mechanicsPageContent.value = response.data.mechanic_page;
      questionsContent.value = response.data.questions;
      participantSubmissionPage.value = response.data.get_result_page[0];
      participantSubmissionPage.value.getJsonAnimationData = participantSubmissionPage.value
        .getJsonAnimationData
        ? JSON.parse(participantSubmissionPage.value.getJsonAnimationData)
        : null;
      isQuizNewlyFetched.value = true;
    } catch (err) {
      console.error(err);
      if (err.response?.status === 404) {
        router.push({ name: 'NotFound' });
        return;
      } else if (err.message === 'Network Error') {
        router.push({ name: 'NotFound' });
      } else {
        router.push({ name: 'NotFound' });
      }
    } finally {
      isLoading.value = false;
    }
  };

  const checkQuizPublishStatus = async () => {
    try {
      const response = await axios.get('/api/quizzes/check-quiz-status', {
        params: {
          quiz_id: quizId.value,
        },
      });
      isQuizPublished.value = response.data.isPublished === 'true';
    } catch (err) {
      console.error('Error fetching quiz status:', err);
    }
  };

  const setQuizPublished = (status) => {
    isQuizPublished.value = status;
  };

  const setQuizId = (id) => {
    if (typeof id === 'number') {
      quizId.value = id;
    } else {
      console.error('Invalid quizId type. Expected a number.');
    }
  };

  const setPreviewMode = (value) => {
    isPreviewMode.value = value;
  };

  watch(
    () => route.params.quizId,
    (newQuizId) => {
      currentQuizUID.value = newQuizId;
    }
  );

  return {
    isLoading,
    error,
    quizId,
    quizTheme,
    landingPage,
    mechanicsPageContent,
    questionsContent,
    participantSubmissionPage,
    quizDetail,
    getQuizzesData,
    currentQuizUID,
    isQuizPublished,
    isQuizNewlyFetched,
    checkQuizPublishStatus,
    setQuizPublished,
    setQuizId,
    isPreviewMode,
    setPreviewMode,
  };
});
