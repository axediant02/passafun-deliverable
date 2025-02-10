<script setup>
import { vibrate } from '@/utils/vibrationButton';
import { computed } from 'vue';

const props = defineProps({
  backgroundColor: {
    type: String,
    default: '#FFFFFF',
  },
  activeBackgroundColor: {
    type: String,
    default: '#1f2937',
  },
  textColor: {
    type: String,
    default: '#FFFFFF',
  },
  activeTextColor: {
    type: String,
    default: '#FFFFFF',
  },
  boxShadowColor: {
    type: String,
    default: 'transparent',
  },
  size: {
    type: String,
    default: 'w-6 h-6',
  },
  active: {
    type: Boolean,
    default: false,
  },
  border: {
    type: String,
    default: 'grey',
  },
});

const buttonStyles = computed(() => ({
  '--background-color': props.active ? props.activeBackgroundColor : props.backgroundColor,
  '--text-color': props.active ? props.activeTextColor : props.textColor,
  '--box-shadow-color': props.boxShadowColor,
  '--size': props.size,
  '--border': props.border,
}));

const handleVibration = () => {
  vibrate(40);
};
</script>

<template>
  <div
    :style="buttonStyles"
    @click="handleVibration"
    :class="[
      'relative rounded-full transition-all duration-150',
      size,
      { 'ring-2 ring-blue-500': active },
    ]"
  >
    <slot></slot>
  </div>
</template>

<style scoped>
div {
  background-color: var(--background-color);
  color: var(--text-color);
  box-shadow: none;
  border: 2px solid var(--border);
}

div.ring-2 {
  box-shadow: 0px 2px 0px var(--box-shadow-color);
  border: 2px solid var(--border);
}

div:active {
  box-shadow: 0px 0px 0px var(--box-shadow-color);
  border: 2px solid var(--border);
  top: 2px;
}
</style>
