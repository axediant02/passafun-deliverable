<script setup>
import {
  DrawerContent,
  DrawerOverlay,
  DrawerPortal,
  DrawerRoot,
  DrawerTrigger,
  DrawerTitle,
  DrawerDescription,
} from 'vaul-vue';
</script>

<template>
  <DrawerRoot should-scale-background>
    <DrawerTrigger class="w-full">
      <slot name="button"></slot>
    </DrawerTrigger>

    <DrawerPortal>
      <DrawerOverlay class="fixed bg-black/40 inset-0" />
      <DrawerContent
        class="bg-gray-50 flex flex-col rounded-t-[10px] min-h-[45%] max-h-[92%] max-w-lg mx-auto p-2 fixed bottom-0 left-0 right-0"
      >
        <DrawerTitle class="text-lg font-bold hidden">Title</DrawerTitle>

        <DrawerDescription class="text-sm text-gray-500 hidden"> Description </DrawerDescription>
        <slot name="content"></slot>
      </DrawerContent>
    </DrawerPortal>
  </DrawerRoot>
</template>
