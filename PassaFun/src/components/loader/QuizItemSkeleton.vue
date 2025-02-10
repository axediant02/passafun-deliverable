<script setup lang="ts">
import { Skeleton } from 'primevue';

const skeletonRows = 10;



</script>

<template>
  <div class="flex flex-col gap-1">
    <div v-for="index in skeletonRows" :key="index" class="flex flex-row gap-3 my-[6px] w-full">
      <Skeleton size="4rem" />
      <div class="flex flex-col gap-[6px]">
        <Skeleton width="11rem" height="1.3rem" />
        <div class="flex flex-col gap-[1px]">
          <Skeleton width="16rem" height=".8rem" />
          <Skeleton width="5rem" height=".8rem" />
        </div>
      </div>
    </div>
  </div>
</template>
