<template>
  <section class="about-intro" dir="rtl" aria-label="معرفی آریاسیس">
    <div class="about-wrap">
      <div class="about-card">
        <div class="media-box">
          <img
            src="../asset/images/woman.webp"
            alt="معرفی استایل و پوشاک آریاسیس"
            loading="lazy"
          />
        </div>

        <div class="content">
          <span class="eyebrow">ARYSYS ATELIER</span>
          <h2>روایت استایل و <span>کیفیت ماندگار</span></h2>
          <p>
            در آریاسیس، هر قطعه با دقت در انتخاب پارچه، دوخت تمیز و فرم استاندارد طراحی می‌شود
            تا در کنار راحتی روزمره، ظاهری آراسته و متمایز برای شما بسازد. تمرکز ما بر
            جزئیات ظریف، رنگ‌های ماندگار و تجربه‌ای ساده و مطمئن از انتخاب تا تحویل است.
          </p>

          <div class="actions">
            <RouterLink to="/shop" class="primary-link">
              <span>مشاهده کلکسیون</span>
              <span class="arrow" aria-hidden="true">←</span>
            </RouterLink>
            <RouterLink to="/contact" class="ghost-link">
              مشاوره و تماس
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.about-intro {
  --gold: #a38253;
  --ink: #18181b;
  --muted: #6b6b73;
  --line-soft: #eee9e0;

  width: 100%;
  /* فاصله امن از بالای صفحه برای هدرهای ثابت یا شناور در دسکتاپ و موبایل */
  padding: calc(var(--header-h, 80px) + 28px) 0 44px;
  box-sizing: border-box;
  overflow-x: clip;
}

.about-wrap {
  width: 100%;
  max-width: var(--site-max, 1360px);
  margin-inline: auto;
  padding-inline: var(--gutter, 28px);
  box-sizing: border-box;
}

.about-card {
  display: grid;
  grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
  gap: clamp(24px, 4vw, 52px);
  align-items: center;
  padding: clamp(24px, 3.5vw, 44px);
  background: #fff;
  border: 1px solid var(--line-soft);
  border-radius: 24px;
  box-shadow: 0 10px 30px -18px rgba(0, 0, 0, 0.14);
  box-sizing: border-box;
}

/* در دسکتاپ متن سمت راست و تصویر سمت چپ قرار بگیرد */
.content {
  order: 1;
  min-width: 0;
}

.media-box {
  order: 2;
  position: relative;
  width: 100%;
  min-width: 0;
  overflow: hidden;
  border-radius: 18px;
  border: 1px solid var(--line-soft);
  background: #f6f3ed;
  aspect-ratio: 4 / 3;
}

.media-box img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;
  transition: transform 0.45s ease;
}

.eyebrow {
  display: inline-block;
  margin-bottom: 10px;
  color: var(--gold);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 3px;
  direction: ltr;
}

.content h2 {
  margin: 0 0 14px;
  color: var(--ink);
  font-size: clamp(22px, 2.8vw, 34px);
  font-weight: 800;
  line-height: 1.45;
  overflow-wrap: break-word;
}

.content h2 span {
  background: linear-gradient(135deg, #c7a66a 0%, #9b7846 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.content p {
  margin: 0;
  color: var(--muted);
  font-size: clamp(14px, 1.15vw, 16px);
  line-height: 2;
  text-align: right;
  overflow-wrap: break-word;
}

.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 26px;
}

.primary-link,
.ghost-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 48px;
  padding: 0 22px;
  border-radius: 12px;
  font-size: 13.5px;
  font-weight: 700;
  text-decoration: none;
  box-sizing: border-box;
  transition:
    background 0.25s ease,
    border-color 0.25s ease,
    color 0.25s ease,
    transform 0.25s ease;
}

.primary-link {
  background: var(--ink);
  border: 1px solid var(--ink);
  color: #fff;
}

.ghost-link {
  background: #fbf8f3;
  border: 1px solid #e6dcc8;
  color: var(--ink);
}

@media (hover: hover) {
  .primary-link:hover {
    background: #27272a;
    border-color: #27272a;
    transform: translateY(-2px);
  }

  .primary-link:hover .arrow {
    transform: translateX(-4px);
  }

  .ghost-link:hover {
    border-color: var(--gold);
    background: #f6f0e4;
    transform: translateY(-2px);
  }

  .media-box:hover img {
    transform: scale(1.03);
  }
}

.arrow {
  transition: transform 0.25s ease;
}

.primary-link:focus-visible,
.ghost-link:focus-visible {
  outline: 2px solid var(--gold);
  outline-offset: 3px;
}

/* =========================================================
   تبلت
   ========================================================= */
@media (max-width: 960px) {
  .about-intro {
    padding: calc(var(--header-h, 80px) + 20px) 0 32px;
  }

  .about-card {
    grid-template-columns: minmax(0, 1fr);
    gap: 22px;
    padding: 22px;
    border-radius: 20px;
  }

  /* در تبلت و موبایل تصویر بالا و متن زیر آن قرار می‌گیرد */
  .media-box {
    order: 1;
    aspect-ratio: 16 / 10;
  }

  .content {
    order: 2;
  }
}

/* =========================================================
   موبایل
   ========================================================= */
@media (max-width: 640px) {
  .about-intro {
    padding: calc(var(--header-h, 72px) + 16px) 0 24px;
    margin-top: 15px;
  }

  .about-wrap {
    padding-inline: 16px;
  }

  .about-card {
    gap: 18px;
    padding: 16px;
    border-radius: 18px;
  }

  .media-box {
    aspect-ratio: 4 / 3;
    border-radius: 14px;
  }

  .eyebrow {
    font-size: 10.5px;
    letter-spacing: 2.5px;
    margin-bottom: 8px;
  }

  .content h2 {
    font-size: 20px;
    margin-bottom: 10px;
  }

  .content p {
    font-size: 13.5px;
    line-height: 1.95;
  }

  .actions {
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
  }

  .primary-link,
  .ghost-link {
    width: 100%;
    min-height: 46px;
    font-size: 13px;
  }
}
</style>