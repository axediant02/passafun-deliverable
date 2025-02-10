<template>
    <div class="my-0 mx-0">
        <ActionButton @click="openModal" size="small" color="primary" variant="tonal" height="35px"
            icon="mdi-lock-reset" label="Change Password" />

        <default-modal v-model="dialog" @close="closeModal" title="Change Password" icon="mdi-lock-reset"
            :custom-style="'max-width: 500px'" titleClass="text-primary font-weight-bold">
            <v-col v-if="successAnimation" align="center">
                <LottieAnimation :animationData="successAnimationData" />
            </v-col>

            <div v-if="successfulSubmission">
                <v-form ref="formRef" v-model="isFormValid" @submit.prevent="changePassword">
                    <v-text-field v-model="adminForm.currentPassword" :rules="rules.password" required
                        label="Current Password" prepend-inner-icon="mdi-lock" class="custom-padding mb-2"
                        variant="outlined" type="password" />
                    <v-text-field v-model="adminForm.newPassword" :rules="rules.password" required label="New Password"
                        prepend-inner-icon="mdi-lock" class="custom-padding mb-2" variant="outlined" type="password" />
                    <v-text-field v-model="adminForm.newConfirmPassword" :rules="rules.confirmPassword" required
                        label="New Confirm Password" prepend-inner-icon="mdi-lock" variant="outlined" class="mb-2"
                        type="password" />

                    <div class="d-flex justify-end">
                        <v-btn class="px-5 me-3" variant="tonal" height="40" color="primary" :loading="loading"
                            @click="closeModal">
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
import { getAuth } from "@/pages/auth/authService";

const dialog = ref(false);
const loading = ref(false);

const isFormValid = ref(false);
const formRef = ref(null);
const adminForm = ref({
    currentPassword: "",
    newPassword: "",
    newConfirmPassword: ""
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
        currentPassword: "",
        newPassword: "",
        newConfirmPassword: ""
    };
};

const rules = {
    password: [
        v => !!v || 'Password is required',
        v => v.length >= 8 || 'Password must be at least 8 characters'
    ],
    confirmPassword: [
        v => !!v || 'Password confirmation is required',
        v => v === adminForm.value.newPassword || 'Passwords must match',
        v => v.length >= 8 || 'Password must be at least 8 characters'
    ]
};

const successAnimation = ref(false);
const successfulSubmission = ref(true);

const changePassword = async () => {
    loading.value = true;
    const { token } = getAuth();
    try {
        const response = await axios.post(
            "/api/change-password",
            {
                current_password: adminForm.value.currentPassword,
                new_password: adminForm.value.newPassword,
                new_confirm_password: adminForm.value.newConfirmPassword,
            },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json",
                },
            }
        );

        window.$snackbar("Password changed successfully!", "success");
        successAnimation.value = true;
        successfulSubmission.value = false;
        setTimeout(() => {
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
                "Please check your password requirements",
                "error"
            );
        } else if (error.response && error.response.status === 401) {
            window.$snackbar(
                "Current password is incorrect",
                "error"
            );
        } else if (error.response && error.response.data?.message) {
            window.$snackbar(error.response.data.message, "error");
        } else {
            window.$snackbar("Error changing password", "error");
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
</style>