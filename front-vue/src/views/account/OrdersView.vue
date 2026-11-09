<template>
  <section class="account-section">
    <h1>سفارش‌های من</h1>
    <p class="subtitle">لیست سفارش‌های ثبت‌شده شما</p>

    <div class="empty-state">
      <span class="empty-icon">▤</span>
      <h3>هنوز سفارشی ثبت نکرده‌اید</h3>
      <p>بعد از ثبت سفارش، اطلاعات آن در این قسمت نمایش داده می‌شود.</p>
      <RouterLink to="/shop" class="shop-link">مشاهده فروشگاه</RouterLink>
    </div>
  </section>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.account-section h1 {
  margin: 0 0 8px;
  font-size: 23px;
  color: #202938;
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
  font-size: 35px;
  color: #9aa5b5;
}

.empty-state h3 {
  color: #303b4b;
  font-size: 16px;
}

.empty-state p {
  color: #9299a5;
  font-size: 13px;
}

.shop-link {
  display: inline-block;
  margin-top: 12px;
  padding: 10px 20px;
  border-radius: 8px;
  background: #2563eb;
  color: white;
  text-decoration: none;
  font-size: 13px;
}
</style>