<template>
  <form novalidate class="flex flex-col" @submit.prevent="emitKeyPressEnter">
    <div class="input-group">
      <input
        :required="isRequired"
        :id="inputId"
        :type="inputType"
        v-model="localResponse"
        class="input rounded-md w-full border-2 h-[50px] p-2"
        :style="inputStyles"
        placeholder=" "
      />
      <label
        class="user-label font-nunito font-semibold text-[#B8B8B8] text-sm"
        :class="shouldUseLightText(quizTheme) ? 'text-white' : 'text-black'"
      >
        {{ placeholder }} <span v-if="!required">(Optional)</span>
      </label>
    </div>
    <div class="relative h-4 w-full">
      <span v-if="error" class="text-error px-1 absolute top-0 w-full h-full text-xs">{{
        error
      }}</span>
    </div>
  </form>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { shouldUseLightText } from '@/utils/lumininanceChecker';
import { storeToRefs } from 'pinia';
import { useQuizzesStore } from '@/stores/quizzesStore';

const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);

const props = defineProps({
  placeholder: String,
  inputType: String,
  inputId: {
    type: [String, Number],
    default: '',
  },
  backgroundColor: {
    type: String,
    default: 'transparent',
  },
  textColor: {
    type: String,
    default: '#FFFFFF',
  },
  borderColor: {
    type: String,
    default: '#E5E7EB', // Default Tailwind gray-300
  },
  errorBorderColor: {
    type: String,
    default: '#EF4444', // Tailwind red-500
  },
  boxShadowColor: String,
  boxShadowSize: {
    type: String,
    default: '4px',
  },
  boxShadowBottom: {
    type: String,
    default: '4px',
  },
  inputValue: [String, Number],
  error: {
    type: String,
    default: '',
  },
  required: {
    type: [Number, Boolean],
    default: false,
  },
});

const isRequired = computed(() => {
  return typeof props.required === 'number' ? props.required === 1 : props.required;
});

const localResponse = ref(props.inputValue || '');

const emit = defineEmits(['update:response', 'emittedKeyPressEnter']);
watch(localResponse, (newValue) => {
  emit('update:response', props.inputId, newValue);
});

const emitKeyPressEnter = () => {
  emit('emittedKeyPressEnter');
};

const borderColorComputed = computed(() =>
  props.error ? props.errorBorderColor : props.borderColor
);

const inputStyles = computed(() => ({
  '--background-color': props.backgroundColor,
  '--text-color': props.textColor,
  '--border-color': borderColorComputed.value,
  '--box-shadow-color': props.boxShadowColor || 'transparent',
  '--box-shadow-size': props.boxShadowSize,
  '--box-shadow-bottom': props.boxShadowBottom,
  backgroundColor: `var(--background-color)`,
  color: `var(--text-color)`,
  borderColor: `var(--border-color)`,
  boxShadow: `0 var(--box-shadow-bottom) var(--box-shadow-size) var(--box-shadow-color)`,
  transition: 'box-shadow 0.3s ease',
}));
</script>
