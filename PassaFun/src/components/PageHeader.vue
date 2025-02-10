<template>
  <div
    class="w-screen sm:w-full max-h-[52px] min-h-[52px] h-full px-4 sm relative"
    :style="{
      backgroundColor: enableBackgroundColor ? quizTheme.main_color : 'transparent',
    }"
  >
    <div class="max-w-sm lg:max-w-2xl mx-auto h-full flex place-items-center justify-center">
      <div
        v-if="showTitle"
        class="text-lg font-nunito font-extrabold"
        :class="{
          'text-white': enableBackgroundColor,
          'text-black': !enableBackgroundColor,
        }"
      >
        {{ quizDetail.name }}
      </div>
    </div>
    <router-link
      v-if="hasRoute"
      :to="getRouterLink()"
      class="w-full cursor-pointer"
    >
      <div
        class="top-0 flex place-items-center cursor-pointer h-full left-2 absolute text-sm font-nunito font-bold"
      >
        <slot name="nav-icon">
          <ChevronLeft :color="enableBackgroundColor ? 'white' : chevronColor" />
        </slot>
        <span
          class="-ml-[6px] mt-[2px]"
          :style="enableBackgroundColor ? { color: 'white' } : { color: chevronColor }"
        >{{ navigationText }}</span>
      </div>
    </router-link>
    <div v-else>
      <div
        class="top-0 flex place-items-center cursor-pointer h-full left-2 absolute text-sm font-nunito font-bold"
      >
        <slot name="nav-icon">
          <ArrowLeft :color="enableBackgroundColor ? 'white' : chevronColor" />
        </slot>
        <span
          class="-ml-[6px] mt-[2px]"
          :style="enableBackgroundColor ? { color: 'white' } : { color: chevronColor }"
        >{{ navigationText }}</span>
      </div>
    </div>
    <!-- <span v-if="currentQuestionIndex > 0">
      <ConfirmationModal
        :PrimaryButtonRoute="to"
        PrimaryButtonText="Yes, leave quiz"
        headerText="Leave Quiz?"
        modalContent="You haven't finished the quiz yet. Are you sure you want to leave? Your progress will not be saved."
      >
        <template #confirmation-modal-button>
          <div
            class="top-0 flex place-items-center h-full left-2 absolute text-sm font-nunito font-bold cursor-pointer hover:opacity-90"
          >
            <ArrowLeft :color="enableBackgroundColor ? 'white' : chevronColor" />
            <span
              class="-ml-[6px] mt-[2px]"
              :style="enableBackgroundColor ? { color: 'white' } : { color: chevronColor }"
            >{{ navigationText }}</span>
          </div>
        </template>
      </ConfirmationModal>
    </span> -->
  </div>
</template>

<script setup>
import { useQuizzesStore } from '@/stores/quizzesStore';
import { ArrowLeft, ChevronLeft } from 'lucide-vue-next';
import { storeToRefs } from 'pinia';
// import ConfirmationModal from './ConfirmationModal.vue';

const store = useQuizzesStore();
const { quizTheme, quizDetail } = storeToRefs(store);

const props = defineProps({
  to: {
    type: [String, Object],
    default: 'library',
  },
  showTitle: {
    type: Boolean,
    default: true,
  },
  enableBackgroundColor: {
    type: Boolean,
    default: true,
  },
  chevronColor: {
    type: String,
    default: '#5197FF',
  },
  navigationText: {
    type: String,
    default: '',
  },
  currentQuestionIndex: {
    type: Number,
    default: 0,
  },
  hasRoute: {
    type: Boolean,
    default: true,
  }
});

const getRouterLink = () => {
  if (typeof props.to === 'string') {
    return { name: props.to };
  } else {
    return { ...props.to };
  }
};
</script>
