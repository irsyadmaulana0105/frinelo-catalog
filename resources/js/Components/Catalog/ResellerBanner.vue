<script setup>
import { computed } from 'vue'
import { rupiah, waLink } from '@/lib/format'
import Icon from '@/Components/Catalog/Icon.vue'

const props = defineProps({ shop: { type: Object, default: () => ({}) } })

const number = computed(() => props.shop.wholesale_number ?? '')
const url = computed(() =>
  waLink(number.value, 'Halo Admin Grosir Frinelo, saya tertarik jadi reseller. Boleh minta info paket usahanya?'),
)
</script>

<template>
  <section v-if="number" class="bg-rose-100/60">
    <div
      class="mx-auto flex max-w-6xl flex-col items-center gap-5 px-4 py-10 text-center md:flex-row md:justify-between md:text-left"
    >
      <div>
        <p class="text-[11px] uppercase tracking-[0.3em] text-rose-400">✦ Reseller welcome ✦</p>
        <h2 class="font-display mt-1 text-3xl text-stone-900 md:text-4xl">Mau jualan Frinelo juga?</h2>
        <p class="mt-2 max-w-md text-sm leading-relaxed text-stone-500">
          Paket usaha mulai {{ rupiah(shop.reseller_from ?? 500000) }}. Hubungi admin grosir untuk info harga dan paketnya.
        </p>
      </div>
      <a
        :href="url"
        target="_blank"
        rel="noopener"
        class="inline-flex shrink-0 items-center gap-2 rounded-full bg-stone-900 px-7 py-3.5 text-xs font-medium uppercase tracking-wider text-white transition hover:bg-rose-400"
      >
        <Icon name="whatsapp" :size="16" /> Chat Admin Grosir
      </a>
    </div>
  </section>
</template>
