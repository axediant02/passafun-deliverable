// src/composables/usePopularQuizzes.js

import { ref } from 'vue';
import axios from 'axios';

export const usePopularQuizzes = () => {
  const popularQuizzes = ref([]);
  const isPopularQuizzesLoading = ref(false);
  const popularQuizzesError = ref(null);

  const fetchPopularQuizzes = async () => {
    isPopularQuizzesLoading.value = true;
    popularQuizzesError.value = null;

    try {
      const response = await axios.get(`/api/library/popular-quizzes`);
      popularQuizzes.value = response.data.data.top_quizzes;
    } catch (err) {
      console.error(err);

      if (err.response) {
        popularQuizzesError.value = `Error: ${err.response.status} - ${
          err.response.data.message || 'Failed to fetch popular quizzes.'
        }`;
      } else if (err.request) {
        popularQuizzesError.value =
          'Network error: Unable to reach the server. Please check your internet connection or try again later.';
      } else {
        popularQuizzesError.value = `An unexpected error occurred: ${err.message}. Please try again later.`;
      }
    } finally {
      isPopularQuizzesLoading.value = false;
    }
  };

  return {
    popularQuizzes,
    isPopularQuizzesLoading,
    popularQuizzesError,
    fetchPopularQuizzes,
  };
};
