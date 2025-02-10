<template>
  <textarea
    v-model="localResponse"
    class="textarea rounded-md my-2"
    rows="5"
    placeholder="Type your answer here..."
    :style="{
      fontSize: '18px',
      fontFamily: 'Nunito',
      backgroundColor: props.backgroundColor,
      border: `1px solid ${props.borderColor}`,
      ...textareaStyle,
    }"
  ></textarea>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import { computed } from 'vue';

const props = defineProps({
  backgroundColor: {
    type: String,
    default: 'white',
  },
  borderColor: {
    type: String,
    default: 'transparent',
  },
  focusOutline: {
    type: String,
    default: 'transparent',
  },
});

const textareaStyle = computed(() => ({
  '--outline-focus-color': props.focusOutline,
}));

const localResponse = ref('');
</script>

<style scoped>
.textarea:focus {
  outline: 1px solid var(--outline-focus-color);
}
</style>

