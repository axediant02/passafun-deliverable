<template>
    <v-dialog v-model="isVisible" :style="customStyle">
        <v-card>
            <v-container fluid>
                <div class="d-flex justify-space-between">
                    <v-card-title class="d-flex align-center mb-3" :class="titleClass">
                        <v-icon v-if="icon" class="mr-3">{{ icon }}</v-icon>
                        {{ title }}
                    </v-card-title>

                    <v-btn @click="handleClose" icon="mdi-close" flat variant="text" />
                </div>
                <v-card-text>
                    <slot>{{ content }}</slot>
                </v-card-text>
                <v-card-actions v-if="showActions">
                    <v-spacer></v-spacer>
                    <slot name="actions">
                        <v-btn style="border-radius: 10px !important" class="px-5" v-if="cancelText"
                            @click="handleClose" :color="cancelColor" :variant="cancelButtonVariant">
                            {{ cancelText }}
                        </v-btn>
                        <v-btn style="border-radius: 10px !important" class="px-5" v-if="confirmText"
                            @click="handleConfirm" :color="confirmColor" :variant="confirmButtonVariant">
                            {{ confirmText }}
                        </v-btn>
                    </slot>
                </v-card-actions>
            </v-container>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { computed, defineProps, defineEmits } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    content: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: ''
    },
    confirmText: {
        type: String,
        default: ''
    },
    cancelText: {
        type: String,
        default: ''
    },
    confirmColor: {
        type: String,
        default: 'primary'
    },
    cancelColor: {
        type: String,
        default: 'grey'
    },
    customStyle: {
        type: String,
        default: ''
    },
    cancelButtonVariant: {
        type: String,
        default: 'tonal'
    },
    confirmButtonVariant: {
        type: String,
        default: 'flat'
    },
    titleClass: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue', 'close', 'confirm']);

const isVisible = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const showActions = computed(() => props.confirmText || props.cancelText);

function handleClose() {
    isVisible.value = false;
    emit('close');
}

function handleConfirm() {
    emit('confirm');
}
</script>
