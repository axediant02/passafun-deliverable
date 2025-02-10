<template>
    <v-container fluid>
        <page-title-with-action-buttons :title="{ show: true, text: 'Quiz Themes' }" :backButton="{ show: false }" />

        <v-col class="px-0">
            <div class="d-flex align-center">
                <Searchbar searchLabel="Search for a Theme" v-model="searchQuery" />
                <AddThemeModal v-if="adminRole === 1" />
            </div>
        </v-col>

        <v-col v-if="loading" class="d-flex justify-center align-center">
            <LottieAnimation :animationData="customLoader" style="width: 200px; height: 200px;" />
        </v-col>

        <v-col v-if="!loading">

            <v-container v-if="!isThemeExists" class="text-center text-h4" style="color: gray">Theme not Found</v-container>

            <div v-else>
            <div v-if="themes.length === 0" class="d-flex justify-center align-center">
                <h3>Theme not found</h3>
            </div>
            <div v-else class="d-flex flex-wrap justify-start">
                <v-card v-for="theme in themes" :key="theme.id" max-width="500" class="mb-5 me-5">
                    <div class="d-flex align-center justify-space-between">
                        <h3 class="ml-5 py-3">{{ theme.name }}</h3>
                        <div v-if="adminRole === 1">
                            <div v-if="theme.isEditing" class="d-flex">
                                <v-btn icon="mdi-check" variant="text" color="success" class="me-2"
                                    @click="saveChanges(theme)" />
                                <v-btn icon="mdi-close" variant="text" color="error" @click="cancelEdit(theme)" />
                            </div>

                            <v-menu v-else>
                                <template v-slot:activator="{ props }">
                                    <v-btn v-bind="props" icon="mdi-dots-vertical" variant="text" color="grey" />
                                </template>
                                <v-list>
                                    <v-list-item @click="startEdit(theme)">
                                        <v-list-item-title>Edit</v-list-item-title>
                                    </v-list-item>
                                    <v-list-item @click="deleteTheme(theme.id)">
                                        <v-list-item-title>Delete</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </div>
                    </div>
                    <v-divider />
                    <v-table>
                        <tbody>
                            <tr>
                                <td><v-icon>mdi-palette-outline</v-icon> Main Color</td>
                                <td class="d-flex align-center">
                                    <template v-if="theme.isEditing">
                                        <v-text-field v-model="theme.main_color" type="color" class="me-3"
                                            style="width: 100px" />
                                        <span>{{ theme.main_color }}</span>
                                    </template>
                                    <template v-else>
                                        <div class="me-3" :style="{ backgroundColor: theme.main_color }"
                                            style="width: 30px; height: 30px; border-radius: 5px;">
                                        </div>
                                        <span>{{ theme.main_color }}</span>
                                    </template>
                                </td>
                            </tr>

                            <tr>
                                <td><v-icon>mdi-palette-outline</v-icon> Accent Color</td>
                                <td class="d-flex align-center">
                                    <template v-if="theme.isEditing">
                                        <v-text-field v-model="theme.accent_color" type="color" class="me-3"
                                            style="width: 100px" />
                                        <span>{{ theme.accent_color }}</span>
                                    </template>
                                    <template v-else>
                                        <div class="me-3" :style="{ backgroundColor: theme.accent_color }"
                                            style="width: 30px; height: 30px; border-radius: 5px;">
                                        </div>
                                        <span>{{ theme.accent_color }}</span>
                                    </template>
                                </td>
                            </tr>

                            <tr>
                                <td><v-icon>mdi-palette-outline</v-icon> Text Color</td>
                                <td class="d-flex align-center">
                                    <template v-if="theme.isEditing">
                                        <v-text-field v-model="theme.text_color" type="color" class="me-3"
                                            style="width: 100px" />
                                        <span>{{ theme.text_color }}</span>
                                    </template>
                                    <template v-else>
                                        <div class="me-3" :style="{ backgroundColor: theme.text_color }"
                                            style="width: 30px; height: 30px; border-radius: 5px;">
                                        </div>
                                        <span>{{ theme.text_color }}</span>
                                    </template>
                                </td>
                            </tr>

                            <tr>
                                <td><v-icon>mdi-palette-outline</v-icon> Button Color</td>
                                <td class="d-flex align-center">
                                    <template v-if="theme.isEditing">
                                        <v-text-field v-model="theme.button_color" type="color" class="me-3"
                                            style="width: 100px" />
                                        <span>{{ theme.button_color }}</span>
                                    </template>
                                    <template v-else>
                                        <div class="me-3" :style="{ backgroundColor: theme.button_color }"
                                            style="width: 30px; height: 30px; border-radius: 5px;">
                                        </div>
                                        <span>{{ theme.button_color }}</span>
                                    </template>
                                </td>
                            </tr>

                            <tr>
                                <td><v-icon>mdi-compare</v-icon> Background Type</td>
                                <td class="d-flex align-center">
                                    <template v-if="theme.isEditing">
                                        <v-select v-model="theme.background_type" :items="['color', 'image']"
                                            label="Background Type" variant="outlined" style="width: 150px" />
                                    </template>
                                    <template v-else>
                                        <span>{{ theme.background_type }}</span>
                                    </template>
                                </td>
                            </tr>

                            <tr>
                                <td><v-icon>mdi-palette-outline</v-icon> Background Value</td>
                                <td class="d-flex align-center">
                                    <template v-if="theme.isEditing">
                                        <template v-if="theme.background_type === 'color'">
                                            <v-text-field v-model="theme.background_value" type="color" class="me-3"
                                                style="width: 100px" />
                                            <span>{{ theme.background_value }}</span>
                                        </template>
                                        <template v-else-if="theme.background_type === 'image'">
                                            <v-card height="93px" width="150px" variant="outlined" color="grey"
                                                class="d-flex align-center justify-center position-relative">
                                                <input type="file" :ref="el => theme.imageInput = el"
                                                    @change="handleImageUpload(theme)" accept="image/*"
                                                    style="display: none" />
                                                <v-icon v-if="!theme.previewImage">mdi-image</v-icon>
                                                <v-img v-else :src="theme.previewImage" cover height="100%"
                                                    width="100%"></v-img>
                                                <div class="action-buttons position-absolute"
                                                    style="top: 5px; right: 5px;">
                                                    <v-btn class="me-2" icon="mdi-square-edit-outline" size="25px"
                                                        color="grey" @click.stop="triggerImageUpload(theme)" />
                                                    <v-btn v-if="theme.previewImage" icon="mdi-delete-outline"
                                                        size="25px" color="grey" @click.stop="deleteImage(theme)" />
                                                </div>
                                            </v-card>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <div v-if="theme.background_type === 'color'" class="me-3"
                                            :style="{ backgroundColor: theme.background_value }"
                                            style="width: 30px; height: 30px; border-radius: 5px;">
                                        </div>
                                        <img v-else-if="theme.background_type === 'image'" :src="theme.background_value"
                                            alt="Background Image" style="max-width: 100px; height: auto;" />
                                        <span>{{ theme.background_value }}</span>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>
            </div>
        </div>
        </v-col>
    </v-container>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { getAuth } from '@/pages/auth/authService';
import customLoader from "@/json/loader.json";
import { debounce } from 'lodash';

const loading = ref(true);
const themes = ref([]);
const searchQuery = ref('');
const adminRole = ref();
const isThemeExists = ref(true);
const getAdminRole = () => {
    const adminData = JSON.parse(localStorage.getItem('admin'));
    adminRole.value = adminData?.role_id;
}

const processThemesFromResponse = (fetchedThemes) =>
    fetchedThemes.map((theme) => ({
        ...theme,
        isEditing: false,
        originalName: theme.name
    }));

const fetchThemeData = async () => {
    isThemeExists.value = true;
    const { token } = getAuth();
    try {
        const response = await axios.get('/api/themes', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        themes.value = processThemesFromResponse(response.data);
        loading.value = false;
    } catch (error) {
        console.error('Failed to fetch theme data.', error);
    }
};

const searchThemes = async () => {
    const { token } = getAuth();
    try {
        const response = await axios.get('/api/themes/searching', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
            params: {
                name: searchQuery.value,
            },
        });
        if (response.data.length === 0) {
            themes.value = [];
            isThemeExists.value = false;
        } else {
            themes.value = processThemesFromResponse(response.data);
            isThemeExists.value = true;
        }
    } catch (error) {
        console.error('Failed to search themes.', error);
        window.$snackbar("Failed to search themes", "error");
    }
};

const debouncedSearchThemes = debounce(searchThemes, 300);

const startEdit = (theme) => {
    theme.isEditing = true;
    theme.originalName = theme.name;
};

const cancelEdit = (theme) => {
    theme.isEditing = false;
    theme.name = theme.originalName;
};

const saveChanges = async (theme) => {
    const { token } = getAuth();
    try {
        const response = await axios.put(`/api/themes/${theme.id}`, {
            name: theme.name,
            main_color: theme.main_color,
            accent_color: theme.accent_color,
            text_color: theme.text_color,
            button_color: theme.button_color,
            background_type: theme.background_type,
            background_value: theme.background_value,
        }, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        const index = themes.value.findIndex((t) => t.id === theme.id);
        themes.value[index] = response.data;
        theme.isEditing = false;
        window.$snackbar("Theme updated successfully!", "success");
    } catch (error) {
        if (error.response && error.response.status === 403) {
            window.$snackbar("Oops! You don't have access to perform this action!", "error");
        } else {
            console.error("Error updating theme", "error");
        }
    }
};

const deleteTheme = async (themeId) => {
    const { token } = getAuth();
    try {
        await axios.delete(`/api/themes/${themeId}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        themes.value = themes.value.filter((theme) => theme.id !== themeId);
        window.$snackbar("Theme deleted successfully!", "success");
    } catch (error) {
        if (error.response && error.response.status === 403) {
            window.$snackbar("Oops! You don't have access to perform this action!", "error");
        } if (error.response && error.response.status === 400) {
            window.$snackbar(error.response.data.message, "error");
        } else {
            console.error("Error deleting theme", "error");
        }
    }
};

const handleImageUpload = (theme) => (event) => {
    const file = event.target.files[0];
    if (file) {
        theme.imageFile = file;
        theme.previewImage = URL.createObjectURL(file);
        theme.imageRemoved = false;
    }
};

const triggerImageUpload = (theme) => {
    if (theme.imageInput) {
        theme.imageInput.click();
    }
};

const deleteImage = (theme) => {
    theme.previewImage = null;
    theme.imageFile = null;
    theme.imageRemoved = true;
    if (theme.imageInput) {
        theme.imageInput.value = '';
    }
};

onMounted(() => {
    fetchThemeData();
    themes.value.forEach(theme => {
        theme.imageInput = null;
        theme.previewImage = null;
        theme.imageFile = null;
        theme.imageRemoved = false;
    });
    getAdminRole();
});

watch(searchQuery, (newQuery) => {
    if (newQuery) {
        debouncedSearchThemes();
    } else {
        fetchThemeData();
    }
});
</script>
