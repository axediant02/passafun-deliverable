<template>
    <v-container>
        <page-title-with-action-buttons :title="{ show: true, text: 'Dashboard' }" :backButton="{ show: false }" />

        <v-overlay v-if="loading" class="d-flex align-center justify-center">
            <v-progress-circular indeterminate color="primary" />
        </v-overlay>

        <v-alert v-if="error" type="error" class="mb-4">
            {{ error }}
        </v-alert>

        <v-col>
            <div class="d-flex w-100 h-100 flex-wrap">
                <div class="left-container mb-10 me-10">
                    <v-slide-y-transition group>
                        <v-card v-for="(stat, index) in statistics" :key="index" class="stat-card mb-4" elevation="0"
                            :to="stat.to" :style="{ transitionDelay: `${index * 100}ms` }">
                            <v-container class="d-flex align-center position-relative overflow-hidden pa-6">
                                <div class="stat-content d-flex align-center">
                                    <div class="icon-wrapper me-4 rounded-lg d-flex align-center justify-center"
                                        :class="stat.iconClass">
                                        <v-icon size="24">{{ stat.icon }}</v-icon>
                                    </div>
                                    <div>
                                        <div class="text-h4 font-weight-bold mb-1">{{ quiz[stat.value] || 0 }}</div>
                                        <div class="text-subtitle-1 text-medium-emphasis">
                                            {{ quiz[stat.value] !== 1 ? stat.pluralLabel : stat.singularLabel }}
                                        </div>
                                    </div>
                                </div>
                                <div class="stat-background-icon">
                                    <v-icon size="120" :class="stat.iconClass + '-light'">{{ stat.icon }}</v-icon>
                                </div>
                            </v-container>
                        </v-card>
                    </v-slide-y-transition>

                    <Top5Quiz :quizzes="quiz.top_quizzes" :loading="loading" />
                </div>

                <div class="right-container d-flex h-100 mb-10 flex-column me-10">
                    <v-card v-if="adminRole === 1"
                        class="create-quiz-card d-flex flex-column justify-center align-center px-10 py-15 mb-4"
                        variant="outlined" height="100%" width="100%" max-width="450px" min-width="300px"
                        to="/create-quiz" elevation="1" :class="{ 'shake-animation': shouldAnimate }">
                        <v-card height="100%" width="100%" max-width="200px" variant="text" class="position-relative">
                            <v-img height="100%" width="100%" cover
                                src="https://img.freepik.com/free-vector/creation-process-concept-illustration_114360-2091.jpg" />
                            <v-fade-transition>
                                <div class="overlay-content" v-if="!quiz.total_quizzes">
                                    <v-icon size="40" color="primary">mdi-plus</v-icon>
                                </div>
                            </v-fade-transition>
                        </v-card>
                        <p class="text-h6 text-center my-3">Create your first quiz</p>
                        <p class="text-body-2 text-center mb-4 text-medium-emphasis">
                            Start by creating an engaging quiz for your participants
                        </p>
                        <v-btn class="create-quiz-button" color="primary" height="40px" to="/create-quiz"
                            prepend-icon="mdi-plus">
                            Create Quiz
                        </v-btn>
                    </v-card>

                    <v-card class="manage-participants-card d-flex flex-column justify-center align-center px-10 py-15"
                        variant="outlined" height="100%" width="100%" max-width="450px" min-width="300px"
                        to="/participant" elevation="1">
                        <v-card height="100%" width="100%" max-width="200px" variant="text" class="position-relative">
                            <v-img height="100%" width="100%" cover
                                src="https://img.freepik.com/free-vector/audience-segmentation-abstract-concept-illustration_335657-1854.jpg" />
                        </v-card>
                        <p class="text-h6 text-center my-3">Manage Participants</p>
                        <p class="text-body-2 text-center mb-4 text-medium-emphasis">
                            View and manage all your quiz participants
                        </p>
                        <v-btn class="manage-participants-button" color="primary" height="40px" to="/participant"
                            prepend-icon="mdi-account-group">
                            View Participants
                        </v-btn>
                    </v-card>
                </div>
            </div>
        </v-col>
    </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { getAuth } from '@/pages/auth/authService';

const quiz = ref({
    daily_participants: 0,
    total_participants: 0,
    total_quizzes: 0,
    top_quizzes: []
});

const adminRole = ref();
const loading = ref(true);
const error = ref(null);
const shouldAnimate = ref(false);

const statistics = [
    {
        icon: 'mdi-account-group',
        iconClass: 'bg-primary',
        value: 'daily_participants',
        singularLabel: 'Daily Participant',
        pluralLabel: 'Daily Participants',
        to: '/Participant'
    },
    {
        icon: 'mdi-account-group',
        iconClass: 'bg-secondary',
        value: 'total_participants',
        singularLabel: 'Total Participant',
        pluralLabel: 'Total Participants',
        to: '/Participant'
    },
    {
        icon: 'mdi-feather',
        iconClass: 'bg-success',
        value: 'total_quizzes',
        singularLabel: 'Total Quiz',
        pluralLabel: 'Total Quizzes',
        to: '/quiz'
    }
];

const getAdminRole = () => {
    const adminData = JSON.parse(localStorage.getItem('admin'));
    adminRole.value = adminData?.role_id;
};

const fetchDashboardData = async () => {
    loading.value = true;
    error.value = null;
    const { token } = getAuth();

    try {
        const response = await axios.get('/api/dashboard/statistics', {
            headers: {
                'Authorization': `Bearer ${token}`,
            },
        });

        quiz.value = response.data.data;
    } catch (err) {
        error.value = 'Failed to load dashboard data. Please try again.';
        console.error("Error fetching dashboard data", err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchDashboardData();
    if (!quiz.value.total_quizzes) {
        setInterval(() => {
            shouldAnimate.value = true;
            setTimeout(() => {
                shouldAnimate.value = false;
            }, 1000);
        }, 5000);
    }
    getAdminRole();
});
</script>

<style scoped>
.create-quiz-card:hover .create-quiz-button {
    transform: scale(1.05);
}

.manage-participants-card:hover .manage-participants-button {
    transform: scale(1.05);
}

.stat-card {
    transition: all 0.3s ease;
    width: 100%;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    overflow: hidden;
}

.overlay-content {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
}

.left-container {
    width: 100%;
    max-width: 500px;
}

.icon-wrapper {
    width: 52px;
    height: 52px;
    transition: all 0.3s ease;
    border-radius: 15px !important;
}

.stat-background-icon {
    position: absolute;
    right: -20px;
    bottom: -20px;
    opacity: 0.1;
    transform: rotate(-15deg);
    transition: all 0.3s ease;
}

.stat-card:hover .stat-background-icon {
    opacity: 0.15;
    transform: rotate(-5deg) scale(1.1);
}

.bg-primary-light {
    color: var(--v-primary-base);
}

.bg-secondary-light {
    color: var(--v-secondary-base);
}

.bg-success-light {
    color: var(--v-success-base);
}
</style>
