import { reactive } from 'vue'
import api from './api'

const auth = reactive({
  user: null,
  isLoggedIn: false,
  loading: false,
  checked: false,
})

let checkPromise = null

// ذخیره اطلاعات کاربر بعد از ورود یا ثبت‌نام
const setUser = (user) => {
  auth.user = user
  auth.isLoggedIn = !!user
  auth.checked = true
}

// بررسی نشست کاربر از طریق Laravel Sanctum
const checkAuth = async () => {
  if (auth.checked) return auth.user

  if (checkPromise) return checkPromise

  auth.loading = true

  checkPromise = api.get('/api/user')
    .then((response) => {
      setUser(response.data)
      return response.data
    })
    .catch(() => {
      setUser(null)
      return null
    })
    .finally(() => {
      auth.loading = false
      checkPromise = null
    })

  return checkPromise
}

// خروج از حساب
const clearAuth = () => {
  setUser(null)
}

export {
  auth,
  setUser,
  checkAuth,
  clearAuth,
}