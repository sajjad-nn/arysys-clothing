<template>
  <section class="account-section">
    <h1>آدرس‌های من</h1>
    <p class="subtitle">مدیریت آدرس‌های ارسال سفارش</p>

    <div class="empty-state">
      <span class="empty-icon">⌖</span>
      <h3>هنوز آدرسی ثبت نکرده‌اید</h3>
      <p>آدرس‌های خود را برای ثبت سفارش‌های بعدی مدیریت کنید.</p>
      <button class="add-button" type="button" @click="showMessage">
        افزودن آدرس
      </button>
    </div>
  </section>
</template>

<script setup>
function showMessage() {
  alert('فرم ثبت آدرس را در مرحله بعد به این بخش اضافه می‌کنیم.')
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

.empty-state {
  padding: 45px 15px;
  text-align: center;
  background: #f8f9fb;
  border-radius: 12px;
}

.empty-icon {
  color: #2563eb;
  font-size: 38px;
}

.empty-state h3 {
  color: #303b4b;
  font-size: 16px;
}

.empty-state p {
  color: #9299a5;
  font-size: 13px;
}

.add-button {
  margin-top: 12px;
  padding: 10px 20px;
  border: 0;
  border-radius: 8px;
  background: #2563eb;
  color: white;
  font-family: inherit;
  cursor: pointer;
}
</style>
