import axios from 'axios'

const envUrl = (import.meta.env.VITE_API_URL || '').trim().replace(/\/+$/, '')

export const API_BASE =
  envUrl || `${window.location.protocol}//${window.location.hostname}:8000`

const api = axios.create({
  baseURL: API_BASE,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

export const logout = () => {
  return api.post('/api/logout')
}

export default api