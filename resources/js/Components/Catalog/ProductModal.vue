<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { rupiah, isNew, waLink } from '@/lib/format'
import { colorHex } from '@/lib/colors'
import Icon from '@/Components/Catalog/Icon.vue'

const props = defineProps({
  product: { type: Object, required: true },
  whatsappNumber: { type: String, required: true },
})
const emit = defineEmits(['close'])

// true = pesan WhatsApp menyertakan link produk (memudahkan admin membuka produknya)
const INCLUDE_LINK = true

// Kalau hanya ada satu pilihan, langsung terpilih (mis. "All Size")
const size = ref(props.product.sizes.length === 1 ? props.product.sizes[0] : null)
const color = ref(props.product.colors.length === 1 ? props.product.colors[0] : null)
const ready = computed(() => Boolean(size.value && color.value))
const hint = computed(() => {
  if (!size.value && !color.value) return 'Pilih ukuran dan warna dulu'
  return !size.value ? 'Pilih ukuran dulu' : 'Pilih warna dulu'
})

const productUrl = computed(() => `${window.location.origin}/?p=${props.product.id}`)

const waUrl = computed(() => {
  const lines = [
    'Halo Admin Frinelo, saya mau pesan produk ini:',
    `- Produk: ${props.product.name}`,
    `- Ukuran: ${size.value}`,
    `- Warna: ${color.value}`,
    `- Harga: ${rupiah(props.product.price)}`,
  ]
  const isLocal = ['localhost', '127.0.0.1'].includes(window.location.hostname)
  if (INCLUDE_LINK && !isLocal) lines.push(`- Link: ${productUrl.value}`)
  lines.push('Apakah stoknya masih ada?')

  return waLink(props.whatsappNumber, lines.join('\n'))
})

const copied = ref(false)
async function copyLink() {
  try {
    await navigator.clipboard.writeText(productUrl.value)
    copied.value = true
    setTimeout(() => (copied.value = false), 1500)
  } catch {
    window.prompt('Salin link ini:', productUrl.value)
  }
}

const chip = (on) =>
  on
    ? 'border-rose-400 bg-rose-400 text-white'
    : 'border-stone-200 bg-white text-stone-600 hover:border-rose-300'

const onKey = (e) => e.key === 'Escape' && emit('close')

onMounted(() => {
  document.body.style.overflow = 'hidden'
  window.addEventListener('keydown', onKey)
})
onBeforeUnmount(() => {
  document.body.style.overflow = ''
  window.removeEventListener('keydown', onKey)
})
</script>

<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-end justify-center bg-stone-900/40 md:items-center md:p-6"
      @click.self="emit('close')"
    >
      <div
        role="dialog"
        aria-modal="true"
        :aria-label="product.name"
        class="relative max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-t-3xl bg-white md:rounded-3xl"
      >
        <!-- Tombol tutup tetap terlihat saat konten di-scroll -->
        <div class="sticky top-0 z-10 h-0">
          <button
            type="button"
            class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-stone-600 shadow transition hover:text-rose-500"
            aria-label="Tutup"
            @click="emit('close')"
          >
            <Icon name="close" :size="18" />
          </button>
        </div>

        <div class="grid md:grid-cols-2">
          <!-- Foto -->
          <div class="relative h-72 bg-rose-50 md:h-full md:min-h-[540px]">
            <img
              v-if="product.image_src"
              :src="product.image_src"
              :alt="product.name"
              class="h-full w-full object-cover object-top"
            />
            <span
              v-if="isNew(product)"
              class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-[10px] font-medium uppercase tracking-wider text-rose-500 shadow-sm"
            >
              Baru
            </span>
          </div>

          <!-- Info -->
          <div class="flex flex-col">
            <div class="flex-1 p-6">
              <p class="text-[11px] uppercase tracking-[0.25em] text-rose-400">{{ product.category }}</p>
              <h2 class="font-display mt-1 text-3xl leading-tight text-stone-900">{{ product.name }}</h2>
              <p class="mt-2 text-xl text-stone-800">{{ rupiah(product.price) }}</p>
              <p v-if="product.description" class="mt-4 whitespace-pre-line text-sm leading-relaxed text-stone-500">
                {{ product.description }}
              </p>

              <!-- Ukuran -->
              <div class="mt-6">
                <p class="mb-2 text-xs uppercase tracking-wider text-stone-500">
                  Ukuran <span v-if="size" class="text-stone-900">· {{ size }}</span>
                </p>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="s in product.sizes"
                    :key="s"
                    type="button"
                    :aria-pressed="size === s"
                    class="min-w-[2.75rem] rounded-xl border px-4 py-2 text-sm transition"
                    :class="chip(size === s)"
                    @click="size = s"
                  >
                    {{ s }}
                  </button>
                </div>
              </div>

              <!-- Warna -->
              <div class="mt-5">
                <p class="mb-2 text-xs uppercase tracking-wider text-stone-500">
                  Warna <span v-if="color" class="text-stone-900">· {{ color }}</span>
                </p>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="c in product.colors"
                    :key="c"
                    type="button"
                    :aria-pressed="color === c"
                    class="flex items-center gap-2 rounded-full border py-1.5 pl-2.5 pr-4 text-sm transition"
                    :class="chip(color === c)"
                    @click="color = c"
                  >
                    <span
                      class="h-4 w-4 rounded-full border border-black/10"
                      :style="{ background: colorHex(c) }"
                    />
                    {{ c }}
                  </button>
                </div>
              </div>

              <button
                type="button"
                class="mt-6 inline-flex items-center gap-1.5 text-xs text-stone-400 transition hover:text-rose-500"
                @click="copyLink"
              >
                <Icon :name="copied ? 'check' : 'link'" :size="14" />
                {{ copied ? 'Link tersalin' : 'Salin link produk' }}
              </button>
            </div>

            <!-- Tombol pesan: selalu menempel di bawah -->
            <div
              class="sticky bottom-0 border-t border-rose-100 bg-white/95 p-4 pb-[max(1rem,env(safe-area-inset-bottom))] backdrop-blur"
            >
              <a
                v-if="ready"
                :href="waUrl"
                target="_blank"
                rel="noopener"
                class="flex items-center justify-center gap-2 rounded-full bg-emerald-500 py-3.5 text-sm font-medium uppercase tracking-wider text-white transition hover:bg-emerald-600"
              >
                <Icon name="whatsapp" :size="18" /> Pesan via WhatsApp
              </a>
              <button
                v-else
                type="button"
                disabled
                class="block w-full cursor-not-allowed rounded-full bg-stone-200 py-3.5 text-sm uppercase tracking-wider text-stone-400"
              >
                {{ hint }}
              </button>
              <p class="mt-2 text-center text-[11px] text-stone-400">
                Ketersediaan stok dikonfirmasi admin lewat WhatsApp.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
