<template>
  <v-snackbar
    v-model="internalVisible"
    :color="snackbarColor"
    :timeout="snackbarTimeout"
    class="d-flex align-start justify-center mt-10"
    max-width="600"
  >
    <span class="d-flex align-center justify-center">
      <v-icon class="mr-2" v-if="snackbarColor === 'error'">mdi-alert-circle-outline</v-icon>
      <v-icon class="mr-2" v-else-if="snackbarColor === 'success'">mdi-check-circle-outline</v-icon>
      <v-icon class="mr-2" v-else-if="snackbarColor === 'warning'">mdi-alert</v-icon>
      {{ snackbarText }}
    </span>
  </v-snackbar>
</template>

<script setup>
import { defineProps, ref, watch, onMounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  text: {
    type: String,
    default: '',
  },
  color: {
    type: String,
    default: 'success',
  },
  timeout: {
    type: Number,
    default: 3000,
  },
});

const internalVisible = ref(props.modelValue);
const snackbarText = ref(props.text);
const snackbarColor = ref(props.color);
const snackbarTimeout = ref(props.timeout);

watch(() => props.modelValue, (newValue) => {
  internalVisible.value = newValue;
});

watch(() => props.text, (newText) => {
  snackbarText.value = newText;
});

watch(() => props.color, (newColor) => {
  snackbarColor.value = newColor;
});

watch(() => props.timeout, (newTimeout) => {
  snackbarTimeout.value = newTimeout;
});

const showSnackbar = (text, color = 'success', timeout = 3000) => {
  snackbarText.value = text;
  snackbarColor.value = color;
  snackbarTimeout.value = timeout;
  internalVisible.value = true;

  setTimeout(() => {
    internalVisible.value = false;
  }, snackbarTimeout.value);
};

onMounted(() => {
  if (!window.$snackbar) {
    window.$snackbar = showSnackbar;
  }
});
</script>

<style scoped>
.mt-10 {
  margin-top: 10px;
}
</style>
