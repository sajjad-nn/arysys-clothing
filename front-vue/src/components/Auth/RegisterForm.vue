<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import Swal from 'sweetalert2'

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')

const error = ref('')
const loading = ref(false)

const register = async () => {
  error.value = ''

  if (password.value !== passwordConfirmation.value) {
    error.value = 'رمز عبور و تکرار رمز عبور یکسان نیستند.'
    return
  }

  loading.value = true

  try {
    console.log('VITE_API_URL:', import.meta.env.VITE_API_URL)
    console.log('API BASE URL:', api.defaults.baseURL)
    // دریافت CSRF Cookie
    await api.get('/sanctum/csrf-cookie')

    // ثبت نام
    await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })

    // دریافت کاربر ثبت‌نام‌شده
    const response = await api.get('/api/user')
    const showSuccess = () => {
      Swal.fire({
        icon: 'success',
        title: 'موفق!',
        text: 'ثبت‌نام با موفقیت انجام شد.',
        confirmButtonText: 'باشه',
        timer: 2500,
        timerProgressBar: true,
      })
    }
    showSuccess()
    console.log('ثبت نام موفق:', response.data)

    // انتقال به داشبورد
    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'ثبت نام انجام نشد.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="register-form">
    <div class="form-header">
      <h1>ثبت نام</h1>
      <p>حساب کاربری جدید خود را ایجاد کنید</p>
    </div>

    <form @submit.prevent="register">
      <div class="input-group">
        <label for="name">نام و نام خانوادگی</label>

        <input
          id="name"
          v-model="name"
          type="text"
          placeholder="نام خود را وارد کنید"
          autocomplete="name"
          required
        />
      </div>

      <div class="input-group">
        <label for="email">ایمیل</label>

        <input
          id="email"
          v-model="email"
          type="email"
          placeholder="example@email.com"
          autocomplete="email"
          required
        />
      </div>

      <div class="input-group">
        <label for="password">رمز عبور</label>

        <input
          id="password"
          v-model="password"
          type="password"
          placeholder="حداقل ۸ کاراکتر"
          autocomplete="new-password"
          minlength="8"
          required
        />
      </div>

      <div class="input-group">
        <label for="passwordConfirmation"> تکرار رمز عبور </label>

        <input
          id="passwordConfirmation"
          v-model="passwordConfirmation"
          type="password"
          placeholder="رمز عبور را دوباره وارد کنید"
          autocomplete="new-password"
          required
        />
      </div>

      <p v-if="error" class="error">
        {{ error }}
      </p>

      <button type="submit" class="submit-btn" :disabled="loading">
        {{ loading ? 'در حال ثبت نام...' : 'ایجاد حساب' }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.register-form {
  width: 100%;
}

.form-header {
  margin-bottom: 25px;
}

.form-header h1 {
  margin: 0 0 10px;
  color: white;
  font-size: 34px;
}

.form-header p {
  margin: 0;
  color: #8994b2;
  font-size: 14px;
}

form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.input-group label {
  color: #cbd3e8;
  font-size: 14px;
}

.input-group input {
  width: 100%;
  height: 50px;
  padding: 0 16px;
  border: 1px solid #293451;
  border-radius: 12px;
  outline: none;
  background: #11192d;
  color: white;
  font-size: 14px;
  transition: 0.25s;
}

.input-group input::placeholder {
  color: #66718c;
}

.input-group input:focus {
  border-color: #5275ff;
  box-shadow: 0 0 0 3px rgba(82, 117, 255, 0.12);
}

.error {
  margin: 0;
  color: #ff7272;
  font-size: 13px;
}

.submit-btn {
  width: 100%;
  height: 52px;
  margin-top: 4px;
  border: none;
  border-radius: 12px;
  background: linear-gradient(135deg, #5275ff, #3458e8);
  color: white;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(52, 88, 232, 0.3);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
