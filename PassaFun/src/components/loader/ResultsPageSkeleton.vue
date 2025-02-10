<script setup lang="ts">
import { Skeleton } from 'primevue';

</script>

<template>
  <div class="flex flex-col justify-between place-items-center h-full px-4">

    <div class="flex w-full  flex-col justify-between place-items-center h-full px-4 font-nunito">
      <div></div>
      <div class="flex flex-col place-items-center gap-4 w-full">
        <div class="w-full  flex flex-col  place-items-center py-4">
          <Skeleton size="12.5rem" />
        </div>

        <div class="flex flex-col w-full gap-1 text-center">
          <div>
          </div>
          <h1 class=" mx-auto text-center flex w-full">
            <div class="flex items-center gap-1 flex-wrap justify-center w-full px-2">
              <Skeleton width="100%" height="2.5rem" />
            </div>
          </h1>
          <div class="px-4 w-full flex-col flex gap-1 text-center pt-4">
            <div class="flex flex-row gap-1 place-items-center  w-full ">
              <div class="w-full place-items-center gap-1 flex flex-row">
                <Skeleton width="12rem" height="2rem" />
                <Skeleton width="100%" height="1.3rem" />
              </div>
            </div>
            <Skeleton width="100%" height="1rem" />
            <Skeleton width="100%" height="1rem" />
            <Skeleton width="50%" height="1rem" />
          </div>
        </div>
      </div>
      <div class="h-[20%]"></div>
    </div>


  </div>
  <div class="flex flex-col place-items-center px-4 gap-3 mt-6 w-full absolute bottom-0 py-2 z-10">
    <Skeleton width="50%" height="1.5rem" />
    <Skeleton width="100%" height="3.3rem" />
  </div>
</template>
