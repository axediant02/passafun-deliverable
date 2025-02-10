import { ref } from 'vue';
import axios from 'axios';

export const useParticipantData = (uniqueResultId) => {
  const participantData = ref(null);
  const isLoading = ref(false);
  const error = ref(null);

  const fetchParticipantData = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await axios.get(`/api/participant/${uniqueResultId}`);

      if (response.data && response.data.full_name) {
        participantData.value = response.data.full_name;
      } else {
        error.value = 'No participant found or no full name available';
      }
    } catch (err) {
      if (err.response) {
        error.value = `Error: ${err.response.status} - ${
          err.response.data.message || 'Failed to load participant data.'
        }`;
      } else if (err.request) {
        error.value = 'Network error: Please check your internet connection or try again later.';
      } else {
        error.value = 'An unexpected error occurred. Please try again later.';
      }
    } finally {
      isLoading.value = false;
    }
  };

  return {
    participantData,
    isLoading,
    error,
    fetchParticipantData,
  };
};
