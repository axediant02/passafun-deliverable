<template>
  <div class="mx-auto py-5" :style="style">
    <div v-if="mainLoader">
      <v-col class="d-flex justify-center align-center">
        <LottieAnimation :animationData="customLoader" style="width: 100px; height: 100px" />
      </v-col>
    </div>

    <div v-else>
      <div v-if="props.location === 'CreateResults'">
        <v-col>
          <h2>Create Quiz</h2>
          <p>
            <strong>Possible Results: </strong>Setup all the possible results
            for <strong>{{ quiz?.name || "Loading..." }}</strong>.
          </p>
        </v-col>

        <v-col class="my-5">
          <div class="d-flex justify-space-between">
            <v-btn prepend-icon="mdi-arrow-left" :to="`/create-quiz/${props.quizId}/questions`" color="primary"
              variant="text">Back</v-btn>
            <div>
              <v-btn prepend-icon="mdi-content-save" class="me-3" variant="tonal" color="primary"
                @click="saveAsDraft">Save as Draft</v-btn>
              <v-btn prepend-icon="mdi-content-save" color="primary" @click="saveResultWithoutValidation">Save &
                Continue</v-btn>
            </div>
          </div>
        </v-col>
      </div>

      <v-col v-if="shouldShowResultGuide">
        <v-card color="primary" variant="tonal" class="mb-5">
          <v-card-text>
            <div class="d-flex align-center mb-2">
              <v-icon icon="mdi-information" class="me-2 mb-2"></v-icon>
              <strong>Results Guide</strong>
            </div>

            <ul class="ms-3 mb-0 px-3">
              <li>
                Result score must cover scores from 0 up to {{ perfectScore }}.
              </li>
              <li v-if="
                savedResults.length > 1 && showResultGuide === 'Unpublished'
              ">
                Ensure there are no gaps between score ranges
              </li>
            </ul>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col v-if="savedResults.length === 0">
        <v-card color="primary" variant="tonal" class="mb-5">
          <v-card-text>No results added yet. Please add a result.</v-card-text>
        </v-card>
      </v-col>

      <div class="v-else">
        <v-col v-for="(savedResult, index) in savedResults" :key="index">
          <v-card v-if="editingIndex === index" class="mb-5">
            <v-container fluid>
              <div class="d-flex justify-end">
                <v-btn icon="mdi-close" variant="text" color="grey" @click="resetForm" />
              </div>

              <div class="d-flex">
                <div class="text-field-group w-100 me-3">
                  <v-text-field v-model="result.header" label="Enter Result Name" variant="outlined"
                    @keydown.enter="saveOnEnter" :rules="getValidationRules('required')" required counter
                    maxlength="50" />
                  <v-text-field v-model="result.description" style="height: 150px" label="Enter Result Description"
                    variant="outlined" @keydown.enter="saveOnEnter" :rules="getValidationRules('required')" required
                    counter maxlength="250" />
                </div>
                <v-card height="170px" width="200px" variant="outlined" color="grey"
                  class="d-flex align-center justify-center position-relative">
                  <input type="file" ref="imageInput" @change="(e) => handleImageUpload(e, 'edit')" accept="image/*"
                    style="display: none" />
                  <v-icon v-if="!previewImage">mdi-image</v-icon>
                  <v-img v-else :src="savedResult.image_url" cover height="100%" width="100%"></v-img>
                  <div class="action-buttons position-absolute" style="top: 5px; right: 5px">
                    <v-btn class="me-2" icon="mdi-square-edit-outline" size="25px" color="grey"
                      @click.stop="triggerImageUpload('edit')" />
                    <v-btn v-if="previewImage" icon="mdi-delete-outline" size="25px" color="grey"
                      @click.stop="deleteImage" />
                  </div>
                </v-card>
              </div>

              <p style="color: grey" class="mb-3">
                Set the score range for this result
              </p>
              <div class="d-flex">
                <v-text-field v-model="result.min_points" label="Minimum Score" class="me-3" variant="outlined"
                  type="number" @keydown.enter="saveOnEnter" :rules="getValidationRules('required')"
                  :disabled="isDefaultResult(savedResult)" required />
                <v-text-field v-model="result.max_points" label="Maximum Score" variant="outlined" type="number"
                  @keydown.enter="saveOnEnter" :rules="getValidationRules('required')" required />
              </div>
            </v-container>
          </v-card>

          <v-card v-else class="mb-5" @click="!isResultsCardDisabled ? editResult(index) : null" :style="{
            cursor: isResultsCardDisabled ? 'not-allowed' : 'pointer',
            pointerEvents: isResultsCardDisabled ? 'none' : 'auto',
          }">
            <v-container fluid>
              <div class="d-flex align-center justify-space-between">
                <v-chip color="grey-darken-2" class="mb-3" style="border-radius: 10px">Result {{ index + 1 }}</v-chip>
                <template v-if="isDefaultResult(savedResult)">
                  <v-chip variant="tonal" style="border-radius: 10px" color="primary" class="mb-3">Default
                    Result</v-chip>
                </template>
                <template v-else>
                  <v-btn v-if="!isResultsCardDisabled" class="mb-3" icon="mdi-close" variant="text" color="grey"
                    @click.stop="removeSavedResult(index)" style="border-radius: 0" />
                </template>
              </div>
              <div class="d-flex">
                <div class="me-3 w-100">
                  <p class="text-h6">{{ savedResult.header }}</p>
                  <p class="text-body-1">{{ savedResult.description }}</p>
                  <v-chip class="mt-3" color="grey-darken-2" style="border-radius: 10px" variant="outlined">
                    Score Range: {{ savedResult.min_points }} -
                    {{ savedResult.max_points }}
                  </v-chip>
                </div>
                <v-card v-if="savedResult.image" height="170px" width="200px" variant="outlined" color="grey"
                  class="d-flex align-center justify-center">
                  <v-img :key="savedResult.image_url" :src="savedResult.image_url || savedResult.image" cover
                    height="100%" width="100%"></v-img>
                </v-card>
              </div>
            </v-container>
          </v-card>
        </v-col>

        <v-col v-if="editingIndex === null">
          <v-card>
            <v-container fluid v-if="!isResultsCardDisabled">
              <div class="d-flex justify-end">
                <v-btn v-if="result.min_points !== 0" icon="mdi-close" variant="text" color="grey" @click="resetForm" />
              </div>

              <div class="d-flex">
                <div class="text-field-group w-100 me-3">
                  <v-text-field v-model="result.header" label="Enter Result Name" variant="outlined"
                    :rules="getValidationRules('required')" @keydown.enter="submitOnEnterNewResult" required counter
                    maxlength="50" />
                  <v-text-field v-model="result.description" style="height: 150px" label="Enter Result Description"
                    variant="outlined" :rules="getValidationRules('required')" @keydown.enter="submitOnEnterNewResult"
                    required counter maxlength="250" />
                </div>
                <v-card height="170px" width="200px" variant="outlined" color="grey"
                  class="d-flex align-center justify-center position-relative">
                  <input type="file" ref="imageInput" @change="handleImageUpload" accept="image/*"
                    style="display: none" />
                  <v-icon v-if="!previewImage">mdi-image</v-icon>
                  <v-img v-else :src="previewImage" cover height="100%" width="100%"></v-img>
                  <div class="action-buttons position-absolute" style="top: 5px; right: 5px">
                    <v-btn class="me-2" icon="mdi-square-edit-outline" size="25px" color="grey"
                      @click.stop="triggerImageUpload" />
                    <v-btn v-if="previewImage" icon="mdi-delete-outline" size="25px" color="grey"
                      @click.stop="deleteImage" />
                  </div>
                </v-card>
              </div>

              <p style="color: grey" class="mb-3">
                Set the score range for this result
              </p>
              <div class="d-flex">
                <v-text-field v-model="result.min_points" label="Minimum Score" class="me-3" variant="outlined"
                  type="number" @keydown.enter="submitOnEnterNewResult" :rules="getValidationRules('required')"
                  required />
                <v-text-field v-model="result.max_points" label="Maximum Score" variant="outlined" type="number"
                  @keydown.enter="submitOnEnterNewResult" :rules="getValidationRules('required')" required />
              </div>
            </v-container>
          </v-card>
        </v-col>

        <v-col v-if="!isResultsCardDisabled">
          <v-btn :loading="loading" width="100%" height="50px" variant="tonal" color="primary" prepend-icon="mdi-plus"
            @click="addNewResult">
            Add Result
          </v-btn>
        </v-col>
      </div>

      <default-modal v-model="incompleteDetailsModal" title="Incomplete Quiz Details" title-class="font-weight-bold"
        confirm-text="Complete All Fields" max-width="500px">
        <div class="font-weight-medium mb-5">
          The following items need to be completed before publishing:
        </div>
        <v-col type="error" icon="false" variant="tonal" class="bg-grey-lighten-3 custom-radius"
          prepend-icon="mdi-alert-circle">
          <ul class="alert-title list-unstyle">
            <li v-for="(error, index) in incompleteDetails" :key="index">
              <v-icon icon="mdi-alert-circle-outline" color="orange" class="me-2 my-1"></v-icon>
              {{ error }}
            </li>
          </ul>
        </v-col>
      </default-modal>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, onBeforeUnmount } from "vue";
import axios from "axios";
import { getAuth } from "@/pages/auth/authService";
import { useRouter } from "vue-router";
import customLoader from "@/json/loader.json";
import { editQuizResult, submitQuizResult, publishQuiz as publishQuizUtil } from "@/utils/results/SaveResults";
import { validateBasicFields, validatePoints, validatePointRanges, validateOverlappingRanges } from "@/utils/results/ResultValidator";
import { debounce } from "lodash";
const props = defineProps({
  quizId: {
    type: Number,
    required: true,
  },
  location: {
    type: String,
    required: true,
  },
  style: {
    type: String,
    default: "max-width: 800px;",
  },
});
const router = useRouter();
const incompleteDetailsModal = ref(false);
const incompleteDetails = ref([]);
const quiz = ref(null);
const mainLoader = ref(false);
const loading = ref(false);
const savedResults = ref([]);
const editingIndex = ref(null);
const tempFormData = ref(null);
const unsavedChanges = ref(false);
const result = ref({
  header: "",
  description: "",
  image: "",
  min_points: null,
  max_points: null,
});
const errorMessage = ref({
  header: "",
  description: "",
  min_points: "",
  max_points: "",
});
const readyToDraft = ref(false);
const savedQuestions = ref([]);
const perfectScore = ref(0);
const previewImage = ref(null);
const imageInput = ref(null);
const imageFile = ref(null);
const imageRemoved = ref(false);
const tempImagePreview = ref(null);
const originalImageState = ref(null);
const inputRules = {
  required: [v => !!v || 'This field is required'],
  points: [
    v => !!v || 'Points are required',
    v => !isNaN(v) || 'Must be a number'
  ],
  image: [v => !!v || 'Image is required']
};
const getValidationRules = (fieldName) => {
  return !isFormEmpty.value ? inputRules[fieldName] : [];
};
const closeEditCard = (isSaving = false) => {
  if (!isSaving) {
    if (originalImageState.value && editingIndex.value !== null) {
      const savedResult = savedResults.value[editingIndex.value];
      savedResult.image_url = originalImageState.value.image_url;
      savedResult.image = originalImageState.value.image;
    }
  }
  result.value = {
    header: "",
    description: "",
    image: "",
    min_points: null,
    max_points: null,
  };
  previewImage.value = null;
  tempImagePreview.value = null;
  imageFile.value = null;
  editingIndex.value = null;
  tempFormData.value = null;
  imageRemoved.value = false;
  originalImageState.value = null;
  unsavedChanges.value = false;
  if (imageInput.value) {
    imageInput.value.value = "";
  }
};
const resetForm = () => {
  closeEditCard(false);
};
const triggerImageUpload = (type = null) => {
  if (type === "edit") {
    imageInput.value[0].click();
  } else {
    imageInput.value.click();
  }
};
const handleImageUpload = (event, type) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 2048 * 1024) {
      window.$snackbar("Image must be smaller than 2 MB.", "error");
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
      const imageUrl = e.target.result;
      if (type === "edit") {
        savedResults.value[editingIndex.value].image_url = imageUrl;
        previewImage.value = imageUrl;
      } else {
        previewImage.value = imageUrl;
      }
    };
    reader.readAsDataURL(file);
    imageFile.value = file;
    imageRemoved.value = false;
  }
};
const deleteImage = async () => {
  try {
    const resultId = savedResults.value[editingIndex.value].id;
    const { token } = getAuth();
    await axios.delete(`/api/results/${resultId}/image/delete`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });
    previewImage.value = null;
    imageFile.value = null;
    result.value.image = "";
    imageRemoved.value = true;
    window.$snackbar("Image deleted successfully!", "success");
  } catch (error) {
    console.error("Error deleting image:", error.response || error.message);
    window.$snackbar("Failed to delete image", "error");
  }
};
const validateResult = () => {
  return validateBasicFields(result.value, window.$snackbar, errorMessage.value) &&
    validatePoints(result.value, window.$snackbar, errorMessage.value) &&
    validatePointRanges(result.value, perfectScore.value, window.$snackbar, errorMessage.value) &&
    validateOverlappingRanges(result.value, savedResults.value, editingIndex.value, window.$snackbar, errorMessage.value);
};
const addNewResult = async () => {
  if (loading.value) return;
  loading.value = true;
  try {
    if (editingIndex.value !== null) {
      await saveEditedResult();
    } else {
      await submitForm();
    }
  } catch (error) {
    console.error("Error handling result:", error);
  } finally {
    loading.value = false;
  }
};
const formRef = ref(null);
const saveResultWithoutValidation = async () => {
  try {
    if (editingIndex.value !== null) {
      await saveEditedResult();
    } else if (!isFormEmpty.value) {
      const isValid = await formRef.value?.validate();
      if (!isValid) {
        inputRules.required.forEach(rule => rule(''));
        window.$snackbar('Please fill in all required fields', 'error');
        return;
      }
      await submitForm();
    } else {
      await router.push(`/create-quiz/${props.quizId}/thumbnail-customization`);
    }
  } catch (error) {
    console.error('Error saving result:', error);
    window.$snackbar('Failed to save result', 'error');
  }
};

const submitOnEnterNewResult = debounce(() => {
  submitForm();
}, 500);
const submitForm = async () => {
  if (!validateResult()) {
    loading.value = false;
    readyToDraft.value = false;
    return;
  }
  readyToDraft.value = true;
  loading.value = true;
  await submitQuizResult({
    quizId: props.quizId,
    result: result.value,
    imageFile: imageFile.value,
    imageRemoved: imageRemoved.value,
    previewImage: previewImage.value,
    onSuccess: (newResult) => {
      savedResults.value.push(newResult);
      resetForm();
    },
    onError: (error) => {
      console.error("Error submitting result:", error);
    },
    onComplete: () => {
      loading.value = false;
    }
  });
};
const removeSavedResult = async (index) => {
  try {
    const resultToRemove = savedResults.value[index];
    if (resultToRemove.id) {
      const { token } = getAuth();
      await axios.delete(`/api/results/${resultToRemove.id}`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    }
    savedResults.value = savedResults.value.filter((_, i) => i !== index);
  } catch (error) {
    console.error("Error removing result:", error);
  }
};
let isChangingResult = false;
const editResult = async (index) => {
  if (editingIndex.value !== null && unsavedChanges.value) {
    try {
      await saveEditedResult();
    } catch (error) {
      console.error("Error saving result:", error);
      return;
    }
  }
  const savedResult = savedResults.value[index];
  originalImageState.value = {
    image_url: savedResult.image_url,
    image: savedResult.image
  };
  result.value.header = savedResult.header;
  result.value.description = savedResult.description;
  previewImage.value = savedResult.image_url;
  tempImagePreview.value = null;
  imageFile.value = null;
  result.value.min_points = savedResult.min_points;
  result.value.max_points = savedResult.max_points;
  editingIndex.value = index;
  unsavedChanges.value = true;
  setupSaveClickListener();
};
const saveOnEnter = debounce(() => {
  saveEditedResult();
}, 500);
const saveEditedResult = async () => {
  if (!validateResult()) {
    return;
  }
  isChangingResult = true;
  await editQuizResult({
    editingIndex: editingIndex.value,
    result: result.value,
    imageFile: imageFile.value,
    imageRemoved: imageRemoved.value,
    previewImage: previewImage.value,
    savedResults: savedResults.value,
    onSuccess: (updatedResult, index) => {
      savedResults.value[index] = updatedResult;
      closeEditCard(true);
      editingIndex.value = null;
    },
    onError: (error) => {
      result.value.min_points =
        result.value.originalMinPoints || result.value.min_points;
    },
    onComplete: () => {
      isChangingResult = false;
    }
  });
};
const fetchExistingResults = async () => {
  mainLoader.value = true;
  try {
    const { token } = getAuth();
    const response = await axios.get(`/api/quizzes/${props.quizId}/results`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });
    savedResults.value = response.data.results || [];
  } catch (error) {
    console.error("Error fetching existing results:", error);
  } finally {
    mainLoader.value = false;
  }
};
const fetchQuiz = async () => {
  try {
    const { data } = await axios.get(`/api/quizzes/${props.quizId}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
        Accept: "application/json",
      },
    });
    quiz.value = data;
  } catch (error) {
    console.error("Error fetching quiz:", error);
  }
};
const saveOnClickOutside = (event) => {
  if (
    editingIndex.value !== null &&
    !event.target.closest(".v-card") &&
    !isChangingResult
  ) {
    saveEditedResult().catch((error) => {
      console.error("Error saving result on outside click:", error);
    });
  }
};
const cleanupEventListener = () => {
  document.removeEventListener('click', saveOnClickOutside);
};
const setupSaveClickListener = () => {
  cleanupEventListener();
  document.addEventListener("click", saveOnClickOutside);
};
const isResultsCardDisabled = computed(() => {
  return quiz.value && quiz.value.quiz_status_id === 2;
});
const isDefaultResult = (result) => {
  return result.min_points === 0;
};
const isFormEmpty = computed(() => {
  return (
    !result.value.header &&
    !result.value.description &&
    !result.value.min_points &&
    !result.value.max_points &&
    !result.value.image
  );
});

const saveAsDraft = debounce(async () => {
  if (isFormEmpty.value) {
    router.push(`/quiz/details/${props.quizId}`);
    return;
  }
  try {
    await submitForm("draft");
    if (readyToDraft.value) {
      setTimeout(() => {
        window.$snackbar("Draft saved successfully!", "success");
        router.push(`/quiz/details/${props.quizId}`);
      }, 1500);
      readyToDraft.value = false;
    }
  } catch (error) {
    window.$snackbar("Failed to save draft", "error");
  }
}, 500);

const fetchQuestions = async () => {
  try {
    const { token } = getAuth();
    const response = await axios.get(`/api/quizzes/${props.quizId}/questions`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });
    savedQuestions.value = response.data.questions || [];
    calculatePerfectScore();
  } catch (error) {
    console.error("Error fetching questions:", error);
  }
};
const calculatePerfectScore = () => {
  let totalPoints = 0;
  savedQuestions.value.forEach((question) => {
    if (question.question_type_id === 4) {
      return;
    }
    if (question.question_type_id === 1) {
      totalPoints += question.choices.reduce(
        (sum, choice) => sum + (choice.points || 0),
        0
      );
    } else {
      const maxPoints = Math.max(
        ...question.choices.map((choice) => choice.points || 0),
        0
      );
      totalPoints += maxPoints;
    }
  });
  perfectScore.value = totalPoints;
};
const showResultGuide = computed(() => {
  return quiz.value?.quiz_status?.status || quiz.value?.status;
});
const shouldShowResultGuide = computed(() => {
  return (
    savedQuestions.value.length > 0 &&
    showResultGuide.value === "Unpublished"
  );
});
onBeforeUnmount(() => {
  cleanupEventListener();
});
onMounted(() => {
  fetchQuiz();
  fetchExistingResults();
  fetchQuestions();
});
</script>
<style scoped>
.action-buttons .v-btn {
  border-radius: 5px;
  font-size: 12px;
}

.text-field-group {
  position: relative;
}

.position-relative {
  position: relative;
}

.position-absolute {
  position: absolute;
}

.alert-title {
  list-style: none;
  padding-left: 0;
}
</style>
