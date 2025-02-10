<template>
    <v-app class="pageContainer">
        <v-container class="fill-height">
            <v-row justify="center" align="center">
                <v-col cols="12" sm="8" md="6" lg="5">
                    <v-card class="auth-card">
                        <div class="progress-bar">
                            <div class="progress" :style="{ width: `${(currentStep / 3) * 100}%` }"></div>
                        </div>

                        <v-card-text class="pa-8">
                            <v-window v-model="currentStep">
                                <v-window-item :value="1">
                                    <h2 class="text-h5 font-weight-bold text-center mb-6">Reset Password</h2>
                                    <p class="text-subtitle-1 text-medium-emphasis text-center mb-6">
                                        Enter your email address to reset your password
                                    </p>
                                    <v-form ref="emailFormRef" v-model="isEmailFormValid" @submit.prevent="submitEmail">
                                        <v-text-field v-model="resetForm.email" :rules="rules.email" label="Email"
                                            variant="outlined" prepend-inner-icon="mdi-email" class="mb-4" required />
                                        <v-btn block color="primary" size="large" :loading="loading"
                                            @click="submitEmail" class="mt-2">
                                            Continue
                                        </v-btn>
                                        <v-btn block variant="text" to="/auth/login">
                                            Back to Login
                                        </v-btn>
                                    </v-form>
                                </v-window-item>

                                <v-window-item :value="2">
                                    <h2 class="text-h5 font-weight-bold text-center mb-4">Verify OTP</h2>
                                    <p class="text-subtitle-1 text-medium-emphasis text-center mb-6">
                                        We've sent a verification code to<br>
                                        <strong>{{ resetForm.email }}</strong>
                                    </p>
                                    <v-otp-input v-model="resetForm.otp" :length="6" type="number"
                                        class="mb-6 justify-center" />
                                    <div class="d-flex flex-column gap-3">
                                        <v-btn block color="primary" size="large" :loading="loading" @click="submitOtp">
                                            Verify Code
                                        </v-btn>
                                        <v-btn block variant="text" @click="currentStep = 1">
                                            Back to Email
                                        </v-btn>
                                    </div>
                                </v-window-item>

                                <v-window-item :value="3">
                                    <h2 class="text-h5 font-weight-bold text-center mb-6">Create New Password</h2>
                                    <v-form ref="passwordFormRef" v-model="isPasswordFormValid">
                                        <v-text-field v-model="resetForm.newPassword" :rules="rules.password"
                                            label="New Password" type="password" variant="outlined"
                                            prepend-inner-icon="mdi-lock" class="mb-4" required />
                                        <v-text-field v-model="resetForm.confirmPassword"
                                            :rules="[...rules.password, passwordConfirmationRule]"
                                            label="Confirm Password" type="password" variant="outlined"
                                            prepend-inner-icon="mdi-lock-check" class="mb-6" required />
                                        <div class="d-flex flex-column gap-3">
                                            <v-btn block color="primary" size="large" :loading="loading"
                                                @click="resetPassword">
                                                Reset Password
                                            </v-btn>
                                            <v-btn block variant="text" @click="currentStep = 2">
                                                Back
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-window-item>
                            </v-window>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-app>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const currentStep = ref(1);
const loading = ref(false);
const isEmailFormValid = ref(false);
const isPasswordFormValid = ref(false);

const resetForm = ref({
    email: '',
    otp: '',
    newPassword: '',
    confirmPassword: ''
});

const rules = {
    email: [
        inputValue => !!inputValue || 'Email is required',
        inputValue => /.+@.+\..+/.test(inputValue) || 'Email must be valid'
    ],
    password: [
        inputValue => !!inputValue || 'Password is required',
        inputValue => inputValue?.length >= 8 || 'Password must be at least 8 characters'
    ]
};

const passwordConfirmationRule = computed(() => {
    return inputValue => inputValue === resetForm.value.newPassword || 'Passwords must match'
});

const submitEmail = async () => {
    if (!isEmailFormValid.value) return;
    loading.value = true;
    try {
        await axios.post('/api/password/email', {
            email: resetForm.value.email
        });
        currentStep.value = 2;
        window.$snackbar('OTP sent successfully', 'success');
    } catch (error) {
        if (error.response?.status === 404) {
            window.$snackbar('Email not found', 'error');
        } else {
            window.$snackbar('Failed to send OTP', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const submitOtp = async () => {
    if (!resetForm.value.otp) return;
    loading.value = true;
    try {
        await axios.post('/api/password/verify-otp', {
            email: resetForm.value.email,
            otp: resetForm.value.otp
        });
        currentStep.value = 3;
        window.$snackbar('OTP verified successfully', 'success');
    } catch (error) {
        if (error.response?.status === 400) {
            window.$snackbar('Invalid or expired OTP', 'error');
        } else {
            window.$snackbar('Failed to verify OTP', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const resetPassword = async () => {
    if (!isPasswordFormValid.value) return;
    loading.value = true;
    try {
        await axios.post('/api/password/reset', {
            email: resetForm.value.email,
            otp: resetForm.value.otp,
            password: resetForm.value.newPassword,
            password_confirmation: resetForm.value.confirmPassword
        });
        window.$snackbar('Password reset successfully', 'success');
        router.push('/auth/login');
    } catch (error) {
        if (error.response?.status === 400) {
            window.$snackbar('Invalid or expired OTP', 'error');
        } else {
            window.$snackbar('Failed to reset password', 'error');
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.auth-card {
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    position: relative;
}

.progress-bar {
    height: 4px;
    background: rgba(var(--v-theme-primary), 0.1);
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
}

.progress {
    height: 100%;
    background: rgb(var(--v-theme-primary));
    transition: width 0.3s ease;
}

.v-window-item {
    transition: all 0.3s ease;
}

:deep(.v-field) {
    border-radius: 12px;
}

:deep(.v-otp-input) {
    gap: 8px;
}

:deep(.v-otp-input input) {
    border-radius: 12px;
    font-size: 1.5rem;
    background: rgba(var(--v-theme-primary), 0.05);
}
</style>