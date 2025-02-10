<template>
  <div class="relative">
    <button class="flex gap-1 items-center cursor-pointer" @click="toggleSortTypesListVisibility">
      <ArrowUpDown :size="16" />
      <h1 class="text-sm">Sort by</h1>
    </button>
    <div v-if="isSortTypesListOpen" class="absolute top-full right-0 z-50">
      <Listbox
        @click="handleSortTypeClick"
        v-model="selectedSortType"
        :options="typesOfSort"
        optionLabel="name"
        class="w-full whitespace-nowrap"
      />
    </div>
  </div>
</template>

<script setup>
import { ArrowUpDown } from 'lucide-vue-next';
import { Listbox } from 'primevue';
import { ref } from 'vue';




const props = defineProps({
  initialSort: {
    type: String,
    default: 'Newest'
  }
});

const emit = defineEmits(['update:selectedSortType']);

const typesOfSort = ref([{ name: 'Newest' }, { name: 'Oldest' }, { name: 'A-Z' }, { name: 'Z-A' }]);
const selectedSortType = ref(typesOfSort.value.find(type => type.name === props.initialSort) || typesOfSort.value[0]);
const oldSelectedSortType = ref(selectedSortType.value);

const isSortTypesListOpen = ref(false);

const toggleSortTypesListVisibility = () => {
  isSortTypesListOpen.value = !isSortTypesListOpen.value;
};

const handleSortTypeClick = () => {
  if (!selectedSortType.value) {
    selectedSortType.value = oldSelectedSortType.value;
    toggleSortTypesListVisibility();
    return;
  }
  emit('update:selectedSortType', selectedSortType.value);
  oldSelectedSortType.value = selectedSortType.value;
  toggleSortTypesListVisibility();
};
</script>
