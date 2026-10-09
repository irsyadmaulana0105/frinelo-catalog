<script setup>
import { computed, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { rupiah } from '@/lib/format'

const props = defineProps({
  product: { type: Object, default: null },
  categories: { type: Array, default: () => [] },
  sizeOptions: { type: Array, required: true },
})

const isEdit = Boolean(props.product)

const form = useForm({
  ...(isEdit ? { _method: 'put' } : {}), // upload file + PUT harus lewat POST spoofing
  name: props.product?.name ?? '',
  price: props.product?.price ?? '',
  category: props.product?.category ?? '',
  sizes: props.product?.sizes ?? ['All Size'],
  colors: props.product?.colors ?? [],
  description: props.product?.description ?? '',
  is_active: props.product?.is_active ?? true,
  image: null,
  gallery_new: [],
  keep_gallery: [],
})

const preview = ref(props.product?.image_src ?? null)
const colorInput = ref('')
const busy = ref(false)

// ---- Foto tambahan (galeri) ----
const MAX_GALLERY = 5
const existing = ref(
  (props.product?.gallery ?? []).map((path, i) => ({ path, src: props.product.gallery_src?.[i] ?? path })),
)
const added = ref([]) // { file, src }
const room = computed(() => MAX_GALLERY - existing.value.length - added.value.length)
const galleryError = computed(
  () => Object.entries(form.errors).find(([key]) => key.startsWith('gallery_new'))?.[1],
)

// Foto dari HP sering 3-6 MB. Kecilkan dulu sebelum diunggah; kalau gagal, pakai file asli.
async function shrink(file, max = 1600, quality = 0.85) {
  if (!file.type.startsWith('image/') || file.type === 'image/gif') return file
  try {
    const bmp = await createImageBitmap(file, { imageOrientation: 'from-image' })
    const scale = Math.min(1, max / Math.max(bmp.width, bmp.height))
    const w = Math.round(bmp.width * scale)
    const h = Math.round(bmp.height * scale)
    const canvas = document.createElement('canvas')
    canvas.width = w
    canvas.height = h
    const ctx = canvas.getContext('2d')
    ctx.fillStyle = '#ffffff'
    ctx.fillRect(0, 0, w, h)
    ctx.drawImage(bmp, 0, 0, w, h)
    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality))
    if (!blob || blob.size >= file.size) return file // jangan memperbesar
    return new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' })
  } catch {
    return file
  }
}

async function onGallery(e) {
  const picked = Array.from(e.target.files ?? []).slice(0, Math.max(room.value, 0))
  e.target.value = ''
  busy.value = true
  for (const original of picked) {
    const file = await shrink(original)
    added.value.push({ file, src: URL.createObjectURL(file) })
  }
  busy.value = false
}
function removeExisting(path) {
  existing.value = existing.value.filter((g) => g.path !== path)
}
function removeAdded(i) {
  added.value.splice(i, 1)
}

const field =
  'mt-1 block w-full rounded-lg border border-stone-200 bg-white px-3 py-2.5 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400'
const chip = (on) =>
  on
    ? 'border-rose-400 bg-rose-400 text-white'
    : 'border-stone-200 bg-white text-stone-600 hover:border-rose-300'

function toggleSize(s) {
  form.sizes = form.sizes.includes(s) ? form.sizes.filter((x) => x !== s) : [...form.sizes, s]
}

function addColor() {
  colorInput.value
    .split(',')
    .map((c) => c.trim())
    .filter((c) => c && !form.colors.includes(c))
    .forEach((c) => form.colors.push(c))
  colorInput.value = ''
}
function onColorKey(e) {
  if (e.key === 'Enter' || e.key === ',') {
    e.preventDefault()
    addColor()
  }
}
function removeColor(c) {
  form.colors = form.colors.filter((x) => x !== c)
}

async function onFile(e) {
  const picked = e.target.files[0] ?? null
  if (!picked) return
  busy.value = true
  const file = await shrink(picked)
  busy.value = false
  form.image = file
  preview.value = URL.createObjectURL(file)
}

function submit() {
  addColor() // masukkan warna yang masih terketik
  form.keep_gallery = existing.value.map((g) => g.path)
  form.gallery_new = added.value.map((g) => g.file)
  const url = isEdit
    ? route('admin.products.update', props.product.id)
    : route('admin.products.store')
  form.post(url, { forceFormData: true })
}
</script>

<template>
  <Head :title="isEdit ? 'Edit Produk' : 'Tambah Produk'" />

  <AdminLayout>
    <Link :href="route('admin.products.index')" class="text-sm text-stone-500 hover:text-rose-500">
      &larr; Kembali
    </Link>
    <h1 class="font-display mt-1 text-3xl">{{ isEdit ? 'Edit Produk' : 'Produk Baru' }}</h1>

    <div class="mt-5 grid gap-6 lg:grid-cols-[1fr_280px]">
      <form class="space-y-6 rounded-2xl bg-white p-5 ring-1 ring-rose-100" @submit.prevent="submit">
        <!-- Foto -->
        <div>
          <InputLabel value="Foto produk" />
          <label
            for="image"
            class="mt-1 flex cursor-pointer items-center gap-4 rounded-xl border border-dashed border-rose-200 bg-rose-50/40 p-3 transition hover:border-rose-400"
          >
            <img v-if="preview" :src="preview" alt="Preview" class="h-24 w-[4.5rem] rounded-lg object-cover" />
            <div v-else class="flex h-24 w-[4.5rem] items-center justify-center rounded-lg bg-rose-100 text-2xl text-rose-300">+</div>
            <span class="text-sm text-stone-500">
              <span class="font-medium text-rose-500">{{ preview ? 'Ganti foto' : 'Pilih foto' }}</span><br />
              Portrait 3:4. Ukuran foto dikecilkan otomatis
            </span>
          </label>
          <input id="image" type="file" accept="image/*" class="sr-only" @change="onFile" />
          <InputError :message="form.errors.image" class="mt-1" />
        </div>

        <!-- Foto tambahan -->
        <div>
          <InputLabel :value="`Foto tambahan (${existing.length + added.length}/${MAX_GALLERY})`" />
          <p class="mt-0.5 text-xs text-stone-400">Tampil sebagai foto geser di halaman detail produk.</p>
          <div class="mt-2 flex flex-wrap gap-3">
            <div v-for="g in existing" :key="g.path" class="relative">
              <img :src="g.src" alt="" class="h-24 w-[4.5rem] rounded-lg object-cover" />
              <button
                type="button"
                class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-stone-900 text-sm leading-none text-white"
                aria-label="Hapus foto"
                @click="removeExisting(g.path)"
              >
                &times;
              </button>
            </div>
            <div v-for="(g, i) in added" :key="g.src" class="relative">
              <img :src="g.src" alt="" class="h-24 w-[4.5rem] rounded-lg object-cover" />
              <button
                type="button"
                class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-stone-900 text-sm leading-none text-white"
                aria-label="Batalkan foto"
                @click="removeAdded(i)"
              >
                &times;
              </button>
            </div>
            <label
              v-if="room > 0"
              for="gallery"
              class="flex h-24 w-[4.5rem] cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-rose-200 bg-rose-50/40 text-xs text-rose-500 transition hover:border-rose-400"
            >
              <span class="text-2xl leading-none">+</span>
              Tambah
            </label>
          </div>
          <input id="gallery" type="file" accept="image/*" multiple class="sr-only" @change="onGallery" />
          <InputError :message="galleryError" class="mt-1" />
        </div>

        <!-- Nama & harga -->
        <div>
          <InputLabel for="name" value="Nama produk" />
          <input id="name" v-model="form.name" :class="field" placeholder="Contoh: Princess Tanktop" required />
          <InputError :message="form.errors.name" class="mt-1" />
        </div>

        <div>
          <InputLabel for="price" value="Harga" />
          <div class="relative">
            <span class="absolute left-3 top-1/2 mt-0.5 -translate-y-1/2 text-sm text-stone-400">Rp</span>
            <input
              id="price"
              v-model="form.price"
              type="number"
              min="0"
              step="1000"
              :class="[field, 'pl-10']"
              placeholder="60000"
              required
            />
          </div>
          <InputError :message="form.errors.price" class="mt-1" />
        </div>

        <!-- Kategori -->
        <div>
          <InputLabel for="category" value="Kategori" />
          <input
            id="category"
            v-model="form.category"
            list="kategori-list"
            :class="field"
            placeholder="Tanktop / Rajut / Cardigan / Rok / Celana"
            required
          />
          <datalist id="kategori-list">
            <option v-for="c in categories" :key="c" :value="c" />
          </datalist>
          <div v-if="categories.length" class="mt-2 flex flex-wrap gap-2">
            <button
              v-for="c in categories"
              :key="c"
              type="button"
              class="rounded-full border px-3 py-1 text-xs transition"
              :class="chip(form.category === c)"
              @click="form.category = c"
            >
              {{ c }}
            </button>
          </div>
          <InputError :message="form.errors.category" class="mt-1" />
        </div>

        <!-- Ukuran -->
        <div>
          <InputLabel value="Ukuran tersedia" />
          <div class="mt-2 flex flex-wrap gap-2">
            <button
              v-for="s in sizeOptions"
              :key="s"
              type="button"
              class="min-w-[2.75rem] rounded-lg border px-3 py-2 text-sm transition"
              :class="chip(form.sizes.includes(s))"
              @click="toggleSize(s)"
            >
              {{ s }}
            </button>
          </div>
          <InputError :message="form.errors.sizes" class="mt-1" />
        </div>

        <!-- Warna -->
        <div>
          <InputLabel for="color" value="Warna" />
          <div class="mt-1 flex flex-wrap items-center gap-2 rounded-lg border border-stone-200 bg-white p-2 focus-within:border-rose-400 focus-within:ring-1 focus-within:ring-rose-400">
            <span
              v-for="c in form.colors"
              :key="c"
              class="flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-600"
            >
              {{ c }}
              <button type="button" class="text-rose-400 hover:text-rose-700" :aria-label="`Hapus ${c}`" @click="removeColor(c)">&times;</button>
            </span>
            <input
              id="color"
              v-model="colorInput"
              class="min-w-[8rem] flex-1 border-0 p-1 text-sm focus:outline-none focus:ring-0"
              placeholder="Ketik warna lalu Enter"
              @keydown="onColorKey"
              @blur="addColor"
            />
          </div>
          <InputError :message="form.errors.colors" class="mt-1" />
        </div>

        <!-- Deskripsi -->
        <div>
          <InputLabel for="description" value="Deskripsi (opsional)" />
          <textarea id="description" v-model="form.description" rows="3" :class="field" />
          <InputError :message="form.errors.description" class="mt-1" />
        </div>

        <!-- Tampil -->
        <label class="flex items-center justify-between rounded-xl bg-rose-50/60 px-4 py-3 text-sm">
          <span>
            <span class="font-medium text-stone-800">Tampilkan di katalog</span><br />
            <span class="text-xs text-stone-500">Matikan untuk menyimpan sebagai draft.</span>
          </span>
          <input v-model="form.is_active" type="checkbox" class="h-5 w-5 rounded border-stone-300 text-rose-400 focus:ring-rose-400" />
        </label>

        <!-- Simpan -->
        <div class="sticky bottom-0 -mx-5 -mb-5 flex items-center gap-4 rounded-b-2xl border-t border-rose-100 bg-white/95 px-5 py-3 backdrop-blur">
          <PrimaryButton :disabled="form.processing || busy">
            {{ form.processing ? 'Menyimpan...' : busy ? 'Memproses foto...' : 'Simpan' }}
          </PrimaryButton>
          <Link :href="route('admin.products.index')" class="text-sm text-stone-500 hover:text-rose-500">Batal</Link>
        </div>
      </form>

      <!-- Preview kartu seperti di katalog -->
      <aside class="self-start lg:sticky lg:top-20">
        <p class="mb-2 text-xs uppercase tracking-wider text-stone-400">Tampilan di katalog</p>
        <div class="mx-auto max-w-[220px] rounded-2xl bg-white p-3 ring-1 ring-rose-100">
          <div class="aspect-[3/4] overflow-hidden bg-stone-100">
            <img v-if="preview" :src="preview" alt="" class="h-full w-full object-cover" />
            <div v-else class="flex h-full items-center justify-center text-xs text-stone-400">Belum ada foto</div>
          </div>
          <h3 class="mt-3 truncate text-sm text-stone-800">{{ form.name || 'Nama produk' }}</h3>
          <p class="mt-0.5 text-sm text-stone-500">{{ form.price !== '' ? rupiah(Number(form.price)) : 'Rp 0' }}</p>
        </div>
      </aside>
    </div>
  </AdminLayout>
</template>
