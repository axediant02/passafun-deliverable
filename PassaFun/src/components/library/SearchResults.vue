<template>
  <div class="h-full">
    <div
      v-if="!isSearchEmpty"
      class="flex justify-center gap-1 text-library select-none px-6 pt-4 sm:px-12"
    >
      Try Searching for a quiz
    </div>
    <div v-else class="flex flex-row gap-1 text-library select-none px-4 pt-4 sm:px-12">
      <Search class="cursor-pointer" size="24" />
      Search results
    </div>

    <div v-if="isThereSearchTerms" class="p-4">
      <PublishedQuizzes
        v-if="searchedPublishedQuizzes.length > 0"
        :quizzes="searched"
        :isLoading="isSearchedPublishedQuizzesLoading"
        :errorMessage="searchedPublishedQuizzesError"
      >
        <template #quizLoading>
          <LoadingSpinner />
        </template>
        <template #quizError>
          <Error>
            {{ searchedPublishedQuizzesError }}
          </Error>
        </template>
      </PublishedQuizzes>
      <div
        v-if="!isSearchedPublishedQuizzesLoading && searchedPublishedQuizzes.length === 0"
        class="text-center text-gray-500 mt-4"
      >
        No results found for this search
      </div>
    </div>
  </div>
</template>

<script setup>
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import Error from '../ui/Error.vue';
import PublishedQuizzes from './PublishedQuizzes.vue';
import { useSearchedPublishedQuizzes } from '@/composables/useSearchedPublishedQuizzes';
import LoadingSpinner from '../loader/LoadingSpinner.vue';

const props = defineProps({
  userSearchInput: {
    type: String,
    required: false,
    default: '',
  },
});

const {
  searchedPublishedQuizzes,
  isSearchedPublishedQuizzesLoading,
  searchedPublishedQuizzesError,
  searchPublishedQuizzes,
} = useSearchedPublishedQuizzes();

const isThereSearchTerms = ref(false);

const debouncedSearch = debounce(() => {
  searchPublishedQuizzes(props.userSearchInput);
}, 500);

const isSearchEmpty = ref(true);

const searched = ref([])

watch(
  () => searchedPublishedQuizzes.value,
  (newValue) => {
    if (newValue) {
      searched.value = newValue
    }
  },
  { immediate: true }
);

watch(
  () => props.userSearchInput,
  (newValue) => {
    isThereSearchTerms.value = newValue.trim().length > 0;
    if (isThereSearchTerms.value) {
      isSearchEmpty.value = true;
      debouncedSearch();
    }
    if (props.userSearchInput === '') {
      isSearchEmpty.value = false;
      searched.value = []
    }
  },
  { immediate: true }
);
</script>
