<template>
  <div class="my-0 mx-0">
    <ActionButton @click="openModal" color="primary" variant="elevated" height="40px" icon="mdi-plus"
      label="Temporary Add Admin" />

    <default-modal v-model="dialog" @close="closeModal" title="Add Admin" icon="mdi-account-plus" confirm-text="Submit"
      cancel-text="Cancel" confirm-color="primary" :custom-style="'max-width: 500px'"
      titleClass="text-primary font-weight-bold">
      <v-col v-if="successAnimation" align="center">
        <LottieAnimation :animationData="successAnimationData" />
      </v-col>

      <div v-if="successfulSubmission">
        <v-form ref="formRef" v-model="isFormValid" @submit.prevent="submitForm">
          <v-text-field v-model="adminForm.firstName" :rules="[rules.required]" label="Firstname" required
            prepend-inner-icon="mdi-account" class="custom-padding" variant="outlined" />

          <v-text-field v-model="adminForm.lastName" :rules="[rules.required]" label="Lastname" required
            prepend-inner-icon="mdi-account" class="custom-padding" variant="outlined" />

          <v-text-field v-model="adminForm.email" :rules="[rules.required, rules.email]" label="Email" required
            prepend-inner-icon="mdi-email" variant="outlined" />
        </v-form>
      </div>

      <template #actions>
        <v-btn class="px-5" variant="tonal" color="primary" :loading="loading" @click="closeModal">
          Cancel
        </v-btn>
        <v-btn class="bg-primary px-5" color="white" :loading="loading" @click="submitForm" type="submit">
          Submit
        </v-btn>
      </template>

    </default-modal>
  </div>
</template>

<script setup>
import successAnimationData from "../../json/success.json";
import { ref } from "vue";
import axios from "axios";
import router from "@/router";
import { getAuth } from "@/pages/auth/authService";

const dialog = ref(false);
const loading = ref(false);
const isFormValid = ref(false);
const formRef = ref(null);
const adminForm = ref({
  firstName: "",
  lastName: "",
  email: "",
});

const openModal = () => {
  dialog.value = true;
};

const closeModal = () => {
  dialog.value = false;
  resetForm();
};

const resetForm = () => {
  adminForm.value = {
    firstName: "",
    lastName: "",
    email: "",
  };
};

const rules = {
  required: (value) => !!value || "Required.",
  email: (value) => /.+@.+\..+/.test(value) || "E-mail must be valid.",
};

const successAnimation = ref(false);
const successfulSubmission = ref(true);

const register = async () => {
  loading.value = true;
  try {
    const response = await axios.post(
      "/api/register",
      {
        first_name: adminForm.value.firstName,
        last_name: adminForm.value.lastName,
        email: adminForm.value.email,
      },
    );

    window.$snackbar("Action completed successfully!", "success");
    successAnimation.value = true;
    successfulSubmission.value = false;
    setTimeout(() => {
      location.reload();
    }, 3000);
  } catch (error) {
    if (error.response && error.response.status === 403) {
      window.$snackbar(
        "Oops! You don't have access to perform this action!",
        "error"
      );
    } else {
      window.$snackbar("Error registering admin", "error");
    }
  } finally {
    loading.value = false;
    resetForm();
  }
};

const submitForm = () => {
  if (formRef.value.validate()) {
    register();
  }
};
</script>

<style scoped>
.mt-5 {
  margin-top: 5rem;
}

.pa-5 {
  padding: 2rem;
}

.border-card {
  border: 1px solid #ccc;
  border-radius: 8px;
}

.w-100 {
  width: 100%;
}

.mt-3 {
  margin-top: 1rem;
}

.custom-padding .v-input__control .v-input__slot {
  padding-left: 10px;
}
</style>
