<template>
    <v-card class="upload-card">
        <input type="file" ref="fileInput" @change="(e) => onUpload(e, imageType)" accept="image/*"
            style="display: none" />
        <v-icon v-if="!image" size="48" color="grey-lighten-1">mdi-image</v-icon>
        <v-img v-else :src="props.image" cover height="100%" width="100%"></v-img>
        <div class="upload-actions" v-if="isEditable">
            <v-btn icon="mdi-square-edit-outline" size="35" @click.stop="triggerImageUpload" class="action-btn" />
            <v-btn v-if="image && deletionNeeded" icon="mdi-delete-outline" size="35" @click.stop="onDelete(imageType)"
                class="action-btn" />
        </div>
    </v-card>

</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    imageType: {
        type: String,
        required: true,
    },
    image: {
        type: String,
        default: null,
    },
    onUpload: {
        type: Function,
        required: true,
    },
    onDelete: {
        type: Function,
        required: true,
    },
    quizStatus: {
        type: Object,
        required: true,
    },
    deletionNeeded: {
        type: Boolean,
        required: true,
    }

});

const fileInput = ref(null);

const triggerImageUpload = () => {
    fileInput.value.click();
};

const isEditable = computed(() => props.quizStatus.status === 'Unpublished');
</script>

<style scoped>
.upload-card {
    height: 150px;
    width: 150px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    border: 3px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.upload-actions {
    position: absolute;
    top: 5px;
    right: 5px;
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    background-color: rgba(255, 255, 255, 0.8) !important;
    font-size: 12px !important;
}
</style>