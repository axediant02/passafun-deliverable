import { ref } from 'vue';
import axios from 'axios';

export const useThumbnailFetcher = () => {
  const thumbnails = ref([]);
  const isLoading = ref(false);
  const error = ref(null);

  const fetchThumbnails = async (quizId) => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await axios.get(`/api/thumbnail-customization/${quizId}`);
      thumbnails.value = response.data || [];
    } catch (err) {
      console.error(err);

      if (err.response) {
        error.value = `Error: ${err.response.status} - ${
          err.response.data.message || 'Failed to fetch thumbnails.'
        }`;
      } else if (err.request) {
        error.value =
          'Network error: Unable to reach the server. Please check your internet connection or try again later.';
      } else {
        error.value = `An unexpected error occurred: ${err.message}. Please try again later.`;
      }
    } finally {
      isLoading.value = false;
    }
  };

  return {
    thumbnails,
    isLoading,
    error,
    fetchThumbnails,
  };
};
