
import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

const CART_STORAGE_KEY = 'arysys_cart'

export const useCartStore = defineStore('cart', () => {
  // خواندن سبد خرید ذخیره‌شده
  const loadCart = () => {
    try {
      const savedCart = localStorage.getItem(CART_STORAGE_KEY)
      return savedCart ? JSON.parse(savedCart) : []
    } catch (error) {
      console.error('خطا در خواندن سبد خرید:', error)
      return []
    }
  }

  const items = ref(loadCart())
  const isCartOpen = ref(false)

  // ذخیره خودکار تغییرات سبد خرید
  watch(
    items,
    (newItems) => {
      localStorage.setItem(
        CART_STORAGE_KEY,
        JSON.stringify(newItems)
      )
    },
    { deep: true }
  )

  // تعداد کل محصولات
  const totalItems = computed(() =>
    items.value.reduce(
      (total, item) => total + item.quantity,
      0
    )
  )

  // مبلغ کل با احتساب تخفیف
  const subtotal = computed(() =>
    items.value.reduce(
      (total, item) =>
        total + Number(item.final_price ?? item.price ?? 0) * item.quantity,
      0
    )
  )

  const openCart = () => {
    isCartOpen.value = true
  }

  const closeCart = () => {
    isCartOpen.value = false
  }

  // افزودن محصول
  const addToCart = (product) => {
    const productId = product.product_id ?? product.id

    const variantId = product.variant_id ?? null

    const existingItem = items.value.find(
      (item) =>
        (item.product_id ?? item.id) === productId &&
        (item.variant_id ?? null) === variantId &&
        item.size === (product.size || '') &&
        item.color === (product.color || '')
    )

    if (existingItem) {
      existingItem.quantity += Number(product.quantity || 1)
    } else {
      items.value.push({
        ...product,
        id: productId,
        product_id: productId,
        variant_id: variantId,
        size: product.size || '',
        color: product.color || '',
        price: Number(product.price ?? 0),
        final_price: Number(
          product.final_price ?? product.price ?? 0
        ),
        quantity: Number(product.quantity || 1),
      })
    }

    
  }

  // تغییر تعداد
  const updateQuantity = (id, size, color, quantity) => {
    const item = items.value.find(
      (item) =>
        item.id === id &&
        item.size === size &&
        item.color === color
    )

    if (item) {
      const newQuantity = Math.max(1, Number(quantity))

      if (item.stock != null && item.stock >= 0) {
        item.quantity = Math.min(newQuantity, item.stock)
      } else {
        item.quantity = newQuantity
      }
    }
  }

  // حذف محصول
  const removeFromCart = (id, size, color) => {
    items.value = items.value.filter(
      (item) =>
        !(
          item.id === id &&
          item.size === size &&
          item.color === color
        )
    )
  }

  // خالی کردن سبد خرید
  const clearCart = () => {
    items.value = []
  }

  return {
    items,
    isCartOpen,
    totalItems,
    subtotal,
    openCart,
    closeCart,
    addToCart,
    updateQuantity,
    removeFromCart,
    clearCart,
  }
})
