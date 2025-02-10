<template>
    <v-table class="bg-transparent w-100">
        <thead>
            <tr>
                <th class="text-left">Quiz</th>
                <th></th>
                <th v-if="showCount" class="text-left">Play Count</th>
                <th class="text-left">Status</th>
                <th class="text-left">Date Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in items" :key="item.id" @mouseenter="setHovered(item.id, true)"
                @mouseleave="setHovered(item.id, false)" :class="{ 'cursor-pointer': true }"
                @click="$router.push(`${basePath}/${item.id}`)">
                <td class="d-flex align-center h-100 py-2">
                    <v-card flat class="me-3" height="60px" width="60px">
                        <v-img :src="item[imageField] || defaultImage" class="rounded-lg" width="80" height="80"
                            cover />
                    </v-card>
                    <div class="d-flex ">
                        {{ item[titleField] || titlePlaceholder }}
                    </div>
                </td>
                <td>
                    <small v-if="item.is_featured" class="feature-label d-flex ms-3 text-primary align-center pa-1">
                        <v-icon size="16">mdi-star</v-icon>
                        Featured
                    </small>
                </td>
                <td v-if="showCount">
                    <div class="d-flex align-center">
                        {{ item[countField] || 0 }}
                    </div>
                </td>
                <td v-if="tab !== 'allQuiz'">{{ item.quiz_status?.status }}</td>
                <td v-else>{{ getStatusText(item.quiz_status_id) }}</td>
                <td>{{ new Date(item.created_at).toLocaleDateString('en-PH', {
                    year: 'numeric', month: 'short', day:
                        'numeric'
                }) }}</td>
                <td>
                    <v-btn v-if="adminRole === 1 && item.quiz_status_id === 2" size="35" variant="text" @click.stop.prevent>
                        <v-icon>mdi-dots-vertical</v-icon>
                        <v-menu activator="parent">
                            <v-list>
                                <v-list-item v-for="(dropdownItem, index) in dropdownItems(item)" :key="index"
                                    :value="index" @click="handleDropdownAction(dropdownItem.action, item)">
                                    <v-list-item-title>{{ dropdownItem.title }}</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-btn>
                </td>
            </tr>
        </tbody>
    </v-table>
</template>

<script setup>
import { ref } from 'vue';

const hoveredItems = ref({});

const setHovered = (itemId, value) => {
    hoveredItems.value[itemId] = value;
};

defineProps({
    items: {
        type: Array,
        required: true
    },
    basePath: {
        type: String,
        required: true
    },
    imageField: {
        type: String,
        default: 'thumbnailUrl'
    },
    coverField: {
        type: String,
        default: 'coverImageUrl'
    },
    defaultImage: {
        type: String,
        default: 'https://img.freepik.com/premium-vector/default-image-icon-vector-missing-picture-page-website-design-mobile-app-no-photo-available_87543-11093.jpg'
    },
    titleField: {
        type: String,
        default: 'name'
    },
    titlePlaceholder: {
        type: String,
        default: 'No name Available'
    },
    showCount: {
        type: Boolean,
        default: true
    },
    countField: {
        type: String,
        default: 'participant_count'
    },
    countLabel: {
        type: String,
        default: 'plays'
    },
    countIcon: {
        type: String,
        default: 'mdi-account-group'
    },
    tab: {
        type: String,
        default: ''
    },
    dropdownItems: {
        type: Function,
        required: true
    },
    adminRole: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['feature-quiz']);

const handleDropdownAction = (action, item) => {
    if (action === 'featureQuiz') {
        if (item.quiz_status_id === 2) {
            emit('feature-quiz', item);
        }
        else {
            window.$snackbar("Only published quizzes can be featured.", "error");
        }
    }
};

const quizStatus = [
    { id: 1, status: 'Unpublished' },
    { id: 2, status: 'Published' },
    { id: 3, status: 'Archived' }
];

const getStatusText = (statusId) => {
    const status = quizStatus.find(s => s.id === statusId);
    return status ? status.status : 'Unknown';
};
</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

tbody tr:hover {
    background-color: #ccc;
}
</style>