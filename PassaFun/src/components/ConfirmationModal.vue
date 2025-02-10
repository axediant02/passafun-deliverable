<template>
  <div class="card flex justify-center select-none w-full">
    <div
      class="flex gap-1 flex-row place-items-center w-full justify-center font-nunito font-bold text-lg cursor-pointer"
      :style="{ color: quizTheme.button_color }" @click="visible = true">
      <slot name="confirmation-modal-button"></slot>
    </div>
    <Dialog v-model:visible="visible" modal :style="{ width: '25rem' }" :header="headerText" class="m-4">
      <span class="text-surface-500 dark:text-surface-400 block mb-8">
        {{ modalContent }}
      </span>
      <div class="flex justify-between font-nunito flex-row place-items-center gap-2 w-full">
        <Button ::boxShadowColor="quizTheme.accent_color" :boxShadowColor="quizTheme.accent_color"
          :borderColor="quizTheme.accent_color" :textColor="quizTheme.main_color" type="button" severity="secondary"
          @click="visible = false" class="w-[75%] font-nunito text-md font-bold pr-2">
          <span class="pl-1 pt-[2px]">Cancel</span>
        </Button>

        <router-link :to="getRouterLink()" class="w-full">
          <Button
            :backgroundColor="quizTheme.main_color"
            :boxShadowColor="quizTheme.accent_color"
            :borderColor="quizTheme.accent_color"
            :textColor="isDarkColor(quizTheme.button_color) ? 'white' : 'black'"
            type="button"
            severity="secondary"
            @click="handleYesButtonAction"
            class="w-full font-nunito text-md font-bold pr-2"
          >
            <span class="pl-1 pt-[2px]"> {{ PrimaryButtonText }} </span>
          </Button>
        </router-link>
      </div>
    </Dialog>
  </div>
</template>

<script setup>
import { useQuizzesStore } from '@/stores/quizzesStore';
import { isDarkColor } from '@/utils/lumininanceChecker';
import { storeToRefs } from 'pinia';
import { Dialog } from 'primevue';
import { ref } from 'vue';
import Button from './ui/Button.vue';
import { removeQuizInSessionStorage } from '@/utils/sessionStorage';
import { useRoute } from 'vue-router';

const route = useRoute();
const visible = ref(false);
const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);

const props = defineProps({
  headerText: {
    type: String,
    default: 'Confirmation',
  },
  PrimaryButtonRoute: {
    type: [String, Object],
    default: 'library',
  },
  PrimaryButtonText: {
    type: String,
    default: 'Go to home',
  },
  modalContent: {
    type: String,
    default: 'Are you sure?',
  },
});

const getRouterLink = () => {
  if (typeof props.PrimaryButtonRoute === 'string') {
    return { 
      name: props.PrimaryButtonRoute,
      query: route.query
    };
  } else {
    return { 
      ...props.PrimaryButtonRoute,
      query: {
        ...route.query,
        ...props.PrimaryButtonRoute?.query
      }
    };
  }
};

const handleYesButtonAction = () => {
  visible.value = false;
  removeQuizInSessionStorage(
    `quiz_${route.params.quizId}`,
    `getResult_${route.params.quizId}`,
    `user_responses_${route.params.quizId}`,
    `quiz_status_${route.params.quizId}`
  );
};
</script>
