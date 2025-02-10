<template>
  <div class="flex flex-col items-center justify-center h-screen bg-gray-100">
    <LottieAnimations :animationData="animation" />
    <div class="mt-5 text-2xl font-bold text-blue-600">Page Not Found</div>
  </div>
</template>

<script setup>
import LottieAnimations from '@/components/LottieAnimations.vue';
import animationData from '@/static/animation/NotFound.json';
import { ref } from 'vue';

const animation = ref(animationData);
</script>
