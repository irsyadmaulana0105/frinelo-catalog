// Nama warna (Indonesia/Inggris) -> kode warna untuk bulatan swatch.
// Warna yang tidak dikenal tampil abu-abu muda.
const COLORS = [
  ['dusty pink', '#d4a0ad'], ['baby pink', '#f8c8d8'], ['hot pink', '#f0508c'], ['pink', '#f4a6c0'],
  ['biru muda', '#a9cbe8'], ['hijau muda', '#bfe0c4'], ['off white', '#f3eee4'], ['broken white', '#f3eee4'],
  ['putih', '#ffffff'], ['white', '#ffffff'],
  ['hitam', '#1c1917'], ['black', '#1c1917'],
  ['cream', '#f3e6cf'], ['krem', '#f3e6cf'],
  ['navy', '#1e2a4a'], ['biru', '#5b8bd0'],
  ['abu', '#9ca3af'], ['grey', '#9ca3af'], ['gray', '#9ca3af'],
  ['sage', '#a7b99a'], ['hijau', '#5f9a73'], ['mint', '#bfe8d4'],
  ['mocca', '#a47c64'], ['mocha', '#a47c64'], ['coklat', '#7b5a44'], ['brown', '#7b5a44'],
  ['khaki', '#c8b68a'], ['merah', '#d9344f'], ['maroon', '#7a1f33'],
  ['kuning', '#f2d36b'], ['ungu', '#b9a3d9'], ['lilac', '#c9b6e4'], ['lavender', '#d6c8ee'],
  ['peach', '#f8c9b0'], ['salem', '#f4a58a'],
].sort((a, b) => b[0].length - a[0].length) // kata terpanjang dicocokkan dulu

export function colorHex(name = '') {
  const n = String(name).toLowerCase()
  const hit = COLORS.find(([key]) => n.includes(key))
  return hit ? hit[1] : '#d6d3d1'
}
