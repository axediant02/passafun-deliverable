<template>
  <div class="my-0 mx-0">
    <v-btn @click="openModal" icon="mdi-tune" color="grey" variant="text" />

    <default-modal v-model="dialog" @close="closeModal" :title="`Apply Filters`" confirm-text="Submit"
      cancel-text="Cancel" confirm-color="primary" :custom-style="'max-width: 500px;'" titleClass="font-weight-bold">
      <div class="d-flex">

        <v-text-field v-model="fromDate" label="From Date" class="text-field-from" prepend-inner-icon="mdi-calendar"
          :append-inner-icon="fromDate ? 'mdi-close' : ''" readonly variant="outlined" @click="dateFromModal = true"
          @click:append-inner="fromDate = null" />

        <Modal v-model="dateFromModal" @close="dateFromModal = false" style="max-width: 400px;">
          <template #title>Select From Date</template>
          <template #content>
            <v-date-picker v-model="fromDate" single />
          </template>
          <template #actions>
            <v-btn color="primary" class="px-5" variant="flat" @click="dateFromModal = false">
              Close
            </v-btn>
          </template>
        </Modal>

        <v-text-field v-model="toDate" label="To Date" class="text-field-to" prepend-inner-icon="mdi-calendar"
          :append-inner-icon="toDate ? 'mdi-close' : ''" readonly variant="outlined" @click="dateToModal = true"
          @click:append-inner="toDate = null" />

        <Modal v-model="dateToModal" @close="dateToModal = false" style="max-width: 400px;">
          <template #title>Select To Date</template>
          <template #content>
            <v-date-picker v-model="toDate" single />
          </template>
          <template #actions>
            <v-btn color="primary" class="px-5" variant="flat" @click="dateToModal = false">
              Done
            </v-btn>
          </template>
        </Modal>
      </div>

      <v-select
        v-model="selectedQuizzes"
        :items="props.quizzes"
        item-title="title"
        item-value="id"
        multiple
        chips
        closable-chips
        label="Select quizzes"
        variant="outlined"
        :menu-props="{ maxHeight: 400 }"
        persistent-hint
        hint="Select one or more quizzes"
      />

      <div class="mt-3">
        <p>Only Show Participants with:</p>
        <v-checkbox v-for="info in participantInformations" :key="info.value" v-model="selectedParticipantInformations"
          :label="info.label" :value="info.value" hide-details class="font-weight-medium" />
      </div>

      <template #actions>
        <v-btn class="px-5" variant="tonal" color="primary" height="40px" :loading="loading" @click="closeModal">
          Cancel
        </v-btn>
        <v-btn class="bg-primary px-5" color="white" height="40px" :loading="loading" @click="submitForm" type="submit">
          Confirm
        </v-btn>
      </template>
    </default-modal>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch, computed} from 'vue';
import { DateTime } from 'luxon';

const props = defineProps({
  quizzes: {
    type: Array,
    required: true,
    default: () => []
  }
});

const emit = defineEmits(['applyFilters', 'closeModal', 'clearFilters']);

const dialog = ref(false);
const fromDate = ref(null);
const toDate = ref(null);
const selectedQuizzes = ref([]);
const selectedParticipantInformations = ref([]);

const participantInformations = [
  { label: 'Email Address', value: 'email' },
  { label: 'Contact Number', value: 'contact_number' },
  { label: 'Age', value: 'age' },
];


const dateFromModal = ref(false);
const dateToModal = ref(false);

const formattedFromDate = computed(() => {
  if (!fromDate.value) return null;

  return DateTime.fromJSDate(fromDate.value)
    .startOf('day')
    .toISO();
});

const formattedToDate = computed(() => {
  if (!toDate.value) return null;

  return DateTime.fromJSDate(toDate.value)
    .endOf('day')
    .toISO();
});


const openModal = () => {
  dialog.value = true;
};

const resetForm = () => {
  fromDate.value = null;
  toDate.value = null;
  selectedQuizzes.value = [];
  selectedParticipantInformations = [];
}

const closeModal = () => {
  dialog.value = false;
  emit('closeModal')
};

const submitForm = () => {
  emit('applyFilters', {
    fromDate: formattedFromDate.value,
    toDate: formattedToDate.value,
    selectedQuizzes: selectedQuizzes.value,
    selectedParticipantInformations: selectedParticipantInformations.value,
  });
  closeModal();
};

watch(fromDate, (newFromDate) => {
  if (newFromDate) {
    dateFromModal.value = false;
    dateToModal.value = true;
  }
});
watch(toDate, (newToDate) => {
  if (newToDate) {
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
</script>
