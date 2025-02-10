<template>
  <div class="flex flex-col gap-2 pl-4 md:px-4">
    <div class="flex flex-row justify-between">
      <slot name="new-releases-category"></slot>
      <div class="flex gap-1" v-if="isDesktop">
        <button
          class="hover:bg-slate-200 transition-all duration-300 ease-in-out rounded-sm p-1"
          @click="scrollLeft"
        >
          <ChevronLeft color="#5197FF" />
        </button>
        <button
          class="hover:bg-slate-200 transition-all duration-300 ease-in-out rounded-sm p-1"
          @click="scrollRight"
        >
          <ChevronRight color="#5197FF" />
        </button>
      </div>
    </div>
    <div v-if="errorMessage" class="pr-4 w-full text-center text-error">
      <slot name="quizError"></slot>
    </div>

    <div class="relative">
      <div ref="scrollContainer" class="flex gap-2 overflow-x-auto hide-scrollbar">
        <div v-if="quizzes && !isLoading" class="w-fit flex gap-3">
          <router-link
            v-for="(quiz, index) in quizzes"
            :key="quiz.id"
            :to="`/${quiz.uid}`"
            @click="handlePopularQuizCardAction(quiz.id)"
            class="flex rounded-md transition select-none relative"
          >
            <div
              class="absolute top-0 left-0 h-6 w-6 rounded-tl-md bg-[#5197FF] pt-[6px] z-10 text-center text-white rounded-br-sm text-xs"
            >
              0{{ index + 1 }}
            </div>
            <div class="flex flex-col gap-1">
              <div
                class="masked-card rounded-[18px] bg-white w-28 sm:w-44 aspect-square quiz-card select-none"
              >
                <template v-if="quiz.thumbnail_url">
                  <img
                    class="object-cover aspect-square w-28 sm:w-44 rounded-md select-none"
                    :src="quiz.thumbnail_url"
                  />
                </template>
                <template v-else>
                  <ImagePlaceholderSmall />
                </template>
              </div>
              <div class="flex flex-col">
                <h2
                  class="text-sm max-w-28 w-28 sm:w-44 sm:max-w-48 text-wrap leading-none p-1 font-nunito font-bold text-center"
                >
                  {{ quiz.name }}
                </h2>
              </div>
            </div>
          </router-link>
        </div>
        <div v-if="isLoading" class="w-full flex justify-center">
          <slot name="quizLoading"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import screenSizeUtils from '@/utils/screenSize';
import { vibrate } from '@/utils/vibrationButton';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';
import ImagePlaceholderSmall from '../ui/ImagePlaceholderSmall.vue';
import { useQuizzesStore } from '@/stores/quizzesStore';

const store = useQuizzesStore();

const props = defineProps({
  isLoading: {
    type: Boolean,
    default: false,
  },
  quizzes: {
    type: Array,
  },
  errorMessage: {
    type: [Boolean, String],
    default: false,
  },
});

const scrollContainer = ref(null);

const isDesktop = ref(false);

const updateScreenSize = () => {
  isDesktop.value = screenSizeUtils.isDesktop();
};

onMounted(() => {
  updateScreenSize();
  window.addEventListener('resize', updateScreenSize);
});

onUnmounted(() => {
  window.removeEventListener('resize', updateScreenSize);
});

const scrollLeft = () => {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: -200, behavior: 'smooth' });
  }
};

const scrollRight = () => {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: 200, behavior: 'smooth' });
  }
};
const handleVibration = () => {
  vibrate(40);
};

const handlePopularQuizCardAction = (quizId) => {
  handleVibration();
  store.setQuizId(quizId);
};
</script>
