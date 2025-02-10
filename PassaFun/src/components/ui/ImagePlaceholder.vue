<template>
  <svg
    viewBox="0 0 80 80"
    fill="none"
    role="img"
    xmlns="http://www.w3.org/2000/svg"
    width="128"
    height="128"
    style="border-radius: 20px; overflow: hidden"
  >
    <mask id=":r136:" maskUnits="userSpaceOnUse" x="0" y="0" width="80" height="80">
      <rect width="80" height="80" fill="#FFFFFF" rx="8" ry="8"></rect>
    </mask>
    <g mask="url(#:r136:)">
      <rect width="80" height="80" :fill="accentColor" rx="8" ry="8"></rect>
      <path
        filter="url(#filter_:r136:)"
        d="M32.414 59.35L50.376 70.5H72.5v-71H33.728L26.5 13.381l19.057 27.08L32.414 59.35z"
        :fill="mainColor"
        transform="translate(-2 -2) rotate(-242 40 40) scale(1.5)"
      ></path>
      <path
        filter="url(#filter_:r136:)"
        d="M22.216 24L0 46.75l14.108 38.129L78 86l-3.081-59.276-22.378 4.005 12.972 20.186-23.35 27.395L22.215 24z"
        fill="#ffffff"
        transform="translate(-7 7) rotate(-183 40 40) scale(1.5)"
        style="mix-blend-mode: overlay"
      ></path>
    </g>
    <defs>
      <filter id="filter_:r136:" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
        <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
        <feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape"></feBlend>
        <feGaussianBlur stdDeviation="7" result="effect1_foregroundBlur"></feGaussianBlur>
      </filter>
    </defs>
  </svg>
</template>

<script setup>
defineProps({
  mainColor: {
    type: String,
    default: '#1c3879',
  },
  accentColor: {
    type: String,
    default: '#346ecf',
  },
});
</script>
