<template>
  <div class="my-0 mx-0">
    <ActionButton @click="openModal" color="primary" variant="elevated" height="40px" icon="mdi-plus"
      label="Add Admin" />

    <default-modal v-model="dialog" @close="closeModal" title="Add Admin" icon="mdi-account-plus"
      confirm-color="primary" :custom-style="'max-width: 500px'" titleClass="text-primary font-weight-bold">
      <v-col v-if="successAnimation" align="center">
        <LottieAnimation :animationData="successAnimationData" />
      </v-col>

      <div v-if="successfulSubmission">
        <v-form ref="formRef" v-model="isFormValid" @submit.prevent="register">
          <v-text-field v-model="adminForm.firstName" :rules="[rules.required]" label="Firstname" required
            prepend-inner-icon="mdi-account" class="custom-padding" variant="outlined" />

          <v-text-field v-model="adminForm.lastName" :rules="[rules.required]" label="Lastname" required
            prepend-inner-icon="mdi-account" class="custom-padding" variant="outlined" />

          <v-text-field v-model="adminForm.email" :rules="[rules.required, rules.email]" label="Email" required
            prepend-inner-icon="mdi-email" variant="outlined" />

          <v-select v-model="adminForm.role_id" :items="roles" prepend-inner-icon="mdi-shield-account"
            variant="outlined" item-title="role" item-value="id" label="Role" :rules="[rules.role]" required />

          <div class="d-flex justify-end">
            <v-btn class="px-5 me-3" variant="tonal" height="40" color="primary" :loading="loading" @click="closeModal">
              Cancel
            </v-btn>
            <v-btn class="bg-primary px-5" color="white" height="40" :loading="loading" @click="submitForm"
              type="submit">
              Submit
            </v-btn>
          </div>
        </v-form>
      </div>
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

const roles = ref([
  { id: 1, role: 'admin' },
  { id: 2, role: 'viewer' }
]);

const isFormValid = ref(false);
const formRef = ref(null);
const adminForm = ref({
  firstName: "",
  lastName: "",
  email: "",
  role_id: null
});

const openModal = () => {
  dialog.value = true;
  successAnimation.value = false;
  successfulSubmission.value = true;
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
    role_id: null
  };
};

const rules = {
  required: v => !!v || 'This field is required',
  email: v => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(v) || 'Enter a valid email address'
  },
  role: v => !!v || 'Role is required'
}

const successAnimation = ref(false);
const successfulSubmission = ref(true);

const emit = defineEmits(['admin-added']);

const register = async () => {
  loading.value = true;
  const { token } = getAuth();
  try {
    const response = await axios.post(
      "/api/register",
      {
        first_name: adminForm.value.firstName,
        last_name: adminForm.value.lastName,
        email: adminForm.value.email,
        role_id: adminForm.value.role_id,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      }
    );

    window.$snackbar("Action completed successfully!", "success");
    successAnimation.value = true;
    successfulSubmission.value = false;
    setTimeout(() => {
      emit('admin-added', response.data.admin);
      closeModal();
      resetForm();
    }, 1000);
  } catch (error) {
    if (error.response && error.response.status === 403) {
      window.$snackbar(
        "Oops! You don't have access to perform this action!",
        "error"
      );
    } else if (error.response && error.response.status === 422) {
      window.$snackbar(
        "Please fill up all the required fields!",
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

:deep(.v-text-field) {
  position: relative;
}

:deep(.v-text-field .v-messages) {
  position: absolute;
  right: 2%;
  bottom: 30px;
}
</style>
