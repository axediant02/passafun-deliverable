<template>
  <div class="flex justify-center items-center min-h-screen">
    <div class="flex justify-between px-4 font-nunito gap-2 place-items-center w-full max-w-md flex-col">
      <div>
        <Construction size="150" stroke-width="1.25" color="#5197FF" />
      </div>
      <div class="font-bold font-nunito text-lg pt-2">Quiz Unavailable</div>
      <div class="font-nunito text-center pb-5">
        This quiz has been unpublished for updates. Please try again later or explore other
        quizzes.
      </div>
      <Button
        @click="handleGoBack"
        backgroundColor="#5197FF"
        boxShadowColor="#2563eb"
        borderColor="#2563eb"
        textColor="white"
        class="font-nunito font-extrabold text-xl w-full px-8"
      >
        Go to home
      </Button>
    </div>
  </div>
</template>

<script setup>
import { useQuizzesStore } from '@/stores/quizzesStore';
import Button from '@/components/ui/Button.vue';
import { Construction } from 'lucide-vue-next';
import { useRouter } from 'vue-router';

const router = useRouter();
const store = useQuizzesStore();

const handleGoBack = () => {
  store.setQuizPublished(true);
  router.push('/');
};
</script>
