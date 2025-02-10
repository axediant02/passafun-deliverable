<template>
  <v-card class="mb-10" width="100%" elevation="2" variant="flat">
    <v-card-title class="text-h6 font-weight-bold px-6 pt-4">
      Participants
    </v-card-title>
    <v-container>
      <v-col class="px-0 py-0">
        <div class="d-flex align-center">
          <Searchbar searchLabel="Search for a participant..." v-model="searchQuery" />
          <ExportModal page="4" :quizId="quizId" :quizSummaryName="quizName" v-model="isModalOpen" />
        </div>
      </v-col>
      <clickable-table-with-pagination :thead="headers" :items="paginatedParticipants"
        @row-click="(id, summaryId) => goToAnswers(id, summaryId)" @update-page="updatePage" :currentPage="currentPage"
        :totalPages="totalPages" :disablePagination="false" />
    </v-container>
  </v-card>
</template>

<script setup>
import { defineProps, ref, computed, watch } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
  quizId: {
    type: Number,
    required: true,
  },
  headers: {
    type: Array,
    required: true,
  },
  participants: {
    type: Array,
    required: true,
  },
  quizName: {
    type: String,
    required: true
  }
});

const headers = ref([
  { title: 'Full Name', key: 'full_name' },
  { title: 'Email', key: 'email' },
  { title: 'Contact Number', key: 'contact_number' },
  { title: 'Age', key: 'age' },
  { title: 'Score', key: 'score' },
  { title: 'Result', key: 'result_header' },
  { title: 'Date Created', key: 'completed_at' },
]);

const participants = ref(props.participants);

watch(() => props.participants, (newParticipants) => {
  participants.value = newParticipants;
});

const currentPage = ref(1);
const itemsPerPage = ref(5);
const searchQuery = ref("");
const isModalOpen = ref(false);

const filteredParticipants = computed(() => {

  const filtered = participants.value.filter(participant =>
    participant.full_name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );

  return filtered.sort((a, b) => new Date(b.completed_at) - new Date(a.completed_at));
});

const totalPages = computed(() => {
  return Math.ceil(filteredParticipants.value.length / itemsPerPage.value);
});

const paginatedParticipants = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredParticipants.value.slice(start, end);
});

const goToAnswers = (participantId, summaryId) => {
  router.push({
    path: `/participant/${participantId}/answers`,
    query: { summaryId },
  });
};

const updatePage = (page) => {
  currentPage.value = page;
};
</script>
