<template>
    <v-card class="upload-card">
        <input type="file" ref="jsonInput" @change="handleFileChange" accept=".json" style="display: none" />
        <LottieAnimation v-if="animationData" :animationData="animationData" />
        <v-icon v-else size="48" color="grey-lighten-1">mdi-file-document-outline</v-icon>
        <div class="upload-actions" v-if="isEditable">
            <v-btn icon="mdi-square-edit-outline" size="35" @click.stop="triggerJsonUpload" class="action-btn" />
            <v-btn v-if="animationData && deletionNeeded" size="35" icon="mdi-delete-outline" @click.stop="deleteJson"
                class="action-btn" />
        </div>
    </v-card>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    onUpload: Function,
    onDelete: Function,
    quizStatus: Object,
    animationData: Object,
    deletionNeeded: {
        type: Boolean,
        required: true,
    }
});

const jsonInput = ref(null);
const animationData = ref(props.animationData);

watch(() => props.animationData, (newValue) => {
    animationData.value = newValue;

});

const triggerJsonUpload = () => {
    jsonInput.value.click();
};

const handleFileChange = async (event) => {
    const file = event.target.files[0];
    if (file) {
        await props.onUpload(event, 'json');
    }
};

const deleteJson = () => {
    props.onDelete('json');
    animationData.value = null;
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
    font-size: 12px;
}
</style>