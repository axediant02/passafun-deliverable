<script setup>
import { ref } from 'vue';

const visible = ref(false);

import Card from '@/components/ui/Card.vue';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { storeToRefs } from 'pinia';
import { Asterisk, Circle, HelpCircle, ShieldCheck } from 'lucide-vue-next';
import Drawer from './ui/Drawer.vue';

const store = useQuizzesStore();
const { quizTheme, mechanicsPageContent } = storeToRefs(store);
</script>

<template>
  <Drawer>
    <template #button>
      <div class="flex gap-1 flex-row place-items-center select-none justify-center font-nunito font-semibold text-xl"
        :style="{ color: quizTheme.main_color }">
        <HelpCircle size="18" /> View quiz mechanics
      </div>
    </template>
    <template #content>
      <div class="flex flex-col h-fit w-full max-w-lg rounded-md">
        <div class="w-full place-items-center flex flex-col">
          <div class="w-24 h-2 rounded-full " :style="{ backgroundColor: quizTheme.main_color }"></div>
        </div>
        <div class="flex flex-col py-4">
          <Card class="text-2xl font-bold pb-2 px-4" :textColor="quizTheme.main_color">
            Mechanics
          </Card>
          <ul class="flex flex-col gap-3 px-2 font-roboto">
            <li v-for="(mechanic, index) in mechanicsPageContent.mechanic_page_instructions" :key="index"
              class="flex  gap-1">
              <span>
                <div
                  class="rounded-full place-items-center justify-center select-none text-xs font-semibold text-white flex font-nunito w-5 h-5"
                  :style="{ backgroundColor: quizTheme.main_color }">
                  {{
                    index + 1 }}
                </div>
              </span>
              <p class="text-gray-900 font-roboto text-md px-4 opacity-50 leading-tight">
                {{ mechanic.mechanic_instruction.instruction }}
              </p>
            </li>
          </ul>
        </div>
      </div>
    </template>
  </Drawer>
  <div></div>
</template>
