<template>
    <div>
        <v-card class="mb-10" v-if="props.adminRole === 2 || props.quizStatus.status !== 'Unpublished'">
            <div>
                <v-card class="position-relative" style="border-radius: 0px !important" height="200px" width="100%">
                    <v-card class="border-lg quiz-thumbnail" height="130px" width="130px" elevation="5">
                        <v-img class="border-sm" :src="previewImage.image || props.quiz.thumbnailUrl" height="100%"
                            width="100%" cover />
                    </v-card>

                    <v-img :src="previewImage.coverImage || props.quiz.coverImageUrl" height="100%" width="100%"
                        cover />
                </v-card>
            </div>
            <v-container>
                <div class="d-flex justify-space-between">
                    <h1 class="title">{{ quiz.name }}</h1>
                    <div v-if="props.adminRole === 1">
                        <v-btn class="ms-2" @click="debounceQuizIsFeatured" :loading="isFeaturedProcess"
                            :disabled="isFeaturedProcess" v-if="isFeatured" variant="tonal" color="primary"
                            :prepend-icon="props.quiz.is_featured ? 'mdi-star-off' : 'mdi-star'">
                            {{ props.quiz.is_featured ? "Unpin Quiz" : "Pin Quiz" }} </v-btn>
                    </div>
                </div>
                <h1 class="text-h6 mb-2">
                    {{ quiz.landing_page?.sub_header }}
                </h1>
                <p class="description mb-5">
                    {{ quiz.description }}
                </p>

                <v-col>
                    <v-row>
                        <v-chip class="me-3" prepend-icon="mdi-star" v-if="props.quiz.is_featured">
                            Featured
                        </v-chip>
                        <v-chip class="me-3"
                            :prepend-icon="props.quizStatus.status === 'Published' ? 'mdi-web' : 'mdi-archive'">
                            {{ props.quizStatus.status }}
                        </v-chip>
                        <v-chip class="me-3" prepend-icon="mdi-account-group">
                            {{ participantCount }} Participants
                        </v-chip>
                        <v-chip class="me-3" prepend-icon="mdi-help-circle">
                            {{ questionCount }} Questions
                        </v-chip>
                        <v-chip prepend-icon="mdi-poll">
                            {{ possibleResultCount }} Possible Results
                        </v-chip>
                    </v-row>
                </v-col>
            </v-container>
        </v-card>

        <v-card class="mb-10" v-else>
            <div>
                <v-card class="position-relative" style="border-radius: 0px !important" height="200px" width="100%">
                    <v-card class="border-lg quiz-thumbnail" height="130px" width="130px" elevation="5">
                        <div class="action-buttons top-0 right-0">
                            <v-btn @click.stop="triggerImageUpload" size="30">
                                <v-icon>mdi-square-edit-outline</v-icon>
                                <v-tooltip activator="parent" location="top">Change Quiz Thumbnail</v-tooltip>
                            </v-btn>
                        </div>
                        <v-img class="border-sm" :src="previewImage.image || props.quiz.thumbnailUrl" height="100%"
                            width="100%" cover />
                        <input type="file" ref="getQuizImageInput" @change="(e) => handleImageUpload(e, 'quizImage')"
                            accept="image/*" style="display: none" />
                    </v-card>

                    <div class="action-buttons">
                        <v-btn @click.stop="triggerCoverImageUpload" prepend-icon="mdi-square-edit-outline">Change Cover
                            Photo</v-btn>
                    </div>

                    <v-img :src="previewImage.coverImage || props.quiz.coverImageUrl" height="100%" width="100%"
                        cover />
                    <input type="file" ref="getQuizCoverImageInput"
                        @change="(e) => handleImageUpload(e, 'quizCoverImage')" accept="image/*"
                        style="display: none" />
                </v-card>
            </div>

            <v-container>
                <v-text-field v-model="quizInfo.name" label="Quiz Name" variant="outlined"
                    :rules="[(v) => !!v || 'Title is required']" @change="debouncedSave('quizName')"
                    @keyup.enter="handleEnterPress" @blur="handleBlur" counter maxlength="20">
                    <template v-slot:append-inner>
                        <v-fade-transition leave-absolute>
                            <v-progress-circular v-if="isSaving" color="info" size="24"
                                indeterminate></v-progress-circular>
                        </v-fade-transition>
                    </template>
                </v-text-field>
                <v-text-field v-model="quizInfo.landingSubheader" label="Quiz Subheading" variant="outlined"
                    :rules="[(v) => !!v || 'Subheading is required']" @change="debouncedSave('quizSubheader')"
                    @keyup.enter="handleEnterPress" @blur="handleBlur" counter maxlength="20">
                    <template v-slot:append-inner>
                        <v-fade-transition leave-absolute>
                            <v-progress-circular v-if="isSaving" color="info" size="24"
                                indeterminate></v-progress-circular>
                        </v-fade-transition>
                    </template>
                </v-text-field>
                <v-textarea v-model="quizInfo.description" label="Description" variant="outlined" auto-grow rows="4"
                    :rules="[(v) => !!v || 'Description is required']" counter="400" maxlength="400"
                    @change="debouncedSave('description')">
                    <template v-slot:append-inner>
                        <v-fade-transition leave-absolute>
                            <v-progress-circular v-if="isSaving" color="info" size="24"
                                indeterminate></v-progress-circular>
                        </v-fade-transition>
                    </template>
                </v-textarea>

                <v-col>
                    <v-row>
                        <v-chip class="me-3" prepend-icon="mdi-publish-off">
                            {{ props.quizStatus.status }}
                        </v-chip>
                        <v-chip class="me-3" prepend-icon="mdi-account-group">
                            {{ participantCount }} Participants
                        </v-chip>
                        <v-chip class="me-3" prepend-icon="mdi-help-circle">
                            {{ questionCount }} Questions
                        </v-chip>
                        <v-chip prepend-icon="mdi-poll">
                            {{ possibleResultCount }} Possible Results
                        </v-chip>
                    </v-row>
                </v-col>
            </v-container>
        </v-card>
    </div>
</template>

<script setup>
import customLoader from "@/json/loader.json";
import { ref, computed, watch } from "vue";
import { debounce } from "lodash";
import axios from "axios";
import { getAuth } from "@/pages/auth/authService";


const emit = defineEmits(['updateQuizInfo']);
const { token } = getAuth();
const isSaving = ref(false);
const isFeaturedProcess = ref(false);
const isFeatured = ref(false)
const quizInfo = ref();
const getQuizImageInput = ref(null);
const getQuizCoverImageInput = ref(null);
const imageFile = ref(null);
const previewImage = ref({
    image: null,
    coverImage: null,
});
const props = defineProps({
    quiz: {
        type: Object,
        required: true,
    },
    quizStatus: {
        type: Object,
        required: true,
    },
    quizId: {
        type: Number,
        required: true,
    },
    adminRole: {
        type: Number,
        required: false
    }
});

watch(() => props.quiz, (newQuiz) => {
    quizInfo.value = { ...newQuiz };
    quizInfo.value.landingSubheader = newQuiz.landing_page?.sub_header || '';
});

watch(() => props.quizStatus, (newStatus) => {
    isFeatured.value = newStatus.status === 'Published' ? true : false
})
  
const triggerImageUpload = () => {
    getQuizImageInput.value.click();
};

const triggerCoverImageUpload = () => {
    getQuizCoverImageInput.value.click();
};

const handleImageUpload = async (event, imageType) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2048 * 1024) {
            window.$snackbar("Image must be smaller than 2 MB.", "error");
            return;
        }
        imageFile.value = file;
        const imageUrl = URL.createObjectURL(file);

        previewImage.value[imageType === 'quizImage' ? 'image' : 'coverImage'] = imageUrl
        const input = imageType === 'quizImage' ? getQuizImageInput : getQuizCoverImageInput;
        const property = imageType === 'quizImage' ? 'image' : 'coverImage';
        input.value[property] = imageUrl;

        try {
            const formData = new FormData();
            if (imageType === 'quizImage') {
                formData.append('thumbnail', file);
            } else if (imageType === 'quizCoverImage') {
                formData.append('coverImage', file);
            }
            await axios.post(`api/quizzes/${props.quizId}/update`, formData, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data'
                }
            });
            window.$snackbar("Image uploaded successfully", "success");
        } catch (error) {
            console.error("Failed to upload image:", error);
            window.$snackbar("Failed to upload image", "error");
        }
    }
};



const updateQuiz = async (quizDetail) => {
    if (quizDetail === 'quizName') {
        if (!quizInfo.value.name || /^\s*$/.test(quizInfo.value.name)) {
            window.$snackbar('Quiz Name is Required', 'error');
            quizInfo.value.name = props.quiz.name;
            return;
        }
        if( /^[^\w]*$/.test(quizInfo.value.name)) {
            window.$snackbar('Invalid Quiz Name', 'error');
            quizInfo.value.name = props.quiz.name;
            return;
        }
    } else if (quizDetail === 'description') {
        if (!quizInfo.value.description || /^\s*$/.test(quizInfo.value.description)) {
            window.$snackbar('Quiz Description is Required', 'error');
            quizInfo.value.description = props.quiz.description;
            return;
        }
    } else if (quizDetail === 'quizSubheader') {
        if (!quizInfo.value.landingSubheader || /^\s*$/.test(quizInfo.value.landingSubheader)) {
            window.$snackbar('Subheading is Required', 'error');
            quizInfo.value.landingSubheader = props.quiz.landing_page?.sub_header;
            return;
        }
    }

    try {
        isSaving.value = true;
        const updateData = {};

        if (quizDetail === 'quizName') {
            updateData.name = quizInfo.value.name;
            props.quiz.name = quizInfo.value.name;
        } else if (quizDetail === 'description') {
            updateData.description = quizInfo.value.description;
            props.quiz.description = quizInfo.value.description;
        } else if (quizDetail === 'quizSubheader') {
            updateData.landingSubheader = quizInfo.value.landingSubheader;
            props.quiz.landing_page.sub_header = quizInfo.value.landingSubheader;
        }

        await axios.post(
            `api/quizzes/${props.quizId}/update`,
            updateData,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        window.$snackbar("Quiz saved successfully", "success");
    } catch (error) {
        console.error("Failed to update quiz:", error);
        window.$snackbar("Failed to save quiz", "error");
    } finally {
        isSaving.value = false;
    }
};

const questionCount = computed(() => {
    return props.quiz.questions?.length || 0;
});

const participantCount = computed(() => {
    return props.quiz.participant_quiz_summaries_count || 0;
});

const possibleResultCount = computed(() => {
    return props.quiz.results_count;
});

const handleEnterPress = (event) => {
    debouncedSave.flush();
    event.target.blur();
};

const handleBlur = () => {
    debouncedSave.flush();
};


const debouncedSave = debounce((quizDetail) => {
    updateQuiz(quizDetail);
}, 1000);


const debounceQuizIsFeatured = debounce(() => {
    props.quiz.is_featured = !props.quiz.is_featured
    updateIsFeaturedQuiz();
}, 300)


const updateIsFeaturedQuiz = async () => {
    isFeaturedProcess.value = true
    try {
        await axios.patch(`api/quizzes/${props.quizId}/isFeatured`, { isFeatured: props.quiz.is_featured }, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
        window.$snackbar(
            props.quiz.is_featured ? 'This Quiz is now Featured' : 'This Quiz is No Longer Featured',
            'success'
        )
        setTimeout(() => {
            isFeaturedProcess.value = false
        }, 2000)
    } catch (error) {
        console.error(error);
        isFeaturedProcess.value = false
    }
}


</script>

<style scoped>
.quiz-thumbnail {
    position: absolute;
    z-index: 10;
    bottom: 10px;
    left: 15px;
    border-color: white !important;
}

.action-buttons {
    position: absolute;
    z-index: 10;
    right: 10px;
    top: 10px;
}

.action-buttons .v-btn {
    border-radius: 10px !important;
}

.v-chip {
    border-radius: 10px;
}
</style>
