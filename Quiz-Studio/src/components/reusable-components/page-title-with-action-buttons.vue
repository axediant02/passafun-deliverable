<template>
    <div>
        <v-col class="my-5 d-flex justify-space-between align-center">
            <div class="d-flex align-center">
                <div v-if="backButton.show">
                    <ActionButton @click="() => handleBack(backButton.to)" color="primary" icon="mdi-arrow-left"
                        variant="text" label="Back" flat />
                </div>
                <div v-if="title.show">
                    <h2>{{ title.text }}</h2>
                </div>
            </div>

            <div v-if="adminRole === 1" class="d-flex align-center">
                <template v-for="button in actionButtons">
                    <ActionButton v-if="button.show" :key="button.label" :class="button.class"
                        :variant="button.variant || 'tonal'" :color="button.color || 'primary'" :icon="button.icon"
                        :prepend-icon="button.prependIcon" :label="button.label" :href="button.href"
                        :target="button.target" :disabled="button.disabled" :loading="button.isLoading" @click="button.onClick" />
                </template>
            </div>
        </v-col>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();

defineProps({
    title: {
        type: Object,
        default: () => ({
            show: true,
            text: ''
        })
    },
    actionButtons: {
        type: Array,
        default: () => [],
    },
    backButton: {
        type: Object,
        default: () => ({
            show: true,
            to: null
        })
    },
    adminRole: {
        type: Number,
        required: false
    }
});

const handleBack = (to) => {
    if (to) {
        router.push(to);
    } else {
        router.back();
    }
};
</script>