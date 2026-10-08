<script setup>
const steps = [
  { n: 1, title: 'Pilih baju', desc: 'Telusuri koleksi dan ketuk yang kamu suka.' },
  { n: 2, title: 'Pilih ukuran & warna', desc: 'Ketuk pilihan yang tersedia di halaman detail.' },
  { n: 3, title: 'Kirim ke WhatsApp', desc: 'Pesan sudah tersusun otomatis, tinggal kirim.' },
]
</script>

<template>
  <section class="border-y border-rose-100 bg-white">
    <ol class="mx-auto grid max-w-6xl grid-cols-3 gap-2 px-4 py-5 md:gap-6 md:py-7">
      <li
        v-for="s in steps"
        :key="s.n"
        class="flex flex-col items-center text-center md:flex-row md:items-start md:gap-4 md:text-left"
      >
        <span
          class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-rose-100 text-lg text-rose-500 md:h-11 md:w-11 md:text-xl"
        >
          {{ s.n }}
        </span>
        <div class="mt-2 md:mt-0">
          <p class="text-[11px] font-medium uppercase tracking-wider text-stone-800 md:text-xs">{{ s.title }}</p>
          <p class="mt-1 hidden text-sm text-stone-500 md:block">{{ s.desc }}</p>
        </div>
      </li>
    </ol>
  </section>
</template>
