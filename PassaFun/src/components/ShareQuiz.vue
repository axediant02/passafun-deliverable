<template>
  <div>
    <Drawer>
      <template #button>
        <div class="flex items-center justify-center select-none max-w-20 w-20">
          <Button
            :boxShadowColor="quizTheme.accent_color"
            :borderColor="quizTheme.accent_color"
            backgroundColor="transparent"
            boxShadowSize="3px"
            class="px-4 w-full"
          >
            <Share2 :color="quizTheme.main_color" />
          </Button>
        </div>
      </template>
      <template #content>
        <div class="w-24 h-2 rounded-full opacity-30 mx-auto bg-blue-300"></div>

        <div class="flex flex-col gap-4 p-4">
          <DrawerClose>
            <button
              class="absolute right-4 top-4 text-gray-500 hover:text-gray-800 transition-all duration-300"
            ></button>
          </DrawerClose>
          <div class="flex flex-col gap-4 justify-start items-start">
            <h2 class="text-xl font-semibold text-center text-gray-700">Share this Quiz to</h2>
            <div class="w-full">
              <p class="text-lg font-semibold mb-2 text-gray-700">Link</p>
              <div class="flex items-center gap-2 p-2 border border-gray-300 rounded-md">
                <input
                  ref="urlInput"
                  type="text"
                  :value="contentUrl"
                  readonly
                  class="w-full bg-transparent text-gray-500 outline-none truncate cursor-pointer"
                  @click="$event.target.select()"
                />
                <button
                  @click="copyToClipboard"
                  :class="{ 'bg-green-50 text-success': copySuccess }"
                  class="p-2 rounded-md transition-all duration-300"
                >
                  <Link v-if="!copySuccess" class="w-5 h-5" />
                  <Check v-else class="w-5 h-5" />
                </button>
              </div>
            </div>

            <div class="flex flex-col w-full">
              <p class="text-lg font-semibold mb-2 text-gray-700">Social Options</p>
              <div class="flex flex-row w-full flex-wrap gap-4">
                <a
                  :href="facebookShareUrl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="p-2 w-[55px] h-[55px] justify-center items-center flex rounded-full border border-gray-300 hover:border-blue-600 transition-all duration-300"
                >
                  <img src="/svg/facebook.svg" alt="Facebook" class="w-8 h-8" />
                </a>
                <a
                  :href="messengerShareUrl"
                  class="p-2 w-[55px] h-[55px] justify-center items-center flex rounded-full border xl:hidden border-gray-300"
                >
                  <img src="/svg/messenger.svg" alt="Messenger" class="w-8 h-8" />
                </a>
                <a
                  :href="twitterShareUrl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="p-2 w-[55px] h-[55px] justify-center items-center flex rounded-full border border-gray-300 hover:border-blue-600 transition-all duration-300"
                >
                  <img src="/svg/x.svg" alt="Twitter" class="w-6 h-6" />
                </a>
              </div>
            </div>
          </div>
        </div>
      </template>
    </Drawer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Button from '@/components/ui/Button.vue';
import Drawer from '@/components/ui/Drawer.vue';
import { DrawerClose } from 'vaul-vue';
import { Check, Link, Share, Share2 } from 'lucide-vue-next';
import { useRoute } from 'vue-router';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { storeToRefs } from 'pinia';

const route = useRoute();
const store = useQuizzesStore();
const quizId = route.params.quizId;

const { quizTheme } = storeToRefs(store);

const contentUrl = `${import.meta.env.VITE_API_BASE_URL}meta/${quizId}`;

const facebookShareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
  contentUrl
)}`;
const messengerShareUrl = `fb-messenger://share?link=${encodeURIComponent(contentUrl)}`;
const twitterShareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(
  contentUrl
)}&text=Check out this quiz!`;

const copySuccess = ref(false);
const urlInput = ref(null);

const copyToClipboard = async () => {
  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(contentUrl);
    } else {
      const input = urlInput.value;
      input.select();
      input.setSelectionRange(0, 99999);
      document.execCommand('copy');
    }

    copySuccess.value = true;
    setTimeout(() => (copySuccess.value = false), 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
    alert('Please manually copy the link: ' + contentUrl);
  }
};

// onMounted(() => {
//   store.getQuizzesData(quizId);
// });
</script>
