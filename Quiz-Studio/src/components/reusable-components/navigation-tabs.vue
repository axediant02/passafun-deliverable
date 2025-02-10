<template>
    <div>
        <v-col>
            <v-tabs v-model="selectedTab" :color="color" :dark="dark" :slider-color="sliderColor">
                <v-tab v-for="tab in tabs" :key="tab.value" :value="tab.value">
                    {{ tab.label }}
                </v-tab>
            </v-tabs>
        </v-col>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: null
    },
    tabs: {
        type: Array,
        required: true,
    },
    color: {
        type: String,
        default: 'primary'
    },
    dark: {
        type: Boolean,
        default: true
    },
    sliderColor: {
        type: String,
        default: 'primary'
    }
})

const emit = defineEmits(['update:modelValue'])

const selectedTab = ref(props.modelValue)

watch(selectedTab, (newValue) => {
    emit('update:modelValue', newValue)
})

watch(() => props.modelValue, (newValue) => {
    selectedTab.value = newValue
})
</script>