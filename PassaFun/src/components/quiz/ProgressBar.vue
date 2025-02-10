<template>
  <div class="flex flex-row place-items-center justify-center py-2 w-full">
    <div
      class="relative w-full h-4 rounded-[5px] flex-1"
      :style="{
        border: `1px solid ${quizTheme.main_color}`,
        backgroundColor:
          quizTheme.background_type === 'color' ? quizTheme.background_value : '#e2e8f0',
      }"
    >
      <div
        class="absolute top-0 left-0 h-full rounded-2 transition-all duration-500 ease-in-out"
        :style="{
          width: progress + '%',
          background: `linear-gradient(to right, ${quizTheme.button_color}, ${quizTheme.main_color},  ${quizTheme.accent_color})`,
        }"
      ></div>

      <LottieAnimations
        :animationData="animationsData"
        :progress="progress"
        class="lottie-animation absolute top-1/4 transform -translate-y-1/2"
        :style="{
          left: `calc(${progress}% - 18px)`,
          transition: 'left 0.4s ease-in-out',
        }"
      />
    </div>
    <div
      class="rounded text-sm font-bold flex items-center font-nunito justify-center h-4 w-11 ml-2"
      :style="{
        color: quizTheme.accent_color,
      }"
    >
      {{ currentQuestionIndex + 1 }}/{{ totalQuestions }}
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useQuizzesStore } from '@/stores/quizzesStore';
import animationData from '@/static/animation/progress.json';
import LottieAnimations from '@/components/LottieAnimations.vue';
import { hexToRgba } from '@/utils/hexToRGB';

const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);

const props = defineProps({
  totalQuestions: {
    type: Number,
    required: true,
  },
  currentQuestionIndex: {
    type: Number,
    required: true,
  },
});

const progress = ref(0);

watch(
  [() => props.currentQuestionIndex, () => props.totalQuestions],
  ([currentIndex, totalQuestions]) => {
    progress.value = (currentIndex / totalQuestions) * 100;
  },
  { immediate: true }
);

const animationsData = ref(animationData);
</script>

<style scoped>
.lottie-animation {
  width: 40px;
  height: 40px;
}
</style>
