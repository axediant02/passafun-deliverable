<template>

</template>
<script setup>
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'
import { isLoggedIn } from '../pages/auth/authService'

const router = useRouter();

onMounted(() => {

    if(isLoggedIn()){
      router.replace('/dashboard');
    } else{
      router.replace('/auth/login');
    }

});
</script>
