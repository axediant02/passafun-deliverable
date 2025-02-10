import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

export const useResultData = (uniqueResultId) => {
  const resultData = ref(null);
  const isResultLoading = ref(false);
  const resultError = ref(null);
  const router = useRouter();

  const fetchResultData = async () => {
    isResultLoading.value = true;
    resultError.value = null;

    try {
      const response = await axios.get(`/api/result/${uniqueResultId}`);

      if (response.data) {
        resultData.value = response.data;

        if (resultData.value.image) {
          resultData.value.image = resultData.value.image;
        }
      } else {
        resultError.value = 'No data found in the response';
      }
    } catch (err) {
      if (err.response) {
        if (err.response.status === 404) {
          router.push({ name: 'NotFound' });
          return;
        }
        resultError.value = `Error: ${err.response.status} - ${
          err.response.data.message || 'Failed to load result data.'
        }`;
      } else if (err.request) {
        resultError.value =
          'Network error: Please check your internet connection or try again later.';
      } else {
        resultError.value = 'An unexpected error occurred. Please try again later.';
      }
    } finally {
      isResultLoading.value = false;
    }
  };

  return {
    resultData,
    isResultLoading,
    resultError,
    fetchResultData,
  };
};
