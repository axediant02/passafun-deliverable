<template>
    <v-card class="submission-form-card mb-5" width="100%" elevation="0">
        <v-card-title class="text-h6 font-weight-bold px-6 pt-4">
            Submission Form
        </v-card-title>

        <v-card-text class="pb-0 mb-0" v-if="props.quizStatus.status === 'Unpublished' && props.adminRole === 1">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Header</div>
                    <div class="info-content">
                        <v-text-field @keydown.enter.prevent="handleEnterKey" @change="debouncedSave('header')"
                            v-model="getResultPage.header" variant="outlined" maxlength="20" counter></v-text-field>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Select which details to collect ( select 1 or more) </div>
                    <div class="info-content">
                        <v-select v-model="selectedInputFields" :items="availableInputFields" item-title="label"
                            item-value="type" variant="outlined" multiple chips box
                            @blur="createOrUpdateFieldSelection">
                            <template v-slot:item="{ props, item }">
                                <v-list-item v-bind="props" :disabled="item.raw.isDisabled">
                                </v-list-item>
                            </template>
                        </v-select>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Select Required Fields</div>
                    <div class="info-content">
                        <v-select v-model="requiredInputFields" :items="getResultPage.inputForms" item-value="type"
                            item-title="label" variant="outlined" multiple chips @blur="createOrUpdateFieldSelection">
                            <template v-slot:item="{ props, item }">
                                <v-list-item v-bind="props" :disabled="item.raw.type === 'text'"></v-list-item>
                            </template>
                        </v-select>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Button Text</div>
                    <div class="info-content">
                        <v-text-field @keydown.enter.prevent="handleEnterKey" @change="debouncedSave('buttonText')"
                            v-model="getResultPage.buttonText" variant="outlined" maxlength="20" counter></v-text-field>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Thumbnail Image Type</div>
                    <div class="info-content">
                        <v-select v-model="uploadType" :items="availableUploadTypes" item-title="label"
                            item-value="type" variant="outlined" class="mb-4"></v-select>
                    </div>
                </div>
            </div>
        </v-card-text>

        <v-card-text v-else class="form-summary mb-0 pb-0">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Header</div>
                    <div class="info-content">
                        <v-text-field v-if="isEditable" v-model="getResultPage.header" variant="outlined"
                            @change="debouncedSave('header')" @keyup.enter="handleEnterKey" counter maxlength="20"
                            placeholder="Enter header" />
                        <span v-else>{{ getResultPage.header || "N/A" }}</span>
                    </div>
                </div>
                <div class="info-item mb-3">
                    <div class="info-label">Required Fields</div>
                    <div class="info-content">
                        <v-chip v-for="field in requiredInputFields" :key="field" class="mr-2 mb-2">
                            {{
                                availableInputFields.find(
                                    (availableField) => availableField.type === field
                                )?.label
                            }}
                        </v-chip>
                    </div>
                </div>
                <div class="info-item mb-4">
                    <div class="info-label">Optional Fields</div>
                    <div class="info-content">
                        <v-chip v-for="field in selectedInputFields.filter(
                            (field) => !requiredInputFields.includes(field)
                        )" :key="field" class="mr-2 mb-2">
                            {{ availableInputFields.find((af) => af.type === field)?.label }}
                        </v-chip>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Button Text</div>
                    <div class="info-content">
                        <v-text-field v-if="isEditable" v-model="getResultPage.buttonText" variant="outlined"
                            @change="debouncedSave('buttonText')" @keyup.enter="handleEnterKey" counter maxlength="20"
                            placeholder="Enter button text" />
                        <span v-else>{{ getResultPage.buttonText || "N/A" }}</span>
                    </div>
                </div>
            </div>
        </v-card-text>

        <div class="image-upload-section px-8">
            <div class="info-item me-10">
                <div class="info-label">Page Thumbnail Image</div>
                <div class="info-content">
                    <ImageUploadCard v-if="uploadType === 'image'" :imageType="'thumbnail'"
                        :image="previewImage.thumbnail || getResultPage.image" :onUpload="handleImageUpload"
                       :deletionNeeded="false" :onDelete="deleteImage" :quizStatus="props.quizStatus" ref="getResultImageInput" />
                    <JsonUploadCard v-if="uploadType === 'json'" :onUpload="handleImageUpload" :deletionNeeded="false" :onDelete="deleteImage"
                        :quizStatus="props.quizStatus" :animationData="jsonAnimationData.animation"
                        ref="getResultJsonInput" />
                    <input type="file" ref="getResultImageInput" @change="handleImageUpload" accept="image/*"
                        style="display: none" />
                </div>
            </div>

            <!-- <div class="info-item">
                <div class="info-label">Background Image</div>
                <div class="info-content">
                    <ImageUploadCard :imageType="'background'"
                        :image="previewImage.background || getResultPage.backgroundImage" :onUpload="handleImageUpload" :deletionNeeded="true"
                        :onDelete="deleteImage" :quizStatus="props.quizStatus" ref="getResultBackgroundImageInput" />
                    <input type="file" ref="getResultBackgroundImageInput"
                        @change="(e) => handleImageUpload(e, 'background')" accept="image/*" style="display: none" />
                </div>
            </div> -->
        </div>
    </v-card>
</template>

<script setup>
import { getAuth } from "@/pages/auth/authService";
import axios from "axios";
import { debounce } from "lodash";
import { onMounted, ref } from "vue";
import ImageUploadCard from "@/components/reusable-components/image-upload-card.vue";
import JsonUploadCard from "@/components/reusable-components/json-upload-card.vue";

const { token } = getAuth();
const resultId = ref();
const imageFile = ref(null);
const getResultImageInput = ref(null);
const getResultJsonInput = ref(null);
const getResultBackgroundImageInput = ref(null);
const imageRemoved = ref(false);
const uploadType = ref("image");
const selectedInputFields = ref([]);
const requiredInputFields = ref([]);
const oldInputForms = ref([]);
const oldGetResultData = ref();
const isEditable = ref();
const getResultPage = ref({
    header: null,
    image: null,
    imageTypeId: null,
    backgroundImage: null,
    buttonText: null,
    inputForms: [],
});

const availableUploadTypes = ref([
    { label: "Image", type: "image" },
    { label: "LottieJson", type: "json" },
]);

const previewImage = ref({
    thumbnail: null,
    background: null,
});

const jsonAnimationData = ref({
    jsonId: null,
    animation: null,
});

const availableInputFields = ref([
    { label: "Full name", type: "text", isDisabled: true },
    { label: "Email address", type: "email", isDisabled: false },
    { label: "Phone number", type: "tel", isDisabled: false },
    { label: "Age", type: "age", isDisabled: false },
]);


const props = defineProps({
    quizId: Number,
    quizStatus: Object,
    adminRole: Number
});

const handleImageUpload = async (event, imageType) => {
    const file = getFileFromEvent(event);
    if (!file) return;

    if (file.size > 2048 * 1024) {
      window.$snackbar("Image must be smaller than 2 MB.", "error");
      return;
    }

    imageFile.value = file;
    const imageUrl = URL.createObjectURL(file);

    const formData = new FormData();

    try {
        await processFile(file, imageType, imageUrl, formData);
        await uploadFormData(formData);

        imageRemoved.value = false;
        window.$snackbar("Image uploaded successfully", "success");
    } catch (error) {
        console.error("Error during image upload:", error);
        window.$snackbar(error.message || "Error uploading image", "error");
    }
};

const getFileFromEvent = (event) => {
    const file = event.target.files[0];
    return file || null;
};

const processFile = async (file, imageType, imageUrl, formData) => {
    const actions = {
        background: () => processBackground(file, imageUrl, formData),
        thumbnail: () => processThumbnail(file, imageUrl, formData),
        json: () => processJson(file, formData),
    };

    const action = actions[imageType];
    if (!action) throw new Error("Incorrect image type");

    await action();
};

const processBackground = (file, imageUrl, formData) => {
    previewImage.value.background = imageUrl;
    getResultPage.value.backgroundImage = imageUrl;
    formData.append("backgroundImage", file);
};

const processThumbnail = (file, imageUrl, formData) => {
    previewImage.value.thumbnail = imageUrl;
    getResultPage.value.image = imageUrl;
    formData.append("image", file);
    formData.append("imageType", uploadType.value);
    jsonAnimationData.value.animation = null;
};

const processJson = async (file, formData) => {
    previewImage.value.thumbnail = null;
    getResultPage.value.image = null;

    try {
        const jsonContent = await file.text();
        const parsedJson = JSON.parse(jsonContent);

        if (!isLottieJson(parsedJson)) {
            throw new Error("Uploaded JSON is not a valid Lottie JSON.");
        }

        jsonAnimationData.value.animation = parsedJson;
        formData.append("imageType", uploadType.value);
        formData.append("jsonFile", file);
    } catch (error) {
        throw new Error("Invalid JSON file. Please upload a valid Lottie JSON.");
    }
};

const isLottieJson = (json) =>
    json &&
    json.v &&
    json.fr !== undefined &&
    json.ip !== undefined &&
    json.op !== undefined;

const uploadFormData = async (formData) => {
    try {
        await axios.post(`api/get-result-page/${props.quizId}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
                Authorization: `Bearer ${token}`,
            },
        });
    } catch (error) {
        throw new Error("Error uploading image");
    }
};

const deleteImage = async (imageType) => {
    try {
        handleImageRemoval(imageType);
        const payload = buildDeletionPayload(imageType);
        await sendDeletionRequest(payload);

        imageRemoved.value = true;
        window.$snackbar("Image deleted successfully", "success");
    } catch (error) {
        console.error("Error deleting image:", error);
        window.$snackbar("Error deleting image", "error");
    }
};

const handleImageRemoval = (imageType) => {
    const actions = {
        background: () => {
            previewImage.value.background = null;
            getResultPage.value.backgroundImage = null;
            resetInputField(getResultBackgroundImageInput);
        },
        thumbnail: () => {
            previewImage.value.thumbnail = null;
            getResultPage.value.image = null;
            resetInputField(getResultImageInput);
        },
        json: () => {
            jsonAnimationData.value.animation = null;
        },
    };

    const action = actions[imageType];
    if (!action) {
        throw new Error("Invalid image type for deletion");
    }

    action();
};

const resetInputField = (inputRef) => {
    if (inputRef.value) {
        inputRef.value.value = "";
    }
};

const buildDeletionPayload = (imageType) => {
    const key =
        imageType === "background"
            ? "backgroundImage"
            : imageType === "thumbnail"
                ? "image"
                : "jsonFile";

    return { [key]: null };
};

const sendDeletionRequest = async (payload) => {
    await axios.post(`api/get-result-page/${props.quizId}`, payload, {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    });
};

const fetchGetResultForm = async (id) => {
    const { data } = await axios.get(`api/get-result-page/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
    });
    setGetResultData(data);
    setInputFormsData();
    setPreviewImages(data);
    if (uploadType.value === "json") {
        setLottieJson(data);
    }
};

const setGetResultData = (data) => {
    resultId.value = data.id;
    uploadType.value = data.image_type_id === 1 ? "image" : "json";
    oldGetResultData.value = data;
    getResultPage.value = {
        ...getResultPage.value,
        header: data.header,
        buttonText: data.button_text,
        imageTypeId: data.image_type_id,
        image: data.getResultImageUrl,
        backgroundImage: data.getResultBackgroundImageUrl,
        inputForms: data.input_forms
            .map((form) => ({
                ...form,
                status: "old",
                is_required: !!form.is_required,
            }))
            .sort((a, b) => {
                return (
                    availableInputFields.value.findIndex(
                        (field) => field.type === a.type
                    ) -
                    availableInputFields.value.findIndex((field) => field.type === b.type)
                );
            }),
    };

    oldInputForms.value = getResultPage.value.inputForms;
};

const setInputFormsData = () => {
    selectedInputFields.value = getResultPage.value.inputForms.map(
        (form) => form.type
    );
    requiredInputFields.value = getResultPage.value.inputForms
        .filter((form) => form.is_required)
        .map((form) => form.type);
};

const setPreviewImages = (data) => {
    previewImage.value = {
        thumbnail: data.getResultImageUrl,
        background: data.getResultBackgroundImageUrl,
    };
};

const setLottieJson = (data) => {
    jsonAnimationData.value = {
        animation: JSON.parse(data.getJsonAnimationData),
        jsonId: data,
    };
};

const createOrUpdateFieldSelection = () => {
    validateSelectedInputFields();
    const missingForms = getMissingForms();
    updateInputForms();
    addNewInputForms();
    if (missingForms.length > 0) {
        removeInputForms(missingForms);
    } else {
        debouncedSave('inputFields');
    }
};

const validateSelectedInputFields = () => {
    if (selectedInputFields.value.length === 0) {
        window.$snackbar(
            "Warning: You cannot remove all input fields. Please select at least one.",
            "warning"
        );
        selectedInputFields.value = [availableInputFields.value[0].type];
    }
};

const getMissingForms = () => {
    return getResultPage.value.inputForms
        .filter((form) => !selectedInputFields.value.includes(form.type))
        .map((form) => form.id);
};

const updateInputForms = () => {
    getResultPage.value.inputForms = getResultPage.value.inputForms
        .filter((form) => selectedInputFields.value.includes(form.type))
        .map((form) => ({
            ...form,
            is_required: requiredInputFields.value.includes(form.type),
        }));
};

const addNewInputForms = () => {
    let latestId =
        getResultPage.value.inputForms.length > 0
            ? Math.max(...getResultPage.value.inputForms.map((form) => form.id))
            : 0;

    selectedInputFields.value.forEach((type) => {
        if (!getResultPage.value.inputForms.some((form) => form.type === type)) {
            const label =
                availableInputFields.value.find((form) => form.type === type)?.label ||
                "Unknown";
            const newForm = {
                id: latestId + 1,
                type: type,
                label: label,
                status: "new",
                is_required: false,
                get_result_page_id: resultId.value,
            };
            getResultPage.value.inputForms.push(newForm);
            latestId++;
        }
    });
};

const removeInputForms = async (formIds) => {
    try {
        await axios.delete(`api/get-result-page/${resultId.value}`, {
            headers: { Authorization: `Bearer ${token}` },
            params: { formIds },
        });
        const updatedForms = getResultPage.value.inputForms.filter(
            (form) => !formIds.includes(form.id)
        );
        getResultPage.value.inputForms = updatedForms;
        oldInputForms.value = updatedForms;
        const existingFormTypes = new Set(updatedForms.map((form) => form.type));
        requiredInputFields.value = requiredInputFields.value.filter((type) =>
            existingFormTypes.has(type)
        );
        window.$snackbar("Forms removed successfully", "success");
    } catch (error) {
        console.error("Failed to remove forms:", error);
        window.$snackbar("Failed to remove forms", "error");
    }
};

const updateGetResultForm = async (id, field) => {
    const isValid = getResultValidation(field);
    if (!isValid) {
        return;
    }
    const formData = { ...getResultPage.value };
    ["image", "backgroundImage"].forEach((key) => {
        if (!(formData[key] === null || formData[key] instanceof File)) {
            delete formData[key];
        }
    });

    try {
        const response = await axios.post(`api/get-result-page/${id}`, formData, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        oldGetResultData.value = response.data;
        getResultPage.value.inputForms = response.data.input_forms
            .map((form) => ({
                ...form,
                status: "old",
                is_required: !!form.is_required,
            }))
            .sort((a, b) => {
                return (
                    availableInputFields.value.findIndex(
                        (field) => field.type === a.type
                    ) -
                    availableInputFields.value.findIndex((field) => field.type === b.type)
                );
            });
        oldInputForms.value = getResultPage.value.inputForms;

        window.$snackbar("Submission Form updated successfully", "success");
    } catch (error) {
        console.error("Failed to update Submission Form:", error);
        window.$snackbar("Failed to update Submission Form", "error");
    }
}


const getResultValidation = (field) => {
    const isInputFormsChanged = JSON.stringify(getResultPage.value.inputForms) !== JSON.stringify(oldInputForms.value);
    if (!isInputFormsChanged && field === 'inputFields') {
        return false;
    }

    const fieldsToValidate = [
        { field: 'header', oldField: 'header', message: "Submission Header is required" },
        { field: 'buttonText', oldField: 'button_text', message: "Button Text is required" }
    ];

    for (const { field: fieldName, oldField, message } of fieldsToValidate) {
        if (fieldName === field) {
            if (!getResultPage.value[fieldName] || /^\s*$/.test(getResultPage.value[fieldName])) {
                window.$snackbar(message, "error");
                getResultPage.value[fieldName] = oldGetResultData.value[oldField];
                return false;
            }
        }
    }
    return true;
}




const debouncedSave = debounce(async (field) => {
    await updateGetResultForm(props.quizId, field);
}, 1000);

const handleEnterKey = (event) => {
    debouncedSave();
    event.target.blur();
};

onMounted(() => {
    fetchGetResultForm(props.quizId);
});
</script>

<style scoped>
.submission-form-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.image-upload-section {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-fields,
.form-summary {
    padding: 1rem;
}

.field-label {
    font-weight: 500;
    color: #616161;
    width: 200px;
}

.info-grid {
    display: grid;
    padding: 1rem;
}

.info-item {
    display: grid;
    gap: 0.5rem;
}

.info-label {
    font-weight: 500;
    color: #616161;
    font-size: 12px;
}

.info-content {
    min-height: 40px;
}
</style>
