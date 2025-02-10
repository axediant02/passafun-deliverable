<template>
    <div class="mx-auto py-5" :style="style">
        <div>
            <div>
                <div v-if="props.location === 'CreateQuiz'">
                    <v-col>
                        <h2>Create Quiz</h2>
                        <p>
                            <strong>Customize Your Thumbnail:</strong> Design your quiz with personalized colors and
                            backgrounds that reflect your style.
                        </p>
                    </v-col>

                    <v-col class="my-5">
                        <div class="d-flex justify-space-between">
                            <v-btn prepend-icon="mdi-arrow-left" :to="`/create-quiz/${props.quizId}/results`"
                                color="primary" variant="text">Back</v-btn>
                            <div>
                                <v-btn prepend-icon="mdi-content-save" class="me-3" variant="tonal" color="primary"
                                    @click="saveAsDraft">Save as Draft</v-btn>
                                <v-btn prepend-icon="mdi-web" color="primary" @click="publishQuiz">Publish</v-btn>
                            </div>
                        </div>
                    </v-col>
                </div>

                <v-col class="mb-5">
                    <div class="d-flex align-center justify-space-between">
                        <h4 class="font-weight-bold w-100">Thumbnail Preview</h4>
                        <div class="w-100">
                            <v-select v-model="selectedResult" :items="results" item-title="header" return-object
                                label="Choose a result to preview" variant="outlined" density="compact"
                                @update:model-value="onResultSelect" />
                        </div>
                    </div>
                    <v-card class="container d-flex align-center justify-center custom-radius"
                        :style="getBackgroundStyle">

                        <div v-if="mainLoader">
                            <v-col class="d-flex justify-center align-center">
                                <LottieAnimation :animationData="customLoader" style="width: 100px; height: 100px" />
                            </v-col>
                        </div>

                        <div v-else class="d-flex flex-column align-center justify-center" style="max-width: 90%;">
                            <h1 class="text-h6 mb-2 font-weight-bold text-center"
                                :style="{ color: theme.prefix_text_color }">
                                {{ prefixText || 'Your Elemental Persona is...' }}
                            </h1>
                            <div class="d-flex flex-column align-center justify-center">
                                <v-card class="custom-radius mb-3 border-lg" height="150px" width="150px">
                                    <v-img :src="resultImage" height="150" width="150" cover
                                        class="custom-radius mb-3" />
                                </v-card>
                                <h1 class="text-h3 font-weight-bold text-center"
                                    :style="{ color: theme.header_text_color }">
                                    {{ selectedResult?.header || 'Preview Header' }}
                                </h1>
                                <h1 class="text-subtitle-1 mb-5 text-center"
                                    :style="{ color: theme.description_text_color }">
                                    {{ truncatedDescription || 'Preview Description' }}
                                </h1>

                                <div class="px-10 custom-radius d-flex align-center justify-center"
                                    :style="{ backgroundColor: theme.button_color }">
                                    <h1 class="text-h6 me-5 py-3 font-weight-bold text-center"
                                        :style="{ color: theme.button_text_color }">
                                        {{ ctaButtonText || 'Whats my Elemental Persona?' }}
                                    </h1>
                                    <v-icon :style="{ color: theme.button_text_color }">mdi-arrow-right</v-icon>
                                </div>
                            </div>
                        </div>
                    </v-card>
                </v-col>

                <div v-if="mainLoader">
                    <v-col class="d-flex justify-center align-center">
                        <LottieAnimation :animationData="customLoader" style="width: 100px; height: 100px" />
                    </v-col>
                </div>

                <div v-else>
                    <v-col v-if="!publishedStatus" class="position-relative">
                        <h4 class="font-weight-bold mb-5">Text & Color Customization</h4>

                        <!-- HEADER  -->
                        <div class="d-flex align-center">
                            <v-text-field v-model="prefixText" class="me-5" label="Result Header" required
                                :rules="rules.required" placeholder="e.g. Your Elemental Persona is..."
                                @keyup.enter="saveOnEnter" variant="outlined" />
                            <v-menu v-model="prefixTextColorMenu" :close-on-content-click="false" :offset-y="true"
                                location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-text-field v-bind="props" v-model="theme.prefix_text_color" max-width="200px"
                                        label="Text Color" variant="outlined" required readonly>
                                        <template v-slot:append>
                                            <div class="custom-radius border-lg ml-n2" :style="{
                                                height: '55px',
                                                width: '55px',
                                                backgroundColor: theme.prefix_text_color
                                            }" />
                                        </template>
                                    </v-text-field>
                                </template>

                                <v-color-picker v-model="theme.prefix_text_color" format="hex" hide-inputs elevation="2"
                                    @update:modelValue="colorChangeAutoSave" />
                            </v-menu>
                        </div>

                        <!-- RESULT NAME  -->
                        <div class="d-flex align-center">
                            <v-text-field v-model="safeSelectedResult.header" class="me-5" label="Result Name"
                                maxlength="40" variant="outlined" counter required :rules="rules.required" disabled />
                            <v-menu v-model="resultNameTextColorMenu" :close-on-content-click="false" :offset-y="true"
                                location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-text-field v-bind="props" v-model="theme.header_text_color" max-width="200px"
                                        label="Text Color" variant="outlined" required readonly>
                                        <template v-slot:append>
                                            <div class="custom-radius border-lg ml-n2" :style="{
                                                height: '55px',
                                                width: '55px',
                                                backgroundColor: theme.header_text_color
                                            }" />
                                        </template>
                                    </v-text-field>
                                </template>
                                <v-color-picker v-model="theme.header_text_color"
                                    @update:modelValue="colorChangeAutoSave" format="hex" hide-inputs elevation="2" />
                            </v-menu>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="d-flex align-center">
                            <v-text-field v-model="truncatedDescription" class="me-5" label="Description Text"
                                maxlength="40" placeholder="e.g. As a water element you are flexible."
                                variant="outlined" counter required :rules="rules.required" disabled />
                            <v-menu v-model="descriptionTextColorMenu" :close-on-content-click="false" :offset-y="true"
                                location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-text-field v-bind="props" v-model="theme.description_text_color"
                                        max-width="200px" label="Text Color" variant="outlined" required readonly>
                                        <template v-slot:append>
                                            <div class="custom-radius border-lg ml-n2" :style="{
                                                height: '55px',
                                                width: '55px',
                                                backgroundColor: theme.description_text_color
                                            }" />
                                        </template>
                                    </v-text-field>
                                </template>
                                <v-color-picker v-model="theme.description_text_color"
                                    @update:modelValue="colorChangeAutoSave" format="hex" hide-inputs elevation="2" />
                            </v-menu>
                        </div>

                        <!-- BUTTON -->
                        <div class="d-flex align-center">
                            <v-text-field v-model="ctaButtonText" class="me-5" label="Button Text" maxlength="40"
                                placeholder="e.g. Play Now!" variant="outlined" counter required
                                @keyup.enter="saveOnEnter" :rules="rules.required" />
                            <v-menu v-model="buttonTextColorMenu" :close-on-content-click="false" :offset-y="true"
                                location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-text-field v-bind="props" v-model="theme.button_text_color" max-width="200px"
                                        label="Text Color" class="me-5" variant="outlined" required readonly>
                                        <template v-slot:append>
                                            <div class="custom-radius border-lg ml-n2" :style="{
                                                height: '55px',
                                                width: '55px',
                                                backgroundColor: theme.button_text_color
                                            }" />
                                        </template>
                                    </v-text-field>
                                </template>
                                <v-color-picker v-model="theme.button_text_color"
                                    @update:modelValue="colorChangeAutoSave" format="hex" hide-inputs elevation="2" />
                            </v-menu>
                            <v-menu v-model="buttonColorMenu" :close-on-content-click="false" :offset-y="true"
                                location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-text-field v-bind="props" v-model="theme.button_color" max-width="200px"
                                        label="Button Color" variant="outlined" required readonly>
                                        <template v-slot:append>
                                            <div class="custom-radius border-lg ml-n2" :style="{
                                                height: '55px',
                                                width: '55px',
                                                backgroundColor: theme.button_color
                                            }" />
                                        </template>
                                    </v-text-field>
                                </template>
                                <v-color-picker v-model="theme.button_color" @update:modelValue="colorChangeAutoSave"
                                    format="hex" hide-inputs elevation="2" />
                            </v-menu>
                        </div>

                        <!-- BACKGROUND -->
                        <div class="d-flex align-center">
                            <v-select v-model="backgroundType" class="me-5" :items="['color', 'image']"
                                :prepend-inner-icon="backgroundType === 'color' ? 'mdi-palette' : 'mdi-image'"
                                label="Select Background Type" variant="outlined" max-width="336px"
                                :rules="[v => !!v || 'Background type is required']" />

                            <v-menu v-if="backgroundType === 'color'" v-model="colorMenuState"
                                :close-on-content-click="false" :offset-y="true" location="bottom">
                                <template v-slot:activator="{ props }">
                                    <v-text-field v-bind="props" v-model="theme.background_value"
                                        label="Choose Background Color" variant="outlined" required readonly>
                                        <template v-slot:append>
                                            <div class="custom-radius border-lg ml-2" :style="{
                                                height: '55px',
                                                width: '55px',
                                                backgroundColor: theme.background_value
                                            }" />
                                        </template>
                                    </v-text-field>
                                </template>
                                <v-color-picker v-model="theme.background_value"
                                    @update:modelValue="colorChangeAutoSave" format="hex" hide-inputs elevation="2" />
                            </v-menu>

                            <v-file-input v-else v-model="backgroundImage" ref="fileInput" variant="outlined"
                                max-width="400px" prepend-icon="" label="Upload Background Image" show-size
                                accept="image/*" @update:modelValue="colorChangeAutoSave"
                                :rules="[fileSizeRule]"></v-file-input>
                        </div>
                    </v-col>
                </div>
            </div>
        </div>

        <default-modal v-model="incompleteDetailsModal" title="Incomplete Quiz Details" title-class="font-weight-bold"
            confirm-text="Complete All Fields" @confirm="completeAllFields" max-width="500px">
            <div class="font-weight-medium mb-5">
                The following items need to be completed before publishing:
            </div>
            <v-col type="error" variant="tonal" class="bg-grey-lighten-3 custom-radius">
                <ul class="alert-title list-unstyle">
                    <li v-for="(error, index) in incompleteDetails" :key="index" style="list-style-type: none;">
                        <v-icon icon="mdi-alert-circle-outline" color="orange" class="me-2 my-1"></v-icon>
                        {{ error }}
                    </li>
                </ul>
            </v-col>
        </default-modal>
    </div>
</template>

<script setup>
import axios from "axios";
import { ref, reactive, onMounted, computed, watch } from "vue";
import { getAuth } from "@/pages/auth/authService";
import { debounce } from "lodash";
import { useRouter } from "vue-router";
import customLoader from "@/json/loader.json";

const router = useRouter();

const safeSelectedResult = computed(() => selectedResult.value || { header: '' });

const backgroundType = ref('color');
const backgroundImage = ref(null);
const mainLoader = ref(false);
const prefixText = ref('');
const ctaButtonText = ref('');
const incompleteDetailsModal = ref(false);
const incompleteDetails = ref([]);

const colorMenuState = ref(false);
const prefixTextColorMenu = ref(false);
const resultNameTextColorMenu = ref(false);
const descriptionTextColorMenu = ref(false);
const buttonTextColorMenu = ref(false);
const buttonColorMenu = ref(false);

const emit = defineEmits([
    'update:modelValue',
    'close',
    'confirm'
]);

const rules = {
    required: [(v) => !!v || 'Field is required'],
    inputColor: [(v) => !!v || 'Color is required'],
    backgroundType: [(v) => !!v || 'Background type is required']
};

const props = defineProps({
    quizId: {
        type: Number,
        required: true,
    },
    location: {
        type: String,
        required: true,
    },
    style: {
        type: String,
        default: "max-width: 800px;",
    },
    modelValue: {
        type: Boolean,
        default: false
    }
});

const colorFields = [
    { key: 'prefix_text_color', label: 'Prefix Text Color' },
    { key: 'header_text_color', label: 'Header Text Color' },
    { key: 'description_text_color', label: 'Description Text Color' },
    { key: 'button_text_color', label: 'CTA Button Text Color' },
    { key: 'button_color', label: 'Call to Action Button Color' },
];

const theme = ref({
    prefix_text_color: "",
    header_text_color: "",
    description_text_color: "",
    button_text_color: "",
    button_color: "",
    background_value: "",
});


const colorChangeAutoSave = debounce(() => {
    saveCustomization();
}, 1000);


const getBackgroundStyle = computed(() => {
    if (backgroundType.value === 'image') {
        if (backgroundImage.value instanceof File) {
            return {
                backgroundImage: `url(${URL.createObjectURL(backgroundImage.value)})`,
                backgroundSize: 'cover',
                backgroundPosition: 'center'
            };
        }
        if (theme.value.background_value?.startsWith('http')) {
            return {
                backgroundImage: `url(${theme.value.background_value})`,
                backgroundSize: 'cover',
                backgroundPosition: 'center'
            };
        }
    }
    return {
        backgroundColor: theme.value.background_value || '#FFFFFF'
    };
});

const fileSizeRule = (file) => {
    if (file && file.size > 2 * 1024 * 1024) {
        return "File size exceeds 2MB.";
    }
    return true;
};

const quiz = ref({});

const fetchQuiz = async () => {
    try {
        const { data } = await axios.get(`/api/quizzes/${props.quizId}`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                Accept: "application/json",
            },
        });
        quiz.value = data;
    } catch (error) {
        console.error("Error fetching quiz:", error);
    }
};

const publishedStatus = computed(() => {
    return quiz.value.quiz_status_id === 2;
});

const checkExistingCustomization = async (quizId) => {
    const { token } = getAuth();
    try {
        const response = await axios.get(`/api/thumbnail-customization/${quizId}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        return !!response.data;
    } catch (error) {
        return false;
    }
};

const saveAsDraft = debounce(async () => {
    try {
        await saveCustomization();
        window.$snackbar('Customization saved as draft!', 'success');
        setTimeout(() => {
            router.push(`/quiz/details/${props.quizId}`);
        }, 1500);
    } catch (error) {
        console.error('Error saving as draft:', error);
        window.$snackbar('Failed to save as draft', 'error');
    }
}, 500);



const saveCustomization = async () => {
    try {
        const formData = new FormData();

        formData.append('quiz_id', props.quizId);
        formData.append('background_type', backgroundType.value);
        formData.append('prefix_text', prefixText.value || 'Your Result is...');
        formData.append('prefix_text_color', theme.value.prefix_text_color);
        formData.append('header_text_color', theme.value.header_text_color);
        formData.append('description_text_color', theme.value.description_text_color);
        formData.append('button_text', ctaButtonText.value || 'Take Quiz Now');
        formData.append('button_color', theme.value.button_color);
        formData.append('button_text_color', theme.value.button_text_color);

        if (backgroundType.value === 'image') {
            if (backgroundImage.value instanceof File) {
                formData.append('background_value', backgroundImage.value);
            }
        } else {
            formData.append('background_value', theme.value.background_value);
        }

        const { token } = getAuth();
        const exists = await checkExistingCustomization(props.quizId);

        const response = await axios({
            method: 'post',
            url: exists ? `/api/update/thumbnail-customization/${props.quizId}` : '/api/thumbnail-customization',
            data: formData,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'multipart/form-data',
            }
        });
        window.$snackbar('Customization saved successfully!', 'success');
    } catch (error) {
        console.error('Error:', error.response?.data);
        window.$snackbar(error.response?.data?.message || 'Failed to save customization', 'error');
    }
};

const fetchCustomization = async () => {
    const { token } = getAuth();
    try {
        const response = await axios.get(`/api/thumbnail-customization/${props.quizId}`, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });
        const customization = response.data;
        theme.value = {
            ...theme.value,
            ...customization,
            background_value: customization.background_value // Use background_value from API
        };
        prefixText.value = customization.prefix_text;
        ctaButtonText.value = customization.button_text;
        backgroundType.value = customization.background_type;
    } catch (error) {
        console.error('Error fetching customization:', error);
    }
};

const validateCustomization = () => {
    const errors = [];

    if (!prefixText.value?.trim()) {
        errors.push('Thumbnail Prefix text');
    }
    if (!ctaButtonText.value?.trim()) {
        errors.push('Thumbnail Call to action button text');
    }
    if (!theme.value.background_value) {
        if (backgroundType.value === 'image' && !backgroundImage.value) {
            errors.push('Thumbnail Background Image');
        }
    }

    return errors;
};

const publishQuiz = async () => {
    const errors = validateCustomization();
    if (errors.length > 0) {
        incompleteDetails.value = errors;
        incompleteDetailsModal.value = true;
        return;
    }

    try {
        await saveCustomization();
        await changeQuizStatus(props.quizId);
        router.push(`/quiz/details/${props.quizId}`);
    } catch (error) {
        if (error.response?.data?.errors) {
            incompleteDetails.value = error.response.data.errors;
            incompleteDetailsModal.value = true;
        }
        window.$snackbar('Failed to publish quiz', 'error');
    }
};

const saveOnEnter = debounce((event) => {
    saveCustomization();
    event.target.blur();
}, 500)

const changeQuizStatus = async (quizId) => {
    const { token } = getAuth();
    try {
        await axios.patch(
            `/api/quizzes/${quizId}`,
            { quiz_status_id: 2 },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );
        window.$snackbar('Quiz published successfully!', 'success');
    } catch (error) {
        if (error.response?.status === 403) {
            window.$snackbar('You don\'t have permission to perform this action.', 'error');
        }
        throw error;
    }
};

const completeAllFields = async () => {
    try {
        incompleteDetailsModal.value = false;
        saveCustomization();
        await router.push(`/quiz/details/${props.quizId}`);
    } catch (error) {
        console.error('Navigation error:', error);
        window.$snackbar('Failed to navigate', 'error');
    }
};

const results = ref([]);
const onResultSelect = (result) => {
    if (result) {
        selectedResult.value = result;
    }
};
const selectedResult = ref(results[0]);
const resultImage = computed(() =>
    selectedResult.value?.image_url || 'https://tmhagarwood.com/wp-content/uploads/2021/08/water-element-1-1400x788.jpg'
);
const truncatedDescription = computed(() => {
    if (!selectedResult.value?.description) return '';
    return selectedResult.value.description.length > 100
        ? selectedResult.value.description.substring(0, 100) + '...'
        : selectedResult.value.description;
});

const fetchResults = async () => {
    const { token } = getAuth();
    mainLoader.value = true;
    try {
        const response = await axios.get(`/api/quizzes/${props.quizId}/results`, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json'
            }
        });
        results.value = response.data.results;
        selectedResult.value = results.value[0];
    } catch (error) {
        console.error('Error fetching results:', error);
        window.$snackbar('Failed to fetch results', 'error');
    } finally {
        mainLoader.value = false;
    }
};

onMounted(() => {
    if (props.quizId) {
        fetchCustomization();
        fetchResults();
        fetchQuiz();
    }
});
</script>

<style scoped>
.container {
    aspect-ratio: 16 / 9;
}

.rounded-circle {
    border-radius: 100% !important;
}

.color-picker {
    position: absolute;
    z-index: 1000 !important;
    top: 50%;
    left: 50%;
}

.custom-file-input {
    border: 2px dashed #b0bec5;
    border-radius: 15px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    height: 200px;
    color: #616161;
    cursor: pointer;
    transition: background-color 0.2s, border-color 0.2s;
    position: relative;
}

.custom-file-input.dragging {
    border-color: #90caf9;
    background-color: #e3f2fd;
}

.file-upload-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    pointer-events: none;
}

.file-upload-container .v-icon {
    color: #90caf9;
}

.text-secondary {
    color: #9e9e9e;
}

.v-file-input-hidden {
    display: none;
}

.remove-button {
    position: absolute;
    top: 10px;
    right: 10px;
}
</style>