<template>
    <v-card :class="customClass" width="100%" :max-width="maxWidth" :elevation="elevation" :variant="variant"
        :style="style">
        <v-container>
            <div v-if="showTitle">
                <h1 class="text-h6 font-weight-bold mb-3">
                    Participants
                </h1>
            </div>
            <slot name="content"></slot>
        </v-container>
    </v-card>
</template>

<script setup>
defineProps({
    showTitle: {
        type: Boolean,
        default: true
    },
    maxWidth: {
        type: String,
        default: '1000'
    },
    customClass: {
        type: String,
        default: ''
    },
    elevation: {
        type: Number,
        default: 2
    },
    variant: {
        type: String,
        default: 'flat'
    },
    style: {
        type: String,
        default: ''
    }
})
</script>