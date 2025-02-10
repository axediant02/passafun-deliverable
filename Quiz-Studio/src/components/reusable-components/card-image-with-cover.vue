<template>
    <div>
        <v-col v-if="featuredItems.length > 0">
            <h1 class="text-h6">Featured ({{ featuredItems.length }}/10)</h1>
        </v-col>
        <v-col v-if="featuredItems.length > 0">
            <div class="d-flex flex-wrap align-center">
                <div class="d-flex flex-wrap align-center mb-10 me-10" v-for="item in featuredItems" :key="item.id">
                    <v-card :to="{ path: `${basePath}/${item.id}` }" class="position-relative" width="250"
                        max-width="250" height="100%" min-height="350px" elevation="3" hover
                        @mouseenter="setHovered(item.id, true)" @mouseleave="setHovered(item.id, false)">

                        <div class="feature-label bg-primary">
                            Featured
                        </div>

                        <div v-if="hoveredItems[item.id]" class="overlay">
                            <div class="overlay-text">
                                <h2>View Details</h2>
                            </div>
                        </div>
                        <v-img :src="item[coverField] || defaultImage" width="250" height="250" max-height="250"
                            cover />

                        <div class="d-flex w-100 px-2">
                            <v-img :src="item[imageField] || defaultImage" class="rounded-lg mt-3" width="50"
                                height="50" max-height="50" max-width="50" cover />
                            <div class="w-100">
                                <v-card-title class="pb-1 text-subtitle-1 text-wrap">
                                    {{ item[titleField] || titlePlaceholder }}
                                </v-card-title>

                                <v-card-text v-if="showCount" class="d-flex justify-space-between">
                                    <div class="d-flex align-center">
                                        <v-icon :icon="countIcon" class="mr-1" size="small"></v-icon>
                                        {{ item[countField] || 0 }} {{ countLabel }}
                                    </div>

                                    <v-btn v-if="props.adminRole === 1 && item.quiz_status_id === 2" size="35"
                                        variant="text" @click.stop.prevent>
                                        <v-icon>mdi-dots-vertical</v-icon>
                                        <v-menu activator="parent">
                                            <v-list>
                                                <v-list-item v-for="(dropdownItem, index) in dropdownItems(item)"
                                                    :key="index" :value="index"
                                                    @click="handleDropdownAction(dropdownItem.action, item)">
                                                    <v-list-item-title>{{ dropdownItem.title }}</v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </v-btn>
                                </v-card-text>
                            </div>
                        </div>
                    </v-card>
                </div>
            </div>
        </v-col>

        <v-divider class="mb-5" v-if="featuredItems.length > 0"></v-divider>

        <v-col>
            <div class="d-flex flex-wrap align-center">
                <div class="d-flex flex-wrap align-center mb-10 me-10" v-for="item in nonFeaturedItems" :key="item.id">
                    <v-card :to="{ path: `${basePath}/${item.id}` }" class="position-relative" width="250"
                        max-width="250" height="100%" min-height="350px" elevation="3" hover
                        @mouseenter="setHovered(item.id, true)" @mouseleave="setHovered(item.id, false)">

                        <div v-if="hoveredItems[item.id]" class="overlay">
                            <div class="overlay-text">
                                <h2>View Details</h2>
                            </div>
                        </div>
                        <v-img :src="item[coverField] || defaultImage" width="250" height="250" max-height="250"
                            cover />

                        <div class="d-flex w-100 px-2">
                            <v-img :src="item[imageField] || defaultImage" class="rounded-lg mt-3" width="50"
                                height="50" max-height="50" max-width="50" cover />
                            <div class="w-100">
                                <v-card-title class="pt-3 pb-1 text-subtitle-1 text-wrap">
                                    {{ item[titleField] || titlePlaceholder }}
                                </v-card-title>

                                <v-card-text v-if="showCount" class="d-flex justify-space-between">
                                    <div class="d-flex align-center">
                                        <v-icon :icon="countIcon" class="mr-1" size="small"></v-icon>
                                        {{ item[countField] || 0 }} {{ countLabel }}
                                    </div>

                                    <v-btn v-if="props.adminRole === 1 && item.quiz_status_id === 2" size="35"
                                        variant="text" @click.stop.prevent>
                                        <v-icon>mdi-dots-vertical</v-icon>
                                        <v-menu activator="parent">
                                            <v-list>
                                                <v-list-item v-for="(dropdownItem, index) in dropdownItems(item)"
                                                    :key="index" :value="index"
                                                    @click="handleDropdownAction(dropdownItem.action, item)">
                                                    <v-list-item-title>{{ dropdownItem.title }}</v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </v-btn>
                                </v-card-text>
                            </div>
                        </div>
                    </v-card>
                </div>
            </div>
        </v-col>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const hoveredItems = ref({});

const setHovered = (itemId, value) => {
    hoveredItems.value[itemId] = value;
};

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    basePath: {
        type: String,
        required: true
    },
    imageField: {
        type: String,
        default: 'thumbnailUrl'
    },
    coverField: {
        type: String,
        default: 'coverImageUrl'
    },
    defaultImage: {
        type: String,
        default: 'https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'
    },
    titleField: {
        type: String,
        default: 'name'
    },
    titlePlaceholder: {
        type: String,
        default: 'No name Available'
    },
    showCount: {
        type: Boolean,
        default: true
    },
    countField: {
        type: String,
        default: 'participant_quiz_summaries_count'
    },
    countLabel: {
        type: String,
        default: 'plays'
    },
    countIcon: {
        type: String,
        default: 'mdi-account-group'
    },
    dropdownItems: {
        type: Array,
        default: () => [
            { title: 'Pin Quiz', action: 'featureQuiz' },
            { title: 'Unpin Quiz', action: 'featureQuiz' },
        ]
    },
    adminRole: {
        type: Number,
        required: true
    }
});



const emit = defineEmits(['feature-quiz']);

const handleDropdownAction = (action, item) => {
    if (item.quiz_status_id === 2) {
        const featuredCount = props.items.filter(i => i.is_featured).length;
        if (featuredCount >= 10) {
            window.$snackbar("You can't feature more than 10 quizzes.", "error");
            return;
        }
        emit('feature-quiz', item);
    } else {
        window.$snackbar("Only published quizzes can be featured.", "error");
    }
};

const featuredItems = computed(() => {
    return props.items.filter(item => item.is_featured);
});

const nonFeaturedItems = computed(() => {
    return props.items.filter(item => !item.is_featured);
});
</script>

<style scoped>
.overlay {
    position: absolute;
    width: 100%;
    height: 250px;
    overflow: hidden;
    z-index: 1000;
    background-color: rgb(0, 0, 0, 0.7);
}

.overlay-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    justify-content: center;
    width: 100%;
    text-align: center;
    color: white;
}

.feature-label {
    border-radius: 0 0 10px 0;
    width: 100px;
    text-align: center;
    position: absolute;
    z-index: 100;
    top: 0;
    left: 0;
}
</style>