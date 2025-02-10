<script setup>
import { Skeleton } from 'primevue';
</script>

<template>
  <div class="flex gap-2 overflow-x-auto hide-scrollbar">
    <div class="w-fit">
      <div class="flex rounded-md">
        <div class="flex flex-col gap-1 mx-1 place-items-center">
          <div class="rounded-[18px] w-32 sm:w-44 aspect-square">
            <Skeleton size="8rem" class="sm:p-20 mx-auto"></Skeleton>
          </div>
          <div class="flex flex-col">
            <h2 class="flex max-w-32 w-32 sm:w-44 sm:max-w-48 text-wrap">
              <Skeleton width="6rem" height="1.5rem" class="mb-2"></Skeleton>
            </h2>
          </div>
        </div>
        <div class="flex flex-col gap-1 mx-1">
          <div class="rounded-[18px] w-32 sm:w-44 aspect-square">
            <Skeleton size="8rem" class="sm:p-20 mx-auto"></Skeleton>
          </div>
          <div class="flex flex-col">
            <h2 class="flex max-w-32 w-32 sm:w-44 sm:max-w-48 text-wrap">
              <Skeleton width="6rem" height="1.5rem" class="mb-2"></Skeleton>
            </h2>
          </div>
        </div>
        <div class="flex flex-col gap-1 mx-1">
          <div class="rounded-[18px] w-32 sm:w-44 aspect-square">
            <Skeleton size="8rem" class="sm:p-20 mx-auto"></Skeleton>
          </div>
          <div class="flex flex-col">
            <h2 class="flex max-w-32 w-32 sm:w-44 sm:max-w-48 text-wrap">
              <Skeleton width="6rem" height="1.5rem" class="mb-2"></Skeleton>
            </h2>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
