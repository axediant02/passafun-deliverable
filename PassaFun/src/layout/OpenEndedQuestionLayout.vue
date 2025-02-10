<template>
  <div class="h-full relative z-0">
    <div class="flex flex-col gap-2 py-2">
      <div class="flex rounded-md justify-center items-center py-2 h-[190px] font-nunito">
        <QuestionContainer class="w-full flex">
          <div v-if="question" key="question"
            class="break-words flex flex-col w-full justify-around items-center text-ellipsis h-full">
            <div v-if="question.question_image_url" class="mb-2">
              <div class="overflow-hidden flex place-items-center mx-auto justify-center rounded-md "
                :class="question.question_text ? 'max-h-[120px] h-[120px]' : 'max-h-[150px] h-[150px]'">
                <Image :src="question.question_image_url" preview class=" w-fit"
                  :width="question.question_text ? 120 : 150" />
              </div>
            </div>

            <div v-if="question.question_text" class="flex flex-col text-center text-lg font-medium leading-tight px-1"
              style="max-height: 63px; font-size: 18px">
              <div class="" :class="shouldUseLightText(quizTheme) ? 'text-white' : 'text-black'">
                {{ question.question_text }}
              </div>
            </div>
          </div>
        </QuestionContainer>
      </div>

      <Modal v-if="isModalVisible" :isVisible="isModalVisible" :imageSrc="selectedImage" @close="closeModal" />

      <div class="flex h-full w-full">
        <OpenEndedTextArea v-model="userResponse" :value="userResponse"
          class="open-ended-textarea w-full p-2 overflow-y-auto max-h-[200px]"
          :class="shouldUseLightText(quizTheme) ? 'text-white' : 'text-black'" backgroundColor="transparent"
          :borderColor="quizTheme.accent_color" :focusOutline="quizTheme.accent_color" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch, onBeforeUnmount } from 'vue';
import QuestionContainer from '../components/QuestionContainer.vue';
import OpenEndedTextArea from '../components/quiz/OpenEndedTextArea.vue';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { storeToRefs } from 'pinia';
import Modal from '../components/ui/Modal.vue';
import { useRoute } from 'vue-router';
import { Image } from 'primevue';
import { shouldUseLightText } from '@/utils/lumininanceChecker';

const route = useRoute();

const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);

const isKeyboardVisible = ref(false);
const originalWindowHeight = ref(window.innerHeight);

const handleResize = () => {
  const currentHeight = window.innerHeight;
  isKeyboardVisible.value = currentHeight < originalWindowHeight.value;
};

onMounted(() => {
  store.getQuizzesData();
  loadFromLocalStorage();
  window.addEventListener('resize', handleResize);
  originalWindowHeight.value = window.innerHeight;
});

const selectedImage = ref(null);
const isModalVisible = ref(false);

const openModal = (imageSrc) => {
  selectedImage.value = imageSrc;
  isModalVisible.value = true;
};

const closeModal = () => {
  selectedImage.value = null;
  isModalVisible.value = false;
};

const props = defineProps({
  question: Object,
});

const emit = defineEmits(['update:response']);

const userResponse = ref('');

const saveToSessionStorage = (data) => {
  const quizIdLocal = route.params.quizId;
  const quizResponses = JSON.parse(sessionStorage.getItem(`quiz_${quizIdLocal}`)) || {};
  quizResponses[`question_${props.question.id}`] = data.open_ended_response;
  sessionStorage.setItem(`quiz_${quizIdLocal}`, JSON.stringify(quizResponses));
};

const loadFromLocalStorage = () => {
  const quizIdLocal = route.params.quizId;
  const quizResponses = JSON.parse(sessionStorage.getItem(`quiz_${quizIdLocal}`)) || {};
  if (quizResponses[`question_${props.question.id}`]) {
    userResponse.value = quizResponses[`question_${props.question.id}`];
  }
};

watch(userResponse, (newValue) => {
  const responseData = {
    question_id: props.question.id,
    choice_id: null,
    open_ended_response: newValue,
    point: 0,
  };
  emit('update:response', responseData);
  saveToSessionStorage(responseData);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});
</script>

<style scoped>
.text-area {
  width: 100%;
}
</style>
