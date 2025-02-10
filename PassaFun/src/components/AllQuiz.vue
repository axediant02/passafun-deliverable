<template>
  <div class="w-full flex flex-col sm:gap-2">
    <div v-if="isLoading" class="w-full flex justify-center">
      <slot name="allQuizLoading"></slot>
    </div>

    <router-link
      v-for="(quiz, index) in allQuizData"
      :key="quiz.id"
      :data-index="index"
      :to="`/${quiz.uid}`"
      class="flex items-center gap-2 p-2 my-1 rounded-md quiz-card transition-all duration-300 ease-in-out hover:shadow-sm relative"
    >
      <img
        class="object-cover aspect-square w-16 rounded-[10px]"
        :src="quiz.thumbnail_url"
        alt="Quiz Thumbnail"
      />
      <div class="flex flex-col gap-1">
        <h2 class="font-[400] max-w-44 text-wrap leading-none sm:max-w-72">
          {{ quiz.name }}
        </h2>
        <small class="opacity-60 max-w-44 flex-wrap leading-none text-wrap sm:max-w-72">{{
          quiz.landing_page.sub_header
        }}</small>
      </div>
    </router-link>

    <div v-if="isError" class="w-full text-center text-error">
      <slot name="allQuizError"></slot>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  isLoading: {
    type: Boolean,
  },
  allQuizData: {
    type: Array,
  },
  isError: {
    type: Boolean || String,
  },
});
</script>
