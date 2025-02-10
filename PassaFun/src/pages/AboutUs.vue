<template>
  <span>
    <section>
      <div class="mx-auto w-full max-w-7xl px-5 py-12 md:px-10 md:py-16 lg:py-20">
        <div class="flex flex-col gap-8 lg:flex-row-reverse lg:gap-10">
          <div class="flex flex-col gap-8 text-center">
            <h2 class="text-3xl md:text-5xl text-[#5197FF] font-feather font-bold">
              About Us
            </h2>
            <p class="text-sm sm:text-base font-nunito">
              Passafun is a platform designed for users to explore a wide variety of quizzes. From
              personality tests to surveys, assessments, and more, we offer an interactive
              experience where users can discover new quizzes and dive into fun, educational, or
              insightful content. Whether you're looking to test your knowledge, gain personalized
              insights, or just have fun, Passafun provides a seamless and engaging way to enjoy
              interactive quizzes, all in one place.
            </p>
          </div>
        </div>
      </div>
    </section>
    <section>
      <div class="mx-auto w-full max-w-7xl px-5 py-16 md:px-10 md:py-20">
        <h2
          class="mb-8 text-3xl font-extrabold md:text-5xl text-center lg:mb-14 font-feather text-[#5197FF]"
        >
          Meet Passafun
        </h2>
        <p
          class="mb-8 max-w-lg text-sm text-center mx-auto font-nunito text-slate-500 sm:text-base lg:mb-24"
        >
          At Passafun, we believe in the power of interactive experiences. Whether you're seeking a
          fun personality test, testing your knowledge with assessments, or diving into surveys,
          Passafun offers a diverse library of quizzes that provide both entertainment and insights.
          With a constantly expanding selection, we strive to create a platform where everyone can
          find something that excites them.
        </p>
        <div class="grid gap-10 lg:grid-cols-2 lg:gap-12">
          <img
            src="/images/passafun.png"
            alt=""
            class="inline-block h-full w-full rounded-2xl object-cover"
          />
          <div
            class="flex flex-col gap-5 border border-solid border-library rounded-md p-10 sm:p-20"
          >
            <h2 class="text-3xl font-bold md:text-5xl font-feather text-[#5197FF]">Our Mission</h2>
            <p class="text-sm text-slate-500 font-nunito sm:text-base">
              Our mission is to make learning, exploring, and having fun more engaging through
              interactive quizzes. We're committed to providing users with a dynamic and
              personalized experience. With each quiz tailored to meet a variety of interests, our
              goal is to create an accessible space where everyone can discover, learn, and enjoy.
              Additionally, we make it easy to share the fun! Users can quickly share their favorite
              quizzes or games with friends, making it easier to spread the excitement and invite
              others to join in.
              <br />
              <br />
              We aim to provide a seamless, interactive experience that keeps you engaged, whether
              you're challenging yourself or sharing your results with others.
            </p>
          </div>
        </div>
      </div>
    </section>
    <section>
      <div class="mx-auto w-full max-w-7xl px-5 py-16 md:px-10 md:py-24 lg:py-32">
        <h2 class="text-center text-3xl font-bold md:text-5xl text-[#5197FF] font-feather">
          Our Team Members
        </h2>
        <p
          class="mx-auto mb-8 mt-4 font-nunito text-center text-sm text-slate-500 sm:text-base md:mb-12 lg:mb-16"
        >
          A dedicated group of passionate individuals working together to create an engaging quiz
          experience.
        </p>
        <div
          class="mx-auto grid justify-items-center gap-5 sm:grid-cols-2 md:grid-cols-3 md:gap-8 lg:grid-cols-4 lg:justify-items-stretch"
        >
          <div
            v-for="(member, index) in teamMembers"
            :key="index"
            class="flex max-w-sm flex-col items-center gap-4 rounded-md border border-solid border-[#5197FF] px-8 py-6 md:max-w-full"
          >
            <img :src="member.image" alt="" class="mb-4 inline-block h-52 w-full object-cover" />
            <p class="font-bold font-nunito text-[#5197FF]">{{ member.name }}</p>
            <p class="text-sm text-slate-500 font-nunito">{{ member.role }}</p>
          </div>
        </div>
      </div>
      <router-link to="/">
        <div
          class="fixed top-4 right-4 bg-gray-100 hover:bg-gray-200 transition-all ease-in-out duration-300 rounded-full p-2"
        >
          <XIcon color="#5197FF" />
        </div>
      </router-link>
    </section>
  </span>
</template>

<script setup>
import Button from '@/components/ui/Button.vue';
import { XIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const teamMembers = ref([
  {
    name: 'Jilian Garette',
    role: 'Fullstack developer',
    image:
      'https://cdn2.iconfinder.com/data/icons/female-avatars-4/256/avatars_accounts___woman_female_person_people_broken_glasses_glasses_earrings_necklace_short_hair.png',
  },
  {
    name: 'Jeric John',
    role: 'Fullstack Developer | Leader',
    image:
      'https://cdn2.iconfinder.com/data/icons/male-avatars/256/avatars_accounts___man_male_people_person_hat_cap_baseball_cap_necklace_shirtless.png',
  },
  {
    name: 'Charles David',
    role: 'Fullstack Developer',
    image:
      'https://cdn2.iconfinder.com/data/icons/male-avatars/256/avatars_accounts___man_male_people_person_shirt_headphone_headset_music_listen.png',
  },
  {
    name: 'Ian Fred',
    role: 'Tester | Frontend Developer',
    image:
      'https://cdn2.iconfinder.com/data/icons/male-avatars/256/avatars_accounts___man_male_people_person_curly_hair_suit_formal_tie_blazer_shirt.png',
  },
  {
    name: 'John Barry',
    role: 'Fullstack Developer',
    image:
      'https://cdn2.iconfinder.com/data/icons/male-avatars/256/avatars_accounts___man_male_people_person_wavy_hair_jacket_turtleneck.png',
  },
  {
    name: 'Daven Reez',
    role: 'UI/UX Engineer | Frontend Dev',
    image:
      'https://cdn2.iconfinder.com/data/icons/female-avatars-4/256/avatars_accounts___female_woman_people_person_pearl_earrings_earrings_necklace_shirt.png',
  },

  {
    name: 'Joshua Paulo',
    role: 'UI/UX | Fullstack Developer',
    image:
      'https://cdn2.iconfinder.com/data/icons/female-avatars-4/128/avatars_accounts___female_woman_people_person_earrings_hoodie_short_hair.png',
  },
]);
</script>
