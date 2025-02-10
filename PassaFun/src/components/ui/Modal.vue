<template>
  <div v-if="isVisible" class="fixed h-full w-full inset-0 bg-neutral-900 bg-opacity-90 flex z-50" @click.self="close">
    <div class="flex justify-center items-center">
      <slot @close-modal="close" />
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  isVisible: Boolean,
  imageSrc: String,
});

const emit = defineEmits(['close']);

const close = () => {
  emit('close');
};
</script>
