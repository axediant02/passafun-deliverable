import { createRouter, createWebHistory } from 'vue-router';
import { useQuizzesStore } from '@/stores/quizzesStore';
import LibraryPage from '@/pages/LibraryPage.vue';
import UnpublishedQuizPage from '@/pages/UnpublishedQuizPage.vue';
import QuizDetailPage from '@/pages/QuizDetailPage.vue';
import QuizQuestionsPage from '@/pages/QuizQuestionsPage.vue';
import ParticipantSubmissionPage from '@/pages/ParticipantSubmissionPage.vue';
import ResultsPage from '@/pages/ResultsPage.vue';
import NotFoundPage from '@/pages/NotFoundPage.vue';
import TermsAndConditionPage from '@/components/TermsAndConditionPage.vue';
import ThumbnailPage from '@/pages/ThumbnailPage.vue';
import PreviewResultPage from '@/pages/PreviewResultPage.vue';
import AboutUs from '@/pages/AboutUs.vue';

const routes = [
  {
    path: '/',
    name: 'library',
    component: LibraryPage,
  },
  {
    path: '/about-us',
    name: 'about-us',
    component: AboutUs,
  },
  {
    path: '/unavailable-quiz',
    name: 'unpublished-quiz-page',
    component: UnpublishedQuizPage,
  },
  {
    path: '/:quizId',
    name: 'quiz-detail',
    component: QuizDetailPage,
  },
  {
    path: '/:quizId/q/:questionId?',
    name: 'questions-page',
    component: QuizQuestionsPage,
  },
  {
    path: '/:quizId/get-result',
    name: 'get-results-page',
    component: ParticipantSubmissionPage,
  },
  {
    path: '/:quizId/r/:uniqueResultId',
    name: 'result-page',
    component: ResultsPage,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFoundPage,
  },
  {
    path: '/:quizId/terms',
    name: 'terms',
    component: TermsAndConditionPage,
  },
  {
    path: '/:quizId/r/:uniqueResultId/thumbnail',
    name: 'thumbnail',
    component: ThumbnailPage,
  },
  {
    path: '/preview-result',
    name: 'previewResult',
    component: PreviewResultPage,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  linkActiveClass: 'link-active',
});

router.beforeEach((to, from, next) => {
  const quizStatus = sessionStorage.getItem(`quiz_status_${to.params.quizId}`);
  const store = useQuizzesStore();

  const isPreview = Boolean(to.query.preview);
  store.setPreviewMode(isPreview);

  if (from.name === 'NotFound' && to.name !== 'library') {
    next({ name: 'library' });
  } else if (from.name === 'unpublished-quiz-page' && to.name !== 'library') {
    next({ name: 'library' });
  } else if (quizStatus === 'started' || quizStatus === 'completed') {
    next();
  } else if (to.path.includes('/q/')) {
    next({ path: `/${to.params.quizId}` });
  } else if (to.path.includes('/get-result')) {
    next({ path: `/${to.params.quizId}` });
  } else {
    next();
  }
});

export default router;
