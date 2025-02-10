<template>
  <v-container fluid>
    <page-title-with-action-buttons :title="{ show: true, text: 'Participants' }" :backButton="{ show: false }" />

    <v-col class="px-0">
      <div class="d-flex align-center justify-space-between">
        <div class="d-flex align-center">
          <Searchbar searchLabel="Search for a participant..." v-model="searchQuery" />
          <filter-modal :quizzes="quizzes" @apply-filters="onFilterApply" @close-modal="closeFilterModal" />
        </div>
        <ExportModal page="1" v-model="isModalOpen" :available-quizzes="allQuizzes" />
      </div>
    </v-col>

    <clickable-table-with-pagination :thead="tableHeaders" :items="filteredParticipants" :loading="loading"
      :currentPage="currentPage" :totalPages="totalPages" @update-page="onUpdatePage"
      @row-click="(id, summaryId) => goToAnswers(id, summaryId)" />
    <div v-if="filteredParticipants.length === 0" class="text-center">
      No data found
    </div>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch, reactive } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { DateTime } from 'luxon';
import debounce from 'lodash/debounce'

const tableHeaders = [
  { title: 'Full Name', key: 'full_name' },
  { title: 'Email', key: 'email' },
  { title: 'Phone Number', key: 'contact_number' },
  { title: 'Age', key: 'age' },
  { title: 'Quiz Name', key: 'quiz_name' },
  { title: 'Score', key: 'score' },
  { title: 'Result', key: 'result' },
  { title: 'Date Created', key: 'created_at' },
];

const isModalOpen = ref(false);
const participants = ref([]);
const searchQuery = ref("");
const router = useRouter();
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(5);
const loading = ref(false);

const filters = reactive({
  selectedQuizzes: [],
  fromDate: null,
  toDate: null,
  selectedParticipantInformations: [],
});

const allQuizzes = ref([]);

const quizzes = computed(() => {
  if (allQuizzes.value.length === 0) {
    fetchAllQuizzes();
  }
  return allQuizzes.value;
});

const fetchAllQuizzes = async () => {
  try {
    const response = await axios.get('/api/quizzes/names');

    if (Array.isArray(response.data)) {
      allQuizzes.value = response.data;
    } else {
      console.error('Unexpected quiz data format:', response.data);
      allQuizzes.value = [];
    }
  } catch (error) {
    console.error('Error fetching quizzes:', error);
    allQuizzes.value = [];
  }
};

const filteredParticipants = computed(() => {
  const query = searchQuery.value.toLowerCase();

  return participants.value.flatMap((participant) => {
    return participant.summaries
      .filter((summary) => {
        return (
          participant.full_name?.toLowerCase().includes(query) ||
          participant.email?.toLowerCase().includes(query)
        );
      })
      .map((summary) => ({
        id: participant.id,
        full_name: participant.full_name || "N/A",
        email: participant.email || "N/A",
        age: participant.age || "N/A",
        quiz_name: summary.quiz_name || "N/A",
        score: summary.score || "N/A",
        result: summary.result || "N/A",
        contact_number: participant.contact_number || "N/A",
        created_at: participant.created_at || "N/A",
        summaryId: summary.summaryId,
      }));
  });
});

const searchParticipants = debounce(async (query) => {
  loading.value = true;
  try {
    const response = await axios.get('/api/participants/search', {
      params: {
        query,
        page: currentPage.value,
        per_page: perPage.value
      }
    });

    participants.value = response.data.data.map(participant => {
      const formattedDate = formatDate(participant.created_at);
      return {
        ...participant,
        created_at: formattedDate,
        summaries: participant.participant_quiz_summaries.map(summary => ({
          summaryId: summary.id,
          quiz_name: summary.quiz?.name || 'N/A',
          score: summary.score || 'N/A',
          result: summary.result?.header || 'N/A',
          completed_at: summary.completed_at
        }))
      };
    });

    currentPage.value = response.data.current_page;
    totalPages.value = response.data.last_page;

  } catch (error) {
    console.error('Search failed:', error);
    participants.value = [];
  } finally {
    loading.value = false;
  }
}, 300);

const formatDate = (dateStr) => {
  if (!dateStr) return 'N/A';
  try {

    const date = DateTime.fromFormat(dateStr, 'yyyy-MM-dd HH:mm:ss');
    if (date.isValid) {
      return date.toFormat('yyyy-MM-dd');
    }

    const isoDate = DateTime.fromISO(dateStr);
    if (isoDate.isValid) {
      return isoDate.toFormat('yyyy-MM-dd');
    }

    return 'N/A';
  } catch (error) {
    console.error('Error formatting date:', error, dateStr);
    return 'N/A';
  }
};

watch(searchQuery, (newQuery) => {
  if (newQuery.trim()) {
    searchParticipants(newQuery);
  } else {
    fetchParticipants(currentPage.value);
  }
});

const fetchParticipants = async (page = 1) => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/participants`, {
      params: {
        page,
        per_page: perPage.value,
      },
    });

    participants.value = response.data.data.map(summary => {
      const formattedDate = formatDate(summary.completed_at);

      return {
        id: summary.participant.id,
        full_name: summary.participant.full_name || 'N/A',
        email: summary.participant.email || 'N/A',
        age: summary.participant.age || 'N/A',
        contact_number: summary.participant.contact_number || 'N/A',
        created_at: formattedDate,
        summaries: [{
          summaryId: summary.id,
          quiz_name: summary.quiz ? summary.quiz.name : 'N/A',
          score: summary.score || 'N/A',
          result: summary.result ? summary.result.header : 'N/A',
          completed_at: summary.completed_at
        }]
      };
    });

    currentPage.value = response.data.current_page;
    totalPages.value = response.data.last_page;
  } catch (error) {
    console.error("Error fetching participants:", error);
  } finally {
    loading.value = false;
  }
};

const applyFilters = async (newFilters = null, page = currentPage.value) => {
  if (newFilters) {
    filters.selectedQuizzes = newFilters.selectedQuizzes;
    filters.fromDate = newFilters.fromDate;
    filters.toDate = newFilters.toDate;
    filters.selectedParticipantInformations = newFilters.selectedParticipantInformations;
  }

  loading.value = true;

  try {
    const response = await axios.get('/api/participants/filter', {
      params: {
        fromDate: filters.fromDate,
        toDate: filters.toDate,
        selectedParticipantInformations: filters.selectedParticipantInformations,
        selectedQuizzes: filters.selectedQuizzes,
        page: page,
        per_page: perPage.value,
      }
    });

    if (response.data.data && response.data.data.length > 0) {
      participants.value = response.data.data.map(participant => ({
        id: participant.id,
        full_name: participant.full_name || 'N/A',
        email: participant.email || 'N/A',
        age: participant.age || 'N/A',
        contact_number: participant.contact_number || 'N/A',
        created_at: formatDate(participant.completed_at) || 'N/A',
        summaries: [{
          summaryId: participant.summary_id,
          quiz_name: participant.quiz_name || 'N/A',
          score: participant.score || 'N/A',
          result: participant.result || 'N/A',
          completed_at: participant.completed_at
        }]
      }));

      currentPage.value = response.data.current_page;
      totalPages.value = response.data.last_page;
    } else {
      participants.value = [];
      currentPage.value = 1;
      totalPages.value = 1;
    }
  } catch (error) {
    console.error('Filter failed:', error);
    participants.value = [];
  } finally {
    loading.value = false;
  }
};

const closeFilterModal = () => {
  isModalOpen.value = false;
};

const onFilterApply = (newFilters) => {
  applyFilters(newFilters, 1);
};

watch(
  () => filters,
  () => {
    applyFilters(filters.value);
  },
  { deep: true }
);


watch(() => filters.selectedQuizzes, (newSelectedQuizzes) => {
  if (newSelectedQuizzes.length === 0) {
    fetchParticipants(currentPage.value);
  }
});

const onUpdatePage = (page) => {
  if (Object.values(filters).some(value =>
    Array.isArray(value) ? value.length > 0 : value !== null
  )) {
    applyFilters(null, page);
  } else {
    fetchParticipants(page);
  }
};

onMounted(() => {
  fetchParticipants();
});

const goToAnswers = (participantId, summaryId) => {
  if (!participantId || !summaryId) {
    console.error("Invalid participantId or summaryId:", participantId, summaryId);
    return;
  }
  router.push({
    path: `/participant/${participantId}/answers`,
    query: { summaryId },
  });
};
</script>
