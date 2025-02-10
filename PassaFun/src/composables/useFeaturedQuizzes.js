import { ref } from 'vue';
import axios from 'axios';

export const useFeaturedQuizzes = () => {
  const featuredQuizzes = ref(null);
  const isFeaturedQuizzesLoading = ref(false);
  const featuredQuizzesError = ref(null);

  const fetchFeaturedQuizzes = async () => {
    isFeaturedQuizzesLoading.value = true;
    featuredQuizzesError.value = null;

    try {
      const response = await axios.get('/api/library/featured-quizzes');
      featuredQuizzes.value = response.data;
    } catch (err) {
      console.error(err);

      if (err.response) {
        featuredQuizzesError.value = `Error: ${err.response.status} - ${
          err.response.data.message || 'Failed to fetch featured quizzes.'
        }`;
      } else if (err.request) {
        featuredQuizzesError.value =
          'Network error: Unable to reach the server. Please check your internet connection or try again later.';
      } else {
        featuredQuizzesError.value = `An unexpected error occurred: ${err.message}. Please try again later.`;
      }
    } finally {
      isFeaturedQuizzesLoading.value = false;
    }
  };

  return {
    featuredQuizzes,
    isFeaturedQuizzesLoading,
    featuredQuizzesError,
    fetchFeaturedQuizzes,
  };
};
