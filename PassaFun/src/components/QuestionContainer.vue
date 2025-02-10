<template>
  <Card :backgroundColor="CardBackgroundColor" class="flex flex-col place-items-center w-full">
    <div
      :style="{
        backgroundColor: backgroundColor,
        color: textColor,
      }"
    >
      <slot></slot>
    </div>
  </Card>
</template>

<script setup>
import Card from './ui/Card.vue';

const props = defineProps({
  backgroundColor: {
    type: String,
    default: 'transparent',
  },
  textColor: {
    type: String,
    default: 'black',
  },
  CardBackgroundColor: {
    type: String,
    default: 'transparent',
  },
});
</script>
