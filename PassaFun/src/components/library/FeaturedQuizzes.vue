<template>
  <div
    class="carousel-container relative py-4 sm:rounded-sm overflow-hidden"
    :class="errorMessage || isLoading ? '' : 'bg-library '"
    @touchstart="handleTouchStart"
    @touchend="handleTouchEnd"
  >
    <div
      class="w-full px-4 py-1"
      :class="errorMessage || isLoading ? 'text-library' : 'text-white'"
    >
      <slot name="featured-category"></slot>
    </div>
    <div class="hidden lg:flex absolute right-2 lg:-mt-6 z-20">
      <button
        class="hover:bg-slate-200 transition-all duration-300 ease-in-out rounded-sm p-1"
        @click="moveLeft"
      >
        <ChevronLeft color="white" />
      </button>
      <button
        class="hover:bg-slate-200 transition-all duration-300 ease-in-out rounded-sm p-1"
        @click="moveRight"
      >
        <ChevronRight color="white" />
      </button>
    </div>
    <div v-if="errorMessage" class="px-4 w-full text-center text-error mr-4">
      <slot name="quizError"></slot>
    </div>
    <div class="place-items-center relative" :class="errorMessage ? '' : 'carousel'">
      <div v-if="isLoading" class="w-full absolute flex justify-center rounded-md overflow-hidden">
        <slot name="quizLoading"></slot>
      </div>
      <router-link
        :to="`/${quiz.uid}`"
        v-for="(quiz, index) in quizzes"
        :key="quiz.id"
        :class="['carousel-item', getPositionClass(index)]"
        class="rounded-md overflow-hidden"
        @click="handleFeaturedQuizCardAction(quiz.id)"
      >
        <section
          class="game-section rounded-md flex flex-col justify-center place-items-center overflow-hidden bg-gray-200"
        >
          <div
            class="item aspect-square flex place-items-end relative cursor-pointer rounded-md shadow-lg overflow-hidden quiz-card-featured w-48"
            :style="{
              backgroundImage: `url(${quiz.cover_image_url || './images/image-placeholder.svg'})`,
            }"
          >
            <div class="absolute"></div>

            <div class="item-desc w-full relative z-10">
              <div class="flex place-items-center justify-between p-2 pt-2 gap-2 relative">
                <div class="flex place-items-center flex-row gap-2">
                  <div class="rounded-sm bg-white w-8 sm:w-8 overflow-hidden aspect-square">
                    <img
                      class="object-cover aspect-square w-8 sm:w-8 rounded-sm"
                      :src="quiz.thumbnail_url"
                    />
                  </div>
                  <h3 class="font-nunito text-xs font-bold leading-none">
                    {{ quiz.name }}
                  </h3>
                </div>
              </div>
            </div>
          </div>
        </section>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { vibrate } from '@/utils/vibrationButton';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';
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

const currentIndex = ref(0);
let autoplayInterval = null;

const moveLeft = () => {
  currentIndex.value = (currentIndex.value - 1 + props.quizzes.length) % props.quizzes.length;
};

const moveRight = () => {
  currentIndex.value = (currentIndex.value + 1) % props.quizzes.length;
};

const getPositionClass = (index) => {
  const total = props.quizzes.length;
  if (index === currentIndex.value) return 'main-pos';
  if (index === (currentIndex.value - 1 + total) % total) return 'left-pos';
  if (index === (currentIndex.value + 1) % total) return 'right-pos';
  return 'back-pos';
};

const startAutoplay = () => {
  autoplayInterval = setInterval(moveRight, 3000);
};

const stopAutoplay = () => {
  clearInterval(autoplayInterval);
};

const handleFeaturedQuizCardAction = (quizId) => {
  handleVibration();
  store.setQuizId(quizId);
};

const handleVibration = () => {
  vibrate(40);
};

let startTouch = 0;
const handleTouchStart = (event) => {
  startTouch = event.touches[0].clientX;
};

const handleTouchEnd = (event) => {
  const endTouch = event.changedTouches[0].clientX;
  const swipeDistance = startTouch - endTouch;

  if (swipeDistance > 50) {
    moveRight();
  } else if (swipeDistance < -50) {
    moveLeft();
  }
};

onMounted(() => {
  startAutoplay();
});

onUnmounted(() => {
  stopAutoplay();
});
</script>
