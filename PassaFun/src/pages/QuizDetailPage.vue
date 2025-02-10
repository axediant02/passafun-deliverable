<template>
  <PageContainer>
    <div class="background-image-container fixed h-[100%] w-screen flex flex-col max-w-lg">
      <div class="flex flex-col">
        <PageHeader to="library" class="relative select-none z-10 px-4 flex flex-col">
          <template #nav-icon>
            <ChevronLeft size="26" class="text-white -mr-1" />
            <img src="/images/passafun.png" alt="Passafun Logo" class="h-[30px] w-[30px]" />
          </template>
        </PageHeader>
        <div class="flex flex-col gap-2 justify-between place-items-center w-full px-4">
          <template v-if="isLoading">
            <div class="flex flex-col py-[1px] gap-2 justify-center place-items-center">
              <Skeleton size="8rem" />
              <div class="flex flex-col place-items-center gap-1">
                <Skeleton width="9rem" height="1.3rem" />
                <Skeleton width="11rem" height=".8rem" />
              </div>
            </div>
          </template>
          <template v-else>
            <div class="flex flex-col place-items-center w-full gap-2 pt-4">
              <div class="rounded-md overflow-hidden w-fit h-full">
                <img
                  :src="quizDetail.thumbnailUrl"
                  class="object-cover border select-none aspect-square w-32"
                />
              </div>
              <div>
                <div
                  class="text-2xl mx-auto font-extrabold text-center text-wrap w-full font-feather"
                  :style="{ color: quizTheme.main_color }"
                >
                  {{ quizDetail.name }}
                </div>
                <div
                  class="text-sm mx-auto text-center leading-tight text-wrap w-full font-semibold font-nunito"
                >
                  {{ landingPage.sub_header }}
                </div>
              </div>
            </div>
          </template>
        </div>
        <div
          class="px-4 leading-none w-full flex select-none flex-row place-items-center gap-1 sm:py-2 gap-x-1 justify-center py-2 flex-wrap"
        >
          <div
            class="flex gap-1 flex-row place-items-center text-[11px] rounded-[6px] px-2 py-1 font-nunito font-semibold text-nowrap opacity-90 bg-white"
          >
            <Users2 size="12" /> {{ quizDetail.participant_quiz_summaries_count }} played
          </div>
          <div
            class="flex gap-1 flex-row place-items-center text-[11px] rounded-[6px] px-2 py-1 font-nunito font-semibold text-nowrap opacity-90 bg-white"
          >
            <MessageCircleQuestion class="" size="12" /> {{ totalQuestions }} questions
          </div>
          <div
            class="flex gap-1 flex-row place-items-center text-[11px] rounded-[6px] px-2 py-1 font-nunito font-semibold text-nowrap opacity-90 bg-white"
          >
            <Calendar size="12" />
            {{ quizDetail.created_at ? formatDateShort(quizDetail.created_at) : ' error' }}
            published
          </div>
        </div>
        <Divider />
        <div class="flex flex-col px-4 bg-500 overflow-y-scroll scrollable-containers">
          <div
            class="font-bold text-xl font-nunito select-none"
            :style="{ color: quizTheme.main_color }"
          >
            About this quiz
          </div>
          <template v-if="!quizDetail.description">
            <div class="flex flex-col gap-1">
              <span class="flex flex-col place-items-end w-full">
                <Skeleton width="90%" height=".8rem" class="" />
              </span>
              <Skeleton width="100%" height=".8rem" />
              <Skeleton width="100%" height=".8rem" />
              <Skeleton width="100%" height=".8rem" />
              <Skeleton width="12rem" height=".8rem" />
            </div>
          </template>
          <div
            v-else
            class="text-[16px] leading-[21px]  opacity-75 font-nunito "
          >
            {{ quizDetail.description }}
          </div>
        </div>
      </div>
      <template v-if="!quizTheme.main_color">
        <div class="flex flex-col py-[1px] gap-2 justify-center place-items-center"></div>
      </template>
      <div
        v-else
        class="flex flex-col-reverse gap-3 mt-6 w-full absolute bottom-0 px-4 py-2 z-10"
        :style="{
          backgroundColor: quizTheme.background_type === 'color' ? quizTheme.background_value : '',
        }"
      >
        <div class="flex space-x-2">
          <ShareQuiz />
          <router-link
            :to="{ path: `/${quizUid}/q/1`, query: { preview: route.query.preview } }"
            class="w-full"
          >
            <Button
              :backgroundColor="quizTheme.button_color"
              :boxShadowColor="quizTheme.accent_color"
              :borderColor="quizTheme.accent_color"
              :textColor="isDarkColor(quizTheme.button_color) ? 'white' : 'black'"
              @click="startQuiz"
              class="font-nunito font-extrabold text-xl w-full select-none"
            >
              Play
            </Button>
          </router-link>
        </div>
        <div class="w-full">
          <MechanicsDrawer />
        </div>
      </div>
    </div>
  </PageContainer>
</template>

<script setup>
import Button from '@/components/ui/Button.vue';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { Calendar, MessageCircleQuestion, LogOut, Users2, ChevronLeft } from 'lucide-vue-next';
import { storeToRefs } from 'pinia';
import { useRoute, useRouter } from 'vue-router';
import { onMounted, computed, watch } from 'vue';
import { Divider, Skeleton } from 'primevue';
import { formatDateShort } from '@/utils/formatDate';
import PageHeader from '@/components/PageHeader.vue';
import ShareQuiz from '@/components/ShareQuiz.vue';
import MechanicsDrawer from '@/components/MechanicsDrawer.vue';
import { isDarkColor } from '@/utils/lumininanceChecker';
import { removeQuizInSessionStorage } from '@/utils/sessionStorage';
import PageContainer from '@/components/PageContainer.vue';

const store = useQuizzesStore();
const { quizDetail, quizTheme, questionsContent, isLoading, landingPage, isQuizPublished, quizId } =
  storeToRefs(store);
const route = useRoute();
const router = useRouter();
const quizUid = route.params.quizId;

const startQuiz = async () => {
  sessionStorage.setItem(`quiz_status_${route.params.quizId}`, `started`, true);
};

const totalQuestions = computed(() => {
  return questionsContent.value.length;
});

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
  store.getQuizzesData();
});
</script>
