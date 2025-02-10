<template>
  <div>
    <v-btn @click="exportModal = true" height="40px" color="primary">
      <v-icon class="mr-1">mdi-download</v-icon> Export to CSV
    </v-btn>

    <Modal v-model="exportModal" @close="closeModal" style="max-width: 500px;">
      <template #title>Export as CSV</template>

      <template #content>
        <v-card-text v-if="props.page === '1' || props.page === '2' || props.page === '4'">
          <v-card-subtitle class="mb-3">Select date range for submissions</v-card-subtitle>

          <div class="d-flex flex-column">
            <div class="d-flex">
              <v-text-field v-model="fromDate" label="From Date" class="text-field-from"
                prepend-inner-icon="mdi-calendar" :append-inner-icon="fromDate ? 'mdi-close' : ''" readonly
                variant="outlined" @click="dateFromModal = true" @click:append-inner.stop="fromDate = null" />

              <Modal v-model="dateFromModal" @close="dateFromModal = false" style="max-width: 400px;">
                <template #title>Select From Date</template>

                <template #content>
                  <v-date-picker v-model="fromDate" single></v-date-picker>
                </template>

                <template #actions>
                  <v-btn color="primary" class="px-5" variant="flat" @click="dateFromModal = false">Close</v-btn>
                </template>
              </Modal>

              <v-text-field v-model="toDate" label="To Date" class="text-field-to" prepend-inner-icon="mdi-calendar"
                :append-inner-icon="toDate ? 'mdi-close' : ''" readonly variant="outlined" @click="dateToModal = true"
                @click:append-inner="toDate = null" @click:append-inner.stop="toDate = null" />

              <Modal v-model="dateToModal" @close="dateToModal = false" style="max-width: 400px;">
                <template #title>Select To Date</template>

                <template #content>
                  <v-date-picker v-model="toDate" single></v-date-picker>
                </template>

                <template #actions>
                  <v-btn color="primary" class="px-5" variant="flat" @click="dateToModal = false">Done</v-btn>
                </template>
              </Modal>
            </div>

            <div v-if="showDateRangeError" class="date-range-error text-error text-caption mt-1">
              The From date should not be later than the To date.
            </div>
          </div>
        </v-card-text>

        <v-card-text class="py-0" v-if="props.page === '1'|| props.page === '2'">
          <v-card-subtitle class="mb-3">Select quizzes to export</v-card-subtitle>
          <v-select
            v-model="selectedQuizzes"
            :items="availableQuizzes"
            item-title="title"
            item-value="value"
            label="Select Quizzes"
            variant="outlined"
            multiple
            chips
            closable-chips
            class="mb-4"
          />
        </v-card-text>

        <v-card-text>
          <v-card-subtitle class="mb-3">Select fields to export</v-card-subtitle>
          <v-col>
            <v-checkbox
              v-for="option in options"
              :key="option.value"
              v-model="selectedOptions"
              :label="option.label"
              :value="option.value"
              hide-details
            />
          </v-col>
        </v-card-text>
      </template>

      <template #actions>
        <v-btn :loading="loading" class="bg-blue darken-1 w-100" height="44" text :disabled="!isDateRangeValid"
          @click="downloadCsvFile">
          <v-icon>mdi-download</v-icon> Download
        </v-btn>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import axios from "axios";

const props = defineProps({
  page: {
    type: String,
    required: true,
  },
  participantId: {
    type: Array,
    required: false,
    default: () => [],
  },
  summaryId: {
    type: [String, Number],
    required: false,
    default: null,
  },
  participantName: {
    type: String,
    required: false,
    default: "",
  },
  quizSummaryName: {
    type: String,
    required: false,
    default: '',
  },
  quizId: {
    type: [String, Number],
    required: false,
    default: null,
  },
  availableQuizzes: {
    type: Array,
    default: () => []
  }
});

const exportModal = ref(false);
const selectedOptions = ref([]);
const loading = ref(false);

const closeModal = () => {
  exportModal.value = false;
};

const options = computed(() => {
  const pageFields = {
    1: [
      { label: "Date Created", value: "completed_at" },
      { label: "Full Name", value: "full_name" },
      { label: "Phone Number", value: "contact_number" },
      { label: "Email Address", value: "email" },
      { label: "Age", value: "age" },
      { label: "Quiz Name", value: "name" },
      { label: "Score", value: "score" },
      { label: "Result", value: "header" },
    ],
    2: [
      { label: "Full Name", value: "full_name" },
      { label: "Phone Number", value: "contact_number" },
      { label: "Email Address", value: "email" },
      { label: "Quizzes", value: "name" },
      { label: "Score", value: "score" },
      { label: "Submission Date", value: "completed_at" },
      { label: "Result", value: "header" },
    ],
    3: [
      { label: "Quiz Name", value: "name" },
      { label: "Full Name", value: "full_name" },
      { label: "Phone Number", value: "contact_number" },
      { label: "Email Address", value: "email" },
      { label: "Age", value: "age" },
      { label: "Submission Date", value: "completed_at" },
      { label: "Result", value: "header" },
      { label: "Score", value: "score" },
      { label: "Questions", value: "questions" },
      { label: "Answers", value: "answers" },
    ],
    4: [
      { label: "Full Name", value: "full_name" },
      { label: "Phone Number", value: "contact_number" },
      { label: "Email Address", value: "email" },
      { label: "Age", value: "age" },
      { label: "Score", value: "score" },
      { label: "Submission Date", value: "completed_at" },
      { label: "Result", value: "header" },

    ],
  };
  return pageFields[props.page] || [];
});

watch(
  options,
  (newOptions) => {
    selectedOptions.value = newOptions.map((option) => option.value);
  },
  { immediate: true }
);

const dateFromModal = ref(false);
const dateToModal = ref(false);
const fromDate = ref(null);
const toDate = ref(null);

const formatDate = (date) => {
  if (!date) return "";
  if (typeof date === "string" && date.includes("/")) {
    return date;
  }
  const d = new Date(date);
  return `${d.getMonth() + 1}/${d.getDate()}/${d.getFullYear()}`;
};

const isDateRangeValid = computed(() => {
  if ((fromDate.value && !toDate.value) || (!fromDate.value && toDate.value)) return true;
  if (!fromDate.value && !toDate.value) return true;

  if (fromDate.value && toDate.value) {
    const startDate = new Date(fromDate.value);
    const endDate = new Date(toDate.value);
    return startDate <= endDate;
  }

  return true;
});

watch(fromDate, (newFromDate) => {
  if (newFromDate) {
    dateFromModal.value = false;
    dateToModal.value = true;
  }
});
watch(toDate, (newFromDate) => {
  if (newFromDate) {
    dateToModal.value = false;
    dateFromModal.value = true;
  }
});

watch([fromDate, toDate], ([newFromDate, newToDate]) => {
  if (newFromDate && newToDate) {
    dateFromModal.value = false;
    dateToModal.value = false;
  }
});

const selectedQuizzes = ref([]);

const downloadCsvFile = async () => {
  loading.value = true;
  try {
    if (selectedOptions.value.length === 0) {
      window.$snackbar("Please select at least one field to export!", "error");
      return;
    }

    const requestData = {
      selectedFields: selectedOptions.value,
      ...(shouldIncludeDateRange() && {
        dateRange: buildDateRange(),
      }),
      ...((props.page === '1' || props.page === '2') && {
        selectedQuizzes: selectedQuizzes.value
      })
    };

    const apiEndpoint = getApiEndpoint();

    if (!props.participantId && (props.page === "2" || props.page === "3")) {
      window.$snackbar("Participant doesn't exist", "error");
      return;
    }

    const response = props.page === "3"
      ? await axios.get(apiEndpoint, { params: requestData, responseType: "blob" })
      : await axios.post(apiEndpoint, requestData, { responseType: "blob" });

    triggerFileDownload(response.data);

  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
    closeModal();
  }
};

const shouldIncludeDateRange = () => {
  return props.page === "1" || props.page === "2" || props.page === "4";
};

const buildDateRange = () => {
  const toISODate = (date) => {
    if (!date) return null;
    const localTimezoneOffset = date.getTimezoneOffset() * 60000;
    const manilaTime = new Date(date.getTime() - localTimezoneOffset + (8 * 60 * 60000));
    return manilaTime.toISOString();
  };

  const dateRange = {
    from: fromDate.value ? toISODate(new Date(fromDate.value)) : null,
    to: toDate.value ? toISODate(new Date(toDate.value)) : null,
  };

  if (fromDate.value && toDate.value) {
    dateRange.display = `${formatDate(fromDate.value)} ~ ${formatDate(toDate.value)}`;
  } else if (fromDate.value) {
    dateRange.display = `${formatDate(fromDate.value)} onwards`;
  } else if (toDate.value) {
    dateRange.display = `Until ${formatDate(toDate.value)}`;
  }

  return dateRange;
};

const getApiEndpoint = () => {
  if (props.page === "1") {
    return "/api/export-participant-csv"; xt
  }
  if (props.page === "2" && props.participantId) {
    return `/api/export-participant-csv/${props.participantId}`;
  }
  if (props.page === "3" && props.participantId) {
    return `/api/export-participant-quiz-summary-csv/${props.participantId}/${props.summaryId}`;
  }
  if (props.page === "4" && props.quizId) {
    return `/api/export-quiz-participants/${props.quizId}`;
  }
  return "/api/export-participant-csv";
};

const triggerFileDownload = (data) => {
  const url = window.URL.createObjectURL(new Blob([data]));
  const link = document.createElement("a");

  const currentDate = new Date().toLocaleDateString().replace(/\//g, "-");

  let fileName = `participant-report-${currentDate}.csv`;

  if (props.page === "1") {
    fileName = `Participants.csv`;
  } else if (props.page === "2" && props.participantId) {
    fileName = `${props.participantName}'s_Quizzes.csv`;
  } else if (props.page === "3" && props.participantId) {
    fileName = `${props.participantName}'s_${props.quizSummaryName}_summary.csv`;
  } else if (props.page === "4" && props.quizId) {
    fileName = `${props.quizSummaryName}_participants.csv`;
  }

  link.href = url;
  link.setAttribute("download", fileName);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const showDateRangeError = computed(() => {
  if (!fromDate.value || !toDate.value) return false;
  return !isDateRangeValid.value;
});
</script>

<style scoped>
.v-selection-control {
  margin: 0 !important;
  padding: 0 !important;
  display: flex;
  align-items: center;
}

.active-border .v-input__control .v-input__inner {
  border-color: #000 !important;
  box-shadow: none !important;
}

.date-range-error {
  color: #ff5252;
}
</style>
