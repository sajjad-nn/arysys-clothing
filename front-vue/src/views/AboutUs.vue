
<template>
  <div class="container">
    <div class="content">
      <p>
        Lorem ipsum dolor, sit amet consectetur adipisicing elit.
        Cumque corporis placeat similique et expedita qui illo,
        deserunt iure labore laudantium officiis aliquam minus
        aspernatur, earum aliquid dolorum dignissimos praesentium?
        Cumque.
      </p>
    </div>

    <img src="../asset/images/woman.webp" alt="تصویر معرفی فروشگاه">
  </div>
</template>

<script setup></script>

<style scoped>
.container {
  width: min(1200px, calc(100% - 40px));
  margin: 100px auto 40px;

  display: grid;
  grid-template-columns: repeat(
    auto-fit,
    minmax(min(100%, 380px), 1fr)
  );

  gap: clamp(20px, 4vw, 48px);
  align-items: center;
}

.content {
  padding: 20px;
}

.content p {
  font-size: clamp(15px, 1.2vw, 18px);
  line-height: 2;
  color: #444;
  text-align: justify;
}

.container img {
  display: block;
  width: 100%;
  max-height: 480px;
  object-fit: cover;
  border-radius: 12px;
}

@media (max-width: 600px) {
  .container {
    margin-top: 60px;
  }

  .content {
    padding: 5px;
  }
}
</style>
