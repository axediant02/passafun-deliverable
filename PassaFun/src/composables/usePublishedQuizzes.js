import { ref } from 'vue';
import axios from 'axios';

export const usePublishedQuizzes = () => {
  const publishedQuizzes = ref([]);
  const isPublishedQuizzesLoading = ref(false);
  const publishedQuizzesError = ref(null);

  const pageNumber = ref(1);
  const numberOfQuizToFetch = ref(10);

  const hasMore = ref(true);
  const isFetching = ref(false);

  const currentSortType = ref('created_at');
  const currentSortOrder = ref('desc');
  const currentSortName = ref('Newest');

  const fetchPublishedQuizzes = async (resetPublishedQuizzes, typeOfSort, orderOfSort) => {
    if (isFetching.value) return;
    if (resetPublishedQuizzes) {
      hasMore.value = true;
      pageNumber.value = 1;
      publishedQuizzes.value = [];
    }
    if (!hasMore.value) return;

    isFetching.value = true;
    isPublishedQuizzesLoading.value = true;
    publishedQuizzesError.value = null;

    try {
      const response = await axios.get('/api/library/published-quizzes', {
        params: {
          page: pageNumber.value,
          numberOfQuizzes: numberOfQuizToFetch.value,
          sortBy: typeOfSort || currentSortType.value,
          sortOrder: orderOfSort || currentSortOrder.value,
        },
      });
      const data = response.data;

      if (data.data.length > 0) {
        publishedQuizzes.value.push(...data.data);
        pageNumber.value++;
      } else {
        hasMore.value = false;
      }
    } catch (err) {
      console.error(err);

      if (err.response) {
        publishedQuizzesError.value = `Error: ${err.response.status} - ${
          err.response.data.message || 'Failed to fetch published quizzes.'
        }`;
      } else if (err.request) {
        publishedQuizzesError.value =
          'Network error: Unable to reach the server. Please check your internet connection or try again later.';
      } else {
        publishedQuizzesError.value = `An unexpected error occurred: ${err.message}. Please try again later.`;
      }
    } finally {
      isPublishedQuizzesLoading.value = false;
      isFetching.value = false;
    }
  };

  return {
    publishedQuizzes,
    isPublishedQuizzesLoading,
    publishedQuizzesError,
    fetchPublishedQuizzes,
    currentSortType,
    currentSortOrder,
    currentSortName,
  };
};
