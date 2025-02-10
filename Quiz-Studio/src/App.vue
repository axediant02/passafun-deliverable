<template>
  <v-app :theme="theme">
    <v-main>

      <Header :isCollapsed="isCollapsed" @toggle-collapse="toggleCollapse" v-if="isAuthenticated" />
      <Sidebar :isCollapsed="isCollapsed" v-if="isAuthenticated" />
      <v-app>
        <v-main>
          <SnackbarMessage />
          <v-container>
            <v-layout>
              <v-main>
                <router-view />
              </v-main>
            </v-layout>
          </v-container>
        </v-main>
      </v-app>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref, provide, computed } from "vue";
import Sidebar from "./components/layout/Sidebar.vue";
import { getAuth } from "./pages/auth/authService";

const theme = ref("myCustomLightTheme");
const isAuthenticated = computed(() => !!getAuth().token);
const isCollapsed = ref(false);

const toggleCollapse = () => {
  isCollapsed.value = !isCollapsed.value;
};


function setTheme(newTheme) {
  theme.value = newTheme;
}

provide("theme", theme);
provide("setTheme", setTheme);
</script>

<style>
.v-application {
  transition: background-color 0.3s ease, color 0.3s ease;
}
</style>
