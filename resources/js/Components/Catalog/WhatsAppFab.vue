<script setup>
import { computed } from 'vue'
import { waLink } from '@/lib/format'
import Icon from '@/Components/Catalog/Icon.vue'

const props = defineProps({ whatsappNumber: { type: String, required: true } })

const url = computed(() =>
  waLink(props.whatsappNumber, 'Halo Admin Frinelo, saya mau tanya koleksi terbaru ya.'),
)
</script>

<template>
  <a
    :href="url"
    target="_blank"
    rel="noopener"
    aria-label="Chat admin via WhatsApp"
    class="fixed bottom-5 right-4 z-40 flex items-center gap-2 rounded-full bg-emerald-500 p-3.5 text-white shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-600 md:px-5"
  >
    <Icon name="whatsapp" :size="22" />
    <span class="hidden text-sm font-medium md:inline">Chat Admin</span>
  </a>
</template>
