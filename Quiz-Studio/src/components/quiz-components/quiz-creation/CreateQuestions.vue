<template>
  <div class="mx-auto py-5" :style="style">
    <div v-if="mainLoader">
      <v-col class="d-flex justify-center align-center">
        <LottieAnimation :animationData="customLoader" style="width: 100px; height: 100px" />
      </v-col>
    </div>

    <div v-else>
      <div v-if="props.location === 'CreateQuiz'">
        <v-col>
          <h2>Create Quiz</h2>
          <p>
            <strong>Questions: </strong>Setup the questions for
            <strong>{{ quiz?.name || "Loading..." }}</strong>.
          </p>
        </v-col>

        <v-col class="my-5">
          <div class="d-flex justify-space-between">
            <v-btn prepend-icon="mdi-arrow-left" :to="`/quiz/details/${props.quizId}`" color="primary"
              variant="text">Back</v-btn>
            <v-btn prepend-icon="mdi-content-save" color="primary" @click="saveQuestionWithoutValidation">Save &
              Continue</v-btn>
          </div>
        </v-col>
      </div>

      <v-col v-if="adminRole === 2">
        <v-card class="mb-5" v-for="(savedQuestion, index) in savedQuestions" :key="index">
          <v-container fluid>
            <div class="d-flex justify-space-between">
              <v-chip color="primary" class="mb-3" style="border-radius: 5px">Question {{ index + 1 }} : {{
                getQuestionTypeName(savedQuestion.question_type_id) }}</v-chip>
            </div>

            <div class="d-flex flex-column" style="max-height: 100px !important">
              <div class="me-3">
                <p class="text-h6">{{ savedQuestion.question_text }}</p>
              </div>

              <v-card v-if="savedQuestion.question_image" height="93px" width="150px" variant="outlined" color="grey"
                class="d-flex align-center justify-center">
                <v-img :src="savedQuestion.questionImageUrl" cover height="100%" width="100%"></v-img>
              </v-card>
            </div>

            <div v-if="savedQuestion.question_type_id !== 4">
              <p class="mt-5 mb-3">Choices</p>
            </div>

            <div v-for="(choice, choiceIndex) in savedQuestion.choices" :key="choiceIndex" class="mb-2 mt-3">
              <v-chip :color="choice.is_correct ? 'success' : ''" variant="outlined" style="border-radius: 5px">
                {{ choice.choice_text }} ({{ choice.points }} pts)
              </v-chip>
            </div>
          </v-container>
        </v-card>
      </v-col>

      <div v-else>
        <v-col class="" v-if="savedQuestions.length === 0">
          <v-card color="primary" variant="tonal" class="mb-5">
            <v-card-text>No questions added yet. Please add a question.</v-card-text>
          </v-card>
        </v-col>

        <v-col v-for="(savedQuestion, index) in savedQuestions" :key="index">
          <v-card v-if="editingIndex === index" class="mb-5">
            <v-container fluid>
              <div class="d-flex justify-space-between">
                <v-select max-width="250px" label="Question Type" variant="outlined" v-model="questionType"
                  :items="questionTypes" item-title="name" item-value="id" />
                <v-btn class="delete-question-btn" icon="mdi-close" variant="text" color="grey" @click="resetForm" />
              </div>

              <div class="d-flex" style="max-height: 100px !important">
                <v-text-field style="height: 150px" label="Enter Question" class="me-3"
                  placeholder="e.g. 'What is the capital of France?'" variant="outlined" v-model="questionText"
                  :counter="questionTextLimit" :maxlength="questionTextLimit" @keydown.enter="saveOnEnter" />
                <v-card height="93px" width="150px" variant="outlined" color="grey"
                  class="d-flex align-center justify-center position-relative">
                  <input type="file" ref="editImageInput" @change="(e) => handleImageUpload(e, 'edit')" accept="image/*"
                    style="display: none" />
                  <v-icon v-if="!previewImage">mdi-image</v-icon>
                  <v-img v-else :src="savedQuestion.questionImageUrl" cover height="100%" width="100%"></v-img>
                  <div class="action-buttons position-absolute" style="top: 5px; right: 5px">
                    <v-btn class="me-2" icon="mdi-square-edit-outline" size="25px" color="grey"
                      @click.stop="triggerEditImageUpload" />
                    <v-btn v-if="previewImage" icon="mdi-delete-outline" size="25px" color="grey"
                      @click.stop="deleteImage" />
                  </div>
                </v-card>
              </div>

              <template v-if="showChoices">
                <div class="d-flex align-center justify-space-between mt-10 mb-5">
                  <p>Choices</p>
                  <v-btn v-if="
                    currentQuestionType?.id !== 3 &&
                    currentQuestionType?.id !== 5 &&
                    currentQuestionType?.id !== 1
                  " prepend-icon="mdi-plus" color="primary" variant="outlined" @click="addChoice" :disabled="choices.length >= currentQuestionType?.maxChoices
                    ">
                    Add Choice
                  </v-btn>
                </div>

                <div v-for="(choice, choiceIndex) in choices" :key="choiceIndex" class="d-flex mb-3">
                  <div class="text-field-group w-100 me-2">
                    <v-text-field v-model="choice.choice_text" label="Choice" placeholder="e.g. 'Paris'"
                      variant="outlined" :counter="choiceTextLimit" :maxlength="choiceTextLimit"
                      @keydown.enter="saveOnEnter" />
                    <!-- <v-tooltip text="Mark as correct answer" location="top">
                      <template v-slot:activator="{ props }">
                        <v-checkbox v-model="choice.is_correct" class="correct-answer-checkbox" v-bind="props" />
                      </template>
</v-tooltip> -->
                  </div>

                  <v-text-field v-model="choice.points" type="number" style="min-width: 150px; max-width: 150px"
                    label="Choice points" suffix="pts" placeholder="e.g. '1'" variant="outlined"
                    :rules="[(v) => maxPointsRule({ points: v })]" :min="0" :max="5" persistent-placeholder
                    @keydown.enter="saveOnEnter" />
                  <v-btn icon="mdi-close" variant="text" color="grey" v-if="
                    currentQuestionType?.id !== 3 &&
                    currentQuestionType?.id !== 5 &&
                    currentQuestionType?.id !== 1
                  " @click="
                    removeChoice(savedQuestion.id, choice.id, choiceIndex)
                    " :disabled="choices.length === 3" />
                </div>
              </template>
            </v-container>
          </v-card>

          <v-card v-else class="mb-5" @click="!isSavedQuestionDisabled ? editQuestion(index) : null" :style="{
            cursor: isSavedQuestionDisabled ? 'not-allowed' : 'pointer',
            pointerEvents: isSavedQuestionDisabled ? 'none' : 'auto',
          }">
            <v-container fluid>
              <div class="d-flex justify-space-between">
                <v-chip color="primary" class="mb-3" style="border-radius: 5px">Question {{ index + 1 }} : {{
                  getQuestionTypeName(savedQuestion.question_type_id) }}</v-chip>

                <v-btn icon="mdi-close" v-if="!isSavedQuestionDisabled" variant="text" color="grey"
                  @click.stop="removeSavedQuestion(index)" style="border-radius: 0" />
              </div>

              <div class="d-flex flex-column" style="max-height: 100px !important">
                <div class="me-3">
                  <p class="text-h6">{{ savedQuestion.question_text }}</p>
                </div>

                <v-card v-if="savedQuestion.question_image" height="93px" width="93px" variant="outlined" color="grey"
                  class="d-flex align-center justify-center ">
                  <v-img :src="savedQuestion.questionImageUrl" cover height="100%" width="100%"></v-img>
                </v-card>
              </div>

              <div v-if="savedQuestion.question_type_id !== 4">
                <p class="mt-5 mb-3">Choices</p>
              </div>

              <div v-for="(choice, choiceIndex) in savedQuestion.choices" :key="choiceIndex" class="mb-2 mt-3">
                <v-chip :color="choice.is_correct ? 'success' : ''" variant="outlined" style="border-radius: 5px">
                  {{ choice.choice_text }} ({{ choice.points }} pts)
                </v-chip>
              </div>
            </v-container>
          </v-card>
        </v-col>

        <v-col v-if="editingIndex === null">
          <v-card class="pt-5" v-if="!isSavedQuestionDisabled">
            <v-container fluid>
              <div class="d-flex justify-space-between">
                <v-select max-width="250px" label="Question Type" variant="outlined" v-model="questionType"
                  :items="questionTypes" item-title="name" item-value="id" />
                <v-btn class="delete-question-btn" icon="mdi-close" variant="text" color="grey" @click="resetForm" />
              </div>

              <div class="d-flex" style="max-height: 100px !important">
                <v-text-field style="height: 150px" label="Enter Question" class="me-3"
                  placeholder="e.g. 'What is the capital of France?'" variant="outlined" v-model="questionText"
                  :counter="questionTextLimit" :maxlength="questionTextLimit" @keydown.enter="saveOnEnter" />
                <v-card height="93px" width="150px" variant="outlined" color="grey"
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

              <template v-if="showChoices">
                <div class="d-flex align-center justify-space-between mt-10 mb-5">
                  <p>Choices</p>
                  <v-btn v-if="
                    currentQuestionType?.id !== 3 &&
                    currentQuestionType?.id !== 5 &&
                    currentQuestionType?.id !== 1
                  " prepend-icon="mdi-plus" color="primary" variant="outlined" @click="addChoice" :disabled="choices.length >= currentQuestionType?.maxChoices
                    ">
                    Add Choice
                  </v-btn>
                </div>

                <div v-for="(choice, index) in choices" :key="index" class="d-flex mb-3">
                  <div class="text-field-group w-100 me-2">
                    <v-text-field v-model="choice.choice_text" label="Choice" placeholder="e.g. 'Paris'"
                      variant="outlined" :counter="choiceTextLimit" :maxlength="choiceTextLimit"
                      @keydown.enter="saveOnEnter" />
                    <!-- <v-tooltip text="Mark as correct answer" location="top">
                      <template v-slot:activator="{ props }">
                        <v-checkbox v-model="choice.is_correct" class="correct-answer-checkbox" v-bind="props" />
                      </template>
      </v-tooltip> -->
                  </div>
                  <v-text-field v-model="choice.points" type="number" style="min-width: 150px; max-width: 150px"
                    label="Choice points" suffix="pts" placeholder="e.g. '1'" variant="outlined"
                    :rules="[(v) => maxPointsRule({ points: v })]" :min="0" :max="5" persistent-placeholder
                    @keydown.enter="saveOnEnter" />
                  <v-btn icon="mdi-close" variant="text" color="grey" @click="removeChoice(null, null, index)" v-if="
                    currentQuestionType?.id !== 3 &&
                    currentQuestionType?.id !== 5 &&
                    currentQuestionType?.id !== 1
                  " :disabled="choices.length === 3" />
                </div>
              </template>
            </v-container>
          </v-card>
        </v-col>

        <v-col v-if="!isSavedQuestionDisabled">
          <v-btn :loading="loading" width="100%" height="50px" variant="tonal" color="primary" prepend-icon="mdi-plus"
            @click="addNewQuestion">Add Question</v-btn>
        </v-col>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, onBeforeUnmount } from "vue";
import axios from "axios";
import { getAuth } from "@/pages/auth/authService";
import { useRouter } from "vue-router";
import customLoader from "@/json/loader.json";
import { debounce } from "lodash";

const mainLoader = ref(false);
const router = useRouter();
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
  adminRole: {
    type: Number,
    required: false,
  },
});

const quiz = ref({});
const questionType = ref(1);
const questionTypes = ref([
  { id: 1, name: "Multiple Select Choice", maxChoices: 4 },
  { id: 2, name: "Single Select Choice", maxChoices: 4 },
  { id: 3, name: "True/False", maxChoices: 2 },
  { id: 4, name: "Open-Ended", maxChoices: 0 },
  { id: 5, name: "Rating Scale", maxChoices: 5 },
]);
const questionText = ref("");
const previewImage = ref(null);
const choices = ref([
  { choice_text: "", points: 0, is_correct: false },
  { choice_text: "", points: 0, is_correct: false },
  { choice_text: "", points: 0, is_correct: false },
  { choice_text: "", points: 0, is_correct: false },
]);
const loading = ref(false);
const successAnimation = ref(false);
const savedQuestions = ref([]);
const editingIndex = ref(null);
const tempFormData = ref(null);
const imageInput = ref(null);
const editImageInput = ref(null);
const imageFile = ref(null);
const imageRemoved = ref(false);
const unsavedChanges = ref(false);
const tempImagePreview = ref(null);
const originalImageState = ref(null);

const getQuestionTypeName = computed(() => {
  return (id) => {
    const type = questionTypes.value.find((type) => type.id === id);
    return type ? type.name : "Unknown";
  };
});

const saveOnEnter = debounce(async () => {
  if (editingIndex.value !== null) {
    await saveEditedQuestion();
  } else {
    await addNewQuestion();
  }
}, 500);

const maxPointsRule = (choice) => {
  const points = Number(choice.points);
  if (points < 0) {
    choice.points = 0;
    return "Points cannot be negative";
  } else if (points > 5) {
    choice.points = 5;
    return "Points cannot exceed 5";
  }
  return true;
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

const closeEditCard = (isSaving = false) => {
  if (!isSaving) {
    if (originalImageState.value && editingIndex.value !== null) {
      const savedQuestion = savedQuestions.value[editingIndex.value];
      savedQuestion.questionImageUrl =
        originalImageState.value.questionImageUrl;
      savedQuestion.question_image = originalImageState.value.question_image;
    }
  }

  questionType.value = 1;
  questionText.value = "";
  previewImage.value = null;
  if (editingIndex.value === null) {
    choices.value = [
      { choice_text: "", points: 0, is_correct: false },
      { choice_text: "", points: 0, is_correct: false },
      { choice_text: "", points: 0, is_correct: false },
      { choice_text: "", points: 0, is_correct: false },
    ];
  }

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

const addNewQuestion = async () => {
  if (loading.value) return;
  loading.value = true;
  try {
    if (editingIndex.value === null) {
      await saveQuestion();
    } else {
      await saveEditedQuestion();
    }
  } catch (error) {
    console.error("Error adding new question:", error);
  } finally {
    loading.value = false;
  }
};

const choiceTextLimit = computed(() => {
  switch (questionType.value) {
    case 1:
      return 40;
    case 5:
      return 20;
    default:
      return 50;
  }
});

const questionTextLimit = computed(() => {
  return previewImage.value ? 100 : 160;
});

const validateQuestionText = () => {
  if (previewImage.value && questionText.value.length > 100) {
    window.$snackbar(
      `Question text must not exceed 100 character when an image is present`,
      `error`
    );

    questionText.value = questionText.value.slice(0, 100);
    return false;
  }
  return true;
};

watch([questionText, previewImage], ([newText, newImage]) => {
  if (newImage && newText?.length > 100) {
    validateQuestionText();
  }
});

const validateQuestion = () => {
  const isQuestionTextEmpty = !questionText.value?.trim();
  const isQuestionImageEmpty = !imageFile.value && !previewImage.value;
  const nonEmptyChoices = choices.value.filter((choice) =>
    choice.choice_text.trim()
  );

  if (isQuestionTextEmpty && isQuestionImageEmpty) {
    window.$snackbar("Either question text or image must be present", "error");
    return false;
  }

  if (questionType.value === 5) {
    if (
      !choices.value[0].choice_text.trim() ||
      !choices.value[4].choice_text.trim()
    ) {
      window.$snackbar(
        "First and last choices are required for rating scale",
        "error"
      );
      loading.value = false;
      return;
    }
  } else if (questionType.value === 1) {
    if (!choices.value.every((choice) => choice.choice_text.trim())) {
      window.$snackbar(
        "All choices must be filled for multiple select",
        "error"
      );
      return false;
    }
  } else if (questionType.value === 2) {
    if (nonEmptyChoices.length < 3) {
      window.$snackbar("Single select requires at least 3 choices", "error");
      return false;
    }
  } else if (questionType.value === 3) {
    if (!choices.value.every((choice) => choice.choice_text.trim())) {
      window.$snackbar("Both True and False options must be present", "error");
      return false;
    }
  } else if (questionType.value !== 4 && nonEmptyChoices.length === 0) {
    window.$snackbar("At least one choice must be present", "error");
    return false;
  }

  return true;
};

const saveQuestion = async () => {
  if (!validateQuestion()) {
    loading.value = false;
    return;
  }

  try {
    const formData = createFormData();
    const response = await submitFormData(formData);
    savedQuestions.value.push({
      id: response.data.id,
      question_type_id: questionType.value,
      question_text: questionText.value,
      question_image: previewImage.value,
      questionImageUrl: response.data.questionImageUrl,
      choices: response.data.choices.map((choice) => ({
        id: choice.id,
        choice_text: choice.choice_text,
        points: choice.points,
        is_correct: choice.is_correct,
        is_new: false,
      })),
    });

    resetForm();

    successAnimation.value = true;
    window.$snackbar("Question added successfully", "success");
    setTimeout(() => {
      successAnimation.value = false;
    }, 2000);
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const errorMessage =
        error.response.data.message ||
        error.response.data.error ||
        "An error occurred";
      window.$snackbar(errorMessage, "error");
    } else {
      console.error("An unexpected error occurred:", error);
    }
  } finally {
    loading.value = false;
  }
};

const createFormData = () => {
  const formData = new FormData();
  formData.append("question_type_id", questionType.value);
  formData.append("question_text", questionText.value);

  if (imageFile.value) {
    formData.append("question_image", imageFile.value);
  }
  if (imageRemoved.value) {
    formData.append("question_image_removed", "1");
  }

  if (questionType.value === 5) {
    choices.value.forEach((choice, index) => {
      formData.append(
        `choices[${index}][choice_text]`,
        choice.choice_text || ""
      );
      formData.append(`choices[${index}][points]`, choice.points || 0);
      formData.append(
        `choices[${index}][is_correct]`,
        choice.is_correct ? 1 : 0
      );
    });
  } else {
    const validChoices = choices.value.filter((choice) =>
      choice.choice_text.trim()
    );
    validChoices.forEach((choice, index) => {
      formData.append(`choices[${index}][choice_text]`, choice.choice_text);
      formData.append(`choices[${index}][points]`, choice.points);
      formData.append(
        `choices[${index}][is_correct]`,
        choice.is_correct ? 1 : 0
      );

      if (choice.imageFile) {
        formData.append(`choices[${index}][choice_image]`, choice.imageFile);
      }
      if (choice.imageRemoved) {
        formData.append(`choices[${index}][choice_image_removed]`, "1");
      }
    });
  }

  return formData;
};

const submitFormData = async (formData) => {
  const { token } = getAuth();
  return await axios.post(`/api/quizzes/${props.quizId}/questions`, formData, {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "multipart/form-data",
      Accept: "application/json",
    },
  });
};

const addChoice = () => {
  const maxChoices = currentQuestionType.value?.maxChoices || 0;
  if (choices.value.length < maxChoices) {
    choices.value.push({
      choice_text: "",
      points: 0,
      is_correct: false,
      is_new: true,
    });
  }
};

const removeChoice = async (questionId, choiceId, choiceIndex) => {
  try {
    if (!questionId) {
      choices.value.splice(choiceIndex, 1);
      return;
    }
    const choice = choices.value[choiceIndex];

    if (choice.is_new) {
      choices.value.splice(choiceIndex, 1);
      if (editingIndex.value !== null) {
        const questionIndex = savedQuestions.value.findIndex(
          (q) => q.id === questionId
        );
        if (questionIndex !== -1) {
          savedQuestions.value[questionIndex].choices.splice(choiceIndex, 1);
        }
      }
    } else {
      const { token } = getAuth();
      await axios.delete(`/api/quiz-questions/${questionId}/${choiceId}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      choices.value.splice(choiceIndex, 1);
      if (editingIndex.value !== null) {
        const questionIndex = savedQuestions.value.findIndex(
          (q) => q.id === questionId
        );
        if (questionIndex !== -1) {
          savedQuestions.value[questionIndex].choices = [...choices.value];
        }
      }
    }
  } catch (error) {
    console.error("Error removing choice:", error);
  }
};

const removeSavedQuestion = async (index) => {
  try {
    const questionToRemove = savedQuestions.value[index];
    if (questionToRemove.id) {
      const { token } = getAuth();
      await axios.delete(`/api/quiz-questions/${questionToRemove.id}`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    }
    window.$snackbar("Question removed successfully", "success");
    savedQuestions.value.splice(index, 1);
  } catch (error) {
    console.error("Error removing question:", error);
  }
};

const fetchExistingQuestions = async () => {
  mainLoader.value = true;
  try {
    const { token } = getAuth();
    const response = await axios.get(`/api/quizzes/${props.quizId}/questions`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });
    const questions = response.data.questions || [];
    savedQuestions.value = questions.map((question) => ({
      ...question,
      choices: question.choices.map((choice) => ({
        ...choice,
        is_new: false,
      })),
    }));
  } catch (error) {
    console.error("Error fetching existing questions:", error);
  } finally {
    mainLoader.value = false;
  }
};

const editQuestion = async (index) => {
  if (editingIndex.value !== null && unsavedChanges.value) {
    try {
      await saveEditedQuestion();
    } catch (error) {
      console.error("Error saving question:", error);
      return;
    }
  }

  const savedQuestion = savedQuestions.value[index];
  originalImageState.value = {
    questionImageUrl: savedQuestion.questionImageUrl,
    question_image: savedQuestion.question_image,
  };
  questionType.value = Number(savedQuestion.question_type_id);

  questionText.value = savedQuestion.question_text;
  previewImage.value = savedQuestion.questionImageUrl;
  imageFile.value = null;

  choices.value = savedQuestion.choices.map((choice) => ({
    id: choice.id,
    choice_text: choice.choice_text || "",
    points: Number(choice.points) || 0,
    is_correct: Boolean(choice.is_correct),
    is_new: false,
    imageFile: null,
    imageRemoved: false,
  }));

  editingIndex.value = index;
  unsavedChanges.value = true;

  setupSavedQuestionsClickListener();
};

const saveEditedQuestion = async () => {
  if (editingIndex.value === null || loading.value) return;
  loading.value = true;

  if (!validateQuestion()) {
    loading.value = false;
    return;
  }

  try {
    const editedQuestionIndex = editingIndex.value;
    const questionId = savedQuestions.value[editedQuestionIndex].id;
    const isOpenEnded = questionType.value === 4;

    const formData = new FormData();
    formData.append("question_type_id", questionType.value);
    formData.append("question_text", questionText.value === null ? "" : questionText.value);
    formData.append("is_open_ended", isOpenEnded ? "1" : "0");

    if (imageFile.value instanceof File) {
      formData.append("question_image", imageFile.value);
    }

    if (!isOpenEnded) {
      const hasValidPoints = choices.value.every((choice) => {
        const points = Number(choice.points);
        return points <= 5;
      });

      const hasAtLeastOneValidPoint = choices.value.some((choice) => {
        const points = Number(choice.points);
        return points > 0;
      });

      if (!hasValidPoints) {
        window.$snackbar("Points must be between 1 and 5", "error");
        loading.value = false;
        return;
      } else if (!hasAtLeastOneValidPoint) {
        window.$snackbar("At least one choice must be greater than 0", "error");
        loading.value = false;
        return;
      }

      choices.value.forEach((choice, index) => {
        formData.append(`choices[${index}][choice_text]`, choice.choice_text);
        formData.append(`choices[${index}][points]`, choice.points);
        formData.append(
          `choices[${index}][is_correct]`,
          choice.is_correct ? 1 : 0
        );
        if (choice.id) {
          formData.append(`choices[${index}][id]`, choice.id);
        }
      });
    }

    const { token } = getAuth();
    const response = await axios.post(
      `/api/quiz-questions/${questionId}`,
      formData,
      {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
          Accept: "application/json",
        },
      }
    );

    const updatedQuestion = response.data.question;

    if (isOpenEnded) {
      updatedQuestion.choices = [];
    }

    savedQuestions.value[editedQuestionIndex] = updatedQuestion;

    editingIndex.value = null;
    if (editingIndex.value === null) {
      questionType.value = 1;
      questionText.value = "";
      previewImage.value = null;
      choices.value = [
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
      ];
      imageFile.value = null;
    }
    loading.value = false;
    unsavedChanges.value = false;
    window.$snackbar("Question saved successfully", "success");
  } catch (error) {
    console.error("Error updating question:", error);
    loading.value = false;
    throw error;
  }
};

let isChangingType = false;

const saveOnClickOutside = (event) => {
  if (editingIndex.value !== null && !event.target.closest(".v-card")) {
    if (!isChangingType) {
      saveEditedQuestion();
    }
  }
};

const cleanupEventListener = () => {
  document.removeEventListener("click", saveOnClickOutside);
};

const setupSavedQuestionsClickListener = () => {
  cleanupEventListener();
  document.addEventListener("click", saveOnClickOutside);
};

const triggerImageUpload = () => {
  imageInput.value.click();
};

const triggerEditImageUpload = () => {
  editImageInput.value[0].click();
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
        savedQuestions.value[editingIndex.value].questionImageUrl = imageUrl;
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
    if (editingIndex.value !== null) {
      const questionId = savedQuestions.value[editingIndex.value].id;
      const { token } = getAuth();

      await axios.delete(`/api/quiz-questions/${questionId}/image/delete`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      savedQuestions.value[editingIndex.value].questionImageUrl = null;
      savedQuestions.value[editingIndex.value].question_image = null;
      previewImage.value = null;
      imageFile.value = null;
      imageRemoved.value = true;

      if (imageInput.value) {
        imageInput.value.value = "";
      }

      window.$snackbar("Image deleted successfully.", "success");
    } else {
      previewImage.value = null;
      imageFile.value = null;
      imageRemoved.value = true;

      if (imageInput.value) {
        imageInput.value.value = "";
      }
    }
  } catch (error) {
    console.error("Error deleting image:", error);
    window.$snackbar(error.response.data.message, "error");
  }
};

const saveQuestionWithoutValidation = async () => {
  if (loading.value) return;

  loading.value = true;

  try {
    if (choices.value.length < 0) {
      const formData = createFormData();
      await submitFormData(formData);
    }

    await router.push(`/create-quiz/${props.quizId}/results`);
  } catch (error) {
    console.error("Error saving question:", error);
  } finally {
    loading.value = false;
  }
};

const isSavedQuestionDisabled = computed(() => {
  return quiz.value.quiz_status_id === 2;
});

const currentQuestionType = computed(() => {
  return questionTypes.value.find((type) => type.id === questionType.value);
});

const showChoices = computed(() => {
  return currentQuestionType.value?.maxChoices > 0;
});

watch(questionType, (newType) => {
  isChangingType = true;

  choices.value = [];

  const isEditing = editingIndex.value !== null;
  const question = isEditing ? savedQuestions.value[editingIndex.value] : null;

  const getDefaultChoices = (type) => {
    if (type === 3) {
      return [
        { choice_text: "True", points: 0, is_correct: false },
        { choice_text: "False", points: 0, is_correct: false },
      ];
    } else if (type === 4) {
      return [];
    } else if (type === 5) {
      return [
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
      ];
    } else if (type === 1) {
      return [
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
      ];
    } else {
      return [
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
        { choice_text: "", points: 0, is_correct: false },
      ];
    }
  };

  if (newType === 3) {
    choices.value =
      isEditing && question.choices.length === 2
        ? question.choices.map((choice) => ({
          id: choice.id,
          choice_text: choice.choice_text || "",
          points: choice.points || 0,
          is_correct: Boolean(choice.is_correct),
          is_new: false,
        }))
        : getDefaultChoices(newType);
  } else if (newType === 4 && isEditing) {
    choices.value = [];
  } else if (newType === 5) {
    choices.value =
      isEditing && question.choices.length === 5
        ? question.choices.map((choice) => ({
          id: choice.id,
          choice_text: choice.choice_text || "",
          points: choice.points || 0,
          is_correct: Boolean(choice.is_correct),
          is_new: false,
        }))
        : getDefaultChoices(newType);
  } else if (newType === 1) {
    choices.value =
      isEditing && question.choices.length === 4
        ? question.choices.map((choice) => ({
          id: choice.id,
          choice_text: choice.choice_text || "",
          points: choice.points || 0,
          is_correct: Boolean(choice.is_correct),
          is_new: false,
        }))
        : getDefaultChoices(newType);
  } else {
    choices.value =
      isEditing && question.choices.length <= 4
        ? question.choices.map((choice) => ({
          id: choice.id,
          choice_text: choice.choice_text || "",
          points: choice.points || 0,
          is_correct: Boolean(choice.is_correct),
          is_new: false,
        }))
        : getDefaultChoices(newType);
  }

  setTimeout(() => {
    isChangingType = false;
  }, 100);
});

onBeforeUnmount(() => {
  cleanupEventListener();
});

onMounted(() => {
  fetchQuiz();
  fetchExistingQuestions();
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

.correct-answer-checkbox {
  position: absolute;
  top: 0;
  right: 0;
}

.position-relative {
  position: relative;
}

.position-absolute {
  position: absolute;
}
</style>
