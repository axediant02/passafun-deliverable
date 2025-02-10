<template>
    <v-row>
        <template v-if="loading">
            <v-col class="d-flex justify-center align-center">
                <LottieAnimation :animationData="customLoader" style="width: 200px; height: 200px;" />
            </v-col>
        </template>
        <template v-else-if="filteredQuizzes.length > 0">
            <card-image-with-cover v-if="viewFormat === 'card'" :items="filteredQuizzes" base-path="/quiz/details"
                image-field="thumbnailUrl" title-field="name" count-field="participant_count" count-label="plays"
                :dropdown-items="(item) => getDropdownItems(item)" @feature-quiz="handleFeatureQuiz"
                :adminRole="adminRole" />
            <quiz-table-format v-else :items="filteredQuizzes" base-path="/quiz/details" image-field="thumbnailUrl"
                title-field="name" count-field="participant_count" count-label="plays"
                :dropdown-items="getDropdownItems" @feature-quiz="handleFeatureQuiz" :adminRole="adminRole" />
        </template>
        <template v-else>
            <v-col cols="12" class="d-flex align-center flex-column text-center">
                <LottieAnimation :animationData="sadFace" :style="{ width: '300px', height: '300px' }" />
                <p class="font-weight-bold text-grey-darken-1">
                    No {{ statusText }} quizzes found
                </p>
            </v-col>
        </template>
    </v-row>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { getAuth } from '@/pages/auth/authService';
import sadFace from "@/json/sad-magnify-glass.json";
import customLoader from "@/json/loader.json";


const props = defineProps({
    searchQuery: {
        type: String,
        default: '',
    },
    status: {
        type: String,
        required: true,
        validator: (value) => ['1', '2', '3'].includes(value)
    },
    viewFormat: {
        type: String,
        default: 'card'
    }
});

const adminRole = ref();
const quizzes = ref([]);
const loading = ref(true);

const fetchQuizzes = async () => {
    loading.value = true;
    const { token } = getAuth();
    try {
        const response = await axios.get(`/api/quizzes/status?status=${props.status}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
            },
        });
        quizzes.value = response.data;
    } catch (error) {
        console.error(`Error fetching quizzes with status ${props.status}:`, error);
    } finally {
        loading.value = false;
    }
};

const getAdminRole = () => {
    const adminData = JSON.parse(localStorage.getItem('admin'));
    adminRole.value = adminData?.role_id;
}

const filteredQuizzes = computed(() => {
    return quizzes.value.filter(quiz =>
        (quiz.name?.toLowerCase() || '').includes(props.searchQuery.toLowerCase())
    );
});

const statusText = computed(() => {
    switch (props.status) {
        case '1': return 'unpublished';
        case '2': return 'published';
        case '3': return 'archived';
        default: return '';
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

        const quizIndex = quizzes.value.findIndex(q => q.id === quiz.id);
        if (quizIndex !== -1) {
            quizzes.value[quizIndex].is_featured = newFeaturedState;
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
    fetchQuizzes();
    getAdminRole();
});
</script>