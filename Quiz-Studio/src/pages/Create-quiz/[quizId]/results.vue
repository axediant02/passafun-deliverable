<template>
    <CreateResults :quiz-id="Number(quizId)" location="CreateResults" />
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute();
const quizId = route.params.quizId;
</script>
