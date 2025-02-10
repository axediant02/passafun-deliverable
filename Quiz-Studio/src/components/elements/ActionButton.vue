<template>
    <v-btn :color="color" :variant="variant" class="me-2 px-3" :style="style" style="border-radius: 15px"
        :height="height" @click="$emit('click')" :prepend-icon="icon">
        {{ label }}
    </v-btn>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        default: ''
    },
    color: {
        type: String,
        default: 'primary'
    },
    icon: {
        type: String,
        required: true
    },
    variant: {
        type: String,
        default: 'primary'
    },
    height: {
        type: String,
        default: '40px'
    },
    style: {
        type: String,
        default: ''
    }
});

defineEmits(['click']);
</script>