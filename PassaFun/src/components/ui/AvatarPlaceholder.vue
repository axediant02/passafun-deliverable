<template>
  <svg viewBox="0 0 36 36" fill="none" role="img" xmlns="http://www.w3.org/2000/svg" width="150" height="150">
    <mask id=":r100:" maskUnits="userSpaceOnUse" x="0" y="0" width="36" height="36">
      <rect width="36" height="36" fill="#FFFFFF"></rect>
    </mask>
    <g mask="url(#:r100:)">
      <rect width="36" height="36" fill="#ffb238"></rect>
      <rect x="0" y="0" width="36" height="36" transform="translate(6 -2) rotate(296 18 18) scale(1.2)" :fill="bgColor"
        rx="6"></rect>
      <g transform="translate(0 -4) rotate(6 18 18)">
        <path d="M15 21c2 1 4 1 6 0" stroke="#FFFFFF" fill="none" stroke-linecap="round"></path>
        <rect x="13" y="14" width="1.5" height="2" rx="1" stroke="none" fill="#FFFFFF"></rect>
        <rect x="21" y="14" width="1.5" height="2" rx="1" stroke="none" fill="#FFFFFF"></rect>
      </g>
    </g>
  </svg>
</template>

<script setup>
defineProps({
  bgColor: {
    type: String,
    default: '#ff005b',
  },
});
</script>
