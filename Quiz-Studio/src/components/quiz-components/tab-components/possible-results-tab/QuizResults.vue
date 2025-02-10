<template>
  <v-col>
    <QuizDetailHeader :paramsId="quizId">
      <template #modal>
        <AddResultModal :quizId="quizId"/>
      </template>
    </QuizDetailHeader>
    <v-col v-for="(result, index) in filteredResults" :key="index">
      <div v-if="result.isEditMode">
        <v-card color="grey-lighten-3">
          <v-col align="end">
            <v-btn @click="saveResult(index, result)" color="success" class="mr-2">Save</v-btn>
            <v-btn @click="toggleEditMode(index)" color="error">Cancel</v-btn>
          </v-col>
          <v-col>
            <v-text-field v-model="result.header" label="Header" variant="outlined" max-width="500px" />
            <v-row class="ml-1">
              <v-text-field v-model="result.min_points" label="Input Minimum Points" variant="outlined" type="number"
                :rules="inputRules" required max-width="200" />
              <div class="mx-3"></div>
              <v-text-field v-model="result.max_points" label="Input Maximum Points" variant="outlined" type="number"
                :rules="inputRules" required max-width="200" />
            </v-row>
          </v-col>

          <v-col>
            <v-row align="center">
              <v-col>
                <v-textarea v-model="result.description" label="Description" variant="outlined" />
                <v-textarea v-model="result.financial_tips" label="Financial Tips" variant="outlined" />
              </v-col>

              <v-col justify="center" align="center">
                <v-file-input v-model="result.image" label="Upload Image" variant="outlined" accept="image/*" required
                  prepend-icon="" prepend-inner-icon="mdi-image" max-width="500px" />
              </v-col>
            </v-row>
          </v-col>
        </v-card>
      </div>

      <div v-else>
        <v-card color="grey-lighten-3">
          <v-toolbar>
            <v-toolbar-title class="font-weight-bold">
              <v-col>
                <v-row>
                  {{ result.header }}
                  <small class="ml-5 px-2 rounded-lg bg-primary">
                    {{ result.min_points }} - {{ result.max_points }}
                  </small>
                </v-row>
              </v-col>
            </v-toolbar-title>
            <v-col align="end" v-if="quiz.quiz_status_id !== 2">
              <v-btn @click="toggleEditMode(index)" color="primary" class="rounded-sm">
                <v-icon>mdi-square-edit-outline</v-icon>
              </v-btn>
              <v-btn @click="deleteResult(result.id)" color="error" class="rounded-sm">
                <v-icon>mdi-delete-outline</v-icon>
              </v-btn>
            </v-col>
          </v-toolbar>
          <v-col>
            <v-row>
              <v-col>
                <v-card-text>Description:</v-card-text>
                <v-card-subtitle class="text-wrap">{{
                  result.description
                }}</v-card-subtitle>

                <v-card-text>Tips:</v-card-text>

                <v-card-subtitle class="text-wrap">{{
                  result.financial_tips
                }}</v-card-subtitle>
              </v-col>

              <v-col justify="center" align="center">
                <img :src="result.image_url" class="result-image" alt="Results Page Image" />
              </v-col>
            </v-row>
          </v-col>
        </v-card>
      </div>
    </v-col>
  </v-col>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import { getAuth } from "@/pages/auth/authService";

const results = ref([]);
const quiz = ref({});
const searchQuery = ref("");
const route = useRoute();

const props = defineProps({
  quizId: {
    type: Number,
    required: true,
  },
  quizStatusId: {
    type: Number,
    required: true,
  }
})
const quizId = props.quizId;


const fetchQuizResultsData = async () => {
  const { token } = getAuth();
  try {
    const response = await axios.get(`/api/quizzes/${quizId}/results`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    quiz.value = response.data;
    results.value = response.data.results;
  } catch (error) {
    console.error("Error fetching quiz data:", error);
  }
};

const isEditable = computed(() => {
  return quiz.value.quiz_status_id !== 2;
})

const toggleEditMode = (index) => {
  if (isEditable.value) {
    results.value[index].isEditMode = !results.value[index].isEditMode;
  }
};

const saveResult = async (index, result) => {
  const { token } = getAuth();
  try {
    const formData = new FormData();
    formData.append("header", result.header);
    formData.append("description", result.description);
    formData.append("financial_tips", result.financial_tips);

    if (result.image instanceof File) {
      formData.append("image", result.image);
    }

    formData.append("min_points", result.min_points);
    formData.append("max_points", result.max_points);

    await axios.post(`/api/results/${result.id}`, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
        Authorization: `Bearer ${token}`,
      },
    });

    results.value[index].isEditMode = false;
    window.$snackbar(`Result updated successfully!`,`success`)
  } catch (error) {
    if (error.response && error.response.status === 403) {
      window.$snackbar("Oops! You don't have access to perform this action!", "error");
    } else {
      console.error("Error updating question. Invalid question data!", "error", error);
    }
  }
};

const deleteResult = async (resultId) => {
  const { token } = getAuth();
  try {
    await axios.delete(`/api/results/${resultId}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    results.value = results.value.filter((result) => result.id !== resultId);
    window.$snackbar(`Result deleted successfully!`,`success`)
  } catch (error) {
    if (error.response && error.response.status === 403) {
      window.$snackbar("Oops! You don't have access to perform this action!", "error");
    } else {
      console.error("Error updating question. Invalid question data!", "error", error);
    }
  }
};

const filteredResults = computed(() =>
  searchQuery.value
    ? results.value.filter((result) =>
      result.header.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
    : results.value
);

onMounted(fetchQuizResultsData);
</script>

<style scoped>
.result-image {
  max-width: 300px;
  max-height: 300px;
  object-fit: cover;
}
</style>
