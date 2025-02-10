<template>
    <div>
        <v-container fluid>
            <page-title-with-action-buttons :title="{ show: true, text: 'Participant Quizzes' }"
                :backButton="{ show: true, text: 'Back' }" />

            <div v-if="loading">
                <v-row justify="center" align="center">
                    <LottieAnimation :animationData="customLoader" height="300" width="300" />
                </v-row>
            </div>
            <div v-else>
                <v-col class="mb-5">
                    <v-row class="align-center">
                        <v-avatar size="80" color="primary white--text" class="avatar-text">
                            {{ firstLetter || "?" }}
                        </v-avatar>

                        <v-col>
                            <h1>{{ participantQuizDetails.full_name || "N/A" }}</h1>
                            <span class="d-block mb-1">
                                <v-icon class="text-h6" color="grey-darken-1">mdi-email</v-icon>
                                {{ participantQuizDetails.email || "N/A" }}
                            </span>
                            <span>
                                <v-icon class="text-h6" color="grey-darken-1">mdi-phone</v-icon>
                                {{ participantQuizDetails.contact_number || "N/A" }}
                            </span>
                        </v-col>
                    </v-row>
                </v-col>

                <v-col>
                    <v-row class="align-center">
                        <Searchbar searchLabel="Search for Quiz Results" v-model="searchQuery" />
                        <ExportModal v-if="participantQuizDetails.full_name" page="2" :participantId="[route.params.id]"
                            :participantName="participantQuizDetails.full_name" :value="false" v-model="exportModal"
                            :availableQuizzes="mappedQuizzes" />
                    </v-row>
                </v-col>

                <clickable-table-with-pagination :thead="tableHeaders" :items="filteredAllQuizResults"
                    :currentPage="currentPage" :totalPages="totalPages" @update-page="onUpdatePage"
                    @row-click="goToDetails" />
            </div>
        </v-container>
    </div>
</template>


<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter, useRoute } from "vue-router";
import customLoader from "@/json/loader.json";

const router = useRouter();
const route = useRoute();

const participantQuizDetails = ref({});
const firstLetter = ref("");
const quizResults = ref([]);
const searchQuery = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(5);

const loading = ref(false);
const exportModal = ref(false);

const tableHeaders = [
    { title: 'Quiz Title', key: 'quiz_name' },
    { title: 'Score', key: 'score' },
    { title: 'Result', key: 'header' },
    { title: 'Date', key: 'completed_at' },
];

const filteredAllQuizResults = computed(() => {
    return quizResults.value
        .filter(
            (summary) =>
                summary.quiz_name &&
                summary.quiz_name.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
        .sort((a, b) => new Date(b.completed_at) - new Date(a.completed_at))
        .map((summary) => ({
            ...summary,
            completed_at: new Date(summary.completed_at).toLocaleDateString('en-PH', { timeZone: 'Asia/Manila' }),
        }));
});

const mappedQuizzes = computed(() => {
  const uniqueQuizzes = new Map();
  quizResults.value.forEach(quiz => {
    if (!uniqueQuizzes.has(quiz.quiz_id)) {
      uniqueQuizzes.set(quiz.quiz_id, {
        title: quiz.quiz_name,
        value: quiz.quiz_id
      });
    }
  });

  return Array.from(uniqueQuizzes.values());
});

const fetchParticipantData = async (page = 1) => {
    const participantId = route.params.id;
    loading.value = true;
    try {
        const response = await axios.get(`/api/participant/${participantId}/summaries-and-answers`, {
            params: {
                page,
                per_page: perPage.value,
            },
        });

        participantQuizDetails.value = response.data.participant;
        firstLetter.value = participantQuizDetails.value.full_name
            ? participantQuizDetails.value.full_name.charAt(0)
            : "";

        const summariesArray = response.data.summaries.data || [];

        quizResults.value = summariesArray.map((summary) => ({
            ...summary,
            summaryId: summary.id,
            quiz_name: summary.quiz_name || "Unknown Quiz",
        }));

        currentPage.value = response.data.summaries.current_page;
        totalPages.value = response.data.summaries.last_page;
    } catch (error) {
        console.error("Error fetching participant data:", error);
    } finally {
        loading.value = false;
    }
};

const onUpdatePage = (page) => {
    currentPage.value = page;
    fetchParticipantData(page);
};

const goToDetails = (summaryId) => {
    router.push({
        path: `/participant/${route.params.id}/answers`,
        query: { summaryId },
    });
};

onMounted(async () => {
    await fetchParticipantData(currentPage.value);
});
</script>


<style scoped>
.table-contents {
    margin-top: 30px;
}

.avatar-text {
    font-size: 2rem;
}

.clickable-row {
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.clickable-row:hover {
    background-color: #e0e0e0 !important;
}
</style>
