<template>
  <LibraryLayout>
    <router-link to="about-us" class="fixed bottom-4 z-50 right-4">
      <Info :size="40" color="#5197FF" />
    </router-link>
    <template #app-title>
      <h1
        v-if="!isUserSearching"
        class="font-feather px-1 w-full font-extrabold text-xl text-library py-2"
      >
        Passa-Fun
      </h1>
    </template>

    <template #search-all-quizzes>
      <IconField
        class="z-10 transition-all duration-300 ease-in-out w-fit py-[10px]"
        :class="isUserSearching && 'w-full'"
      >
        <div
          class="bg-white flex justify-center place-items-center font-nunito font-semibold h-full"
          :class="isUserSearching && 'px-4'"
        >
          <input
            type="text"
            placeholder="Search"
            autofocus
            class="w-full border-2 mr-2 outline-blue-400 text-slate-700 px-4 py-1 rounded-[14px]"
            @input="handleSearchAction"
            v-if="isUserSearching"
          />
          <Search
            class="cursor-pointer absolute sm:relative right-4"
            size="20"
            color="#5197ff"
            @click="toggleSearchBar"
            v-if="!isUserSearching"
          />
          <X
            class="cursor-pointer border-spacing-5"
            size="24"
            color="#5197ff"
            @click="toggleSearchBar"
            v-if="isUserSearching"
          />
        </div>
      </IconField>
    </template>

    <SearchResults v-if="isUserSearching" :userSearchInput="userSearchInput" />
    <div v-else>
      <div class="flex flex-col gap-6">
        <FeaturedQuizzes
          :quizzes="featuredQuizzes"
          :isLoading="isFeaturedQuizzesLoading"
          :errorMessage="featuredQuizzesError"
        >
          <template #featured-category>
            <h1 class="font-nunito font-bold text-lg flex flex-row gap-1 place-items-center">
              <Sparkles size="20" strokeWidth="2.5" /> Featured Quizzes
            </h1>
          </template>
          <template #quizLoading>
            <FeaturedQuizzesSkeletonLoader />
          </template>
          <template #quizError>
            <Error>{{ featuredQuizzesError }}</Error>
          </template>
        </FeaturedQuizzes>

        <MostPopularQuizzes
          :quizzes="popularQuizzes"
          :isLoading="isPopularQuizzesLoading"
          :errorMessage="popularQuizzesError"
        >
          <template #new-releases-category>
            <h1
              class="font-nunito font-bold text-lg text-library flex flex-row gap-1 place-items-center"
            >
              <Star size="20" strokeWidth="2.5" />Most Popular Quizzes
            </h1>
          </template>
          <template #quizLoading>
            <PopularQuizzesSkeletonLoader />
          </template>
          <template #quizError>
            <Error> {{ popularQuizzesError }}</Error>
          </template>
        </MostPopularQuizzes>
      </div>
      <div class="flex flex-col p-4 pt-6 relative">
        <div class="flex justify-between">
          <h1
            class="font-nunito font-bold text-lg text-library flex flex-row gap-1 place-items-center"
          >
            <Library size="20" strokeWidth="2.5" />
            All Quizzes
          </h1>
          <SortDropdown @update:selectedSortType="sortAllQuizzes" :initial-sort="currentSortName" />
        </div>
        <PublishedQuizzes
          :quizzes="fetchedPublishedQuizzes"
          :isLoading="isPublishedQuizzesLoading"
          :errorMessage="publishedQuizzesError"
        >
          <template #quizLoading>
            <QuizItemSkeleton />
          </template>
          <template #quizError>
            <Error>
              {{ publishedQuizzesError }}
            </Error>
          </template>
        </PublishedQuizzes>

        <div ref="containerRef" class="h-1"></div>
      </div>
      <button
        v-if="showBackToTop"
        @click="scrollToTop"
        class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 bg-blue-600 text-white px-4 py-2 rounded-[12px] shadow-xl hover:bg-blue-700 font-nunito font-semibold flex flex-row gap-1 place-items-center"
      >
        <ArrowUp size="20" class="-mt-1" /> <span>Back to Top</span>
      </button>
    </div>
  </LibraryLayout>
</template>

<script setup>
import PopularQuizzesSkeletonLoader from '@/components/loader/PopularQuizzesSkeletonLoader.vue';
import FeaturedQuizzesSkeletonLoader from '@/components/loader/FeaturedQuizzesSkeletonLoader.vue';
import SearchResults from '@/components/library/SearchResults.vue';
import MostPopularQuizzes from '@/components/library/MostPopularQuizzes.vue';
import PublishedQuizzes from '@/components/library/PublishedQuizzes.vue';
import FeaturedQuizzes from '@/components/library/FeaturedQuizzes.vue';
import { usePopularQuizzes } from '@/composables/usePopularQuizzes';
import { usePublishedQuizzes } from '@/composables/usePublishedQuizzes';
import LibraryLayout from '@/layout/LibraryLayout.vue';
import SortDropdown from '@/components/SortDropdown.vue';
import QuizItemSkeleton from '@/components/loader/QuizItemSkeleton.vue';
import IconField from 'primevue/iconfield';
import { ArrowUp, Info, Library, Search, Sparkles, Star, X } from 'lucide-vue-next';
import Error from '@/components/ui/Error.vue';
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { useFeaturedQuizzes } from '@/composables/useFeaturedQuizzes';

const { popularQuizzes, isPopularQuizzesLoading, popularQuizzesError, fetchPopularQuizzes } =
  usePopularQuizzes();
const { featuredQuizzes, isFeaturedQuizzesLoading, featuredQuizzesError, fetchFeaturedQuizzes } =
  useFeaturedQuizzes();
const {
  publishedQuizzes,
  isPublishedQuizzesLoading,
  publishedQuizzesError,
  fetchPublishedQuizzes,
  currentSortType,
  currentSortOrder,
  currentSortName,
} = usePublishedQuizzes();

const observer = ref(null);
const containerRef = ref(null);
const showBackToTop = ref(false);
const isUserSearching = ref(false);
const fetchedPublishedQuizzes = ref([]);

const toggleSearchBar = () => {
  if (!isUserSearching.value) {
    userSearchInput.value = '';
  }
  isUserSearching.value = !isUserSearching.value;
};

const userSearchInput = ref();
const handleSearchAction = (input) => {
  userSearchInput.value = input.target.value;
};

watch(publishedQuizzes.value, (newPublishedQuizzes) => {
  fetchedPublishedQuizzes.value = newPublishedQuizzes || [];
});

const setupObserver = () => {
  if (observer.value) {
    observer.value.disconnect();
  }
  observer.value = new IntersectionObserver(loadMore, {
    threshold: 1.0,
    rootMargin: '100px',
  });
  if (containerRef.value) {
    observer.value.observe(containerRef.value);
  }
};

watch(isUserSearching, (newValue) => {
  if (!newValue) {
    nextTick(() => {
      setupObserver();
    });
  }
});

const checkScrollPosition = () => {
  if (window.scrollY > 300) {
    showBackToTop.value = true;
  } else {
    showBackToTop.value = false;
  }
};

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  });
};

const loadMore = (entries) => {
  if (entries[0].isIntersecting) {
    fetchPublishedQuizzes(false, currentSortType.value, currentSortOrder.value);
  }
};

const sortAllQuizzes = (sortTypeName) => {
  assignSortTypeAndOrder(sortTypeName.name);
  fetchedPublishedQuizzes.value = [];
  fetchPublishedQuizzes(true, currentSortType.value, currentSortOrder.value);
  fetchedPublishedQuizzes.value = publishedQuizzes.value;
  currentSortName.value = sortTypeName.name;
};

const assignSortTypeAndOrder = (sortOption) => {
  switch (sortOption) {
    case 'Newest':
      currentSortType.value = 'created_at';
      currentSortOrder.value = 'desc';
      break;
    case 'Oldest':
      currentSortType.value = 'created_at';
      currentSortOrder.value = 'asc';
      break;
    case 'A-Z':
      currentSortType.value = 'name';
      currentSortOrder.value = 'asc';
      break;
    case 'Z-A':
      currentSortType.value = 'name';
      currentSortOrder.value = 'desc';
      break;
    default:
      currentSortType.value = 'created_at';
      currentSortOrder.value = 'desc';
  }
};

onMounted(() => {
  fetchFeaturedQuizzes();
  fetchPopularQuizzes();
  fetchPublishedQuizzes(false, currentSortType.value, currentSortOrder.value);
  setupObserver();
  window.addEventListener('scroll', checkScrollPosition);
});

onUnmounted(() => {
  if (observer.value) {
    observer.value.disconnect();
  }
  window.removeEventListener('scroll', checkScrollPosition);
});
</script>
