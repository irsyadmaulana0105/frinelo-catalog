<script setup>
import { computed } from 'vue'

const props = defineProps({ shop: { type: Object, default: () => ({}) } })

const items = computed(() =>
  [
    'New Arrival',
    props.shop.tagline,
    props.shop.hours ? `Buka setiap hari ${props.shop.hours}` : null,
    'Pesan mudah via WhatsApp',
  ].filter(Boolean),
)
</script>

<template>
  <div class="overflow-hidden bg-rose-400 py-2 text-[11px] uppercase tracking-[0.25em] text-white">
    <div class="marquee-track flex w-max">
      <!-- dua grup identik agar putarannya mulus -->
      <div v-for="g in 2" :key="g" class="flex shrink-0 items-center" :aria-hidden="g === 2">
        <template v-for="n in 2" :key="n">
          <span v-for="(t, i) in items" :key="`${n}-${i}`" class="whitespace-nowrap">
            <span class="px-6">{{ t }}</span><span class="opacity-70">✦</span>
          </span>
        </template>
      </div>
    </div>
  </div>
</template>
