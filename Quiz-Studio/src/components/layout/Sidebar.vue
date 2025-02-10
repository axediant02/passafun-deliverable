<template>
  <v-navigation-drawer v-if="isAuthenticated" app :rail="isCollapsed" permanent>
    <v-list density="compact" nav>
      <v-list-item prepend-icon="mdi-view-dashboard" :title="isCollapsed ? '' : 'Dashboard'"
        to="/dashboard"></v-list-item>
      <v-list-item prepend-icon="mdi-controller" :title="isCollapsed ? '' : 'Quizzes'" to="/quiz"></v-list-item>
      <v-list-item prepend-icon="mdi-palette" :title="isCollapsed ? '' : 'Quiz Themes'" to="/themes"></v-list-item>
      <v-list-item v-if="admin.role_id !== 2" prepend-icon="mdi-shield-account" :title="isCollapsed ? '' : 'Admins'" to="/admins"></v-list-item>
      <v-list-item prepend-icon="mdi-account-group" :title="isCollapsed ? '' : 'Participants'"
        to="/participant"></v-list-item>
    </v-list>

    <v-divider></v-divider>

    <v-list-item :prepend-icon="theme === 'light' ? 'mdi-weather-sunny' : 'mdi-weather-night'"
      :title="isCollapsed ? '' : 'Toggle Theme'" @click="toggleTheme"></v-list-item>
  </v-navigation-drawer>
</template>

<script setup>
import { ref, computed, inject } from "vue";
import { getAuth, isLoggedIn } from "@/pages/auth/authService";

const {admin} = getAuth();
const theme = inject("theme");
const setTheme = inject("setTheme");
const isAuthenticated = computed(() => isLoggedIn());


const props = defineProps(['isCollapsed']);

function toggleTheme() {
  setTheme(theme.value === "myCustomLightTheme" ? "dark" : "myCustomLightTheme");
}
</script>
