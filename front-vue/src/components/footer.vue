
<template>
  <footer class="footer">
    <div class="footer-container">

      <!-- معرفی برند -->
      <div class="footer-about">
        <h2 class="footer-logo">
          ARYASYS <span>SHOP</span>
        </h2>

        <p>
          آریاسیـس وب، ارائه‌دهنده خدمات طراحی سایت
          با طراحی مدرن، تجربه کاربری مناسب و کیفیت حرفه‌ای.
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
            <svg
              viewBox="0 0 24 24"
              class="social-icon"
              fill="currentColor"
            >
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
      <p>
        © تمامی حقوق این سایت محفوظ است.
      </p>

      <p class="footer-credit">
        طراحی شده با ❤️ توسط ARYASYS WEB
      </p>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  direction: rtl;
  background: #171717;
  color: #e5e5e5;
  padding: 65px 8% 0;
  margin-top: 80px;
  font-family: inherit;
}

.footer-container {
  display: grid;
  grid-template-columns: 1.3fr 1fr 1fr 1.2fr;
  gap: 40px;
  max-width: 1400px;
  margin: 0 auto;
  padding-bottom: 50px;
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
  font-size: 27px;
  font-weight: 800;
  margin: 0 0 20px;
  letter-spacing: 1px;
}

.footer-logo span {
  color: #b5a078;
}

.footer-about p {
  color: #aaa;
  font-size: 14px;
  line-height: 2.2;
  margin: 0;
}

.footer-column h3 {
  color: #fff;
  font-size: 17px;
  margin: 5px 0 25px;
  font-weight: 600;
}

.footer-column ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-column li {
  margin-bottom: 16px;
}

.footer-column li a {
  color: #aaa;
  font-size: 14px;
  text-decoration: none;
  transition: color 0.25s ease;
}

.footer-column li a:hover {
  color: #b5a078;
}

.footer-contact p {
  color: #aaa;
  font-size: 13px;
  line-height: 2;
  margin: 0 0 14px;
  overflow-wrap: anywhere;
}

.footer-contact p > span {
  color: #fff;
  margin-left: 5px;
}

.social-links {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 15px;
  margin-top: 22px;
}

.social-links a {
  display: inline-flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  color: #aaa;
  font-size: 14px;
  text-decoration: none;
  transition: color 0.25s ease;
}

.social-links a:hover {
  color: #b5a078;
}

.social-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.footer-bottom {
  border-top: 1px solid #333;
  max-width: 1400px;
  margin: 0 auto;
  padding: 22px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  color: #999;
  font-size: 13px;
}

.footer-bottom p {
  margin: 0;
  line-height: 1.8;
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
    padding: 40px 22px 0;
    margin-top: 50px;
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
  }

  .footer-column h3 {
    font-size: 15px;
    margin: 0 0 18px;
  }

  .footer-column li {
    margin-bottom: 13px;
  }

  .footer-column li a {
    font-size: 12px;
  }

  .footer-contact {
    grid-column: 1 / -1;
    border-top: 1px solid #333;
    padding-top: 25px;
  }

  .footer-contact p {
    font-size: 13px;
  }

  .social-links {
    flex-direction: row;
    align-items: center;
    gap: 22px;
    margin-top: 18px;
  }

  .social-links a {
    font-size: 13px;
  }

  .social-icon {
    width: 19px;
    height: 19px;
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
