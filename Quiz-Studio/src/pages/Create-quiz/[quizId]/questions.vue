<template>
    <CreateQuestions :quizId="Number(quizId)" location="CreateQuiz" />
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute();
const quizId = route.params.quizId;
</script>
