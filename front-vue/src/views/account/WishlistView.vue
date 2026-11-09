<template>
  <section class="account-section">
    <h1>علاقه‌مندی‌های من</h1>
    <p class="subtitle">محصولاتی که ذخیره کرده‌اید</p>

    <div class="empty-state">
      <span class="empty-icon">♡</span>
      <h3>لیست علاقه‌مندی‌های شما خالی است</h3>
      <p>محصولات مورد علاقه‌تان را به این لیست اضافه کنید.</p>
      <RouterLink to="/shop" class="shop-link">مشاهده محصولات</RouterLink>
    </div>
  </section>
</template>

<script setup>
import { RouterLink } from 'vue-router'
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
  color: #e45b79;
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