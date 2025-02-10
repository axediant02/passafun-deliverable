<template>
  <v-card class="mb-6 border rounded-lg" width="100%" elevation="0">
    <v-card-title class="text-h6 font-weight-bold px-6 pt-4 mb-3">
      Theme Settings
    </v-card-title>
    <v-card-text v-if="props.quizStatus.status === 'Unpublished' && props.adminRole === 1">

      <div class="info-grid">
        <div class="info-item mb-4">
          <div class="info-label">Theme</div>
          <div class="info-content d-flex align-center">
            <v-select v-model="themeId" :items="availableThemes" :value="theme.name" item-value="id"
              @change="debouncedSave(quizId)" item-title="name" variant="outlined" hide-details>
              <template v-slot:item="{ props: itemProps, item }">
                <v-list-item v-bind="itemProps" class="border-b-sm">
                  <v-list-item-subtitle>
                    <div class="d-flex align-center">
                      <v-chip :style="{ backgroundColor: item?.raw?.main_color || '#cccccc' }" class="px-3 rounded-0"
                        style="border-radius: 10px 0 0 10px !important;" size="small">
                      </v-chip>
                      <v-chip :style="{ backgroundColor: item?.raw?.accent_color || '#cccccc' }" class="px-3 rounded-0"
                        size="small">
                      </v-chip>
                      <v-chip :style="{ backgroundColor: item?.raw?.text_color || '#cccccc' }" class="px-3 rounded-0"
                        size="small">
                      </v-chip>
                      <v-chip :style="{ backgroundColor: item?.raw?.button_color || '#cccccc' }" class="px-3 rounded-0"
                        size="small">
                      </v-chip>
                      <v-chip v-if="item?.raw?.background_type === 'color'"
                        :style="{ backgroundColor: item?.raw?.background_value || '#cccccc' }"
                        style="border-radius: 0 10px 10px 0 !important;" class="px-3 rounded-0" size="small">
                      </v-chip>
                      <v-chip v-else class="px-3 rounded-0" size="small"
                        style="border-radius: 0 10px 10px 0 !important;">
                        <v-icon>mdi-image</v-icon>
                      </v-chip>
                    </div>
                  </v-list-item-subtitle>
                </v-list-item>
              </template>
            </v-select>
          </div>
        </div>
        <div class="info-item mb-4">
          <div class="info-label">Main Color</div>
          <div class="info-content d-flex align-center">
            <div class="me-3" :style="{ backgroundColor: theme.main_color }"
              style="width: 30px; height: 30px; border-radius: 5px"></div>
            <span>{{ theme.main_color }}</span>
          </div>
        </div>

        <div class="info-item mb-4">
          <div class="info-label">Accent Color</div>
          <div class="info-content d-flex align-center">
            <div class="me-3" :style="{ backgroundColor: theme.accent_color }"
              style="width: 30px; height: 30px; border-radius: 5px"></div>
            <span>{{ theme.accent_color }}</span>
          </div>
        </div>

        <div class="info-item mb-4">
          <div class="info-label">Text Color</div>
          <div class="info-content d-flex align-center">
            <div class="me-3" :style="{ backgroundColor: theme.text_color }"
              style="width: 30px; height: 30px; border-radius: 5px"></div>
            <span>{{ theme.text_color }}</span>
          </div>
        </div>

        <div class="info-item mb-4">
          <div class="info-label">Button Color</div>
          <div class="info-content d-flex align-center">
            <div class="me-3" :style="{ backgroundColor: theme.button_color }"
              style="width: 30px; height: 30px; border-radius: 5px"></div>
            <span>{{ theme.button_color }}</span>
          </div>
        </div>

        <div class="info-item mb-4">
          <div class="info-label">Background Type</div>
          <div class="info-content d-flex align-center">{{ theme.background_type }}</div>
        </div>

        <div class="info-item">
          <div class="info-label">Background Value</div>
          <div class="info-content d-flex align-center">
            <div v-if="theme.background_type === 'color'" class="me-3"
              :style="{ backgroundColor: theme.background_value }"
              style="width: 30px; height: 30px; border-radius: 5px"></div>
            <img v-else-if="theme.background_type === 'image'" :src="theme.background_value" alt="Background Image"
              style="max-width: 100px; height: auto" class="rounded" />
            <span>{{ theme.background_value }}</span>
          </div>
        </div>
      </div>
    </v-card-text>

    <v-card-text v-else>
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Selected Theme</div>
          <div class="info-content">{{ theme.name || "N/A" }}</div>
        </div>
        <div class="info-item">
          <div class="info-label">Color Scheme</div>
          <div class="info-content d-flex mb-4">
            <div class="d-flex align-center me-4">
              <div class="me-2" :style="{ backgroundColor: theme.main_color }"
                style="width: 24px; height: 24px; border-radius: 4px"></div>
              <span class="text-caption">Main</span>
            </div>
            <div class="d-flex align-center me-4">
              <div class="me-2" :style="{ backgroundColor: theme.accent_color }"
                style="width: 24px; height: 24px; border-radius: 4px"></div>
              <span class="text-caption">Accent</span>
            </div>
            <div class="d-flex align-center me-4">
              <div class="me-2" :style="{ backgroundColor: theme.text_color }"
                style="width: 24px; height: 24px; border-radius: 4px"></div>
              <span class="text-caption">Text</span>
            </div>
            <div class="d-flex align-center">
              <div class="me-2" :style="{ backgroundColor: theme.button_color }"
                style="width: 24px; height: 24px; border-radius: 4px"></div>
              <span class="text-caption">Button</span>
            </div>
          </div>
        </div>
        <div class="info-item">
          <div class="info-label">Background</div>
          <div class="info-content d-flex align-center">
            <v-chip size="small" class="me-2">{{ theme.background_type }}</v-chip>
            <div v-if="theme.background_type === 'color'" class="me-2"
              :style="{ backgroundColor: theme.background_value }"
              style="width: 24px; height: 24px; border-radius: 4px">
            </div>
            <img v-else-if="theme.background_type === 'image'" :src="theme.background_value" alt="Background Image"
              style="max-width: 100px; height: auto" class="rounded" />
          </div>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
import axios from "axios";
import { defineProps, ref, watch } from "vue";
import { getAuth } from "@/pages/auth/authService";
import { debounce } from "lodash";

const props = defineProps({
  theme: {
    type: Object,
    required: true,
  },
  quizStatus: {
    type: Object,
    required: true,
  },
  availableThemes: {
    type: Array,
    required: true,
  },
  quizId: {
    type: Number,
    required: true,
  },
  adminRole: Number
});
const emit = defineEmits(["updatedTheme"]);
const themeId = ref(props.theme.id);
const quizId = ref(props.quizId);
const currentTheme = ref(props.theme);

const updateTheme = async (id) => {
  const { token } = getAuth();
  const themeData = {
    themeId: themeId.value,
  };

  await axios.put(`api/quizzes/${id}/updateTheme`, themeData, {
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
  });

  emit("updatedTheme", themeData);
};

const debouncedSave = debounce((id) => {
  updateTheme(id);
}, 500);

watch(themeId, () => {
  if (themeId.value) {
    currentTheme.value =
      props.availableThemes.find((t) => t.id === themeId.value) || props.theme;
  }
  debouncedSave(quizId.value);
});
</script>

<style scoped>
.info-grid {
  display: grid;
  padding: 1rem;
}

.info-item {
  display: grid;
  gap: 0.5rem;
}

.info-label {
  font-weight: 500;
  color: #616161;
  font-size: 12px;
}

.info-content {
  min-height: 40px;
}
</style>