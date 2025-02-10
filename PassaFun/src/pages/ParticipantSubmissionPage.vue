<template>
  <PageContainer class="select-none">
    <PageHeader :to="{ name: `questions-page`, query: { preview: route.query.preview } }" v-if="quizTheme" />
    <div v-else class="h-[55px]"></div>
    <div class="flex flex-col h-full px-4 w-full overflow-y-scroll justify-between place-items-center hide-scrollbar">
      <div class="flex flex-col place-items-center justify-between h-full w-full">
        <div class="flex flex-col justify-around h-full w-full">
          <div class="flex flex-col" v-if="quizTheme">
            <div class="mx-auto rounded-md overflow-hidden h-40 sm:h-48 flex place-items-center">
              <LottieAnimations :animationData="participantSubmissionPage.getJsonAnimationData"
                v-if="participantSubmissionPage.image_type_id === 2" />
              <span v-else>
                <AvatarPlaceholder v-if="!participantSubmissionPage.getResultImageUrl" :bgColor="quizTheme.main_color"
                  class="aspect-square object-cover rounded-md max-w-36 sm:max-w-48" />
                <img v-else :src="participantSubmissionPage.getResultImageUrl"
                  class="aspect-square object-cover rounded-md max-w-36 sm:max-w-48 sm:my-1" />
              </span>
            </div>
            <Card class="text-2xl text-center font-nunito font-extrabold sm:py-2" :textColor="quizTheme.main_color">
              {{ participantSubmissionPage.header }}
            </Card>
          </div>

          <div v-else class="flex flex-col gap-2">
            <div class="mx-auto rounded-md overflow-hidden h-52 flex-col gap-2 w-64 px-2 flex place-items-center">
              <Skeleton size="10rem" />
              <Skeleton width="100%" height="2rem" />
            </div>
          </div>
          <div class="flex flex-col w-full mx-auto place-items-center">
            <div v-if="!participantSubmissionPage.input_forms" class="flex flex-col gap-2 max-w-md w-full py-4">
              <div class="relative">
                <div class="absolute top-5">
                  <Skeleton width="8rem" height="2rem" class="my-auto left-8" />
                </div>
                <div class="absolute w-full h-full p-[2px]">
                  <Skeleton width="100%" height="100%" class="m-auto" />
                </div>

                <Skeleton width="100%" height="4rem" />
              </div>

              <div class="relative">
                <div class="absolute top-5">
                  <Skeleton width="8rem" height="2rem" class="my-auto left-8" />
                </div>
                <div class="absolute w-full h-full p-[2px]">
                  <Skeleton width="100%" height="100%" class="m-auto" />
                </div>

                <Skeleton width="100%" height="4rem" />
              </div>
            </div>
            <InputLayout ref="inputLayoutRef" v-else :inputForms="participantSubmissionPage.input_forms"
              :acceptedTerms="acceptedTerms" @emittedIsTermsAccepted="setWhenToDisplayAcceptedError"
              @emittedKeyPressEnter="getResultAction" @update:fieldsData="updateFieldsData"
              @update:areFieldsPopulated="updateStatusOfInputField" class="w-full" />

            <div class="flex place-items-center" v-if="quizTheme">
              <div class="flex gap-1 flex-row place-items-center text-nowrap">
                <Checkbox v-model="acceptedTerms" binary class="bg-transparent" />
                <label class="flex flex-row gap-1">
                  <span :class="shouldUseLightText(quizTheme) ? 'text-white' : 'text-black'">
                    I accept the
                  </span>
                  <span class="underline w-fit">
                    <router-link :to="`/${route.params.quizId}/terms`" :style="{ color: quizTheme.button_color }">
                      Terms and Conditions
                    </router-link>
                  </span>
                </label>
              </div>
            </div>
            <div class="flex justify-center gap-2 w-full px-8 place-items-center" v-else>
              <Skeleton width="1.7rem" height="1.5rem" />
              <Skeleton width="100%" height="1.3rem" />
            </div>
            <div class="h-4 flex place-items-center">
              <span v-if="displayAcceptedTermsError" class="text-error text-center text-xs">
                You must accept the terms to proceed.
              </span>
            </div>
          </div>
        </div>

        <div class="max-w-sm mb-2 w-full" v-if="quizTheme" id="bottomDivElement" ref="bottomDivElement">
          <Button :boxShadowColor="!isSubmissionButtonDisabled ? quizTheme.accent_color : 'transparent'"
            :backgroundColor="!isSubmissionButtonDisabled ? quizTheme.button_color : '#d1d5db'"
            :boxShadowBottom="!isSubmissionButtonDisabled ? '4px' : '0'"
            :borderColor="!isSubmissionButtonDisabled ? quizTheme.accent_color : 'transparent'"
            class="rounded-md text-xl font-nunito font-extrabold w-full" :disabled="isSubmissionButtonDisabled"
            @click="getResultAction" :loading="isParticipantSubmitting"
            :class="isDarkColor(quizTheme.button_color) ? 'text-white' : 'text-black'">
            {{ participantSubmissionPage.button_text }}
          </Button>
        </div>
        <div v-else class="max-w-sm w-full">
          <Skeleton width="100%" height="3.3rem" />
        </div>
      </div>
    </div>
  </PageContainer>
</template>

<script setup>
import PageContainer from '@/components/PageContainer.vue';
import Button from '@/components/ui/Button.vue';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { storeToRefs } from 'pinia';
import Card from '@/components/ui/Card.vue';
import InputLayout from '@/components/InputLayout.vue';
import { onMounted, ref, watch, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useRouter } from 'vue-router';
import LottieAnimations from '@/components/LottieAnimations.vue';
import { generateUniqueResultId } from '@/utils/generateId';
import { addAllIntegersInArray } from '@/utils/calculateArrayValues';
import { removeQuizInSessionStorage } from '@/utils/sessionStorage';
import PageHeader from '@/components/PageHeader.vue';
import { Checkbox, Skeleton } from 'primevue';
import AvatarPlaceholder from '@/components/ui/AvatarPlaceholder.vue';
import { isDarkColor, shouldUseLightText } from '@/utils/lumininanceChecker';
import { useLoadingStore } from '@/stores/useLoadingStore';

const route = useRoute();
const router = useRouter();

const store = useQuizzesStore();
const {
  quizTheme,
  participantSubmissionPage,
  quizId,
  isQuizPublished,
  quizDetail,
  isQuizNewlyFetched,
  currentQuizUID,
} = storeToRefs(store);

const getSessionStoredUserResponses = () => {
  return JSON.parse(sessionStorage.getItem(`user_responses_${route.params.quizId}`)) || {};
};

const getAllSessionStoredPoints = () => {
  const answersArray = Object.values(getSessionStoredUserResponses().answers);
  const points = answersArray.map((answer) => answer.point);
  return points;
};

const displayAcceptedTermsError = ref(false);
const setWhenToDisplayAcceptedError = (isAccepted) => {
  if (isAccepted) {
    displayAcceptedTermsError.value = true;
  } else {
    displayAcceptedTermsError.value = false;
  }
};

const participantData = ref({
  quiz_id: null,
  full_name: null,
  email: null,
  contact_number: null,
  age: null,
  participant_score: null,
  answers: null,
  unique_result_id: null,
});

const updateFieldsData = (response) => {
  sessionStorage.setItem(`getResult_${route.params.quizId}`, JSON.stringify(response));
};

const navigateToResults = (uniqueResultId) => {
  isParticipantSubmitting.value = false;
  router.push(`/${route.params.quizId}/r/${uniqueResultId}`);
  removeQuizInSessionStorage(
    `quiz_${route.params.quizId}`,
    `getResult_${route.params.quizId}`,
    `user_responses_${route.params.quizId}`,
    `quiz_status_${route.params.quizId}`
  );
};

const updateStatusOfInputField = (status) => {
  areInputFieldsComplete.value = status;
};

const acceptedTerms = ref(false);
const areInputFieldsComplete = ref(false);
const isParticipantSubmitting = ref(false);
const isSubmissionButtonDisabled = computed(() => {
  return !areInputFieldsComplete.value || !acceptedTerms.value || isParticipantSubmitting.value;
});

const loadingStore = useLoadingStore();

const takeScreenshot = async () => {
  loadingStore.startLoading();
  try {
    await axios.post(
      `api/screenshot/${currentQuizUID.value}/${participantData.value.unique_result_id}`
    );
  } catch (error) {
    console.error('Error capturing screenshot:', error);
  } finally {
    loadingStore.stopLoading();
  }
};

const inputLayoutRef = ref(null);

const getResultAction = async () => {
  if (store.isPreviewMode) {
    removeQuizInSessionStorage(
      `quiz_${route.params.quizId}`,
      `getResult_${route.params.quizId}`,
      `user_responses_${route.params.quizId}`,
      `quiz_status_${route.params.quizId}`
    );
    router.push({
      path: `/preview-result`,
      query: { preview: route.query.preview },
    });
    return;
  }

  if (isParticipantSubmitting.value) return;
  isParticipantSubmitting.value = true;

  const isEnteredDataValid = inputLayoutRef.value.validateFields();
  if (!isEnteredDataValid) {
    scrollToElement()
    isParticipantSubmitting.value = false;
    return;
  }

  try {
    const userStoredFormsData =
      JSON.parse(sessionStorage.getItem(`getResult_${route.params.quizId}`)) || [];
    const getFieldValue = (type) =>
      userStoredFormsData.find((field) => field.input_type === type)?.input_value || '';

    participantData.value.quiz_id = quizId.value;
    participantData.value.full_name = getFieldValue('text');
    participantData.value.email = getFieldValue('email');
    participantData.value.contact_number = getFieldValue('tel');
    participantData.value.age = getFieldValue('age');
    participantData.value.participant_score = addAllIntegersInArray(getAllSessionStoredPoints());
    participantData.value.answers = getSessionStoredUserResponses().answers;
    participantData.value.unique_result_id = generateUniqueResultId();

    await store.checkQuizPublishStatus();
    if (isQuizPublished.value) {
      const response = await axios.post('api/participant-quiz-summary', participantData.value);

      if (response && response.data) {
        takeScreenshot();
        navigateToResults(participantData.value.unique_result_id);
      }
    }
  } catch (error) {
    console.error('Error submitting form:', error);
  } finally {
    isParticipantSubmitting.value = false;
  }
};

const redirectIfQuizIsUnpublished = () => {
  if (!isQuizPublished.value) {
    removeQuizInSessionStorage(
      `quiz_${route.params.quizId}`,
      `getResult_${route.params.quizId}`,
      `user_responses_${route.params.quizId}`,
      `quiz_status_${route.params.quizId}`
    );
    router.push('/unavailable-quiz');
  }
};

watch(
  () => isQuizPublished.value,
  () => {
    redirectIfQuizIsUnpublished();
  }
);

const bottomDivElement = ref(null);
const scrollToElement = () => {
  bottomDivElement.value?.scrollIntoView({
    behavior: 'smooth',
    block: 'start',
  });
};

watch(
  () => acceptedTerms.value,
  () => {
    displayAcceptedTermsError.value = false;
  }
);

onMounted(() => {
  if (route.query.preview === 'true') {
    store.setPreviewMode(true);
  }
  if (!store.isPreviewMode) {
    if (quizId.value) {
      store.checkQuizPublishStatus();
    }
    if (!isQuizPublished.value) {
      redirectIfQuizIsUnpublished();
    }
  }
  if (!isQuizNewlyFetched.value) {
    store.getQuizzesData();
  }
  if (quizDetail.value) {
    store.getQuizzesData();
  }
});
</script>
