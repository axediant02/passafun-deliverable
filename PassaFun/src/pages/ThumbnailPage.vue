<template>
  <div v-if="resultData" class="flex flex-col h-screen w-screen font-nunito bg-cover bg-center relative" :style="{
    backgroundImage:
      thumbnails.background_type === 'image' && thumbnails.background_value
        ? ` url(${thumbnails.background_value})`
        : thumbnails.background_type === 'color' && thumbnails.background_value
          ? `radial-gradient(circle at center, ${thumbnails.background_value}, ${thumbnails.background_value})`
          : `radial-gradient(circle at center, rgba(0,0,0,0.5), rgba(0,0,0,0.7)), url(${resultData.image})`,
  }">
    <!-- Main content area -->
    <div class="flex-1 flex flex-col items-center justify-start gap-y-3 p-5 sm:px-10">
      <div class="font-extrabold text-white font-poppins text-[30px] leading-none tracking-wide py-2"
        :style="{ color: thumbnails.prefix_text_color ? thumbnails.prefix_text_color : 'white' }">
        {{ thumbnails.prefix_text ? thumbnails.prefix_text : 'Your Result Is...' }}
      </div>
      <img :src="resultData.image" width="220" height="220" preview
        class="rounded-lg overflow-hidden border-2 border-white" />
      <div class="font-extraheavy w-[500px] text-white text-center font-poppins text-[55px] leading-none tracking-tight"
        :style="{ color: thumbnails.header_text_color }">
        {{ resultData.header }} 
      </div>
      <div class="text-white text-center px-5 text-[25px] leading-tight tracking-wider pb-5" :style="{
        color: thumbnails.description_text_color ? thumbnails.description_text_color : 'white',
      }">
        {{ resultData.description }}
      </div>
      <div class="bg-white flex-shrink-0 flex px-8 text-[35px] text-center py-3  rounded-lg" :style="{
        backgroundColor: thumbnails.button_color ? thumbnails.button_color : 'white',
      }">
        <span class="flex font-bold items-center justify-center gap-x-3 whitespace-nowrap" :style="{
          color: thumbnails.button_text_color ? thumbnails.button_text_color : 'black',
        }">
          {{ thumbnails.button_text ? thumbnails.button_text : 'Play Now' }} 
          <MoveRight class="w-[40px] h-10 stroke-[3]" />
        </span>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { storeToRefs } from 'pinia';
import { useResultData } from '@/composables/useResultData';
import { MoveRight } from 'lucide-vue-next';
import { useParticipantData } from '@/composables/useParticipantData';
import { useThumbnailFetcher } from '@/composables/useThumbnailTheme';

const store = useQuizzesStore();
const { quizTheme, landingPage, quizId } = storeToRefs(store);

const uniqueResultId = useRoute().params.uniqueResultId;
const { resultData, fetchResultData } = useResultData(uniqueResultId);
const { participantData, fetchParticipantData } = useParticipantData(uniqueResultId);

const { thumbnails, isLoading, error, fetchThumbnails } = useThumbnailFetcher();

onMounted(() => {
  store.getQuizzesData();
  fetchResultData();
  fetchParticipantData();
});

watch(
  quizId,
  (newQuizId) => {
    if (newQuizId) {
      fetchThumbnails(newQuizId);
    }
  },
  { immediate: true }
);
</script>
