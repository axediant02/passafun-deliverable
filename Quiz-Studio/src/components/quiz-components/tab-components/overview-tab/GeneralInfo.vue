<template>
  <v-card class="general-info-card mb-5" width="100%" elevation="0">
    <v-card-title class="text-h6 font-weight-bold px-6 pt-4">
      General Info
    </v-card-title>
    <v-card-text>
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Title</div>
          <div class="info-content">
            <v-text-field v-if="isEditable" v-model="landingPageToUpdate.header" variant="outlined"
              @change="() => updateLandingPageData('header')" @keyup.enter="handleEnterPress" counter maxlength="20"
              placeholder="Enter quiz title" />
            <span v-else>{{ props.landingPage.header || "Not set" }}</span>
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">Subtitle</div>
          <div class="info-content">
            <v-text-field v-if="isEditable" v-model="landingPageToUpdate.sub_header" variant="outlined"
              @change="() => updateLandingPageData('subHeader')" @keyup.enter="handleEnterPress" counter maxlength="70"
              placeholder="Enter quiz subtitle" />
            <span v-else>{{ props.landingPage.sub_header || "Not set" }}</span>
          </div>
        </div>

        <div class="info-item">
          <div class="info-label">CTA Button Text</div>
          <div class="info-content">
            <v-text-field v-if="isEditable" v-model="landingPageToUpdate.button_text" variant="outlined"
              @change="() => updateLandingPageData('buttonText')" @keyup.enter="handleEnterPress" counter maxlength="20"
              placeholder="Enter CTA button text (e.g. Start Quiz)" />
            <span v-else>{{ props.landingPage.button_text || "Not set" }}</span>
          </div>
        </div>

        <div class="d-flex">
          <div class="info-item me-10">
            <div class="info-label">Thumbnail</div>
            <div class="info-content">
              <ImageUploadCard :image="previewImage.landing || props.landingPage.landingImageUrl
                " :imageType="'landing'" :onUpload="handleImageUpload" :deletionNeeded="false"
                :onDelete="() => handleImageDelete('landing')" :quizStatus="props.quizStatus" />
            </div>
          </div>

          <!-- <div class="info-item">
            <div class="info-label">Background Image</div>
            <div class="info-content">
              <ImageUploadCard :image="previewImage.background || props.landingPage.landingBackgroundImageUrl"
                :imageType="'background'" :onUpload="(e) => handleImageUpload(e, 'background')" :deletionNeeded="true"
                :onDelete="() => handleImageDelete('background')" :quizStatus="props.quizStatus" />
            </div>
          </div> -->
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
import axios from "axios";
import { defineProps, ref, computed, watch } from "vue";
import { getAuth } from "@/pages/auth/authService";
import { debounce } from "lodash";
import ImageUploadCard from "@/components/reusable-components/image-upload-card.vue";

const emit = defineEmits(["updateGeneralInfo"]);
const { token } = getAuth();
const imageFile = ref(null);
const previewImage = ref({
  landing: null,
  background: null,
});

const props = defineProps({
  quizStatus: {
    type: Object,
    required: true,
  },
  admin: {
    type: Object,
    required: true,
  },
  landingPage: {
    type: Object,
    required: true,
  },
  quizId: {
    type: Number,
    required: true,
  },
});
const landingPageToUpdate = ref({ ...props.landingPage });

watch(
  () => props.landingPage,
  (newLanding) => {
    landingPageToUpdate.value = { ...newLanding };
  },
  { immediate: true }
);


const updateLandingPageData = debounce(async (landingData) => {
  const fieldMappings = {
    header: {
      value: 'header',
      updateKey: 'landingHeader',
      errorMsg: 'Landing Page Title is Required'
    },
    subHeader: {
      value: 'sub_header', 
      updateKey: 'landingSubheader',
      errorMsg: 'Landing Page Subtitle is Required'
    },
    buttonText: {
      value: 'button_text',
      updateKey: 'landingButtonText', 
      errorMsg: 'Landing Page Button Text is Required'
    }
  };

  const mapping = fieldMappings[landingData];
  if (!mapping) return;

  const value = landingPageToUpdate.value[mapping.value];
  if (!value || /^\s*$/.test(value)) {
    landingPageToUpdate.value[mapping.value] = props.landingPage[mapping.value];
    window.$snackbar(mapping.errorMsg, "error");
    return;
  }

  try {
    emit('updateGeneralInfo', landingPageToUpdate.value);
    await axios.post(
      `api/quizzes/${props.quizId}/update`,
      { [mapping.updateKey]: value },
      {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      }
    );
    window.$snackbar("Landing page updated successfully", "success");
  } catch (error) {
    console.error("Failed to update landing page:", error);
    window.$snackbar("Failed to update landing page", "error");
  }
}, 500);

const handleImageUpload = async (event, imageType) => {
  const file = event.target.files[0];
  if (!file) return;

  if (file.size > 2048 * 1024) {
    window.$snackbar("Image must be smaller than 2 MB.", "error");
    return;
  }

  imageFile.value = file;
  const imageUrl = URL.createObjectURL(file);

  try {
    previewImage.value[imageType === "background" ? "background" : "landing"] = imageUrl;
    props.landingPage.landingImageUrl = imageUrl;

    const formData = new FormData();
    const imageKey = imageType === "background" ? "landingBackgroundImage" : "landingImage";
    formData.append(imageKey, file);

    await axios.post(`api/quizzes/${props.quizId}/update`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "multipart/form-data",
      },
    });

    window.$snackbar("Image uploaded successfully", "success");
  } catch (error) {
    console.error("Failed to upload image:", error);
    window.$snackbar("Failed to upload image", "error");
  }
};

const handleImageDelete = async (imageType) => {
  previewImage.value[imageType === "background" ? "background" : "landing"] =
    null;
  props.landingPage[
    imageType === "background"
      ? ".landingBackgroundImageUrl"
      : ".landingImageUrl"
  ] = null;

  try {
    const formData = new FormData();
    formData.append(
      [imageType === "background" ? "landingBackgroundImage" : "landingImage"],
      null
    );
    await axios.delete(`api/quizzes/${props.quizId}/destroyImages`, {
      data: formData,
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "multipart/form-data",
      },
    });
    window.$snackbar("Image Removed successfully", "success");
  } catch (error) {
    console.error("Failed to remove image:", error);
    window.$snackbar("Failed to remove image", "error");
  }
};

const isEditable = computed(() => props.quizStatus.status === "Unpublished");

const handleEnterPress = (event) => {
  updateLandingPageData.flush();
  event.target.blur();
};
</script>

<style scoped>
.general-info-card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
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