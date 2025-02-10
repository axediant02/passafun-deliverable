<template>
  <div class="w-screen overflow-hidden min-h-screen">
    <div class="flex flex-col gap-3 max-w-lg mx-auto relative">
      <div class="flex flex-col place-items-center pt-9 z-20">
        <div
          class="w-full bg- pl-4 flex justify-between place-items-center max-w-lg bg-white fixed top-0 py-2 h-12"
        >
          <div>
            <div class="flex items-center">
              <img
                src="/images/passafun.png"
                alt="Passafun Logo"
                class="w-10 h-10 object-contain"
              />
            </div>
          </div>
          <slot name="app-title"></slot>
          <slot name="search-all-quizzes"></slot>
        </div>
      </div>

      <div>
        <slot></slot>
      </div>
    </div>

  </div>
</template>

