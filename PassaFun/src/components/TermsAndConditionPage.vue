<template>
  <PageContainer>
    <PageHeader to="get-results-page" class="relative z-10" navigationText=""
      :currentQuestionIndex="currentQuestionIndex" />

    <div class="w-full h-full overflow-y-auto py-5 px-4 ">
      <div class="flex place-items-center justify-between ">
        <h1 class="text-2xl font-bold">Terms and Conditions</h1>
      </div>

      <div class="max-w-4xl mx-auto mb-10 max-h-[calc(100vh-120px)] ">
        <section class="mb-6" v-for="(terms, index) in TermsAndConditionContent" :key="index">
          <h2 v-if="terms.title" class="font-semibold mb-2 text-lg">
            {{ index + 1 }}. {{ terms.title }}
          </h2>
          <p class="leading-relaxed text-md text-justify">
            {{ terms.content }}
          </p>
        </section>
      </div>
    </div>
  </PageContainer>
</template>

<script setup>
import PageContainer from '@/components/PageContainer.vue';
import Button from '@/components/ui/Button.vue';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { storeToRefs } from 'pinia';
import { useRouter, useRoute } from 'vue-router';
import { isDarkColor } from '@/utils/lumininanceChecker';
import { onMounted, ref } from 'vue';
import PageHeader from './PageHeader.vue';

const router = useRouter();
const route = useRoute();
const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);

const acceptAndReturn = () => {
  window.localStorage.setItem(`terms_accepted_${route.params.quizId}`, 'true');
  router.back();
};

const TermsAndConditionContent = ref([
  {
    title: 'Introduction',
    content: `Welcome to Passafun! These Terms and Conditions govern your use of our website, mobile application, and services (collectively, the "Platform"). By accessing or using Passafun , you agree to comply with these Terms and Conditions. If you disagree with any part of these terms, please do not use our services.`,
  },
  {
    title: 'Acceptance of Terms',
    content: `By using Passafun, you confirm that you are at least 13 years old and capable of entering into a binding agreement. If you are under 18, you must use our services under the supervision of a parent or legal guardian who agrees to these terms.`,
  },
  {
    title: 'Services Provided',
    content: `Passafun is a gamified platform for creating, sharing, and participating in quizzes. It provides tools for quiz management, result tracking, and user engagement features.`,
  },
  {
    title: 'User Responsibilities',
    content: `By using Passafun, you agree to:
Provide accurate and up-to-date information when creating an account.
Safeguard your account credentials and notify us of unauthorized access immediately.
Use the Platform responsibly and in compliance with applicable laws and regulations.`,
  },
  {
    content: `Thank you for using Passafun!`,
  },
  {
    content: `By accessing or using our services, you agree to these Terms and Conditions. Enjoy the experience and let the quizzes begin!`,
  },
]);


onMounted(() => {
  store.getQuizzesData()
})
</script>
