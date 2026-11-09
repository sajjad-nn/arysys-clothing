<template>
  <footer class="footer">
    <div class="footer-container">
      <!-- معرفی برند -->
      <div class="footer-about">
        <h2 class="footer-logo">ARYASYS <span>SHOP</span></h2>

        <p>
          آریاسیـس وب، ارائه‌دهنده خدمات طراحی سایت با طراحی مدرن، تجربه کاربری مناسب و کیفیت
          حرفه‌ای.
        </p>
      </div>

      <!-- لینک‌های سریع -->
      <div class="footer-column">
        <h3>دسترسی سریع</h3>

        <ul>
          <li>
            <RouterLink to="/">خانه</RouterLink>
          </li>
          <li>
            <RouterLink to="/shop">فروشگاه</RouterLink>
          </li>
          <li>
            <RouterLink to="/articles">مقالات</RouterLink>
          </li>
          <li>
            <RouterLink to="/about">درباره ما</RouterLink>
          </li>
          <li>
            <RouterLink to="/contact">تماس با ما</RouterLink>
          </li>
        </ul>
      </div>

      <!-- خدمات مشتریان -->
      <div class="footer-column">
        <h3>خدمات مشتریان</h3>

        <ul>
          <li><a href="#">راهنمای خرید</a></li>
          <li><a href="#">پیگیری سفارش</a></li>
          <li><a href="#">شرایط بازگشت کالا</a></li>
          <li><a href="#">قوانین و مقررات</a></li>
          <li><a href="#">سوالات متداول</a></li>
        </ul>
      </div>

      <!-- تماس و شبکه‌های اجتماعی -->
      <div class="footer-column footer-contact">
        <h3>ارتباط با ما</h3>

        <p>
          <span>تلفن:</span>
          ۰۲۱-۱۲۳۴۵۶۷۸
        </p>

        <p>
          <span>ایمیل:</span>
          info@aryasyshop.com
        </p>

        <p>
          <span>ساعات پاسخگویی:</span>
          شنبه تا پنجشنبه، ۹ تا ۱۸
        </p>

        <div class="social-links">
          <a
            href="https://instagram.com/"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Instagram"
          >
            <svg
              viewBox="0 0 24 24"
              class="social-icon"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
            >
              <rect x="3" y="3" width="18" height="18" rx="5" />
              <circle cx="12" cy="12" r="4" />
              <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
            </svg>
            <span>اینستاگرام</span>
          </a>

          <a
            href="https://telegram.org/"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Telegram"
          >
            <svg viewBox="0 0 24 24" class="social-icon" fill="currentColor">
              <path
                d="M21.5 3.5 18.3 20c-.24 1.17-.87 1.46-1.76.91l-4.86-3.58-2.35 2.26c-.26.26-.48.48-.98.48l.35-4.94 8.99-8.12c.39-.35-.09-.54-.61-.19L6 13.79l-4.7-1.47c-1.02-.32-1.04-1.02.21-1.51L19.9 3.07c.84-.31 1.58.2 1.6.43Z"
              />
            </svg>
            <span>تلگرام</span>
          </a>
        </div>
      </div>
    </div>

    <!-- پایین فوتر -->
    <div class="footer-bottom">
      <p>© تمامی حقوق این سایت محفوظ است.</p>

      <p class="footer-credit">طراحی شده با ❤️ توسط ARYASYS WEB</p>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  --gold: #c7a66a;
  --gold-light: #e0c48c;

  position: relative;
  overflow: hidden;
  direction: rtl;
  background:
    radial-gradient(50% 70% at 90% 0%, rgba(199, 166, 106, 0.16) 0%, transparent 65%),
    radial-gradient(40% 60% at 0% 100%, rgba(199, 166, 106, 0.08) 0%, transparent 70%), #0a0908;
  color: #e5e5e5;
  padding: 72px 8% 0;
  /* margin-top: 0px; */
  /* border-radius: 36px 36px 0 0; */
  font-family: inherit;
}

/* خط طلایی نازک لبه بالا */
.footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 10%;
  right: 10%;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(224, 196, 140, 0.7), transparent);
  pointer-events: none;
}

/* حلقه تزئینی هماهنگ با هیرو */
.footer::after {
  content: '';
  position: absolute;
  width: 300px;
  height: 300px;
  bottom: -150px;
  right: 6%;
  border: 1px solid rgba(199, 166, 106, 0.12);
  border-radius: 50%;
  box-shadow: 0 0 0 40px rgba(199, 166, 106, 0.03);
  pointer-events: none;
}

.footer-container,
.footer-bottom {
  position: relative;
  z-index: 1;
}

.footer-container {
  display: grid;
  grid-template-columns: 1.3fr 1fr 1fr 1.3fr;
  gap: 40px;
  max-width: 1400px;
  margin: 0 auto;
  padding-bottom: 54px;
}

.footer-about,
.footer-column {
  min-width: 0;
  text-align: right;
}

.footer-logo {
  direction: ltr;
  text-align: right;
  color: #fff;
  font-size: 28px;
  font-weight: 800;
  margin: 0 0 20px;
  letter-spacing: 1px;
}

.footer-logo span {
  background: linear-gradient(135deg, var(--gold-light), var(--gold));
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  color: var(--gold);
}

.footer-about p {
  color: #a8a196;
  font-size: 14px;
  line-height: 2.2;
  margin: 0;
  max-width: 340px;
}

/* عنوان ستون‌ها */

.footer-column h3 {
  position: relative;
  color: #fff;
  font-size: 17px;
  margin: 5px 0 28px;
  padding-bottom: 14px;
  font-weight: 700;
}

.footer-column h3::after {
  content: '';
  position: absolute;
  right: 0;
  bottom: 0;
  width: 28px;
  height: 2px;
  border-radius: 2px;
  background: linear-gradient(90deg, var(--gold-light), var(--gold));
}

/* لینک‌ها */

.footer-column ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-column li {
  margin-bottom: 15px;
}

.footer-column li a {
  display: inline-flex;
  align-items: center;
  gap: 0;
  color: #a8a196;
  font-size: 14px;
  text-decoration: none;
  transition:
    color 0.25s ease,
    gap 0.25s ease;
}

.footer-column li a::before {
  content: '';
  width: 0;
  height: 1px;
  background: var(--gold);
  transition: width 0.25s ease;
}

.footer-column li a:hover {
  gap: 8px;
  color: var(--gold-light);
}

.footer-column li a:hover::before {
  width: 12px;
}

/* کارت تماس شیشه‌ای */

.footer-contact {
  padding: 24px 24px 22px;
  border: 1px solid rgba(255, 255, 255, 0.09);
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.04);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
}

.footer-contact h3 {
  margin-bottom: 22px;
}

.footer-contact p {
  color: #a8a196;
  font-size: 13px;
  line-height: 2;
  margin: 0 0 10px;
  overflow-wrap: anywhere;
}

.footer-contact p > span {
  color: var(--gold-light);
  margin-left: 5px;
}

.social-links {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  margin-top: 20px;
}

.social-links a {
  display: inline-flex;
  flex-direction: row;
  align-items: center;
  gap: 9px;
  height: 42px;
  padding: 0 16px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 15px;
  background: rgba(255, 255, 255, 0.05);
  color: #d6d0c4;
  font-size: 13px;
  text-decoration: none;
  transition:
    color 0.25s ease,
    background 0.25s ease,
    border-color 0.25s ease,
    transform 0.25s ease;
}

.social-links a:hover {
  transform: translateY(-3px);
  color: #17130b;
  border-color: transparent;
  background: linear-gradient(135deg, var(--gold-light), var(--gold));
}

.social-icon {
  width: 19px;
  height: 19px;
  flex-shrink: 0;
}

.footer-column li a:focus-visible,
.social-links a:focus-visible {
  outline: 2px solid var(--gold);
  outline-offset: 3px;
}

/* پایین فوتر */

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  color: #8d867b;
  font-size: 13px;
}

.footer-bottom p {
  margin: 0;
  line-height: 1.8;
}

.footer-credit {
  color: #a8a196;
}

@media (prefers-reduced-motion: reduce) {
  .footer-column li a,
  .footer-column li a::before,
  .social-links a {
    transition: none;
  }
}

/* تبلت: دو ستون */
@media (min-width: 577px) and (max-width: 992px) {
  .footer {
    padding-right: 5%;
    padding-left: 5%;
  }

  .footer-container {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 35px;
  }

  .footer-about,
  .footer-contact {
    grid-column: auto;
  }
}

/* موبایل: معرفی بالا، لینک‌ها دو ستونه، تماس تمام‌عرض */
@media (max-width: 576px) {
  .footer {
    padding: 44px 20px 0;
    /* margin-top: 50px; */
    border-radius: 28px 28px 0 0;
  }

  .footer-container {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: 22px;
    row-gap: 30px;
    padding-bottom: 30px;
  }

  .footer-about {
    grid-column: 1 / -1;
  }

  .footer-logo {
    font-size: 25px;
    margin-bottom: 14px;
  }

  .footer-about p {
    font-size: 13px;
    line-height: 2;
    max-width: none;
  }

  .footer-column h3 {
    font-size: 15px;
    margin: 0 0 18px;
    padding-bottom: 12px;
  }

  .footer-column li {
    margin-bottom: 13px;
  }

  .footer-column li a {
    font-size: 12px;
  }

  .footer-contact {
    grid-column: 1 / -1;
    padding: 20px 18px;
    border-radius: 20px;
  }

  .footer-contact p {
    font-size: 13px;
  }

  .social-links {
    margin-top: 16px;
  }

  .social-links a {
    height: 40px;
    padding: 0 14px;
    font-size: 12px;
  }

  .social-icon {
    width: 18px;
    height: 18px;
  }

  .footer-bottom {
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 8px;
    padding: 20px 0;
    font-size: 11px;
  }
}

</style>
