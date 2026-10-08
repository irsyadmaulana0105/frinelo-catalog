<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { rupiah } from '@/lib/format'

const props = defineProps({ products: { type: Array, default: () => [] } })

const q = ref('')
const status = ref('semua')
const copiedId = ref(null)

const tabs = [
  ['semua', 'Semua'],
  ['tampil', 'Tampil'],
  ['sembunyi', 'Disembunyikan'],
]

const stats = computed(() => [
  { label: 'Total produk', value: props.products.length },
  { label: 'Tampil', value: props.products.filter((p) => p.is_active).length },
  { label: 'Disembunyikan', value: props.products.filter((p) => !p.is_active).length },
  { label: 'Kategori', value: new Set(props.products.map((p) => p.category)).size },
])

const filtered = computed(() => {
  const term = q.value.trim().toLowerCase()
  return props.products.filter((p) => {
    const matchText = !term || `${p.name} ${p.category}`.toLowerCase().includes(term)
    const matchStatus =
      status.value === 'semua' || (status.value === 'tampil' ? p.is_active : !p.is_active)
    return matchText && matchStatus
  })
})

function toggle(p) {
  router.patch(route('admin.products.toggle', p.id), {}, { preserveScroll: true })
}

function hapus(p) {
  if (confirm(`Hapus "${p.name}"? Tindakan ini tidak bisa dibatalkan.`)) {
    router.delete(route('admin.products.destroy', p.id), { preserveScroll: true })
  }
}

async function copyLink(p) {
  const link = `${location.origin}/?p=${p.id}`
  try {
    await navigator.clipboard.writeText(link)
    copiedId.value = p.id
    setTimeout(() => (copiedId.value = null), 1500)
  } catch {
    window.prompt('Salin link ini:', link)
  }
}
</script>

<template>
  <Head title="Kelola Produk" />

  <AdminLayout>
    <div class="flex items-end justify-between gap-3">
      <div>
        <h1 class="font-display text-3xl">Produk</h1>
        <p class="text-sm text-stone-500">Atur isi katalog Frinelo di sini.</p>
      </div>
      <Link
        :href="route('admin.products.create')"
        class="shrink-0 rounded-full bg-rose-400 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white shadow-sm transition hover:bg-rose-500"
      >
        + Tambah
      </Link>
    </div>

    <!-- Ringkasan -->
    <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-4">
      <div v-for="s in stats" :key="s.label" class="rounded-2xl bg-white p-4 ring-1 ring-rose-100">
        <p class="text-3xl font-medium text-stone-900">{{ s.value }}</p>
        <p class="text-xs uppercase tracking-wider text-stone-400">{{ s.label }}</p>
      </div>
    </div>

    <!-- Cari & filter -->
    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
      <input
        v-model="q"
        type="search"
        placeholder="Cari nama atau kategori..."
        class="w-full rounded-full border border-stone-200 bg-white px-4 py-2.5 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400 sm:max-w-xs"
      />
      <div class="flex gap-2 overflow-x-auto">
        <button
          v-for="[key, label] in tabs"
          :key="key"
          type="button"
          class="shrink-0 rounded-full border px-4 py-1.5 text-xs uppercase tracking-wider transition"
          :class="status === key
            ? 'border-rose-400 bg-rose-400 text-white'
            : 'border-stone-200 bg-white text-stone-500 hover:border-rose-300'"
          @click="status = key"
        >
          {{ label }}
        </button>
      </div>
    </div>

    <!-- Grid produk -->
    <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
      <article
        v-for="p in filtered"
        :key="p.id"
        class="overflow-hidden rounded-2xl bg-white ring-1 ring-rose-100"
      >
        <div class="relative aspect-[3/4] bg-stone-100">
          <img
            v-if="p.image_src"
            :src="p.image_src"
            :alt="p.name"
            loading="lazy"
            class="h-full w-full object-cover transition"
            :class="{ 'opacity-40 grayscale': !p.is_active }"
          />
          <span
            v-if="!p.is_active"
            class="absolute left-2 top-2 rounded-full bg-stone-900/80 px-2.5 py-1 text-[10px] uppercase tracking-wider text-white"
          >
            Disembunyikan
          </span>
        </div>

        <div class="p-3">
          <p class="text-[10px] uppercase tracking-[0.2em] text-rose-400">{{ p.category }}</p>
          <h3 class="mt-0.5 truncate text-sm font-medium text-stone-900">{{ p.name }}</h3>
          <p class="text-sm text-stone-500">{{ rupiah(p.price) }}</p>
          <p class="mt-1 truncate text-[11px] text-stone-400">
            {{ p.sizes.join(', ') }} · {{ p.colors.join(', ') }}
          </p>

          <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-stone-100 pt-3">
            <button
              type="button"
              role="switch"
              :aria-checked="p.is_active"
              :title="p.is_active ? 'Sembunyikan dari katalog' : 'Tampilkan di katalog'"
              class="relative h-6 w-11 shrink-0 rounded-full transition"
              :class="p.is_active ? 'bg-rose-400' : 'bg-stone-300'"
              @click="toggle(p)"
            >
              <span
                class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition-transform"
                :class="p.is_active ? 'translate-x-5' : 'translate-x-0'"
              />
            </button>

            <div class="flex items-center gap-3 text-xs">
              <button type="button" class="text-stone-500 hover:text-rose-500" @click="copyLink(p)">
                {{ copiedId === p.id ? 'Tersalin ✓' : 'Salin link' }}
              </button>
              <Link :href="route('admin.products.edit', p.id)" class="text-stone-500 hover:text-rose-500">
                Edit
              </Link>
              <button type="button" class="text-stone-400 hover:text-red-600" @click="hapus(p)">
                Hapus
              </button>
            </div>
          </div>
        </div>
      </article>
    </div>

    <p v-if="!filtered.length" class="py-16 text-center text-sm text-stone-400">
      {{ products.length ? 'Tidak ada produk yang cocok.' : 'Belum ada produk. Klik "+ Tambah" untuk mulai.' }}
    </p>
  </AdminLayout>
</template>
