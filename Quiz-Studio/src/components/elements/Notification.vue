<template>
  <v-menu offset-y width="400" :close-on-content-click="false">
    <template v-slot:activator="{ props: activatorProps }">
      <v-btn icon v-bind="activatorProps">
        <v-badge color="error" :content="notificationCount">
          <v-icon color="grey">mdi-bell</v-icon>
        </v-badge>
      </v-btn>
    </template>

    <v-card class="custom-radius">
      <v-col>
        <div class="d-flex align-center justify-space-between">
          <h3 class="text-start"> Notifications </h3>
          <div>
            <v-tooltip location="top" text="Refresh notifications">
              <template v-slot:activator="{ props }">
                <v-btn v-bind="props" class="action-btn" @click="reloadNotification" icon="mdi-refresh" color="white"
                  flat :disabled="isLoading" />
              </template>
            </v-tooltip>
          </div>
        </div>
      </v-col>

      <div style="overflow-y: auto; max-height: 400px;" @scroll="handleScroll" ref="notificationContainer">
        <v-col v-if="isLoading" class="d-flex justify-center">
          <LottieAnimation :animationData="customLoader" height="200" width="200" />
        </v-col>

        <v-col v-else>
          <v-card v-for="notification in quizParticipants" :key="notification.id" to="/quizdetail/1"
            color="grey-lighten-4" class="mb-3" flat>
            <v-col>
              <div class="d-flex">
                <v-card height="100%" width="100%" max-height="50" max-width="50" class="me-2"
                  style="border-radius: 100% !important;">
                  <v-img cover :src="notification.quiz_image || 'https://picsum.photos/200/300'"></v-img>
                </v-card>
                <div>
                  <p>
                    <strong v-if="notification.participant_count <= 3">
                      {{ notification.participants.join(', ') }}
                    </strong>
                    <strong v-else>
                      {{ notification.participants.slice(0, 2).join(', ') }}
                      and {{ notification.participant_count - 2 }} others
                    </strong>
                    played <strong>{{ notification.quiz }}</strong>
                  </p>
                  <small>
                    {{ formatCompletionDate(notification.completed_at) }}
                  </small>
                </div>
              </div>
            </v-col>
          </v-card>
        </v-col>

      </div>
    </v-card>
  </v-menu>
</template>

<script setup>
import axios from "axios";
import { ref, onMounted } from "vue";
import customLoader from "@/json/loader.json";

const quizParticipants = ref([]);
const notificationCount = ref(0);
const isLoading = ref(false);
const isLoadingMore = ref(false);
const currentPage = ref(1);
const hasMorePages = ref(true);
const notificationContainer = ref(null);

const formatCompletionDate = (date) => {
  try {
    const options = {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    return new Date(date).toLocaleDateString('en-US', options);
  } catch (error) {
    console.error('Error formatting date:', error);
    return 'recently';
  }
};

const fetchQuizNotifications = async (page = 1) => {
  if (page === 1) {
    isLoading.value = true;
  } else {
    isLoadingMore.value = true;
  }

  try {
    const response = await axios.get(`/api/participant-quiz-summaries/paginated?page=${page}`);
    if (page === 1) {
      quizParticipants.value = response.data.data;
    } else {
      quizParticipants.value = [...quizParticipants.value, ...response.data.data];
    }
    notificationCount.value = response.data.total;
    hasMorePages.value = response.data.current_page < response.data.last_page;
    currentPage.value = response.data.current_page;
  } catch (error) {
    console.error("Failed to fetch notifications:", error);
    if (page === 1) {
      quizParticipants.value = [];
    }
  } finally {
    isLoading.value = false;
    isLoadingMore.value = false;
  }
};

const handleScroll = async (event) => {
  const container = event.target;
  const bottomOfContainer = container.scrollHeight - container.scrollTop - container.clientHeight;

  if (bottomOfContainer < 50 && !isLoadingMore.value && hasMorePages.value) {
    await fetchQuizNotifications(currentPage.value + 1);
  }
};

const reloadNotification = () => {
  currentPage.value = 1;
  hasMorePages.value = true;
  fetchQuizNotifications(1);
};

onMounted(() => {
  fetchQuizNotifications(1);
});
</script>

<style scoped>
.action-btn {
  font-size: 13px;
  border-radius: 5px !important;
  height: 30px !important;
  width: 30px !important;
}

::-webkit-scrollbar {
  width: 4px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #666;
}

::-webkit-scrollbar-button {
  display: none;
}
</style>
