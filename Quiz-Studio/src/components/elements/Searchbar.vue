<template>
    <v-col class="d-flex align-center pl-0" style="min-width: 300px;">
        <v-text-field v-model="localSearchQuery" class="w-100 mr-2" max-width="300" :label="searchLabel"
            prepend-inner-icon="mdi-magnify" variant="outlined" hide-details="auto" @input="emitSearchQuery" />
    </v-col>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Search',
    },
    searchLabel: {
        type: String,
        default: 'Search',
    },
    modelValue: {
        type: String,
        default: '',
    },
});

const localSearchQuery = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
    localSearchQuery.value = newValue;
});

const emit = defineEmits(['update:modelValue']);
const emitSearchQuery = () => {
    emit('update:modelValue', localSearchQuery.value);
};

const onTuneClick = () => {
    // Handle tune button functionality if needed
};
</script>
