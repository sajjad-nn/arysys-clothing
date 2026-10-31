<template>
  <section class="about-section" dir="rtl">
    <div class="about-container">
      <!-- سمت چپ: کاروسل سه‌بعدی تصاویر -->
      <div class="about-visual">
        <div class="team-carousel">
          <div class="team-ring">
            <article
              v-for="(member, index) in teamMembers"
              :key="member.name"
              class="team-item"
              :style="{ '--rot': index }"
            >
              <img :src="member.image" :alt="member.name" loading="lazy" />

              <div class="member-overlay">
                <span>{{ member.name }}</span>
              </div>
            </article>
          </div>
        </div>
      </div>

      <!-- سمت راست: متن درباره ما -->
      <div class="about-content">
        <div class="section-label">
          <span></span>
          <p>درباره ما</p>
          <span></span>
        </div>

        <h2>
          تجربه‌ای متفاوت از
          <span>خرید آنلاین لباس</span>
        </h2>

        <p class="about-description">
          در آریسیس تلاش می‌کنیم مجموعه‌ای از محصولات باکیفیت و متنوع را در کنار تجربه‌ای آسان و
          لذت‌بخش از خرید آنلاین برای شما فراهم کنیم. هدف ما این است که انتخاب و خرید لباس، ساده‌تر
          و مطمئن‌تر از همیشه باشد. رضایت و اعتماد شما، مهم‌ترین انگیزه ما برای بهتر شدن است.
        </p>

        <div class="about-buttons">
          <RouterLink to="/about" class="about-btn primary-btn">
            <span>بیشتر درباره ما</span>
            <span class="btn-arrow">←</span>
          </RouterLink>

          <RouterLink to="/contact" class="about-btn secondary-btn">
            <span>تماس با ما</span>
            <span class="phone-icon">☎</span>
          </RouterLink>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { RouterLink } from 'vue-router'

// تصاویر موقت از تصاویر موجود پروژه
import womanImage from '../asset/images/woman.webp'
import menImage from '../asset/images/men.webp'
import modernImage from '../asset/images/modern.webp'

const teamMembers = [
  {
    name: 'عضو اول',
    image: womanImage,
  },
  {
    name: 'عضو دوم',
    image: menImage,
  },
  {
    name: 'عضو سوم',
    image: modernImage,
  },
  {
    name: 'عضو چهارم',
    image: womanImage,
  },
  {
    name: 'عضو پنجم',
    image: menImage,
  },
  {
    name: 'عضو ششم',
    image: modernImage,
  },
  {
    name: 'عضو هفتم',
    image: womanImage,
  },
  {
    name: 'عضو هشتم',
    image: menImage,
  },
]
</script>

<style scoped>
/* بخش اصلی */

.about-section {
  width: 100%;
  padding: 70px 0;
  color: #17140f;
  font-family: inherit;
  direction: rtl;
}

.about-container {
  width: 82%;
  max-width: 1360px;
  margin: 0 auto;

  display: grid;
  direction: ltr;
  grid-template-columns: 1fr 1fr;
  align-items: stretch;
  gap: 65px;
}

/* کاروسل سمت چپ */

.about-visual {
  min-width: 0;
  min-height: 390px;

  display: flex;
  align-items: center;
  justify-content: center;

  overflow: hidden;
}

.team-carousel {
  width: 100%;
  height: 390px;

  display: flex;
  align-items: center;
  justify-content: center;

  perspective: 1000px;
  overflow: hidden;
}

.team-ring {
  --card-width: 145px;
  --card-height: 185px;
  --radius: 220px;

  position: relative;
  width: var(--card-width);
  height: var(--card-height);

  transform-style: preserve-3d;
  animation: teamRotate 22s linear infinite;
}

.team-ring:hover {
  animation-play-state: paused;
}

.team-item {
  position: absolute;
  top: 0;
  left: 0;

  width: var(--card-width);
  height: var(--card-height);

  overflow: hidden;
  border-radius: 15px;

  background: #f4f4f4;
  border: 2px solid rgba(199, 166, 106, 0.7);

  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.16);

  transform: rotateY(calc(var(--rot) * 45deg)) translateZ(var(--radius));

  backface-visibility: visible;
}

.team-item img {
  width: 100%;
  height: 100%;

  display: block;
  object-fit: cover;

  transition: transform 0.5s ease;
}

.team-item:hover img {
  transform: scale(1.1);
}

.member-overlay {
  position: absolute;
  bottom: 0;
  right: 0;

  width: 100%;
  padding: 12px 5px;

  text-align: center;
  color: #fff;
  font-size: 12px;
  font-weight: 700;

  background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
}

/* انیمیشن چرخش */

@keyframes teamRotate {
  from {
    transform: perspective(1000px) rotateY(0deg);
  }

  to {
    transform: perspective(1000px) rotateY(360deg);
  }
}

/* متن درباره ما */

.about-content {
  direction: rtl;

  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;

  min-width: 0;
}

.section-label {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 25px;
}

.section-label span {
  width: 45px;
  height: 2px;

  background: linear-gradient(90deg, #e0c48c, #c7a66a);

  border-radius: 5px;
}

.section-label p {
  margin: 0;
  color: #8b7657;
  font-size: 15px;
  font-weight: 700;
}

.about-content h2 {
  margin: 0 0 22px;

  font-size: clamp(25px, 2.5vw, 36px);
  line-height: 1.8;
  font-weight: 800;
  letter-spacing: -0.7px;
}

.about-content h2 span {
  color: #a68a5b;
}

.about-description {
  max-width: 620px;
  margin: 0;

  color: #77736d;
  font-size: 15px;
  line-height: 2.5;
  text-align: justify;
}

/* دکمه ها */

.about-buttons {
  display: flex;
  align-items: center;
  flex-wrap: wrap;

  gap: 14px;
  margin-top: 35px;
}

.about-btn {
  min-width: 190px;
  padding: 13px 22px;

  border-radius: 40px;

  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 12px;

  text-decoration: none;
  font-size: 13px;
  font-weight: 700;

  transition: all 0.3s ease;
}

.primary-btn {
  background: #17140f;
  color: #fff;
  border: 1px solid #c7a66a;
}

.primary-btn:hover {
  background: #8b7657;
  transform: translateY(-3px);
  box-shadow: 0 8px 18px rgba(139, 118, 87, 0.2);
}

.secondary-btn {
  background: transparent;
  color: #a68a5b;
  border: 1px solid #c7a66a;
}

.secondary-btn:hover {
  background: #c7a66a;
  color: #fff;
  transform: translateY(-3px);
}

.btn-arrow,
.phone-icon {
  font-size: 18px;
  transition: transform 0.3s ease;
}

.primary-btn:hover .btn-arrow {
  transform: translateX(-4px);
}

.secondary-btn:hover .phone-icon {
  transform: rotate(-12deg);
}

/* تبلت */

@media (max-width: 1024px) {
  .about-container {
    width: 90%;
    gap: 30px;
  }

  .team-ring {
    --card-width: 120px;
    --card-height: 155px;
    --radius: 180px;
  }

  .about-content h2 {
    font-size: 27px;
  }

  .about-description {
    font-size: 13px;
  }

  .about-btn {
    min-width: 155px;
  }
}

/* موبایل */

@media (max-width: 768px) {
  .about-section {
    padding: 45px 0;
  }

  .about-container {
    width: 92%;
    grid-template-columns: 1fr;
    gap: 15px;
  }

  /* کاروسل بالا، متن پایین */

  .about-visual {
    grid-row: 1;
    min-height: 300px;
  }

  .about-content {
    grid-row: 2;
  }

  .team-carousel {
    height: 300px;
  }

  .team-ring {
    --card-width: 100px;
    --card-height: 130px;
    --radius: 145px;
  }

  .member-overlay {
    font-size: 10px;
    padding: 8px 3px;
  }

  .section-label {
    margin-bottom: 15px;
  }

  .about-content h2 {
    font-size: 24px;
    margin-bottom: 15px;
  }

  .about-description {
    font-size: 13px;
    line-height: 2.3;
  }

  .about-buttons {
    width: 100%;
    margin-top: 25px;
    gap: 10px;
  }

  .about-btn {
    flex: 1;
    min-width: 0;
    padding: 12px 10px;
    font-size: 11px;
  }
}

/* موبایل کوچک */

@media (max-width: 380px) {
  .about-buttons {
    flex-direction: column;
  }

  .about-btn {
    width: 100%;
  }
}

/* رعایت تنظیم کاهش حرکت سیستم */

@media (prefers-reduced-motion: reduce) {
  .team-ring {
    animation: none;
  }
}
</style>
