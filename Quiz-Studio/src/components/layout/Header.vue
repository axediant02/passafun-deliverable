<template>
  <v-row>
    <v-app-bar v-if="isAdminAuthenticated" class="navbar border-b-sm" flat>
      <v-col class="d-md-none"></v-col>
      <v-row align="center" justify="space-between" class="navbar-title d-flex">
        <v-btn icon @click="toggleCollapse" class="toggle-button">
          <v-icon>
            {{ isCollapsed ? "mdi-menu" : "mdi-menu" }}
          </v-icon>
        </v-btn>

        <h3 class="text-primary font-weight-bold">
          PassaQuiz Studio
        </h3>

        <v-spacer />

        <v-col cols="auto">
          <Notification />
        </v-col>

        <v-btn class="mr-5 ml-10" @click="handleLogout" color="primary">
          <v-icon>mdi-logout</v-icon>
          Logout
        </v-btn>
      </v-row>
    </v-app-bar>
  </v-row>
</template>



<script setup>
import { ref, defineProps, defineEmits } from "vue";
import { logout } from "@/pages/auth/authService";

const emit = defineEmits();

const isAdminAuthenticated = ref(!!localStorage.getItem("token"));
const props = defineProps(["isCollapsed"]);

const toggleCollapse = () => {
  emit("toggle-collapse");
};

const handleLogout = () => {
  logout();
  isAdminAuthenticated.value = false;
};

const mainLogo =
  "data: image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAAAV1BMVEUXdP0Ab/0Aa/0Aaf0Abf2JsP7T4f/d6P/c5//k7P+3z/6jwf7P3v+Wt/4dd/0Qcv3////0+P8AYf0AZ/2Ps/5Iiv16pv7r8f9Qjv10ov6pxP7n8P/L2/5CGtN/AAAAj0lEQVR4AWIYkgBQyzwgUQwDAQANtopV9/7n/GatYPVmFhN6PQCEQN+iOEmTjPFYSNVj0MYY68CnxgT6r/6KCUU6v+YCD+Ijl34M7bWgU+gn1sZ0GHV1zZUewNSBi42pCRrAUjZt2ShAQ5iAIv5Kw0j1gLwRbUU/bKCuaDTRA4aDEF0nYlYNKqHk+kCj888FFXUKEtrIAfAAAAAASUVORK5CYII=";
</script>

<style scoped>
.navbar-title {
  margin: 0;
  padding-left: 4px;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.toggle-button {
  margin-right: 10px;
}

@media (max-width: 600px) {
  .navbar-title {
    justify-content: space-between;
  }

  .toggle-button {
    position: absolute;
    left: 10px;
  }
}

@media (min-width: 601px) {
  .navbar-title {
    justify-content: flex-start;
  }
}
</style>
