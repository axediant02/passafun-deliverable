<script setup>
defineProps({
  backgroundColor: {
    type: String,
    default: 'transparent',
  },
  textColor: {
    type: String,
    default: '#000000',
  },
});
</script>

<template>
  <div
    :style="{
    backgroundColor: backgroundColor,
      color: textColor,
    }"
    class="pa-4"
  >
    <slot></slot>
  </div>
</template>
