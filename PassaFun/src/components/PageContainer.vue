<script setup>
import { useQuizzesStore } from '@/stores/quizzesStore';
import { storeToRefs } from 'pinia';

const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);

defineProps({
  backgroundColor: {
    type: String,
    default: '#ffffff',
  },
  backgroundImageUrl: {
    type: String,
    default: '',
  },
});
</script>

<template>
  <div class="w-screen flex justify-center fixed h-screen" :style="{
    backgroundColor: quizTheme.background_type === 'color' ? quizTheme.background_value : '',
  }">
    <div :style="{
      backgroundColor:
        quizTheme.background_type === 'color' ? quizTheme.background_value : 'transparent',
      backgroundImage:
        quizTheme.background_type === 'image'
          ? `url(${quizTheme.background_value})`
          : `url(${backgroundImageUrl || ''})`,
    }" class="background-image-container fixed h-[100%] w-screen flex flex-col max-w-xl ">
      <slot></slot>
    </div>
  </div>
</template>
