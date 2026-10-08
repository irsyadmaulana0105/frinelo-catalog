<script setup>
import { computed } from 'vue'
import { rupiah, isNew } from '@/lib/format'
import { colorHex } from '@/lib/colors'

const props = defineProps({ product: { type: Object, required: true } })
defineEmits(['select'])

const fresh = computed(() => isNew(props.product))
const shownColors = computed(() => props.product.colors.slice(0, 4))
const extra = computed(() => Math.max(0, props.product.colors.length - 4))
</script>

<template>
  <button type="button" class="group block w-full text-left" @click="$emit('select', product)">
    <div class="relative aspect-[3/4] overflow-hidden rounded-2xl bg-rose-50 ring-1 ring-rose-100">
      <img
        v-if="product.image_src"
        :src="product.image_src"
        :alt="product.name"
        loading="lazy"
        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
      />
      <div v-else class="flex h-full items-center justify-center text-2xl text-rose-200">✦</div>

      <span
        v-if="fresh"
        class="absolute left-2.5 top-2.5 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-medium uppercase tracking-wider text-rose-500 shadow-sm"
      >
        Baru
      </span>

      <span
        class="absolute inset-x-3 bottom-3 hidden translate-y-2 rounded-full bg-white/95 py-2 text-center text-[11px] uppercase tracking-wider text-stone-800 opacity-0 shadow transition group-hover:translate-y-0 group-hover:opacity-100 md:block"
      >
        Lihat detail
      </span>
    </div>

    <div class="px-0.5 pt-3">
      <p class="text-[10px] uppercase tracking-[0.2em] text-rose-400">{{ product.category }}</p>
      <h3 class="mt-0.5 truncate text-sm text-stone-800">{{ product.name }}</h3>

      <div class="mt-1 flex items-center justify-between gap-2">
        <p class="text-sm font-medium text-stone-900">{{ rupiah(product.price) }}</p>
        <div class="flex items-center -space-x-1" :title="product.colors.join(', ')">
          <span
            v-for="c in shownColors"
            :key="c"
            class="h-3.5 w-3.5 rounded-full border border-stone-300"
            :style="{ background: colorHex(c) }"
          />
          <span v-if="extra" class="pl-2 text-[10px] text-stone-400">+{{ extra }}</span>
        </div>
      </div>
      <p class="mt-0.5 text-[11px] text-stone-400">{{ product.sizes.join(' · ') }}</p>
    </div>
  </button>
</template>
