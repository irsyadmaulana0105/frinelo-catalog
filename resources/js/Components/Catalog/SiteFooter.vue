<script setup>
import { computed } from 'vue'
import { waLink } from '@/lib/format'
import Icon from '@/Components/Catalog/Icon.vue'

const props = defineProps({
  shop: { type: Object, default: () => ({}) },
  whatsappNumber: { type: String, required: true },
})

const maps = computed(
  () => `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(props.shop.address ?? '')}`,
)
</script>

<template>
  <footer class="border-t border-rose-100 bg-rose-50/60">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 md:grid-cols-3">
      <div>
        <img src="/images/logo.png" alt="Frinelo" class="h-20 w-auto" />
        <p class="mt-2 max-w-xs text-sm leading-relaxed text-stone-500">
          Fashion wanita dengan sentuhan manis dan feminin, langsung dari Sidoarjo.
        </p>
      </div>

      <div>
        <p class="text-[11px] uppercase tracking-[0.25em] text-rose-400">Kunjungi Toko</p>
        <ul class="mt-3 space-y-2 text-sm text-stone-600">
          <li class="flex gap-2">
            <Icon name="pin" :size="16" class="mt-0.5 text-rose-400" />
            <a :href="maps" target="_blank" rel="noopener" class="hover:text-rose-500">{{ shop.address }}</a>
          </li>
          <li class="flex gap-2">
            <Icon name="clock" :size="16" class="mt-0.5 text-rose-400" />
            <span>Buka setiap hari, {{ shop.hours }}</span>
          </li>
        </ul>
      </div>

      <div>
        <p class="text-[11px] uppercase tracking-[0.25em] text-rose-400">Hubungi Kami</p>
        <ul class="mt-3 space-y-2 text-sm text-stone-600">
          <li class="flex gap-2">
            <Icon name="instagram" :size="16" class="mt-0.5 text-rose-400" />
            <a
              :href="`https://instagram.com/${shop.instagram}`"
              target="_blank"
              rel="noopener"
              class="hover:text-rose-500"
            >
              @{{ shop.instagram }}
            </a>
          </li>
          <li class="flex gap-2">
            <Icon name="whatsapp" :size="16" class="mt-0.5 text-rose-400" />
            <a :href="waLink(whatsappNumber)" target="_blank" rel="noopener" class="hover:text-rose-500">
              Chat admin via WhatsApp
            </a>
          </li>
        </ul>
      </div>
    </div>

    <p class="border-t border-rose-100 py-4 text-center text-[11px] text-stone-400">
      © {{ new Date().getFullYear() }} Toko Frinelo · Sidoarjo
    </p>
  </footer>
</template>
