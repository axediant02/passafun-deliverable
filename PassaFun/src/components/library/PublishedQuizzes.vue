<template>
  <div class="w-full flex flex-col sm:gap-2 pb-12">
    <router-link v-for="(quiz, index) in quizzes" :key="quiz.id" :data-index="index" :to="`/${quiz.uid}`"
      @click="handleAllQuizCardAction(quiz.id)"
      class="flex items-center gap-3 my-1 transition-all duration-300 ease-in-out relative z-10 lg:hover:bg-blue-50 rounded-[12px] pb-[6px]">
      <div class="masked-card rounded-[11px] max-w-16 aspect-square object-cover quiz-card select-none">
        <img class="object-cover aspect-square rounded-[10px] w-full" :src="quiz.thumbnail_url" alt="Quiz Thumbnail" />
      </div>
      <div class="flex flex-col gap-1">
        <h2 class="max-w-52 text-wrap leading-none sm:max-w-72 font-nunito font-bold">
          {{ quiz.name }}
        </h2>
        <small class="opacity-75 flex-wrap text-sm leading-none text-wrap font-semibold font-nunito">
          {{ quiz.landing_page.sub_header }}
        </small>
      </div>
    </router-link>
    <div v-if="isLoading" class="w-full flex justify-center">
      <slot name="quizLoading"></slot>
    </div>
    <div v-if="errorMessage" class="w-full text-center text-error">
      <slot name="quizError"></slot>
    </div>
  </div>
</template>

<script setup>
import { useQuizzesStore } from "@/stores/quizzesStore";
const store = useQuizzesStore();

const props = defineProps({
  isLoading: {
    type: Boolean,
    required: true,
  },
  quizzes: {
    type: Array,
    required: true,
  },
  errorMessage: {
    type: [Boolean, String],
    required: false,
  },
});

const handleAllQuizCardAction = (quizId) => {
  store.setQuizId(quizId);
};
</script>
