<script setup>
import { ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const toast = ref('')
let timer

// Pesan sukses muncul sebagai toast 3 detik
watch(
  () => page.props.flash,
  (flash) => {
    if (!flash?.success) return
    toast.value = flash.success
    clearTimeout(timer)
    timer = setTimeout(() => (toast.value = ''), 3000)
  },
  { immediate: true },
)
</script>

<template>
  <div class="min-h-screen bg-rose-50/40 text-stone-800">
    <header class="sticky top-0 z-30 border-b border-rose-100 bg-white/90 backdrop-blur">
      <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
        <Link :href="route('admin.products.index')" class="flex items-center gap-2">
          <img src="/images/logo.png" alt="" class="h-8 w-auto" />
          <span class="font-display text-xl uppercase tracking-[0.3em]">Frinelo</span>
          <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] uppercase tracking-wider text-rose-500">
            Admin
          </span>
        </Link>

        <nav class="flex items-center gap-4 text-sm text-stone-500">
          <a href="/" target="_blank" class="hover:text-rose-500">Lihat katalog</a>
          <Link :href="route('profile.edit')" class="hover:text-rose-500">Profil</Link>
          <Link :href="route('logout')" method="post" as="button" class="hover:text-rose-500">Keluar</Link>
        </nav>
      </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-6">
      <slot />
    </main>

    <Transition
      enter-active-class="transition duration-200"
      enter-from-class="translate-y-2 opacity-0"
      leave-active-class="transition duration-200"
      leave-to-class="translate-y-2 opacity-0"
    >
      <div
        v-if="toast"
        class="fixed bottom-20 left-1/2 z-50 -translate-x-1/2 rounded-full bg-stone-900 px-5 py-2.5 text-sm text-white shadow-lg"
      >
        {{ toast }}
      </div>
    </Transition>
  </div>
</template>
