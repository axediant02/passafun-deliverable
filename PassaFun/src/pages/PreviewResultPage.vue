<template>
  <PageContainer>
    <div class="flex flex-col place-items-center h-fullfont-nunito">
      <header class="relative w-full px-4 py-2 bg-blue-500 text-white flex items-center">
        <div>
          <button
            @click="goToLibrary"
            class="top-0 flex place-items-center cursor-pointer h-full left-2 absolute text-sm font-nunito font-bold"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7"
              />
            </svg>
            <img src="/images/passafun.png" alt="Passafun Logo" class="h-8 w-8" />
          </button>
        </div>
        <div
          class="max-w-sm lg:max-w-2xl mx-auto h-full flex place-items-center justify-center font-extrabold"
        >
          <p class="text-lg">Quiz Name</p>
        </div>
      </header>

      <div class="flex flex-col h-[calc(100vh-120px)]">
        <div class="flex flex-col items-center pt-4 w-full flex-grow overflow-y-auto pb-32">
          <div class="max-h-[200px] rounded-md overflow-hidden mb-4">
            <img src="/images/passafun.png" alt="Result Image" class="w-48 h-auto rounded-md" />
          </div>

          <div class="text-center">
            <h1 class="text-[40px] font-extrabold font-russo text-blue-500">
              Your Amazing Result!
            </h1>
            <p class="text-[20px] font-bold mt-2"></p>
            <p class="text-[16px] text-center px-4 mt-2 leading-tight text-gray-700">
              <span class="uppercase text-blue-500 font-feather text-bold text-[20px]">Name!</span>
              This is a description of your quiz result. You did an outstanding job!
            </p>
          </div>
        </div>
      </div>

      <div class="flex flex-col-reverse px-4 gap-3 mt-6 w-full absolute bottom-0 py-2 z-10">
        <div class="flex space-x-2">
          <div class="w-full flex-col flex gap-y-2">
            <Button
              backgroundColor="#3b82f6"
              textColor="#FFFFFF"
              borderColor="#1e40af"
              boxShadowColor="#1e40af"
              boxShadowSize="4px"
              boxShadowBottom="4px"
              class="w-full text-[20px] font-bold"
            >
              <div class="flex items-center justify-center gap-2">
                <Share2 size="24" />
                Share Result
              </div>
            </Button>

            <Button
              backgroundColor="#FFFFFF"
              textColor="#3b82f6"
              borderColor="#1e40af"
              boxShadowSize="0px"
              class="w-full text-[20px] font-bold"
            >
              <div class="flex items-center justify-center gap-2">
                <RefreshCcw size="24" />
                Play Again
              </div>
            </Button>
          </div>
        </div>
      </div>
    </div>
  </PageContainer>
</template>

<script setup>
import { useRouter } from 'vue-router';
import PageContainer from '@/components/PageContainer.vue';
import PageHeader from '@/components/PageHeader.vue';
import { ChevronLeft, RefreshCcw, Share2 } from 'lucide-vue-next';
import Button from '@/components/ui/Button.vue';

const router = useRouter();

const goToLibrary = () => {
  router.push('/');
};
</script>

