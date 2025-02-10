import { ref } from 'vue';
import axios from 'axios';

export const useSearchedPublishedQuizzes = () => {
  const searchedPublishedQuizzes = ref([]);
  const isSearchedPublishedQuizzesLoading = ref(false);
  const searchedPublishedQuizzesError = ref(null);

  const searchPublishedQuizzes = async (inputSearchTerm = '') => {
    isSearchedPublishedQuizzesLoading.value = true;
    try {
      const response = await axios.get('/api/quizzes/search-quiz', {
        params: { 
          quiz: inputSearchTerm, 
        },
      });
      const data = response.data;
      searchedPublishedQuizzes.value = data.data;
    } catch (err) {
      console.error('Error fetching quizzes:', err);
      searchedPublishedQuizzesError.value = 'Failed to load quizzes.';
    } finally {
      isSearchedPublishedQuizzesLoading.value = false;
    }
  };

  return {
    searchedPublishedQuizzes,
    isSearchedPublishedQuizzesLoading,
    searchedPublishedQuizzesError,
    searchPublishedQuizzes,
  };
};
