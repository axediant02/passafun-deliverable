<template>
    <v-container fluid style="max-width: 1000px;">
        <page-title-with-action-buttons :title="{ show: true, text: 'Quiz Details' }" :action-buttons="actionButtons"
            :adminRole="Number(adminRole)" />

        <QuizInfo :quiz="quiz" :quizStatus="quizStatus" :quizId="Number(id)" :adminRole="Number(adminRole)" />

        <navigation-tabs v-model="activeTab" :tabs="tabs" color="primary" :dark="false" class="mt-10" />

        <default-modal v-model="incompleteDetailsModal" title="Incomplete Quiz Details" title-class="font-weight-bold"
            cancel-text="Got it" cancel-button-variant="flat" cancel-color="primary" max-width="500px">
            <div class="font-weight-medium mb-5">
                The following items need to be completed before publishing:</div>
            <v-col type="error" icon=false variant="tonal" class="bg-grey-lighten-3 custom-radius"
                prepend-icon="mdi-alert-circle">
                <ul class="alert-title list-unstyle">
                    <li v-for="(error, index) in incompleteDetails" :key="index">
                        <v-icon icon="mdi-alert-circle" color="error" class="me-2 my-1"></v-icon>
                        {{ error }}
                    </li>
                </ul>
            </v-col>
        </default-modal>

        <v-col>
            <v-row class="justify-center">
                <template v-if="activeTab === 'Overview'">
                    <v-col class="px-0" :key="`overview-${activeTabKey}`">
                        <MechanicsInfo :quizStatus="quizStatus" :quizId="Number(id)" :mechanicId="Number(mechanicId)"
                            :mechanicInstruction="mechanicInstruction" @updateMechanics="handleMechanics"
                            :adminRole="Number(adminRole)" />
                        <SubmissionInfo :quizId="Number(id)" :quizStatus="quizStatus" :adminRole="Number(adminRole)" />
                        <v-card class="mb-5 border-sm" elevation="0">
                            <v-container>
                                <h3 class="font-weight-bold mb-3">Custom Share Thumbnail</h3>
                                <!-- <image-upload-card></image-upload-card> -->
                                <div class="position-relative">
                                    <div class="action-buttons position-absolute"
                                        style="top: 10px; left: 305px; z-index: 100;">
                                        <v-btn @click="triggerShareThumbnailImageInput" icon="mdi-pencil"
                                            size="small" />
                                        <input type="file" ref="editShareThumbnailImage"
                                            @change="handleShareThumbnailImageUpload" accept="image/*"
                                            style="display: none" />
                                    </div>
                                    <v-card height="200px" style="aspect-ratio: 16 / 9;">
                                        <v-img height="100%" width="100%"
                                            :src="!quiz.shareThumbnailImageUrl ? previewShareImageThumbnailUrl : quiz.shareThumbnailImageUrl"
                                            cover></v-img>
                                    </v-card>
                                </div>
                            </v-container>
                        </v-card>
                        <ThemeInfo :theme="theme" :quizStatus="quizStatus" :quizId="Number(id)"
                            :availableThemes="availableThemes" @updatedTheme="handleUpdatedTheme"
                            :adminRole="Number(adminRole)" />
                    </v-col>
                </template>
                <template v-if="activeTab === 'Participants'">
                    <v-col class="px-0" :key="`participants-${activeTabKey}`">
                        <ParticipantsTable :quizId="Number(id)" :participants="formattedParticipants" :headers="headers"
                            :participantQuizSummaries="formattedParticipants" :quizName="quiz.name" />
                    </v-col>
                </template>
                <template v-if="activeTab === 'Questions'">
                    <v-col :key="`questions-${activeTabKey}`">
                        <CreateQuestions :style="'max-width: 1000px;'" :quizId="Number(id)" location="FullDetails"
                            :adminRole="Number(adminRole)" />
                    </v-col>
                </template>
                <template v-if="activeTab === 'Results'">
                    <v-col :key="`results-${activeTabKey}`">
                        <CreateResults :style="'max-width: 1000px;'" :quizStatus="quizStatus" :quizId="Number(id)"
                            location="FullDetails" :adminRole="Number(adminRole)" />
                    </v-col>
                </template>
                <template v-if="activeTab === 'CustomThumbnail'">
                    <v-col :key="`CustomThumbnail-${activeTabKey}`">
                        <CustomizeThumbnail :style="'max-width: 1000px;'" :quizId="Number(id)" location="FullDetails"
                            :adminRole="Number(adminRole)" />
                    </v-col>
                </template>
            </v-row>
        </v-col>

    </v-container>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";
import { getAuth } from "@/pages/auth/authService";

const route = useRoute();
const router = useRouter();
const id = route.params.id;
const adminRole = ref();
const editShareThumbnailImage = ref(null);
const incompleteDetailsModal = ref(false);
const incompleteDetails = ref([]);
const statusLoading = ref(false);
const quiz = ref({});
const theme = ref({});
const admin = ref({});
const quizParticipants = ref([]);
const quizResults = ref([]);
const activeTab = ref('Details');
const activeTabKey = ref(0);
const availableThemes = ref([]);
const landingPage = ref({});
const questions = ref([]);
const mechanicId = ref({});
const quizStatus = ref({});
const mechanicInstruction = ref([]);
const statusMap = {
    1: "Unpublished",
    2: "Published",
    3: "Archived",
};

const tabs = [
    { label: 'Overview', value: 'Overview' },
    { label: 'Participants', value: 'Participants' },
    { label: 'Questions', value: 'Questions' },
    { label: 'Results', value: 'Results' },
    { label: 'Share Result Thumbnail', value: 'CustomThumbnail' }
];

const headers = ref([
    { title: 'Full Name', key: 'full_name' },
    { title: 'Email', key: 'email' },
    { title: 'Contact Number', key: 'contact_number' },
    { title: 'Age', key: 'age' },
    { title: 'Date Created', key: 'completed_at' },
]);

const PLAY_URL = import.meta.env.VITE_API_PLAY_URL;
const quizUrl = computed(() => {
    return `${PLAY_URL}/${quiz.value?.uid}?preview=true`;
});

const actionButtons = computed(() => [
    {
        label: 'Preview Quiz',
        prependIcon: 'mdi-eye',
        icon: 'mdi-eye',
        href: quizUrl.value,
        target: '_blank',
        show: true,
        variant: 'tonal',
        color: 'primary'
    },
    {
        label: 'Archive',
        prependIcon: 'mdi-archive',
        show: quizStatus.value.status !== 'Archived',
        class: 'archive-button',
        variant: 'tonal',
        icon: 'mdi-archive',
        color: 'primary',
        isLoading: statusLoading.value,
        onClick: () => handleMenuClick('archive')
    },
    {
        label: 'Unpublish',
        prependIcon: 'mdi-publish-off',
        show: quizStatus.value.status !== 'Unpublished',
        class: 'unpublish-button',
        variant: 'tonal',
        icon: 'mdi-publish-off',
        color: 'primary',
        isLoading: statusLoading.value,
        onClick: () => handleMenuClick('unpublish')
    },
    {
        label: 'Publish',
        prependIcon: 'mdi-web',
        show: quizStatus.value.status !== 'Published',
        class: 'publish-button',
        variant: 'tonal',
        icon: 'mdi-web',
        color: 'primary',
        isLoading: statusLoading.value,
        onClick: () => handleMenuClick('publish')
    }
]);

const triggerShareThumbnailImageInput = () => {
    editShareThumbnailImage.value.click();
} 

const handleShareThumbnailImageUpload = async(event) => {
    const { token } = getAuth();
    const file = event.target.files[0];
    const imageUrl = URL.createObjectURL(file);
    quiz.value.shareThumbnailImageUrl = imageUrl

    await axios.post(`api/quizzes/${id}/update`, { shareThumbnailImage: file}, {
        headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data'
                }
    })
    console.log(file);
}

const getAdminRole = () => {
    const adminData = JSON.parse(localStorage.getItem('admin'));
    adminRole.value = adminData?.role_id;
}

const handleMenuClick = async (item) => {
    if (item === "edit") {
        router.push(`/EditQuizDetail/${id}`);
        return;
    }
    const newStatus = determineStatus(item);
    if (newStatus) {
        try {
            await changeQuizStatus(newStatus, id);
        } catch (error) {
            if (error.response?.data?.errors) {
                incompleteDetails.value = error.response.data.errors;
                incompleteDetailsModal.value = true;
                setTimeout(() => {
                    statusLoading.value = false;
                }, 2000);
            }
        }
    }
};

const determineStatus = (action) => {
    const statusMapping = {
        unpublish: 1,
        publish: 2,
        archive: 3,
    };
    return statusMapping[action] || null;
};

const changeQuizStatus = async (newStatus, id) => {
    const { token } = getAuth();
    statusLoading.value = true
    try {
        const response = await axios.patch(
            `/api/quizzes/${id}`,
            { quiz_status_id: newStatus },
            {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        quiz.value.is_featured = response.data.quiz.is_featured;
        window.$snackbar("Quiz status updated successfully!", "success");
        setTimeout(() => {
            updateTab(activeTab.value);
            statusLoading.value = false;
        }, 2000);
        quizStatus.value = { status: statusMap[newStatus] };
    } catch (error) {
        if (error.response?.status === 403) {
            statusLoading.value = false;
            window.$snackbar("You don't have permission to perform this action.", "error");
        }
        statusLoading.value = false;
        throw error;
    }
};

const updateTab = (newTab) => {
    activeTab.value = newTab;
    activeTabKey.value++;
};

const fetchThemes = async () => {
    const { token } = getAuth();
    const response = await axios.get("/api/themes/", {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    });
    availableThemes.value = response.data || {};
};

const fetchQuizData = async () => {
    const { token } = getAuth();

    if (!token) {
        console.error("No token found, admin is not authenticated");
        return;
    }

    try {
        const response = await axios.get(`/api/quizzes/${id}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        quiz.value = response.data;
        quizStatus.value = response.data.quiz_status;
        theme.value = response.data.theme;
        admin.value = response.data.admin;
        landingPage.value = response.data.landing_page;
        mechanicId.value = response.data.mechanic_page.id;
        mechanicInstruction.value =
            response.data.mechanic_page.mechanic_page_instructions || [];
        questions.value = response.data.questions;
        fetchThemes();
    } catch (error) {
        console.error("Failed to fetch quiz data:", error);
        console.error("Response:", error.response);
    }
};

const fetchQuizParticipants = async () => {
    try {
        const response = await axios.get(`/api/quizzes/${id}/participants`);
        quizParticipants.value = response.data.participant_quiz_summaries || [];
        quizResults.value = response.data.results || [];
    } catch (error) {
        console.error("Error fetching quiz participants:", error);
    }
};

const formattedParticipants = computed(() => {

    const resultMapping = quizResults.value.reduce((resultTitleById, result) => {
        resultTitleById[result.id] = result.header;
        return resultTitleById;
    }, {});

    return quizParticipants.value.map((summary) => ({
        id: summary.participant.id,
        summaryId: summary.id,
        full_name: summary.participant.full_name,
        email: summary.participant.email,
        contact_number: summary.participant.contact_number,
        age: summary.participant.age,
        score: summary.score,
        result_header: resultMapping[summary.result_id] || 'Unknown',
        completed_at: summary.completed_at,
    }));
});

const handleMechanics = (data) => {
    mechanicId.value = data.id
    mechanicInstruction.value = data.instruction
}

const handleUpdatedTheme = (data) => {
    const selectedTheme = availableThemes.value.find(theme => theme.id === data.themeId);
    if (selectedTheme) {
        theme.value = selectedTheme;
    } else {
        console.error("Theme not found!");
    }
}

watch(activeTab, (newTab) => {
    router.replace({ query: { tab: newTab } });
});

onMounted(() => {
    fetchQuizData();
    fetchQuizParticipants();
    const tabFromQuery = route.query.tab || 'Overview';
    activeTab.value = tabFromQuery;
    getAdminRole();
});
</script>

<style>
.image-holder {
    max-height: 300px;
    max-width: 300px;
    border-radius: 15px;
    object-fit: cover;
    object-position: center;
}

.game-preview-card {
    max-width: 430px;
    border-radius: 15px;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.game-photo-container {
    width: 200px;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.game-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 15px;
}

.alert-title {
    list-style: none;
    padding-left: 0;
}
</style>
