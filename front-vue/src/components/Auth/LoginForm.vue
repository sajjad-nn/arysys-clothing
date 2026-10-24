<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import Swal from 'sweetalert2'

const router = useRouter()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const loginSuccess = () => {
  Swal.fire({
    icon: 'success',
    title: 'ورود موفق',
    text: 'با موفقیت وارد حساب کاربری شدید.',
    confirmButtonText: 'باشه',
    timer: 2500,
    timerProgressBar: true,
  })
}

const login = async () => {
  error.value = ''
  loading.value = true

  try {
    // دریافت CSRF Cookie
    await api.get('/sanctum/csrf-cookie')

    // ورود
    await api.post('/login', {
      email: email.value,
      password: password.value,
    })

    // دریافت کاربر لاگین‌شده
    const response = await api.get('/api/user')
    loginSuccess()
    console.log('ورود موفق:', response.data)

    // انتقال به داشبورد
    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'ایمیل یا رمز عبور اشتباه است.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-form">
    <div class="form-header">
      <h1>ورود</h1>
      <p>وارد حساب کاربری خود شوید</p>
    </div>

    <form @submit.prevent="login">
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
          placeholder="رمز عبور"
          autocomplete="current-password"
          required
        />
      </div>

      <p v-if="error" class="error">
        {{ error }}
      </p>

      <button type="submit" class="submit-btn" :disabled="loading">
        {{ loading ? 'در حال ورود...' : 'ورود به حساب' }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.login-form {
  width: 100%;
}

.form-header {
  margin-bottom: 32px;
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
  gap: 20px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 9px;
}

.input-group label {
  color: #cbd3e8;
  font-size: 14px;
}

.input-group input {
  width: 100%;
  height: 53px;
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
  height: 53px;
  margin-top: 5px;
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
