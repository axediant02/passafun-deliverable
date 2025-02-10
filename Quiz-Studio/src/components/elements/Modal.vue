<template>
    <v-dialog v-model="isVisible">
        <v-card>
            <v-container fluid>
                <div class="d-flex justify-space-between">
                    <v-card-title class="d-flex align-center mb-3 text-primary font-weight-bold">
                        <slot name="icon">
                            <v-icon class="mr-3">mdi-information-outline</v-icon>
                        </slot>
                        <slot name="title">Default Title</slot>
                    </v-card-title>

                    <v-btn @click="handleClose" icon="mdi-close" flat variant="text" />

                </div>
                <div>
                    <slot name="content">Default Content</slot>
                </div>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <slot name="actions">Default Action</slot>
                </v-card-actions>
            </v-container>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { computed, defineProps, defineEmits } from 'vue';

const props = defineProps({
    value: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['update:value', 'close']);

const isVisible = computed({
    get: () => props.value,
    set: (value) => emit('update:value', value)
});

function handleClose() {
    isVisible.value = false;
    emit('close');
}
</script>
