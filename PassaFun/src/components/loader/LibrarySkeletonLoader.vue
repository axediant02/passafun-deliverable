<template>
  <PageContainer>
    <div class="py-4 min-h-screen">
      <div class="flex flex-col max-w-lg mx-auto font-roboto bg-transparent pt-24">
        <div class="flex flex-col place-items-center fixed top-0 left-0 right-0 z-10">
          <div class="w-full h-full flex flex-col place-items-center p-2">
            <h1 class="text-2xl font-semibold text-blue-600 py-1">
              <Skeleton width="10rem" height="1.5rem" class=""></Skeleton>
            </h1>
          </div>
          <div class="w-full flex flex-col place-items-center">
            <div class="px-4 max-w-lg w-full">
              <Skeleton height="2rem" class="w-full"></Skeleton>
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-5">
          <div class="flex flex-col gap-2 px-4">
            <h1 class="font-semibold text-lg flex place-items-center opacity-80">
              <Skeleton width="8rem" height="1.5rem" class="mb-2"></Skeleton>
            </h1>
            <Skeleton size="20rem" class="mx-auto"></Skeleton>
          </div>
          <div class="flex flex-col gap-2 pl-4 py-2">
            <div class="flex flex-row justify-between">
              <h1 class="font-semibold text-lg flex place-items-center opacity-80">
                <Skeleton width="8rem" height="1.5rem" class="mb-2"></Skeleton>
              </h1>
            </div>
            <div class="relative">
              <div class="flex gap-2 overflow-x-auto hide-scrollbar">
                <div class="w-fit">
                  <div class="flex rounded-md">
                    <div class="flex flex-col gap-1 mx-1">
                      <div class="rounded-[18px] w-32 sm:w-44 aspect-square">
                        <Skeleton size="8rem" class="mx-auto"></Skeleton>
                      </div>
                      <div class="flex flex-col">
                        <h2 class="flex max-w-32 w-32 sm:w-44 sm:max-w-48 text-wrap">
                          <Skeleton width="6rem" height="1.5rem" class="mb-2"></Skeleton>
                        </h2>
                      </div>
                    </div>
                    <div class="flex flex-col gap-1 mx-1">
                      <div class="rounded-[18px] w-32 sm:w-44 aspect-square">
                        <Skeleton size="8rem" class="mx-auto"></Skeleton>
                      </div>
                      <div class="flex flex-col">
                        <h2 class="flex max-w-32 w-32 sm:w-44 sm:max-w-48 text-wrap">
                          <Skeleton width="6rem" height="1.5rem" class="mb-2"></Skeleton>
                        </h2>
                      </div>
                    </div>
                    <div class="flex flex-col gap-1 mx-1">
                      <div class="rounded-[18px] w-32 sm:w-44 aspect-square">
                        <Skeleton size="8rem" class="mx-auto"></Skeleton>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </PageContainer>
</template>
<script setup>
import PageContainer from '@/components/PageContainer.vue';
import { Skeleton } from 'primevue';
</script>
