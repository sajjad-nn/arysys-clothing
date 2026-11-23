import { reactive } from 'vue'
import api, { logout } from './api'

const auth = reactive({
  user: null,
  isLoggedIn: false,
  loading: false,
  checked: false,
})

let checkPromise = null

const setUser = (user) => {
  auth.user = user || null
  auth.isLoggedIn = Boolean(user)
  auth.checked = true
}

const clearAuth = () => {
  auth.user = null
  auth.isLoggedIn = false
  auth.checked = false
  checkPromise = null
}

const checkAuth = async (force = false) => {
  if (auth.checked && !force) {
    return auth.user
  }

  if (checkPromise) {
    return checkPromise
  }

  auth.loading = true

  checkPromise = api
    .get('/api/user')
    .then((response) => {
      setUser(response.data)
      return response.data
    })
    .catch(() => {
      clearAuth()
      return null
    })
    .finally(() => {
      auth.loading = false
      checkPromise = null
    })

  return checkPromise
}

const handleAuthSuccess = async (userData = null) => {
  if (userData) {
    setUser(userData)
  } else {
    await checkAuth(true)
  }
}

const logoutUser = async () => {
  try {
    await logout() // مستقیماً به /api/logout در api.js درخواست می‌زند
  } catch (error) {
    console.error('Logout request failed:', error)
  } finally {
    clearAuth()
  }
}

export {
  auth,
  setUser,
  clearAuth,
  checkAuth,
  handleAuthSuccess,
  logoutUser,
}