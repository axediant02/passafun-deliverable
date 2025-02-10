<template>
    <div>
        <v-container fluid>
            <page-title-with-action-buttons :title="{ show: true, text: 'Quiz Details' }"
                :backButton="{ show: false }" />


            <v-col class="px-0">
                <div class="d-flex flex-wrap align-center justify-space-between">
                    <Searchbar searchLabel="Search for a quiz..." v-model="searchQuery"
                        @update:modelValue="fetchAllQuizzes" />
                    <ActionButton v-if="adminRole === 1" label="Create Quiz" color="primary" variant="elevated"
                        icon="mdi-plus" @click="$router.push('/create-quiz')" />
                </div>
            </v-col>
        </v-container>

        <div class="d-flex justify-space-between align-center">
            <navigation-tabs v-model="selectQuizTab" :tabs="tabs" color="primary" dark slider-color="primary" />
            <v-btn :prepend-icon="currentViewFormat === 'card' ? 'mdi-view-list' : 'mdi-view-module'"
                @click="changeQuizViewFormat" variant="text">
                {{ currentViewFormat === 'card' ? 'List View' : 'Card View' }}
            </v-btn>
        </div>

        <v-container>
            <v-alert v-if="errorMessage" type="error">{{ errorMessage }}</v-alert>

            <QuizList v-if="selectQuizTab === 'publishedQuiz'" status="2" :searchQuery="searchQuery"
                :view-format="currentViewFormat" />
            <QuizList v-if="selectQuizTab === 'unpublishedQuiz'" status="1" :searchQuery="searchQuery"
                :view-format="currentViewFormat" />
            <QuizList v-if="selectQuizTab === 'archivedQuiz'" status="3" :searchQuery="searchQuery"
                :view-format="currentViewFormat" />

            <v-row v-if="selectQuizTab === 'allQuiz'">
                <CardImageWithCover v-if="currentViewFormat === 'card'" :items="filteredAllQuizzes"
                    basePath="/quiz/details" imageField="thumbnailUrl" titleField="name"
                    countField="participant_quiz_summaries_count" countLabel="plays"
                    :dropdown-items="(item) => getDropdownItems(item)" @feature-quiz="handleFeatureQuiz"
                    :adminRole="adminRole" @image-error="handleImageError" />
                <quiz-table-format v-else :items="filteredAllQuizzes" base-path="/quiz/details" tab="allQuiz"
                    image-field="thumbnailUrl" title-field="name" countField="participant_quiz_summaries_count"
                    count-label="plays" :dropdown-items="(item) => getDropdownItems(item)"
                    @feature-quiz="handleFeatureQuiz" :adminRole="adminRole" @image-error="handleImageError" />
            </v-row>
        </v-container>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import { getAuth } from '@/pages/auth/authService';
import { debounce } from 'lodash';

const selectQuizTab = ref('publishedQuiz');
const previousTab = ref(null);
const isSearching = ref(false);
const searchQuery = ref('');
const errorMessage = ref(null);
const allQuizzes = ref([]);
const adminRole = ref();

const currentViewFormat = ref(localStorage.getItem('quizViewFormat') || 'card');
const changeQuizViewFormat = () => {
    currentViewFormat.value = currentViewFormat.value === 'card' ? 'list' : 'card';
    localStorage.setItem('quizViewFormat', currentViewFormat.value);
}

const tabs = [
    { value: 'publishedQuiz', label: 'Published' },
    { value: 'unpublishedQuiz', label: 'Unpublished' },
    { value: 'archivedQuiz', label: 'Archived' },
    { value: 'allQuiz', label: 'All' }
];

const fetchAllQuizzes = async () => {
    const { token } = getAuth();
    try {
        const response = await axios.get('/api/quizzes', {
            headers: {
                'Authorization': `Bearer ${token}`,
            },
        });
        allQuizzes.value = Array.isArray(response.data.quizzes) ? response.data.quizzes : [];
    } catch (error) {
        errorMessage.value = 'Failed to load quizzes';
        allQuizzes.value = [];
    }
};

const searchQuizzes = debounce(async (query) => {
    if (!query.trim()) {
        fetchAllQuizzes();
        return;
    }

    isSearching.value = true;
    try {
        const { token } = getAuth();
        const response = await axios.get('/api/quizzes/search/admin', {
            params: { quiz: query },
            headers: { Authorization: `Bearer ${token}` }
        });

        allQuizzes.value = response.data.data.map(quiz => ({
            ...quiz,
            thumbnailUrl: quiz.thumbnail_url || quiz.thumbnailUrl,
            coverImageUrl: quiz.cover_image_url || quiz.coverImageUrl,
            participant_quiz_summaries_count: quiz.participant_count || quiz.participant_quiz_summaries_count
        })) || [];
    } catch (error) {
        allQuizzes.value = [];
        window.$snackbar('Failed to search quizzes', 'error');
    } finally {
        isSearching.value = false;
    }
}, 300);

watch(searchQuery, (newQuery) => {
    if (newQuery.trim()) {
        if (selectQuizTab.value !== 'allQuiz') {
            previousTab.value = selectQuizTab.value;
            selectQuizTab.value = 'allQuiz';
        }
        searchQuizzes(newQuery);
    } else {
        if (previousTab.value) {
            selectQuizTab.value = previousTab.value;
            previousTab.value = null;
        }
        fetchAllQuizzes();
    }
});

const imageLoading = ref({});
const imageError = ref({});

const filteredAllQuizzes = computed(() => {
    return allQuizzes.value.filter(quiz => {
        const searchTerm = searchQuery.value.toLowerCase();
        const matchesSearch = quiz.name.toLowerCase().includes(searchTerm);
        if (matchesSearch) {
            imageLoading.value[quiz.id] = false;
            return {
                ...quiz,
                thumbnailUrl: quiz.thumbnailUrl || '/path/to/fallback-image.jpg'
            };
        }
        return false;
    });
});

const handleImageError = (itemId) => {
    imageError.value[itemId] = true;
};

watch(selectQuizTab, (newValue) => {
    const url = new URL(window.location);
    url.searchParams.set('tab', newValue);
    window.history.pushState({}, '', url);

    if (newValue === 'allQuiz') {
        fetchAllQuizzes();
    }
});

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam && tabs.some(tab => tab.value === tabParam)) {
        selectQuizTab.value = tabParam;
    }
    if (selectQuizTab.value === 'allQuiz') {
        fetchAllQuizzes();
    }
});

const isFeaturedProcess = ref(false);

const getDropdownItems = (quiz) => {
    return [
        {
            title: quiz.is_featured ? 'Unpin Quiz' : 'Pin Quiz',
            action: 'featureQuiz'
        }
    ];
};

const getAdminRole = () => {
    const adminData = JSON.parse(localStorage.getItem('admin'));
    adminRole.value = adminData?.role_id;
}

const handleFeatureQuiz = async (quiz) => {
    if (isFeaturedProcess.value) return;

    isFeaturedProcess.value = true;
    try {
        const { token } = getAuth();
        const newFeaturedState = !quiz.is_featured;

        await axios.patch(`api/quizzes/${quiz.id}/isFeatured`,
            { isFeatured: newFeaturedState },
            {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        );
        const quizIndex = allQuizzes.value.findIndex(q => q.id === quiz.id);
        if (quizIndex !== -1) {
            allQuizzes.value[quizIndex].is_featured = newFeaturedState;
        }

        window.$snackbar(
            newFeaturedState ? 'This Quiz is now Featured' : 'This Quiz is No Longer Featured',
            'success'
        );
    } catch (error) {
        console.error(error);
        window.$snackbar('Failed to update quiz feature status', 'error');
    } finally {
        isFeaturedProcess.value = false;
    }
};

onMounted(() => {
    getAdminRole();
});
</script>

<style scoped>
.v-icon {
    font-size: 32px;
    border: none;
    box-shadow: none;
    user-select: none;
}

.game-card {
    cursor: pointer;
}

.game-title {
    letter-spacing: 4px;
    font-weight: 500;
    transition: 0.3s;
}
</style>