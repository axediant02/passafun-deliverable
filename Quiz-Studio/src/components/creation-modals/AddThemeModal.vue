<template>
  <div class="my-0 mx-0">
    <ActionButton @click="openModal" color="primary" variant="elevated" height="40px" icon="mdi-plus"
      label="Create New Theme" />

    <default-modal v-model="dialog" title="Create New Theme" icon="mdi-palette" confirm-text="Submit"
      cancel-text="Cancel" @close="closeModal" @confirm="submitForm" cancelColor="primary" cancelButtonVariant="tonal"
      confirmColor="primary" confirmButtonVariant="flat" style="max-width: 600px;"
      titleClass="text-primary font-weight-bold">
      <template #default>
        <v-col v-if="successAnimation" align="center">
          <LottieAnimation :animationData="successAnimationData" />
        </v-col>

        <div v-if="successfulSubmission">
          <v-form @submit.prevent="submitForm">
            <v-text-field v-model="theme.name" label="Enter Theme Name" variant="outlined" :rules="inputRules"
              required></v-text-field>

            <div v-for="(colorField, index) in colorFields" :key="colorField.key">
              <v-menu v-model="colorMenuStates[index]" offset-y :close-on-content-click="false" :nudge-width="4"
                :max-width="200" transition="slide-x-transition">
                <template v-slot:activator="{ attrs }">
                  <v-text-field class="mb-2" v-bind="attrs" v-model="theme[colorField.key]"
                    :label="`Choose ${colorField.label}`" variant="outlined" :rules="inputColorRules" required
                    @click="colorMenuStates[index] = true">
                    <template v-slot:append>
                      <div class="custom-radius border-md ml-2"
                        :style="{ height: '50px', width: '50px', backgroundColor: theme[colorField.key] }" />
                    </template>
                  </v-text-field>
                </template>
                <v-color-picker v-model="theme[colorField.key]" format="hex" hide-inputs class="my-2"></v-color-picker>
              </v-menu>
            </div>

            <v-select v-model="theme.background_type" :items="backgroundTypes" label="Select Background Type"
              variant="outlined" required></v-select>

            <v-menu v-if="theme.background_type === 'color'" v-model="menuBackgroundValue" offset-y
              :close-on-content-click="false" :nudge-width="4" :max-width="200" transition="slide-x-reverse-transition">
              <template v-slot:activator="{ attrs }">
                <div class="d-flex">
                  <v-text-field class="me-5" v-model="theme.background_value" v-bind="attrs"
                    label="Choose Background Color" variant="outlined" :rules="inputColorRules" required
                    @click="menuBackgroundValue = true"></v-text-field>
                  <div class="custom-radius border-md"
                    :style="{ height: '50px', width: '50px', backgroundColor: theme.background_value }" />
                </div>
              </template>
              <v-color-picker v-model="theme.background_value" format="hex" hide-inputs class="my-2"></v-color-picker>
            </v-menu>
            <v-file-input v-if="theme.background_type === 'image'" v-model="theme.background_value"
              label="Upload Background Image" variant="outlined" accept="image/*" required prepend-icon=""
              prepend-inner-icon="mdi-image"></v-file-input>
          </v-form>
        </div>
      </template>
    </default-modal>
  </div>
</template>

<script setup>
import successAnimationData from "../../json/success.json";
import { ref, reactive } from "vue";
import axios from "axios";
import router from "@/router";
import { getAuth } from "@/pages/auth/authService";

const dialog = ref(false);

const theme = ref({
  name: "",
  main_color: "",
  accent_color: "",
  text_color: "",
  button_color: "",
  background_type: "color",
  background_value: null,
});

const colorFields = [
  { key: 'main_color', label: 'Main Color' },
  { key: 'accent_color', label: 'Accent Color' },
  { key: 'text_color', label: 'Text Color' },
  { key: 'button_color', label: 'Call to Action Button Color' }
];

const colorMenuStates = reactive(colorFields.map(() => false));

const backgroundTypes = ref(["color", "image"]);
const menuBackgroundValue = ref(false);

const requiredRule = (v) => !!v || "Field is required";
const lengthRule = (v) =>
  (v && v.length <= 50) || "Must be less than 50 characters";

const inputRules = [requiredRule, lengthRule];
const inputColorRules = [requiredRule, lengthRule];

const openModal = () => {
  dialog.value = true;
};

const closeModal = () => {
  dialog.value = false;
  resetForm();
};

const resetForm = () => {
  theme.value = {
    name: "",
    main_color: "",
    accent_color: "",
    text_color: "",
    button_color: "",
    background_type: "color",
    background_value: null,
  };

};

const successAnimation = ref(false);
const successfulSubmission = ref(true);
const loading = ref(false);

const submitForm = async () => {
  loading.value = true;
  const formData = new FormData();
  formData.append("name", theme.value.name);
  formData.append("main_color", theme.value.main_color);
  formData.append("accent_color", theme.value.accent_color);
  formData.append("text_color", theme.value.text_color);
  formData.append("button_color", theme.value.button_color);
  formData.append("background_type", theme.value.background_type);

  if (
    theme.value.background_type === "image" &&
    theme.value.background_value instanceof File
  ) {
    const file = theme.value.background_value;

    if (file.size > 200 * 1024) {
      loading.value = false;
      return;
    }

    formData.append("background_value", file);
  } else {
    formData.append("background_value", theme.value.background_value);
  }

  const { token } = getAuth();

  try {
    await axios.post("/api/themes", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
        Authorization: `Bearer ${token}`,
      },
    });

    window.$snackbar("Theme created successfully!", "success");
    setTimeout(() => {
      location.reload();
    }, 3000);
  } catch (error) {
    if (error.response && error.response.status === 403) {
      window.$snackbar("Oops! You don't have access to perform this action!", "error");
    } else {
      console.error("Error creating a theme", "error");
    }
  } finally {
    loading.value = false;
    setTimeout(() => {
      closeModal();
    }, 3000);

  }
};
</script>

<style scoped>
:deep(.v-text-field) {
  position: relative;
}

:deep(.v-text-field .v-messages) {
  position: absolute;
  right: 10px;
}
</style>