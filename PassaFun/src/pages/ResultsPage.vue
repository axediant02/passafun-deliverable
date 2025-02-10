<template>
  <PageContainer>
    <template v-if="resultData && quizTheme">
      <div class="flex flex-col place-items-center h-full px-4 font-nunito">
        <PageHeader class="relative select-none z-10 px-4 flex flex-col" :hasRoute="false">
          <template #nav-icon>
            <ConfirmationModal
              ConfirmationModal
              PrimaryButtonRoute="library"
              headerText="Explore Other Quizzes?"
              PrimaryButtonText="Go to home"
              modalContent=" Your current progress will be lost. You'll be redirected to the home page to browse and play other quizzes."
            >
              <template #confirmation-modal-button>
                <ChevronLeft size="26" class="text-white -mr-1" />
                <img src="/images/passafun.png" alt="Passafun Logo" class="h-[30px] w-[30px]" />
              </template>
            </ConfirmationModal>
          </template>
        </PageHeader>
        <div class="flex flex-col h-[calc(100vh-120px)]">
          <div class="flex flex-col pt-4 w-full flex-grow overflow-y-auto hide-scrollbar pb-32">
            <div class="w-full flex flex-col place-items-center">
              <template v-if="resultData.image">
                <div
                  class="max-h-[200px] rounded-md overflow-hidden select-none flex place-items-center"
                >
                  <Image
                    :src="resultData.image"
                    width="200"
                    preview
                    class="rounded-md overflow-hidden"
                  />
                </div>
              </template>
              <template v-else>
                <ImagePlaceholder
                  :mainColor="quizTheme.main_color"
                  :accentColor="quizTheme.accent_color"
                />
              </template>
            </div>

            <div class="flex flex-col gap-1 text-center">
              <div
                class="text-nowrap font text-md"
                :class="shouldUseLightText(quizTheme) ? 'text-white' : 'text-black'"
              ></div>
              <h1
                class="font-extrabold mx-auto leading-snug md:text-[40px] text-[30px] text-center flex flex-wrap font-russo"
                :style="{ color: quizTheme.accent_color }"
              >
                <div class="flex items-center gap-1 justify-center">
                  <span> {{ resultData.header }} </span>
                </div>
              </h1>
              <div
                class="px-4 text-[20px] leading-tight text-center pt-2"
                :class="shouldUseLightText(quizTheme) ? 'text-white' : 'text-black'"
              >
                <span class="text-[16px]"
                  ><span
                    class="font-bold text-[20px] font-feather uppercase"
                    :style="{ color: quizTheme.accent_color }"
                  >
                    {{ participantData }},</span
                  >
                  {{ resultData.description }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="h-[20%]"></div>
      </div>
      <div
        class="flex flex-col-reverse px-4 gap-3 mt-6 w-full absolute bottom-0 py-2 z-10"
        :style="{
          backgroundColor: quizTheme.background_type === 'color' ? quizTheme.background_value : '',
        }"
      >
        <div class="flex space-x-2">
          <!-- <div class="flex items-center justify-center select-none">

          </div> -->

          <div class="w-full flex-col flex gap-y-2">
            <ShareResult class="w-full" />
            <ConfirmationModal
              PrimaryButtonRoute="quiz-detail"
              PrimaryButtonText="Yes, play again"
              headerText="Play this quiz again?"
              modalContent="If you start over, any changes you've made will be lost, and you'll return to the beginning. Are you sure?"
            >
              <template #confirmation-modal-button>
                <Button
                  :boxShadowColor="quizTheme.accent_color"
                  :borderColor="quizTheme.accent_color"
                  backgroundColor="transparent"
                  boxShadowSize="3px"
                  class="px-4 w-full text-[20px] font-nunito gap-x-2"
                >
                  <RefreshCw strokeWidth="2.5" :color="quizTheme.main_color" />
                  <span :style="{ color: quizTheme.main_color }" class="fo">Play Again</span>
                </Button>
              </template>
            </ConfirmationModal>
          </div>
        </div>
      </div>
    </template>
    <template v-else>
      <ResultsPageSkeleton />
    </template>
  </PageContainer>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import PageContainer from '@/components/PageContainer.vue';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { storeToRefs } from 'pinia';
import { useResultData } from '@/composables/useResultData';
import { Image, Skeleton } from 'primevue';
import ShareResult from '../components/ShareResult.vue';
import { ChevronLeft, Quote, RefreshCw, RotateCcw, SquareArrowOutUpLeft } from 'lucide-vue-next';
import { useParticipantData } from '@/composables/useParticipantData';
import Button from '@/components/ui/Button.vue';
import { shouldUseLightText } from '@/utils/lumininanceChecker';
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import ImagePlaceholder from '@/components/ui/ImagePlaceholder.vue';
import LoadingSpinner from '@/components/loader/LoadingSpinner.vue';
import ResultsPageSkeleton from '@/components/loader/ResultsPageSkeleton.vue';
import PageHeader from '@/components/PageHeader.vue';

const store = useQuizzesStore();
const { quizTheme, currentQuizUID, isLoading } = storeToRefs(store);

const route = useRoute();
const uniqueResultId = route.params.uniqueResultId;

const { resultData, fetchResultData } = useResultData(uniqueResultId);
const { participantData, fetchParticipantData } = useParticipantData(uniqueResultId);
onMounted(() => {
  fetchResultData();
  fetchParticipantData();
  store.getQuizzesData();
});
</script>
