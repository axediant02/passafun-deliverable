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
        <SingleSelectButton v-for="(choice, index) in question.choices" :key="index"
          class="flex justify-center items-center w-full rounded-md border-2 my-1 overflow-hidden"
          :box-shadow-color="quizTheme.accent_color" :border-color="quizTheme.accent_color" text-color="black" :class="{
            'h-20': question.choices.length <= 3,
            'h-[70px]': question.choices.length === 4,
          }">
          <label
            class="flex items-center justify-center cursor-pointer relative transition duration-150 ease-in-out w-full h-full"
            :style="{
              backgroundColor: selectedChoice === choice.id ? quizTheme.main_color : 'transparent',
            }" :class="{
              'hover:bg-purple-400': true,
            }">
            <input type="radio" :id="choice.id" :value="choice.id" v-model="selectedChoice" class="peer hidden" />
            <span class="flex items-center h-full w-full flex-col justify-center py-2 leading-tight px-1" :style="{
              fontSize: '14px',
              color:
                selectedChoice === choice.id
                  ? isDarkColor(quizTheme.main_color)
                    ? 'white'
                    : 'black'
                  : shouldUseLightText(quizTheme)
                    ? '#e4e4e7'
                    : 'black',
            }">
              <img v-if="choice.image" :src="choice.image" @click="openModal(choice.image)"
                class="object-cover max-h-20 mb-4" />
              {{ choice.choice_text }}
            </span>
          </label>
        </SingleSelectButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { storeToRefs } from 'pinia';

import QuestionContainer from '@/components/QuestionContainer.vue';
import { useQuizzesStore } from '@/stores/quizzesStore';
import SingleSelectButton from '@/components/quiz/SingleSelectButton.vue';
import { useRoute } from 'vue-router';
import { Image } from 'primevue';
import { isDarkColor, shouldUseLightText } from '@/utils/lumininanceChecker';

const route = useRoute();

const props = defineProps({
  question: {
    type: Object,
    required: true,
  },
});

const selectedImage = ref(null);
const isModalVisible = ref(false);
const selectedChoice = ref(null);

const openModal = (imageSrc) => {
  selectedImage.value = imageSrc;
  isModalVisible.value = true;
};

const closeModal = () => {
  selectedImage.value = null;
  isModalVisible.value = false;
};

const emit = defineEmits(['update:response']);

const saveToSessionStorage = () => {
  const quizIdLocal = route.params.quizId;
  const responses = JSON.parse(sessionStorage.getItem(`quiz_${quizIdLocal}`)) || {};
  responses[`question_${props.question.id}`] = selectedChoice.value;
  sessionStorage.setItem(`quiz_${quizIdLocal}`, JSON.stringify(responses));
};

const loadFromLocalStorage = () => {
  const quizIdLocal = route.params.quizId;
  const responses = JSON.parse(sessionStorage.getItem(`quiz_${quizIdLocal}`)) || {};
  if (responses[`question_${props.question.id}`] !== undefined) {
    selectedChoice.value = responses[`question_${props.question.id}`];
  }
};

watch(selectedChoice, (newValue) => {
  const selectedChoiceData = props.question.choices.find((choice) => choice.id === newValue);

  emit('update:response', {
    question_id: props.question.id,
    choice_id: [newValue],
    open_ended_response: null,
    point: selectedChoiceData?.points || selectedChoice.points || 0,
  });

  saveToSessionStorage();
});

onMounted(() => {
  loadFromLocalStorage();
});

const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);
</script>
