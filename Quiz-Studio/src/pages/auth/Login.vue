<template>
  <v-app>
    <v-container fluid class="fill-height pa-0">
      <v-card class="mx-auto">
        <v-row no-gutters>
          <v-col cols="12" md="6" class="d-none d-md-flex bg-primary">
            <div class="d-flex flex-column justify-center align-center px-16 h-100">
              <v-card width="150" height="150" variant="text">
                <LottieAnimation :animationData="animation" />
              </v-card>
              <h1 class="text-h4 font-weight-bold text-center text-white mt-8">
                PassaQuiz Studio
              </h1>
              <p class="text-caption text-white-darken-2 text-center mt-n1">by Team Passionate</p>
              <p class="text-subtitle-1 text-white text-center mt-4">
                Create engaging quizzes, track participants, and explore diverse question types
              </p>
            </div>
          </v-col>

          <v-col class="py-10" cols="12" md="6">
            <div class="d-flex flex-column justify-center h-100 px-8 px-sm-16">
              <div class="mb-8">
                <h2 class="text-h4 font-weight-bold mb-2">Sign In</h2>
                <p class="text-subtitle-1 text-medium-emphasis">
                  Welcome! Please enter your credentials
                </p>
              </div>

              <v-form @submit.prevent="login" v-model="isFormValid">
                <v-text-field v-model="form.email" label="Email" type="email" variant="outlined" :rules="emailRules"
                  prepend-inner-icon="mdi-email" class="mb-4" />

                <v-text-field v-model="form.password" label="Password" :type="showPassword ? 'text' : 'password'"
                  variant="outlined" :rules="passwordRules" prepend-inner-icon="mdi-lock"
                  :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  @click:append-inner="showPassword = !showPassword" class="mb-2" />

                <div class="d-flex justify-end mb-6">
                  <v-btn variant="text" to="/auth/reset-password" color="primary" class="px-0">
                    Forgot Password?
                  </v-btn>
                </div>

                <v-btn type="submit" color="primary" size="large" :loading="loading" :disabled="!isFormValid" block
                  class="mb-4" height="48">
                  Sign In
                </v-btn>
              </v-form>
            </div>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
  </v-app>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { setAuth } from "./authService";
import animation from "../../json/girl-on-computer.json";

const isFormValid = ref(false)
const showPassword = ref(false)
const loading = ref(false);
const form = ref({
  email: "",
  password: "",
});

const emailRules = [
  v => !!v || 'Email is required',
  v => /.+@.+\..+/.test(v) || 'Email must be valid'
]

const passwordRules = [
  v => !!v || 'Password is required'
]

const login = async () => {
  loading.value = true;
  try {
    const response = await axios.post("/api/login", {
      email: form.value.email,
      password: form.value.password,
    });

    if (
      response &&
      response.data &&
      response.data.token &&
      response.data.admin
    ) {
      setAuth(response.data.token, response.data.admin);
      window.$snackbar("Successful! Loggin in...", "success");
      setTimeout(() => {
        window.location.href = "/dashboard";
      }, 1000)

    } else {
      window.$snackbar("Oops! Something went wrong!", "error");
    }
  } catch (error) {
    if (error.reponse) {
      error.response.data.message || window.$snackbar("Invalid credentials!", "error");
    } else {
      window.$snackbar("Invalid credentials!", "error");
    }
  } finally {
    loading.value = false;
  }
};

</script>

<style scoped>
.text-white-darken-2 {
  opacity: 0.8;
}

.login-illustration {
  filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.1));
  transition: transform 0.3s ease;
}

.login-illustration:hover {
  transform: translateY(-5px);
}

:deep(.v-field) {
  border-radius: 12px;
}

:deep(.v-btn) {
  text-transform: none;
  letter-spacing: 0.5px;
  font-weight: 500;
}
</style>
