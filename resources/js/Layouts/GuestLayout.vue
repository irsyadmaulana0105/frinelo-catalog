<template>
  <div class="flex min-h-screen flex-col items-center justify-center bg-rose-50/40 px-4">
    <div class="text-center">
      <img src="/images/logo.png" alt="Frinelo" class="mx-auto h-24 w-auto" />
      <p class="mt-2 text-[11px] uppercase tracking-[0.25em] text-rose-400">Admin Katalog</p>
    </div>
    <div class="mt-8 w-full max-w-sm rounded-2xl bg-white p-6 shadow-sm ring-1 ring-rose-100">
      <slot />
    </div>
  </div>
</template>
