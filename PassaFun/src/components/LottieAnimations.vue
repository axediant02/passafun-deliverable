<template>
  <div ref="lottieContainer" class="lottie-animation max-w-44 sm:max-w-60 aspect-square"></div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import lottie from 'lottie-web';

const props = defineProps({
  animationData: {
    type: Object,
    required: true,
  },
  progress: {
    type: Number,
  },
});

const lottieContainer = ref(null);

const initLottie = () => {
  lottie.loadAnimation({
    container: lottieContainer.value,
    renderer: 'svg',
    loop: true,
    autoplay: true,
    animationData: props.animationData,
  });
};


onMounted(() => {
  initLottie();
});
</script>
