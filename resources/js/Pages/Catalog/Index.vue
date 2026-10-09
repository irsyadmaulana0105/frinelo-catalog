<script setup>
import { computed, onMounted, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AnnouncementBar from '@/Components/Catalog/AnnouncementBar.vue'
import HeroSection from '@/Components/Catalog/HeroSection.vue'
import HowToOrder from '@/Components/Catalog/HowToOrder.vue'
import ProductCard from '@/Components/Catalog/ProductCard.vue'
import ProductModal from '@/Components/Catalog/ProductModal.vue'
import ResellerBanner from '@/Components/Catalog/ResellerBanner.vue'
import SiteFooter from '@/Components/Catalog/SiteFooter.vue'
import WhatsAppFab from '@/Components/Catalog/WhatsAppFab.vue'
import Icon from '@/Components/Catalog/Icon.vue'
import { waLink } from '@/lib/format'

const props = defineProps({
  products: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  whatsappNumber: { type: String, required: true },
  shop: { type: Object, default: () => ({}) },
})

// Nilai bawaan, jadi halaman tetap rapi walau config belum lengkap
const shopInfo = computed(() => ({
  name: 'Frinelo',
  tagline: 'Little pieces, pretty outfits',
  subtitle: 'Little Bangkok Baju · Sidoarjo',
  instagram: 'frinelo.wear',
  address: 'Jl. KH Mukmin No. 40, Sidoarjo',
  hours: '10.00 - 22.00',
  ...props.shop,
}))

const activeCategory = ref('Semua')
const query = ref('')
const sort = ref('terbaru')
const selected = ref(null)

const categoryList = computed(() => {
  const counts = {}
  props.products.forEach((p) => (counts[p.category] = (counts[p.category] || 0) + 1))
  return [
    { name: 'Semua', count: props.products.length },
    ...Object.keys(counts)
      .sort()
      .map((name) => ({ name, count: counts[name] })),
  ]
})

const filtered = computed(() => {
  const term = query.value.trim().toLowerCase()
  let list = props.products.filter(
    (p) =>
      (activeCategory.value === 'Semua' || p.category === activeCategory.value) &&
      (!term || `${p.name} ${p.category} ${p.colors.join(' ')}`.toLowerCase().includes(term)),
  )
  if (sort.value === 'termurah') list = [...list].sort((a, b) => a.price - b.price)
  if (sort.value === 'termahal') list = [...list].sort((a, b) => b.price - a.price)
  return list
})

function resetFilter() {
  activeCategory.value = 'Semua'
  query.value = ''
}

// Deep link: /?p=3 langsung membuka detail produk #3 (untuk link di bio/caption)
function open(product) {
  selected.value = product
  history.replaceState(history.state, '', `?p=${product.id}`)
}
function close() {
  selected.value = null
  history.replaceState(history.state, '', window.location.pathname)
}
onMounted(() => {
  const id = Number(new URLSearchParams(window.location.search).get('p'))
  if (id) selected.value = props.products.find((p) => p.id === id) ?? null
})
</script>

<template>
  <Head :title="`${shopInfo.name} · Katalog Fashion Wanita`" />

  <div class="min-h-screen bg-white text-stone-800">
    <AnnouncementBar :shop="shopInfo" />

    <header class="sticky top-0 z-30 h-14 border-b border-rose-100 bg-white/90 backdrop-blur">
      <div class="mx-auto flex h-full max-w-6xl items-center justify-between px-4">
        <a href="/" class="flex items-center gap-3" aria-label="Frinelo">
          <img src="/images/logo.png" alt="" class="h-9 w-auto" />
          <span class="font-display text-2xl uppercase tracking-[0.3em]">Frinelo</span>
          <span class="hidden text-[10px] uppercase tracking-[0.25em] text-stone-400 sm:inline">
            {{ shopInfo.subtitle }}
          </span>
        </a>
        <nav class="flex items-center gap-1 text-stone-600">
          <a
            :href="`https://instagram.com/${shopInfo.instagram}`"
            target="_blank"
            rel="noopener"
            aria-label="Instagram"
            class="rounded-full p-2 transition hover:bg-rose-50 hover:text-rose-500"
          >
            <Icon name="instagram" />
          </a>
          <a
            :href="waLink(whatsappNumber)"
            target="_blank"
            rel="noopener"
            aria-label="WhatsApp"
            class="rounded-full p-2 transition hover:bg-rose-50 hover:text-rose-500"
          >
            <Icon name="whatsapp" />
          </a>
        </nav>
      </div>
    </header>

    <HeroSection :products="products" :shop="shopInfo" :whatsapp-number="whatsappNumber" @select="open" />
    <HowToOrder />

    <main id="koleksi" class="mx-auto max-w-6xl scroll-mt-14 px-4 pb-24 pt-10">
      <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <h2 class="font-display text-4xl">Koleksi</h2>
          <p class="text-sm text-stone-500">{{ filtered.length }} produk</p>
        </div>

        <div class="flex gap-2">
          <label class="relative flex-1 md:w-64 md:flex-none">
            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400">
              <Icon name="search" :size="16" />
            </span>
            <input
              v-model="query"
              type="search"
              placeholder="Cari baju atau warna..."
              class="w-full rounded-full border border-stone-200 bg-white py-2.5 pl-10 pr-4 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400"
            />
          </label>
          <select
            v-model="sort"
            aria-label="Urutkan"
            class="rounded-full border border-stone-200 bg-white px-4 py-2.5 text-sm text-stone-600 focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400"
          >
            <option value="terbaru">Terbaru</option>
            <option value="termurah">Harga terendah</option>
            <option value="termahal">Harga tertinggi</option>
          </select>
        </div>
      </div>

      <!-- Filter kategori: menempel di bawah header saat scroll -->
      <nav
        class="sticky top-14 z-20 -mx-4 mt-6 border-b border-rose-50 bg-white/90 px-4 py-3 backdrop-blur"
        aria-label="Filter kategori"
      >
        <div class="no-scrollbar flex gap-2 overflow-x-auto">
          <button
            v-for="c in categoryList"
            :key="c.name"
            type="button"
            class="shrink-0 rounded-full border px-4 py-1.5 text-xs uppercase tracking-wider transition"
            :class="activeCategory === c.name
              ? 'border-rose-400 bg-rose-400 text-white'
              : 'border-stone-200 bg-white text-stone-500 hover:border-rose-300'"
            @click="activeCategory = c.name"
          >
            {{ c.name }} <span class="opacity-60">{{ c.count }}</span>
          </button>
        </div>
      </nav>

      <div class="mt-6 grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 md:gap-x-6 lg:grid-cols-4">
        <ProductCard v-for="p in filtered" :key="p.id" :product="p" @select="open" />
      </div>

      <div v-if="!filtered.length" class="py-20 text-center">
        <p class="font-display text-2xl text-stone-700">
          {{ products.length ? 'Belum ketemu yang cocok' : 'Koleksi segera hadir' }}
        </p>
        <p class="mt-1 text-sm text-stone-400">
          {{ products.length ? 'Coba kata kunci atau kategori lain.' : 'Admin sedang menyiapkan katalog.' }}
        </p>
        <button
          v-if="products.length"
          type="button"
          class="mt-5 rounded-full border border-rose-300 px-5 py-2 text-xs uppercase tracking-wider text-rose-500 transition hover:bg-rose-50"
          @click="resetFilter"
        >
          Reset filter
        </button>
      </div>
    </main>

    <ResellerBanner :shop="shopInfo" />
    <SiteFooter :shop="shopInfo" :whatsapp-number="whatsappNumber" />
    <WhatsAppFab :whatsapp-number="whatsappNumber" />

    <ProductModal
      v-if="selected"
      :key="selected.id"
      :product="selected"
      :whatsapp-number="whatsappNumber"
      @close="close"
    />
  </div>
</template>
