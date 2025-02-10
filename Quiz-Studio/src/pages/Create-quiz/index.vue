<template>
    <v-container style="max-width: 800px">
        <v-col>
            <h2>Create Quiz</h2>
            <p>
                <strong>Quiz Details:</strong> Set-up all essential elements-details,
                layout, instructions, and participant details.
            </p>
        </v-col>

        <v-col class="my-5">
            <div class="d-flex justify-space-between">
                <v-btn prepend-icon="mdi-arrow-left" to="/quiz" color="primary" variant="text">Back</v-btn>
                <v-btn @click="submitForm" prepend-icon="mdi-content-save" color="primary" :loading="loading">Save &
                    Continue</v-btn>
            </div>
        </v-col>

        <v-col>
            <p><strong>Basic Quiz Details</strong></p>
            <p>Enter the primary details of your quiz.</p>
        </v-col>

        <v-col>
            <v-text-field v-model="quiz.name" label="Enter Quiz Name*" class="mb-2" variant="outlined" counter
                maxlength="20" :rules="[rules.required]" :error-messages="nameError" @input="clearNameError" required />
            <v-text-field v-model="quiz.landingSubheader" label="Enter Quiz Subheading" variant="outlined" counter
                maxlength="70" :rules="[rules.required]" :error-messages="quizSubheaderError"
                @input="clearSubheaderError" />
            <v-textarea v-model="quiz.description" label="Enter Quiz Description" variant="outlined" counter
                maxlength="400" :rules="[rules.required]" :error-messages="quizDescriptionError"
                @input="clearDescriptionError" />
            <v-file-input v-model="quiz.thumbnail" prepend-icon="" prepend-inner-icon="mdi-image"
                label="Upload Quiz Image" variant="outlined" :rules="[rules.required]"
                :error-messages="quizThumbnailError" @input="clearThumbnailError"
                hint="Square resolution is recommended (2MB Max)" persistent-hint />
            <v-file-input v-model="quiz.coverImage" prepend-icon="" prepend-inner-icon="mdi-image"
                label="Upload Quiz Cover Image" variant="outlined" accept="image/*" :rules="[rules.required]"
                :error-messages="quizCoverImageError" @input="clearCoverImageError"
                hint="Square resolution is recommended (2MB Max)" persistent-hint />
            <v-file-input v-model="quiz.shareThumbnailImage" prepend-icon="" prepend-inner-icon="mdi-image"
                label="Upload Custom Share Thumbnail Image" variant="outlined" accept="image/*"
                :rules="[rules.required]" :error-messages="quizShareThumbnailImageError"
                @input="clearShareThumbnailImageError" hint="16:9 resolution is recommended (2MB Max)"
                persistent-hint />

            <v-select v-model="quiz.selectedTheme" prepend-inner-icon="mdi-palette" label="Choose a Theme"
                variant="outlined" :items="themes" item-value="id" item-title="name" :rules="[rules.required]"
                :error-messages="quizThemeError" @input="clearThemeError">
                <template v-slot:selection="{ item }">
                    <div class="d-flex align-center">
                        <span class="mr-2">{{ item?.raw?.name || 'Unnamed Theme' }}</span>
                        <div class="d-flex align-center">
                            <v-chip :style="{ backgroundColor: item?.raw?.main_color || '#cccccc' }"
                                class="px-3 rounded-0" style="border-radius: 10px 0 0 10px !important;" size="small">
                            </v-chip>
                            <v-chip :style="{ backgroundColor: item?.raw?.accent_color || '#cccccc' }"
                                class="px-3 rounded-0" size="small">
                            </v-chip>
                            <v-chip :style="{ backgroundColor: item?.raw?.text_color || '#cccccc' }"
                                class="px-3 rounded-0" size="small">
                            </v-chip>
                            <v-chip :style="{ backgroundColor: item?.raw?.button_color || '#cccccc' }"
                                class="px-3 rounded-0" size="small">
                            </v-chip>
                            <v-chip v-if="item?.raw?.background_type === 'color'"
                                :style="{ backgroundColor: item?.raw?.background_value || '#cccccc' }"
                                style="border-radius: 0 10px 10px 0 !important;" class="px-3 rounded-0" size="small">
                            </v-chip>
                            <v-chip v-else class="px-3 rounded-0" size="small"
                                style="border-radius: 0 10px 10px 0 !important;">
                                <v-icon>mdi-image</v-icon>
                            </v-chip>
                        </div>
                    </div>
                </template>

                <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props" class="border-b-sm">
                        <v-list-item-content>
                            <v-list-item-subtitle>
                                <div class="d-flex align-center">
                                    <v-chip :style="{ backgroundColor: item?.raw?.main_color || '#cccccc' }"
                                        class="px-3 rounded-0" style="border-radius: 10px 0 0 10px !important;"
                                        size="small">
                                    </v-chip>
                                    <v-chip :style="{ backgroundColor: item?.raw?.accent_color || '#cccccc' }"
                                        class="px-3 rounded-0" size="small">
                                    </v-chip>
                                    <v-chip :style="{ backgroundColor: item?.raw?.text_color || '#cccccc' }"
                                        class="px-3 rounded-0" size="small">
                                    </v-chip>
                                    <v-chip :style="{ backgroundColor: item?.raw?.button_color || '#cccccc' }"
                                        class="px-3 rounded-0" size="small">
                                    </v-chip>
                                    <v-chip v-if="item?.raw?.background_type === 'color'"
                                        :style="{ backgroundColor: item?.raw?.background_value || '#cccccc' }"
                                        style="border-radius: 0 10px 10px 0 !important;" class="px-3 rounded-0"
                                        size="small">
                                    </v-chip>
                                    <v-chip v-else class="px-3 rounded-0" size="small"
                                        style="border-radius: 0 10px 10px 0 !important;">
                                        <v-icon>mdi-image</v-icon>
                                    </v-chip>
                                </div>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </template>
            </v-select>
        </v-col>

        <v-col>
            <v-divider></v-divider>
        </v-col>

        <v-col>
            <p><strong>Quiz Mechanics & Instructions</strong></p>
            <div class="d-flex align-center justify-space-between">
                <p>
                    Provide detailed instructions to guide participants through the quiz.
                </p>
                <v-btn @click="addInstruction" prepend-icon="mdi-plus" variant="outlined" color="primary">Add
                    Instruction</v-btn>
            </div>
        </v-col>

        <v-col>
            <div v-for="(instruction, index) in quiz.mechanicsInstruction" :key="index" class="d-flex mb-3">
                <v-text-field v-model="quiz.mechanicsInstruction[index]" :label="`Enter Instruction ${index + 1}`"
                    placeholder="e.g. 'Read the following instructions carefully'" variant="outlined" counter
                    maxlength="70" />
                <v-btn icon="mdi-close" variant="text" color="grey" @click="removeInstruction(index)"
                    :disabled="quiz.mechanicsInstruction.length === 1"></v-btn>
            </div>
        </v-col>

        <v-col>
            <v-divider></v-divider>
        </v-col>

        <v-col>
            <p><strong>Participant Details</strong></p>
            <p>Select the details to collect from quiz participants.</p>
        </v-col>

        <v-col class="mb-10">
            <v-text-field v-model="quiz.getResultHeader" label="Enter Page Header" variant="outlined" counter
                maxlength="20" :rules="[rules.required]" :error-messages="resultHeaderError"
                @input="clearResultHeaderError" />
            <v-select v-model="quiz.getResultInputForms" label="Select which details to collect (select 1 or more)"
                variant="outlined" :items="availableInputForms" item-title="label" item-disabled="isRequired"
                item-value="type" multiple chips :rules="[rules.required]">
                <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props" :disabled="item.raw.is_required.value">
                    </v-list-item>
                </template>
            </v-select>
            <v-select :disabled="!quiz.getResultInputForms.length" v-model="selectedRequiredFields"
                label="Select Required Fields" variant="outlined" :items="quiz.getResultInputForms" :item-title="(form) => availableInputForms.find((f) => f.type === form).label
                    " :item-value="(form) => availableInputForms.find((f) => f.type === form).type
                        " multiple chips class="mt-4" :rules="[rules.required]">
                <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props" :disabled="item.raw === 'text'">
                    </v-list-item>
                </template>
            </v-select>
            <v-text-field v-model="quiz.getResultButtonText" label="Enter Button Text" variant="outlined" counter
                maxlength="20" :rules="[rules.required]" :error-messages="resultButtonTextError"
                @input="clearResultButtonTextError" />
            <v-select v-model="quiz.getResultFileType" :items="fileTypes" item-title="label" item-value="type"
                variant="outlined" label="Select File To Upload" :rules="[rules.required]">
            </v-select>
            <v-file-input v-if="quiz.getResultFileType === 'image'" v-model="quiz.getResultImage" prepend-icon=""
                prepend-inner-icon="mdi-image" label="Upload Image" variant="outlined" accept="image/*"
                :rules="[rules.required]" :error-messages="resultImageError" @input="clearResultImageError"
                hint="Square resolution is recommended (2MB Max)" persistent-hint />
            <v-file-input v-else v-model="quiz.getResultLottieJson" prepend-icon="" prepend-inner-icon="mdi-code-json"
                label="Upload LottieJson" variant="outlined" accept=".json" :rules="[rules.required]"
                :error-messages="resultImageError" hint="Square resolution is recommended (2MB Max)" persistent-hint />
        </v-col>

        <default-modal v-model="showConfirmationModal" style="max-width: 500px"
            :title="missingFieldsList.length <= 5 ? 'Almost Done!' : 'Missing Information'"
            title-class="font-weight-bold text-primary" icon="mdi-alert-circle-outline" cancel-text="Fill Missing Info"
            confirm-text="Continue Anyway" cancel-color="primary" @confirm="debounceSave"
            @close="showConfirmationModal = false">
            <p class="text-body-1">
                {{ missingFieldsList.length <= 3
                    ? 'Just a few fields are missing. Please fill them in before continuing.'
                    : 'Several fields are missing. Please fill them in before continuing.' }} </p>
        </default-modal>
    </v-container>
</template>

<script setup>
import axios from "axios";
import { ref, onMounted, watch } from "vue";
import { getAuth } from "@/pages/auth/authService";
import { useRouter } from "vue-router";
import { debounce, result } from "lodash";

const availableInputForms = [
    {
        label: 'Full name',
        type: 'text',
        is_required: ref(true)
    },
    {
        label: 'Phone number',
        type: 'tel',
        is_required: ref(false)
    },
    {
        label: 'Email address',
        type: 'email',
        is_required: ref(false)
    },
    {
        label: "Age",
        type: "age",
        is_required: ref(false),
    },
];

const quiz = ref({
    name: "",
    description: "",
    selectedTheme: null,
    thumbnail: null,
    coverImage: null,
    shareThumbnailImage: null,
    landingSubheader: "",
    mechanicsInstruction: [""],
    getResultHeader: "",
    getResultButtonText: "",
    getResultFileType: "image",
    getResultImage: null,
    getResultLottieJson: null,
    getResultBackgroundImage: null,
    getResultInputForms: ["text"],
    selectedChips: [],
});

const loading = ref(false);
const themes = ref([]);
const router = useRouter();
const showConfirmationModal = ref(false);
const missingFieldsList = ref([]);
const selectedRequiredFields = ref(["text"]);
const fileTypes = ref([
    { label: 'Image', type: 'image' },
    { label: 'LottieJson', type: 'json' },
])
const isJsonUploadedValid = ref(true);

const rules = {
    required: value => !!value || 'This field is required',
}

watch(
    () => quiz.value.getResultInputForms,
    (newForms) => {
        selectedRequiredFields.value = selectedRequiredFields.value.filter((field) =>
            newForms.includes(field)
        );
    }
);

const validateForm = () => {
    const missingFields = [];
    if (quiz.value.name.trim() === "") missingFields.push("Quiz name");
    if (quiz.value.description.trim() === "")
        missingFields.push("Quiz description");
    if (quiz.value.thumbnail === null) missingFields.push("Quiz thumbnail");
    if (quiz.value.coverImage === null || quiz.value.coverImage === undefined) missingFields.push("Quiz Cover Image");
    if (quiz.value.shareThumbnailImage === null || quiz.value.shareThumbnailImage === undefined) missingFields.push("Quiz Custom Share Thumbnail Image");
    return missingFields;
};

const validateLandingForm = () => {
    const missingFields = [];
    if (quiz.value.landingSubheader.trim() === "")
        missingFields.push("Quiz Page subheader");

    return missingFields;
};

const validateMechanicsForm = () => {
    const missingFields = [];
    if (
        !quiz.value.mechanicsInstruction.every((mechanic) => mechanic.trim() !== "")
    ) {
        missingFields.push("No mechanic instructions");
    }
    return missingFields;
};

const validateGetResultsForm = () => {
    const missingFields = [];
    if (quiz.value.selectedChips.length < 0)
        missingFields.push("Participant details selection");
    if (quiz.value.getResultHeader.trim() === "")
        missingFields.push("Result page header");
    if (quiz.value.getResultButtonText.trim() === "")
        missingFields.push("Result page button text");
    if (quiz.value.getResultImage === null && (quiz.value.getResultLottieJson === null || quiz.value.getResultLottieJson === undefined))
        missingFields.push("Result page image");
    if (!isJsonUploadedValid.value)
        missingFields.push("Invalid LottieJson, File will not be Saved");
    return missingFields;
};

const validateJsonFile = async () => {
    if (quiz.value.getResultLottieJson) {
        const jsonContent = await quiz.value.getResultLottieJson.text();
        if (isLottieJsonString(jsonContent)) {
            isJsonUploadedValid.value = true
        } else {
            isJsonUploadedValid.value = false
        }
    }
}

const processFormData = () => {
    const formData = new FormData();

    if (quiz.value.thumbnail instanceof File) {
        formData.append("thumbnail", quiz.value.thumbnail);
    }
    if (quiz.value.coverImage instanceof File) {
        formData.append("coverImage", quiz.value.coverImage);
    }
    if (quiz.value.shareThumbnailImage instanceof File) {
        formData.append("shareThumbnailImage", quiz.value.shareThumbnailImage);
    }
    if (quiz.value.getResultImage instanceof File) {
        formData.append("getResultImage", quiz.value.getResultImage);
    }
    if (quiz.value.getResultLottieJson instanceof File) {
        formData.append("getResultLottieJson", quiz.value.getResultLottieJson);
    }

    const textFields = {
        name: quiz.value.name,
        description: quiz.value.description,
        selectedTheme: quiz.value.selectedTheme || themes.value[0]?.id,
        landingSubheader: quiz.value.landingSubheader,
        getResultHeader: quiz.value.getResultHeader,
        getResultFileType: quiz.value.getResultFileType,
        getResultButtonText: quiz.value.getResultButtonText,
    };

    Object.entries(textFields).forEach(([key, value]) => {
        formData.append(key, value || "");
    });

    quiz.value.mechanicsInstruction.forEach((instruction, index) => {
        if (instruction && instruction.trim() !== "") {
            formData.append(`mechanicsInstruction[${index}]`, instruction);
        }
    });

    if (Array.isArray(quiz.value.getResultInputForms)) {
        quiz.value.getResultInputForms.forEach((inputForm, index) => {
            const formMatch = availableInputForms.find((f) => f.type === inputForm);
            formData.append(`getResultInputForms[${index}][type]`, inputForm);
            formData.append(
                `getResultInputForms[${index}][is_required]`,
                selectedRequiredFields.value.includes(inputForm)
            );
            formData.append(`getResultInputForms[${index}][label]`, formMatch.label);
        });
    }

    return formData;
};

const isLottieJsonString = (jsonString) => {
    return jsonString.includes('"v"') &&
        jsonString.includes('"fr"') &&
        jsonString.includes('"ip"') &&
        jsonString.includes('"op"');
};

const nameError = ref('');
const quizSubheaderError = ref('');
const quizDescriptionError = ref('');
const quizThumbnailError = ref('');
const quizCoverImageError = ref('');
const quizShareThumbnailImageError = ref('');
const quizThemeError = ref('');
const resultHeaderError = ref('');
const resultButtonTextError = ref('');
const resultImageError = ref('');

const clearNameError = () => (nameError.value = "");
const clearSubheaderError = () => (quizSubheaderError.value = "");
const clearDescriptionError = () => (quizDescriptionError.value = "");
const clearThumbnailError = () => (quizThumbnailError.value = "");
const clearCoverImageError = () => (quizCoverImageError.value = "");
const clearShareThumbnailImageError = () => (quizShareThumbnailImageError.value = "");
const clearThemeError = () => (quizThemeError.value = "");
const clearResultHeaderError = () => (resultHeaderError.value = "");
const clearResultButtonTextError = () => (resultButtonTextError.value = "");
const clearResultImageError = () => (resultImageError.value = "");

const submitForm = async () => {
    await validateJsonFile();

    if (!quiz.value.name.trim()) {
        nameError.value = 'Quiz name is required';
        return;
    }
    if (!quiz.value.landingSubheader.trim()) {
        quizSubheaderError.value = 'Quiz subheader is required';
    }
    if (!quiz.value.description.trim()) {
        quizDescriptionError.value = 'Quiz description is required';
    }
    if (!quiz.value.thumbnail) {
        quizThumbnailError.value = 'Quiz thumbnail is required';
    }
    if (!quiz.value.coverImage) {
        quizCoverImageError.value = 'Quiz cover image is required';
    }
    if (!quiz.value.shareThumbnailImage) {
        quizShareThumbnailImageError.value = 'Quiz custom share thumbnail image is required';
    }
    if (!quiz.value.getResultHeader.trim()) {
        resultHeaderError.value = 'Page header is required';
    }
    if (!quiz.value.getResultButtonText.trim()) {
        resultButtonTextError.value = 'Result page button text is required';
    }
    if (!quiz.value.getResultImage && !quiz.value.getResultLottieJson) {
        resultImageError.value = 'Page image is required';
    }

    const allMissingFields = [
        ...validateForm(),
        ...validateLandingForm(),
        ...validateMechanicsForm(),
        ...validateGetResultsForm(),
    ];

    if (allMissingFields.length > 0) {
        missingFieldsList.value = allMissingFields;
        showConfirmationModal.value = true;
        return;
    } else {
        showConfirmationModal.value = false;
    }

    await submitQuiz();
};

const debounceSave = debounce(() => {
    submitQuiz();
}, 300);

const submitQuiz = async () => {
    const formData = processFormData();
    try {
        loading.value = true;
        const { token } = getAuth();
        const response = await axios.post("/api/quizzes", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
                Authorization: `Bearer ${token}`,
            },
        });

        if (!response || !response.data) {
            throw new Error("Failed to receive response from server");
        }

        const quizId = response.data.id;
        router.push(`/Create-quiz/${quizId}/questions`);
    } catch (error) {
        window.$snackbar(
            `Failed to create quiz: ${error.response?.data?.message || error.message
            }`,
            "error"
        );
    } finally {
        loading.value = false;
    }
};

const addInstruction = () => {
    quiz.value.mechanicsInstruction.push(null);
};

const removeInstruction = (index) => {
    quiz.value.mechanicsInstruction.splice(index, 1);
};

const fetchThemes = async () => {
    try {
        const { token } = getAuth();
        const response = await axios.get("/api/themes", {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        themes.value = response.data;
    } catch (error) {
        console.error("Error fetching themes:", error);
    }
};

onMounted(() => {
    fetchThemes();
});
</script>

<style scoped>
:deep(.v-text-field) {
    position: relative;
}

:deep(.v-file-input) {
    position: relative;
}

:deep(.v-text-field .v-messages) {
    position: absolute;
    right: 2%;
    bottom: 30px;
}

:deep(.v-file-input .v-messages) {
    position: absolute;
    right: 2%;
    bottom: 30px;
}
</style>