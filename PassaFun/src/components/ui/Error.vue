<template>
  <div
    severity="error"
    class="relative border-2 pl-10 py-3 rounded-md bg-red-100 border-error w-fit"
  >
    <div class="flex flex-col">
      <div>
        <div class="flex flex-row gap-1">
          <div class="text-md font-medium">Error</div>
        </div>
      </div>
      <div class="text-left leading-none mx-none">
        <slot></slot>
      </div>
    </div>
    <div class="absolute top-[13px] left-3">
      <AlertCircle class="mr-2" size="20" />
    </div>
  </div>
</template>

<script setup>
import { AlertCircle } from 'lucide-vue-next';
</script>
