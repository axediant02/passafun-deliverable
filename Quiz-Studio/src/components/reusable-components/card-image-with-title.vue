<template>
    <div class="d-flex flex-wrap align-center">
        <div class="d-flex flex-wrap align-center mb-10 me-10" v-for="item in items" :key="item.id">
            <v-card :to="{ path: `${basePath}/${item.id}` }" class="position-relative" width="250" max-width="250"
                height="100%" elevation="3" hover @mouseenter="setHovered(item.id, true)"
                @mouseleave="setHovered(item.id, false)">
                <div v-if="hoveredItems[item.id]" class="overlay">
                    <div class="overlay-text">
                        <h2>View Details</h2>
                    </div>
                </div>
                <v-img :src="item[imageField] || defaultImage" width="250" height="250" max-height="250" cover />

                <v-card-title class="pt-3 pb-1 text-subtitle-1">
                    {{ item[titleField] || titlePlaceholder }}
                </v-card-title>

                <v-card-text v-if="showCount">
                    <div class="d-flex align-center">
                        <v-icon :icon="countIcon" class="mr-1" size="small"></v-icon>
                        {{ item[countField] || 0 }} {{ countLabel }}
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const hoveredItems = ref({});

const setHovered = (itemId, value) => {
    hoveredItems.value[itemId] = value;
};

defineProps({
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
    defaultImage: {
        type: String,
        default: 'image.jpg'
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
        default: 'participant_count'
    },
    countLabel: {
        type: String,
        default: 'plays'
    },
    countIcon: {
        type: String,
        default: 'mdi-play-circle'
    }
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
</style>