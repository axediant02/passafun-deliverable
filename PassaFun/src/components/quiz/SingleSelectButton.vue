<script setup>
import { vibrate } from '@/utils/vibrationButton';
import { computed } from 'vue';

const props = defineProps({
  backgroundColor: {
    type: String,
    default: 'transparent',
  },
  textColor: {
    type: String,
    default: '#FFFFFF',
  },
  boxShadowColor: {
    type: String,
    default: 'transparent',
  },
  borderColor: {
    type: String,
    default: 'transparent',
  },
  boxShadowSize: {
    type: String,
    default: '2px',
  },
  boxShadowBottom: {
    type: String,
    default: '2px',
  },
});

const buttonStyles = computed(() => ({
  '--background-color': props.backgroundColor,
  '--text-color': props.textColor,
  '--border-color': props.borderColor,
  '--box-shadow-color': props.boxShadowColor,
  '--box-shadow-size': props.boxShadowSize,
  '--box-shadow-bottom': props.boxShadowBottom,
}));

const handleVibration = () => {
  vibrate(40);
};
</script>

<template>
  <button :style="buttonStyles" @click="handleVibration" class="relative rounded-md">
    <slot></slot>
  </button>
</template>

<style scoped>
button {
  box-shadow: 0px var(--box-shadow-size) 0px var(--box-shadow-color);
  background-color: var(--background-color);
  color: var(--text-color);
  border-color: var(--border-color);
  transition: none;
  top: 0;
  transition: all 0.1s ease-in-out;
}

button:active {
  box-shadow: 0px 0px 0px var(--box-shadow-color);
  top: var(--box-shadow-bottom);
  transition: all 0.1s ease-in-out;
}

button:active {
  box-shadow: 0px 0px 0px var(--box-shadow-color);
  top: 2px;
}
</style>
