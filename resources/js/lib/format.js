export const rupiah = (n) =>
  new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  })
    .format(n)
    .replace(/\u00a0/g, ' ') // "Rp 189.000"

// Produk dianggap "Baru" jika dibuat dalam 14 hari terakhir
export const isNew = (product, days = 14) => {
  if (!product?.created_at) return false
  return (Date.now() - new Date(product.created_at).getTime()) / 86400000 <= days
}

export const waLink = (number, text = '') =>
  `https://wa.me/${number}${text ? `?text=${encodeURIComponent(text)}` : ''}`
