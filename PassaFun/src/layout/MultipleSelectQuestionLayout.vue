<template>
  <div class="h-full w-full flex flex-col gap-2 font-nunito">
    <div class="flex rounded-md justify-center items-center py-2 h-[190px]">
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

          <div v-if="question.question_text"
            class="flex flex-col text-center font-medium leading-tight px-1 overflow-y-auto hide-scrollbar" :style="{
              maxHeight: question.question_image_url ? '63px' : '100px',
              fontSize: '18px',
            }">
            <div class="" :class="shouldUseLightText(quizTheme) ? 'text-white' : 'text-black'">
              {{ question.question_text }}
            </div>
          </div>
        </div>
      </QuestionContainer>
    </div>

    <div class="w-full scrollable-container flex flex-col overflow-y-scroll ">
      <div :class="`flex-grow  flex flex-col   justify-center `" style="width: 100%">
        <MultipleSelectButton v-for="choice in question.choices" :key="choice.id"
          class="flex justify-center items-center w-full rounded-md border-2 my-1 relative overflow-hidden"
          :box-shadow-color="quizTheme.accent_color" :border-color="quizTheme.accent_color" text-color="black" :class="{
            'h-20': question.choices.length <= 3,
            'h-[70px]': question.choices.length === 4,
          }">
          <label :for="choice.id"
            class="flex items-center justify-center p-3 cursor-pointer relative transition duration-150 ease-in-out w-full h-full rounded-sm"
            :style="{
              backgroundColor: selectedChoice.includes(choice.id)
                ? quizTheme.main_color
                : 'transparent',
            }">
            <div class="flex items-center gap-2 w-full">
              <div class="flex-shrink-0 justify-start absolute">
                <label class="relative inline-block">
                  <input class="hidden" type="checkbox" :id="choice.id" :value="choice.id" v-model="selectedChoice"
                    :name="`question-${question.id}`" />
                  <span
                    class="inline-flex items-center justify-center w-[20px] h-[20px] border-2 rounded-full transition-colors duration-200"
                    :style="{
                      borderColor: selectedChoice.includes(choice.id)
                        ? 'white'
                        : quizTheme.accent_color,
                      backgroundColor: selectedChoice.includes(choice.id) ? 'white' : 'transparent',
                      boxShadow: selectedChoice.includes(choice.id)
                        ? `inset 0 0 0 2px ${quizTheme.accent_color}`
                        : 'none',
                    }">
                    <i class="pi pi-check pi-bold transition-opacity duration-200"
                      :class="selectedChoice.includes(choice.id) ? 'opacity-100' : 'opacity-0'" :style="{
                        color: quizTheme.accent_color,
                        fontSize: '10px',
                      }"></i>
                  </span>
                </label>
              </div>
              <span class="flex px-8 py-2 flex-col font-normal items-center w-full justify-start leading-none" :style="{
                color: selectedChoice.includes(choice.id)
                  ? isDarkColor(quizTheme.main_color)
                    ? 'white'
                    : 'black'
                  : shouldUseLightText(quizTheme)
                    ? '#e4e4e7'
                    : 'black',
                fontSize: '16px',
              }">
                <img v-if="choice.image" :src="choice.image" @click="openModal(choice.image)" alt="Choice Image"
                  class="object-cover max-h-16 mx-auto mb-2" />
                {{ choice.choice_text }}
              </span>
            </div>
          </label>
        </MultipleSelectButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import QuestionContainer from '../components/QuestionContainer.vue';
import MultipleSelectButton from '../components/quiz/MultipleSelectButton.vue';
import { storeToRefs } from 'pinia';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { useRoute } from 'vue-router';

import { Image } from 'primevue';
import { shouldUseLightText, isDarkColor } from '@/utils/lumininanceChecker';

const route = useRoute();

const props = defineProps({
  question: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['update:response']);

const selectedChoice = ref([]);

const saveToLocalStorage = () => {
  const quizIdLocal = route.params.quizId;
  const quizResponses = JSON.parse(sessionStorage.getItem(`quiz_${quizIdLocal}`)) || {};
  quizResponses[`question_${props.question.id}`] = selectedChoice.value;
  sessionStorage.setItem(`quiz_${quizIdLocal}`, JSON.stringify(quizResponses));
};

const loadFromLocalStorage = () => {
  const quizIdLocal = route.params.quizId;
  const quizResponses = JSON.parse(sessionStorage.getItem(`quiz_${quizIdLocal}`)) || {};
  if (quizResponses[`question_${props.question.id}`]) {
    selectedChoice.value = quizResponses[`question_${props.question.id}`];
  }
};

watch(selectedChoice, (newValue) => {
  let totalPoints = 0;
  newValue.forEach((choiceId) => {
    const selectedChoice = props.question.choices.find((choice) => choice.id === choiceId);
    totalPoints += selectedChoice.points;
  });

  emit('update:response', {
    question_id: props.question.id,
    choice_id: newValue,
    open_ended_response: null,
    point: totalPoints,
  });
  saveToLocalStorage();
});

onMounted(() => {
  loadFromLocalStorage();
});

watch(
  () => props.question,
  () => {
    loadFromLocalStorage();
  }
);

const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);
</script>

<style scoped></style>
