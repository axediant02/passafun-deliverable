<template>
  <div class="h-full flex flex-col gap-2 p-2 font-nunito">
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

    <div class="flex flex-col items-center">
      <div class="flex justify-center items-center gap-x-6 flex-row">
        <div v-for="(choice, index) in question.choices" :key="choice.id" class="flex flex-col items-center">
          <RatingScaleButton :activeBackgroundColor="quizTheme.main_color" :backgroundColor="quizTheme.background_value"
            :text-color="quizTheme.text_color" :active="selectedChoice === choice.id" :border="quizTheme.accent_color"
            :boxShadowColor="quizTheme.accent_color" class="flex flex-col items-center justify-center" :style="{
              width:
                index === 0 || index === 4 ? '45px' : index === 1 || index === 3 ? '38px' : '33px',
              height:
                index === 0 || index === 4 ? '45px' : index === 1 || index === 3 ? '38px' : '33px',
            }">
            <label :for="`scale-${index}`" class="cursor-pointer w-full h-full flex items-center justify-center">
              <input class="custom-radio peer hidden" type="radio" name="scale" :value="choice.id"
                v-model="selectedChoice" :id="`scale-${index}`" />
            </label>
          </RatingScaleButton>
        </div>
      </div>

      <div class="flex justify-center items-center gap-x-6 flex-row mt-2">
        <div v-for="(choice, index) in question.choices" :key="`label-${choice.id}`" class="flex flex-col items-center"
          :style="{
            width:
              index === 0 || index === 4 ? '45px' : index === 1 || index === 3 ? '40px' : '35px',
          }">
          <span v-if="index === 0 || index === question.choices.length - 1" class="text-center w-20 leading-none"
            style="font-size: 14px" :class="shouldUseLightText(quizTheme) ? 'text-white' : 'text-black'">
            {{ choice.choice_text }}
          </span>
          <span v-else class="invisible"> &nbsp; </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import QuestionContainer from '@/components/QuestionContainer.vue';
import RatingScaleButton from '@/components/quiz/RatingScaleButton.vue';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { storeToRefs } from 'pinia';
import { useRoute } from 'vue-router';
import { Image } from 'primevue';
import { shouldUseLightText } from '@/utils/lumininanceChecker';

const route = useRoute();

const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);

onMounted(() => {
  store.getQuizzesData();
});

const props = defineProps({
  question: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['update:response']);

const selectedChoice = ref('');

const saveToSessionStorage = () => {
  const quizIdLocal = route.params.quizId;
  const responses = JSON.parse(sessionStorage.getItem(`quiz_${quizIdLocal}`)) || {};
  responses[`question_${props.question.id}`] = selectedChoice.value;
  sessionStorage.setItem(`quiz_${quizIdLocal}`, JSON.stringify(responses));
};

const loadFromLocalStorage = () => {
  const quizIdLocal = route.params.quizId;
  const responses = JSON.parse(sessionStorage.getItem(`quiz_${quizIdLocal}`)) || {};
  if (responses[`question_${props.question.id}`]) {
    selectedChoice.value = responses[`question_${props.question.id}`];
  }
};

watch(selectedChoice, (newValue) => {
  const selected = props.question.choices.find((choice) => choice.id === newValue);

  emit('update:response', {
    question_id: props.question.id,
    choice_id: [newValue],
    open_ended_response: null,
    point: selected.points,
  });

  saveToSessionStorage();
});

onMounted(() => {
  loadFromLocalStorage();
});
</script>
