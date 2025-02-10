<script setup>
import { vibrate } from '@/utils/vibrationButton';
import { computed } from 'vue';

const props = defineProps({
  backgroundColor: {
    type: String,
    default: 'white',
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
    default: '3px',
  },
  boxShadowBottom: {
    type: String,
    default: '3px',
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
  <button
    @click="handleVibration"
    :style="buttonStyles"
    class="button-style flex justify-center place-items-center h-[52px] rounded-md"
  >
    <slot></slot>
  </button>
</template>

<style scoped>
.button-style {
  box-shadow: 0px var(--box-shadow-size) 0px var(--box-shadow-color);
  background-color: var(--background-color);
  color: var(--text-color);
  position: relative;
  border: 2px solid var(--border-color);
  transition: all 0.1s ease-in-out;
}

.button-style:active {
  box-shadow: 0px 0px 0px var(--box-shadow-color);
  top: var(--box-shadow-bottom);
  transition: none;
  transition: all 0.1s ease-in-out;
}
</style>
