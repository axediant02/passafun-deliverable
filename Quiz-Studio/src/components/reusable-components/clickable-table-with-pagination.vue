<template>
    <div>
        <v-table class="border custom-radius">
            <thead>
                <tr>
                    <th v-for="header in thead" :key="header.title" class="text-white bg-grey-darken-3">
                        {{ header.title }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in items" :key="index" class="clickable-row" @click="handleRowClick(item)">
                    <td v-for="header in thead" :key="header.title">
                        <template v-if="header.key === 'actions'">
                            <slot name="actions" :item="item"></slot>
                        </template>
                        <template v-else-if="header.key === 'role'">
                            <slot name="role" :item="item">
                                {{ item[header.key] }}
                            </slot>
                        </template>
                        <template v-else>
                            {{ item[header.key] }}
                        </template>
                    </td>
                </tr>
            </tbody>
        </v-table>

        <div v-if="!disablePagination" class="d-flex justify-center mt-5">
            <v-pagination :model-value="currentPage" :length="totalPages" :total-visible="10"
                @update:model-value="$emit('update-page', $event)" active-color="primary" variant="flat"></v-pagination>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    thead: {
        type: Array,
        required: true
    },
    items: {
        type: Array,
        required: true
    },
    disablePagination: {
        type: Boolean,
        default: false
    },
    totalPages: {
        type: Number,
        default: 1
    },
    currentPage: {
        type: Number,
        default: 1
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['row-click', 'update-page']);

const handleRowClick = (item) => {
  if (item.id && item.summaryId) {
    emit('row-click', item.id, item.summaryId);
  }
  else if (item.summaryId) {
    emit('row-click', item.summaryId);
  } else if (item.id) {
    emit('row-click',  item.id);
  }
};


</script>

<style scoped>
.clickable-row {
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.clickable-row:hover {
    background-color: #e0e0e0 !important;
}
</style>
