<template>
    <div ref="animationContainer" :style="{ width: width, height: height }"></div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import lottie from 'lottie-web';

const props = defineProps({
    animationData: Object,
    autoplay: {
        type: Boolean,
        default: true
    },
    loop: {
        type: Boolean,
        default: true
    },
    width: {
        type: String,
        default: '100%'
    },
    height: {
        type: String,
        default: '100%'
    }
});

const animationContainer = ref(null);
let animationInstance = null;

onMounted(() => {
    animationInstance = lottie.loadAnimation({
        container: animationContainer.value,
        animationData: props.animationData,
        renderer: 'svg',
        autoplay: props.autoplay
    });
});

watch(() => props.animationData, (newAnimationData) => {
    if (animationInstance) {
        animationInstance.stop(); 
        animationInstance.destroy(); 
    }
    animationInstance = lottie.loadAnimation({
        container: animationContainer.value,
        animationData: newAnimationData,
        renderer: 'svg',
        autoplay: props.autoplay
    });
});
</script>
