<template>
  <footer class="footer">
    <div class="footer-wrap">

      <!-- مزیت‌های خرید -->
      <ul class="trust-strip">
        <li
          v-for="(item, index) in trustItems"
          :key="item.title"
          class="trust-item"
          :style="{ '--i': index }"
        >
          <span class="trust-icon" aria-hidden="true">
            <component :is="item.icon" :size="22" :stroke-width="1.7" />
          </span>

          <div class="trust-text">
            <strong>{{ item.title }}</strong>
            <span>{{ item.text }}</span>
          </div>
        </li>
      </ul>

      <div class="footer-container">

        <!-- معرفی برند -->
        <div class="footer-about">
          <h2 class="footer-logo">ARYASYS <span>SHOP</span></h2>

          <p>
            آریاسیس، فروشگاه آنلاین پوشاک با کالکشن‌های به‌روز، پارچه و دوخت باکیفیت و تجربه
            خریدی ساده و مطمئن. استایل مورد علاقه‌ات را پیدا کن و راحت سفارش بده.
          </p>
        </div>

        <!-- لینک‌های سریع -->
        <nav class="footer-column" aria-labelledby="footer-quick-title">
          <h3 id="footer-quick-title">دسترسی سریع</h3>

          <ul>
            <li v-for="link in quickLinks" :key="link.to">
              <RouterLink :to="link.to">{{ link.label }}</RouterLink>
            </li>
          </ul>
        </nav>

        <!-- خدمات مشتریان -->
        <nav class="footer-column" aria-labelledby="footer-service-title">
          <h3 id="footer-service-title">خدمات مشتریان</h3>

          <ul>
            <li v-for="link in serviceLinks" :key="link.to">
              <RouterLink :to="link.to">{{ link.label }}</RouterLink>
            </li>
          </ul>
        </nav>

        <!-- تماس و شبکه‌های اجتماعی -->
        <div class="footer-column footer-contact">
          <h3>ارتباط با ما</h3>

          <div class="contact-row">
            <span>تلفن:</span>
            <a :href="`tel:${contact.phoneHref}`" dir="ltr">{{ contact.phone }}</a>
          </div>

          <div class="contact-row">
            <span>ایمیل:</span>
            <a :href="`mailto:${contact.email}`" dir="ltr">{{ contact.email }}</a>
          </div>

          <div class="contact-row">
            <span>ساعات پاسخگویی:</span>
            <b>{{ contact.hours }}</b>
          </div>

          <div class="social-links">
            <a
              :href="socials.instagram"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="اینستاگرام آریاسیس"
            >
              <svg
                viewBox="0 0 24 24"
                class="social-icon"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
              >
                <rect x="3" y="3" width="18" height="18" rx="5" />
                <circle cx="12" cy="12" r="4" />
                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
              </svg>
              <span>اینستاگرام</span>
            </a>

            <a
              :href="socials.telegram"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="تلگرام آریاسیس"
            >
              <svg
                viewBox="0 0 24 24"
                class="social-icon"
                fill="currentColor"
                aria-hidden="true"
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
    </div>

    <!-- پایین فوتر -->
    <div class="footer-wrap footer-bottom">
      <p>© {{ year }} آریاسیس؛ تمامی حقوق این فروشگاه محفوظ است.</p>

      <p class="footer-credit">
        طراحی شده با <span aria-label="عشق" role="img">❤️</span> توسط ARYASYS WEB
      </p>
    </div>
  </footer>
</template>

<script setup>
import { Truck, ShieldCheck, RotateCcw, Lock } from 'lucide-vue-next'

/* ---------------------------------------------------------
   همه متن‌ها و لینک‌های قابل ویرایش اینجا هستند
   --------------------------------------------------------- */

// اطلاعات تماس (مقادیر نمونه؛ با اطلاعات واقعی جایگزین کن)
const contact = {
  phone: '۰۲۱-۱۲۳۴۵۶۷۸',
  phoneHref: '+982112345678', // برای لینک تماس، با کد کشور و بدون خط تیره
  email: 'info@aryasyshop.com',
  hours: 'شنبه تا پنجشنبه، ۹ تا ۱۸',
}

// آدرس صفحه‌های اجتماعی (نمونه)
const socials = {
  instagram: 'https://instagram.com/',
  telegram: 'https://t.me/',
}

// مزیت‌های خرید (متن‌ها را مطابق شرایط واقعی فروشگاه تنظیم کن)
const trustItems = [
  { icon: Truck, title: 'ارسال به سراسر ایران', text: 'سریع و ایمن' },
  { icon: ShieldCheck, title: 'کیفیت و اصالت کالا', text: 'انتخاب‌شده با دقت' },
  { icon: RotateCcw, title: 'بازگشت و تعویض', text: 'طبق شرایط بازگشت' },
  { icon: Lock, title: 'پرداخت امن', text: 'از طریق درگاه بانکی' },
]

const quickLinks = [
  { to: '/', label: 'خانه' },
  { to: '/shop', label: 'فروشگاه' },
  { to: '/articles', label: 'مقالات' },
  { to: '/aboutus', label: 'درباره ما' },
  { to: '/contact', label: 'تماس با ما' },
  { to: 'dashboard/wishlist', label: 'علاقه‌مندی‌ها' },
]

// این مسیرها را باید در router تعریف کنی (یا آدرس‌ها را عوض کنی)
const serviceLinks = [
  { to: '/guide', label: 'راهنمای خرید' },
  { to: '/size-guide', label: 'راهنمای انتخاب سایز' },
  { to: '/order-tracking', label: 'پیگیری سفارش' },
  { to: '/returns', label: 'بازگشت و تعویض کالا' },
  { to: '/faq', label: 'سوالات متداول' },
  { to: '/terms', label: 'قوانین و مقررات' },
]

// سال شمسی برای کپی‌رایت
const year = new Intl.DateTimeFormat('fa-IR', { year: 'numeric' }).format(new Date())
</script>

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
  padding: 64px 0 0;
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

/* هم‌عرض با هدر و بقیه صفحه‌ها */
.footer-wrap {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: var(--site-max, 1360px);
  margin-inline: auto;
  padding-inline: var(--gutter, 28px);
}

/* =========================================================
   مزیت‌های خرید: کارت‌های شیشه‌ای با درخشش
   ========================================================= */

.trust-strip {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  list-style: none;
  margin: 0 0 52px;
  padding: 0;
}

.trust-item {
  position: relative;
  overflow: hidden;
  isolation: isolate;
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
  padding: 16px 18px;
  border: 1px solid rgba(199, 166, 106, 0.22);
  border-radius: 18px;
  background: linear-gradient(145deg, rgba(255, 255, 255, 0.07), rgba(255, 255, 255, 0.015));
  box-shadow:
    inset 0 1px 0 rgba(255, 255, 255, 0.08),
    0 12px 28px -18px rgba(0, 0, 0, 0.8);
  transition:
    transform 0.3s ease,
    border-color 0.3s ease,
    box-shadow 0.3s ease;
}

/* نوار نوری که هر چند ثانیه از روی کارت رد می‌شود */
.trust-item::after {
  content: '';
  position: absolute;
  top: -60%;
  bottom: -60%;
  left: 0;
  width: 38%;
  background: linear-gradient(100deg, transparent, rgba(224, 196, 140, 0.26), transparent);
  transform: translateX(-130%) skewX(-20deg);
  animation: trust-sheen 7s ease-in-out infinite;
  animation-delay: calc(var(--i, 0) * 1.4s);
  pointer-events: none;
}

.trust-icon {
  position: relative;
  display: grid;
  place-items: center;
  flex-shrink: 0;
  width: 46px;
  height: 46px;
  border: 1px solid rgba(224, 196, 140, 0.42);
  border-radius: 14px;
  background: linear-gradient(145deg, rgba(224, 196, 140, 0.3), rgba(199, 166, 106, 0.07));
  color: var(--gold-light);
  animation: trust-glow 3.6s ease-in-out infinite;
  animation-delay: calc(var(--i, 0) * 0.5s);
}

.trust-icon svg {
  filter: drop-shadow(0 0 6px rgba(224, 196, 140, 0.6));
}

.trust-text {
  display: flex;
  flex-direction: column;
  gap: 3px;
  min-width: 0;
}

.trust-text strong {
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  line-height: 1.5;
}

.trust-text span {
  color: #9a9286;
  font-size: 12px;
  line-height: 1.6;
}

@media (hover: hover) {
  .trust-item:hover {
    transform: translateY(-4px);
    border-color: rgba(224, 196, 140, 0.5);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.12),
      0 18px 34px -16px rgba(199, 166, 106, 0.35);
  }
}

@keyframes trust-sheen {
  0% {
    transform: translateX(-130%) skewX(-20deg);
  }
  24%,
  100% {
    transform: translateX(380%) skewX(-20deg);
  }
}

@keyframes trust-glow {
  0%,
  100% {
    box-shadow: 0 0 10px -2px rgba(224, 196, 140, 0.22);
  }
  50% {
    box-shadow: 0 0 22px 0 rgba(224, 196, 140, 0.55);
  }
}

/* =========================================================
   ستون‌ها
   ========================================================= */

.footer-container {
  display: grid;
  grid-template-columns: 1.3fr 1fr 1fr 1.3fr;
  gap: 40px;
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
  margin: 5px 0 26px;
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
  margin-bottom: 8px;
}

.footer-column li a {
  display: inline-flex;
  align-items: center;
  gap: 0;
  padding: 4px 0;
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
  margin-bottom: 20px;
}

.contact-row {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  gap: 4px 8px;
  margin-bottom: 10px;
  color: #a8a196;
  font-size: 13px;
  line-height: 2;
  overflow-wrap: anywhere;
}

.contact-row > span {
  color: var(--gold-light);
}

.contact-row a {
  color: #d6d0c4;
  text-decoration: none;
  transition: color 0.25s ease;
}

.contact-row a:hover {
  color: var(--gold-light);
}

.contact-row b {
  font-weight: 500;
  color: #d6d0c4;
}

.social-links {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  margin-top: 18px;
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
.contact-row a:focus-visible,
.social-links a:focus-visible {
  outline: 2px solid var(--gold);
  outline-offset: 3px;
}

/* ---------- پایین فوتر ---------- */

.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  padding-block: 24px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
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
  .trust-item,
  .footer-column li a,
  .footer-column li a::before,
  .contact-row a,
  .social-links a {
    transition: none;
  }

  /* انیمیشن درخشش خاموش شود */
  .trust-item::after,
  .trust-icon {
    animation: none;
  }

  .trust-item::after {
    display: none;
  }
}

/* =========================================================
   تا عرض ۱۰۲۳ پیکسل منوی پایین موبایل (lg:hidden) دیده می‌شود.
   فوتر باید به اندازه آن جا بدهد تا آخرین متن زیرش نرود
   و پس‌زمینه تیره فوتر هم زیر منو ادامه پیدا کند.
   ========================================================= */
@media (max-width: 1023px) {
  .footer {
    padding-bottom: calc(7rem + env(safe-area-inset-bottom, 0px));
  }
}

/* =========================================================
   تبلت و پایین‌تر: معرفی بالا، لینک‌ها دو ستونه، تماس تمام‌عرض
   ========================================================= */
@media (max-width: 992px) {
  .footer {
    padding-top: 52px;
  }

  .trust-strip {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 44px;
  }

  .footer-container {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 34px 28px;
    padding-bottom: 40px;
  }

  .footer-about,
  .footer-contact {
    grid-column: 1 / -1;
  }

  .footer-about p {
    max-width: 520px;
  }
}

/* =========================================================
   موبایل
   ========================================================= */
@media (max-width: 576px) {
  .footer {
    padding-top: 40px;
  }

  /* کارت‌های کوچک‌تر و فشرده‌تر */
  .trust-strip {
    gap: 10px;
    margin-bottom: 32px;
  }

  .trust-item {
    gap: 9px;
    padding: 11px 10px;
    border-radius: 14px;
  }

  .trust-icon {
    width: 34px;
    height: 34px;
    border-radius: 11px;
  }

  .trust-icon svg {
    width: 17px;
    height: 17px;
  }

  .trust-text {
    gap: 1px;
  }

  .trust-text strong {
    font-size: 11.5px;
    line-height: 1.55;
  }

  .trust-text span {
    font-size: 10px;
    line-height: 1.5;
  }

  .footer-container {
    column-gap: 20px;
    row-gap: 30px;
    padding-bottom: 30px;
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
    margin: 0 0 16px;
    padding-bottom: 12px;
  }

  .footer-column li {
    margin-bottom: 6px;
  }

  .footer-column li a {
    padding: 5px 0;
    font-size: 13px;
  }

  .footer-contact {
    padding: 20px 18px;
    border-radius: 20px;
  }

  .contact-row {
    font-size: 13px;
  }

  .social-links {
    margin-top: 16px;
  }

  .social-links a {
    flex: 1 1 130px;
    justify-content: center;
    height: 42px;
    padding: 0 14px;
    font-size: 12.5px;
  }

  .social-icon {
    width: 18px;
    height: 18px;
  }

  .footer-bottom {
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 6px;
    padding-block: 20px;
    font-size: 11.5px;
  }
}

/* موبایل خیلی کوچک */
@media (max-width: 340px) {
  .trust-strip {
    grid-template-columns: 1fr;
  }

  .footer-container {
    grid-template-columns: 1fr;
  }
}
</style>