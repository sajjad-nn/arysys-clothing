<template>
  <section class="account-section">
    <h1>اطلاعات حساب</h1>
    <p class="subtitle">مشاهده و مدیریت اطلاعات شخصی</p>

    <div class="profile-form">
      <div class="form-group">
        <label for="name">نام و نام خانوادگی</label>
        <input id="name" type="text" placeholder="نام خود را وارد کنید" />
      </div>

      <div class="form-group">
        <label for="email">ایمیل</label>
        <input id="email" type="email" placeholder="ایمیل خود را وارد کنید" />
      </div>

      <div class="form-group">
        <label for="phone">شماره موبایل</label>
        <input id="phone" type="tel" placeholder="شماره موبایل" />
      </div>

      <button type="button" class="save-button" @click="showMessage">
        ذخیره تغییرات
      </button>
    </div>
  </section>
</template>

<script setup>
function showMessage() {
  alert('اتصال فرم به اطلاعات کاربر و API را در مرحله بعد انجام می‌دهیم.')
}
</script>

<style scoped>
.account-section h1 {
  margin: 0 0 8px;
  color: #202938;
  font-size: 23px;
}

.subtitle {
  margin: 0 0 25px;
  color: #9299a5;
  font-size: 14px;
}

.profile-form {
  max-width: 550px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 18px;
}

.form-group label {
  color: #424c5b;
  font-size: 13px;
}

.form-group input {
  width: 100%;
  padding: 12px;
  border: 1px solid #e1e6ed;
  border-radius: 8px;
  outline: none;
  box-sizing: border-box;
  font-family: inherit;
  font-size: 14px;
}

.form-group input:focus {
  border-color: #2563eb;
}

.save-button {
  padding: 11px 25px;
  border: 0;
  border-radius: 8px;
  background: #2563eb;
  color: white;
  font-family: inherit;
  cursor: pointer;
}
</style>