<script setup>
import { computed } from 'vue'
import { rupiah, waLink } from '@/lib/format'
import Icon from '@/Components/Catalog/Icon.vue'

const props = defineProps({
  products: { type: Array, default: () => [] },
  shop: { type: Object, default: () => ({}) },
  whatsappNumber: { type: String, required: true },
})
defineEmits(['select'])

// Dua produk berfoto pertama tampil di bingkai lengkung
const featured = computed(() => props.products.filter((p) => p.image_src).slice(0, 2))
const minPrice = computed(() =>
  props.products.length ? Math.min(...props.products.map((p) => p.price)) : null,
)
const chatUrl = computed(() =>
  waLink(props.whatsappNumber, 'Halo Admin Frinelo, saya mau tanya koleksi terbaru ya.'),
)
</script>

<template>
  <section class="bg-blush-hero">
    <div class="mx-auto grid max-w-6xl items-center gap-8 px-4 py-10 md:grid-cols-2 md:py-16">
      <div class="text-center md:text-left">
        <p class="text-[11px] uppercase tracking-[0.3em] text-rose-400">✦ New Arrival ✦</p>
        <h2 class="font-display mt-3 text-5xl leading-[1.05] text-stone-900 md:text-6xl">
          Little pieces,<br />
          <em class="text-rose-400">pretty outfits</em>
        </h2>
        <p class="mx-auto mt-4 max-w-md text-sm leading-relaxed text-stone-500 md:mx-0">
          Koleksi atasan, rok, dan celana pilihan. Pilih ukuran dan warna favoritmu,
          lalu pesan langsung ke admin lewat WhatsApp.
        </p>

        <div class="mt-6 flex flex-wrap justify-center gap-3 md:justify-start">
          <a
            href="#koleksi"
            class="rounded-full bg-rose-400 px-6 py-3 text-xs font-medium uppercase tracking-wider text-white transition hover:bg-rose-500"
          >
            Lihat Koleksi
          </a>
          <a
            :href="chatUrl"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center gap-2 rounded-full border border-stone-300 bg-white px-6 py-3 text-xs uppercase tracking-wider text-stone-700 transition hover:border-rose-400 hover:text-rose-500"
          >
            <Icon name="whatsapp" :size="16" /> Chat Admin
          </a>
        </div>

        <p v-if="minPrice !== null" class="mt-5 text-xs text-stone-400">
          {{ products.length }} model · mulai dari {{ rupiah(minPrice) }}
        </p>
      </div>

      <!-- Bingkai lengkung (terinspirasi cermin pink di toko) -->
      <div class="grid grid-cols-2 gap-3 md:gap-5">
        <template v-if="featured.length">
          <button
            v-for="(p, i) in featured"
            :key="p.id"
            type="button"
            class="group block text-left"
            :class="i === 1 ? 'mt-10' : ''"
            @click="$emit('select', p)"
          >
            <div class="arch bg-rose-200 p-1.5 md:p-2">
              <div class="arch aspect-[3/4.4] overflow-hidden bg-rose-50">
                <img
                  :src="p.image_src"
                  :alt="p.name"
                  class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                />
              </div>
            </div>
            <p class="mt-2 truncate text-center text-xs text-stone-500">{{ p.name }}</p>
          </button>
        </template>
        <template v-else>
          <div v-for="i in 2" :key="i" class="arch bg-rose-200 p-2" :class="i === 2 ? 'mt-10' : ''">
            <div class="arch flex aspect-[3/4.4] items-center justify-center bg-rose-50 text-3xl text-rose-300">
              ✦
            </div>
          </div>
        </template>
      </div>
    </div>
  </section>
</template>
