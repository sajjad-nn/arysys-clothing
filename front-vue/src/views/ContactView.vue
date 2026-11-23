<template>
  <section class="contact-page" dir="rtl">

    <!-- بنر بالای صفحه -->
    <header class="contact-hero">
      <div class="contact-wrap hero-inner">
        <span class="eyebrow">CONTACT US</span>
        <h1>تماس با <span>آریاسیس</span></h1>
        <p>
          برای انتخاب سایز، پیگیری سفارش یا هر سوال دیگری پیام بگذارید؛
          در ساعات کاری پاسخگوی شما هستیم.
        </p>
      </div>
    </header>

    <div class="contact-wrap contact-body">

      <!-- کارت‌های راه‌های ارتباطی -->
      <ul class="info-grid">
        <li v-for="item in infoCards" :key="item.label">
          <component
            :is="item.href ? 'a' : 'div'"
            :href="item.href"
            class="info-card"
            :class="{ 'is-link': item.href }"
          >
            <span class="info-icon" aria-hidden="true">
              <component :is="item.icon" :size="22" :stroke-width="1.7" />
            </span>

            <span class="info-text">
              <small>{{ item.label }}</small>
              <strong :dir="item.dir || 'rtl'">{{ item.value }}</strong>
            </span>
          </component>
        </li>
      </ul>

      <div class="contact-main">

        <!-- فرم تماس -->
        <div class="panel form-panel">
          <div class="panel-head">
            <h2>پیام بگذارید</h2>
            <p>فرم را پر کنید تا در اولین فرصت با شما تماس بگیریم.</p>
          </div>

          <!-- پیام موفقیت -->
          <div v-if="sent" class="success-box" role="status">
            <span class="success-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12.5l4.5 4.5L19 7.5" />
              </svg>
            </span>
            <h3>پیام شما ارسال شد</h3>
            <p>از اینکه با ما در ارتباط بودید ممنونیم. به‌زودی پاسخ می‌دهیم.</p>
            <button type="button" class="btn-outline-form" @click="sent = false">
              ارسال پیام جدید
            </button>
          </div>

          <!-- فرم -->
          <form v-else class="contact-form" novalidate @submit.prevent="submitForm">

            <div v-if="serverError" class="server-error" role="alert">
              {{ serverError }}
            </div>

            <div class="form-grid">
              <div class="field" :class="{ invalid: fieldError('name') }">
                <label for="c-name">نام و نام خانوادگی <b>*</b></label>
                <input
                  id="c-name"
                  v-model="form.name"
                  type="text"
                  autocomplete="name"
                  maxlength="80"
                  :aria-invalid="!!fieldError('name')"
                  aria-describedby="c-name-err"
                  @blur="touched.name = true"
                  @input="clearServer('name')"
                />
                <p v-if="fieldError('name')" id="c-name-err" class="field-error">
                  {{ fieldError('name') }}
                </p>
              </div>

              <div class="field" :class="{ invalid: fieldError('phone') }">
                <label for="c-phone">شماره موبایل <b>*</b></label>
                <input
                  id="c-phone"
                  v-model="form.phone"
                  type="tel"
                  inputmode="tel"
                  autocomplete="tel"
                  dir="ltr"
                  placeholder="09123456789"
                  maxlength="14"
                  :aria-invalid="!!fieldError('phone')"
                  aria-describedby="c-phone-err"
                  @blur="touched.phone = true"
                  @input="clearServer('phone')"
                />
                <p v-if="fieldError('phone')" id="c-phone-err" class="field-error">
                  {{ fieldError('phone') }}
                </p>
              </div>

              <div class="field" :class="{ invalid: fieldError('email') }">
                <label for="c-email">ایمیل <span class="optional">(اختیاری)</span></label>
                <input
                  id="c-email"
                  v-model="form.email"
                  type="email"
                  inputmode="email"
                  autocomplete="email"
                  dir="ltr"
                  placeholder="name@example.com"
                  maxlength="120"
                  :aria-invalid="!!fieldError('email')"
                  aria-describedby="c-email-err"
                  @blur="touched.email = true"
                  @input="clearServer('email')"
                />
                <p v-if="fieldError('email')" id="c-email-err" class="field-error">
                  {{ fieldError('email') }}
                </p>
              </div>

              <div class="field" :class="{ invalid: fieldError('subject') }">
                <label for="c-subject">موضوع <b>*</b></label>
                <select
                  id="c-subject"
                  v-model="form.subject"
                  :aria-invalid="!!fieldError('subject')"
                  aria-describedby="c-subject-err"
                  @blur="touched.subject = true"
                  @change="clearServer('subject')"
                >
                  <option value="" disabled>انتخاب کنید</option>
                  <option v-for="s in subjects" :key="s.value" :value="s.value">
                    {{ s.label }}
                  </option>
                </select>
                <p v-if="fieldError('subject')" id="c-subject-err" class="field-error">
                  {{ fieldError('subject') }}
                </p>
              </div>

              <div class="field field-full" :class="{ invalid: fieldError('message') }">
                <label for="c-message">پیام شما <b>*</b></label>
                <textarea
                  id="c-message"
                  v-model="form.message"
                  rows="5"
                  maxlength="1000"
                  :aria-invalid="!!fieldError('message')"
                  aria-describedby="c-message-err"
                  @blur="touched.message = true"
                  @input="clearServer('message')"
                ></textarea>

                <div class="message-foot">
                  <p v-if="fieldError('message')" id="c-message-err" class="field-error">
                    {{ fieldError('message') }}
                  </p>
                  <span class="counter">
                    {{ form.message.length.toLocaleString('fa-IR') }} / ۱٬۰۰۰
                  </span>
                </div>
              </div>
            </div>

            <!-- فیلد مخفی برای تشخیص ربات‌ها -->
            <div class="hp-field" aria-hidden="true">
              <label for="c-website">وب‌سایت</label>
              <input id="c-website" v-model="form.website" type="text" tabindex="-1" autocomplete="off" />
            </div>

            <button type="submit" class="submit-btn" :disabled="sending">
              <Send :size="18" class="send-icon" />
              <span>{{ sending ? 'در حال ارسال...' : 'ارسال پیام' }}</span>
            </button>
          </form>
        </div>

        <!-- نقشه و آدرس -->
        <div class="panel map-panel">
          <div class="panel-head">
            <h2>آدرس فروشگاه</h2>
            <p>{{ contact.address }}</p>
          </div>

          <div class="map-frame">
            <iframe
              :src="mapSrc"
              title="نقشه موقعیت آریاسیس"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>

          <!-- دکمه‌های نقشه مستقیم زیر نقشه و هم‌تراز با دکمه ارسال فرم -->
          <div class="map-actions">
            <a :href="directionsUrl" target="_blank" rel="noopener noreferrer" class="map-btn primary">
              <MapPin :size="17" />
              مسیریابی
            </a>

            <a :href="mapViewUrl" target="_blank" rel="noopener noreferrer" class="map-btn">
              مشاهده در نقشه بزرگ
            </a>
          </div>
        </div>
      </div>

      <!-- نوار پایین: لینک‌های کمکی + شبکه‌های اجتماعی -->
      <div class="help-strip">
        <div class="help-group">
          <p>قبل از ارسال پیام، شاید پاسخ سوالت اینجا باشد:</p>

          <div class="help-links">
            <RouterLink to="/faq">سوالات متداول</RouterLink>
            <RouterLink to="/size-guide">راهنمای انتخاب سایز</RouterLink>
            <RouterLink to="/order-tracking">پیگیری سفارش</RouterLink>
          </div>
        </div>

        <div class="social-row">
          <span>شبکه‌های اجتماعی:</span>

          <a :href="socials.instagram" target="_blank" rel="noopener noreferrer" aria-label="اینستاگرام آریاسیس">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
              <rect x="3" y="3" width="18" height="18" rx="5" />
              <circle cx="12" cy="12" r="4" />
              <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
            </svg>
          </a>

          <a :href="socials.telegram" target="_blank" rel="noopener noreferrer" aria-label="تلگرام آریاسیس">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path
                d="M21.5 3.5 18.3 20c-.24 1.17-.87 1.46-1.76.91l-4.86-3.58-2.35 2.26c-.26.26-.48.48-.98.48l.35-4.94 8.99-8.12c.39-.35-.09-.54-.61-.19L6 13.79l-4.7-1.47c-1.02-.32-1.04-1.02.21-1.51L19.9 3.07c.84-.31 1.58.2 1.6.43Z"
              />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { Phone, Mail, MapPin, Clock, Send } from 'lucide-vue-next'
import { API_BASE } from '@/services/api'

/* ---------------------------------------------------------
   اطلاعات قابل ویرایش (مقادیر نمونه؛ با اطلاعات واقعی جایگزین کن)
   پیشنهاد: همین مقادیر را با فوتر یکی نگه دار
   --------------------------------------------------------- */

const contact = {
  phone: '۰۲۱-۱۲۳۴۵۶۷۸',
  phoneHref: '+982112345678', // با کد کشور و بدون خط تیره
  email: 'info@aryasyshop.com',
  address: 'تهران، خیابان نمونه، پلاک ۱۲، طبقه همکف',
  hours: 'شنبه تا پنجشنبه، ۹ تا ۱۸',
}

// مختصات روی نقشه (نمونه: مرکز تهران). عرض و طول جغرافیایی مکان واقعی را بگذار.
const location = { lat: 35.6892, lng: 51.389 }

const socials = {
  instagram: 'https://instagram.com/',
  telegram: 'https://t.me/',
}

const subjects = [
  { value: 'order', label: 'سوال درباره سفارش' },
  { value: 'size', label: 'مشاوره انتخاب سایز' },
  { value: 'return', label: 'بازگشت یا تعویض کالا' },
  { value: 'feedback', label: 'پیشنهاد و انتقاد' },
  { value: 'other', label: 'سایر موارد' },
]

const infoCards = [
  { icon: Phone, label: 'تلفن تماس', value: contact.phone, href: `tel:${contact.phoneHref}`, dir: 'ltr' },
  { icon: Mail, label: 'ایمیل', value: contact.email, href: `mailto:${contact.email}`, dir: 'ltr' },
  { icon: MapPin, label: 'آدرس', value: contact.address },
  { icon: Clock, label: 'ساعات پاسخگویی', value: contact.hours },
]

/* ---------- نقشه (OpenStreetMap، بدون نیاز به کلید) ---------- */

const mapSrc = computed(() => {
  const { lat, lng } = location
  const bbox = [lng - 0.008, lat - 0.005, lng + 0.008, lat + 0.005].join(',')

  return `https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${lat},${lng}`
})

const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${location.lat},${location.lng}`
const mapViewUrl = `https://www.openstreetmap.org/?mlat=${location.lat}&mlon=${location.lng}#map=16/${location.lat}/${location.lng}`

/* ---------- فرم ---------- */

const form = reactive({
  name: '',
  phone: '',
  email: '',
  subject: '',
  message: '',
  website: '', // honeypot
})

const touched = reactive({})
const serverErrors = reactive({})
const submitted = ref(false)
const sending = ref(false)
const sent = ref(false)
const serverError = ref('')

// تبدیل ارقام فارسی و عربی به انگلیسی
const normalizeDigits = (value) =>
  String(value)
    .replace(/[۰-۹]/g, (d) => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
    .replace(/[٠-٩]/g, (d) => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))

const cleanPhone = () => normalizeDigits(form.phone).replace(/[\s-]/g, '')

const clientErrors = computed(() => {
  const e = {}

  if (form.name.trim().length < 3) {
    e.name = 'نام و نام خانوادگی را وارد کنید.'
  }

  if (!/^09\d{9}$/.test(cleanPhone())) {
    e.phone = 'شماره موبایل معتبر نیست (مثال: ۰۹۱۲۱۲۳۴۵۶۷).'
  }

  const email = form.email.trim()
  if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    e.email = 'ایمیل واردشده معتبر نیست.'
  }

  if (!form.subject) {
    e.subject = 'موضوع پیام را انتخاب کنید.'
  }

  if (form.message.trim().length < 10) {
    e.message = 'پیام باید حداقل ۱۰ کاراکتر باشد.'
  }

  return e
})

// خطا فقط بعد از لمس فیلد یا زدن دکمه ارسال نمایش داده می‌شود
const fieldError = (field) => {
  if (serverErrors[field]) return serverErrors[field]
  return touched[field] || submitted.value ? clientErrors.value[field] || '' : ''
}

const clearServer = (field) => {
  if (serverErrors[field]) delete serverErrors[field]
}

function resetForm() {
  Object.assign(form, {
    name: '',
    phone: '',
    email: '',
    subject: '',
    message: '',
    website: '',
  })

  Object.keys(touched).forEach((key) => delete touched[key])
  submitted.value = false
}

async function submitForm() {
  submitted.value = true
  serverError.value = ''

  if (Object.keys(clientErrors.value).length) {
    // اولین فیلد خطادار فوکوس شود
    const first = Object.keys(clientErrors.value)[0]
    document.getElementById(`c-${first}`)?.focus()
    return
  }

  // ربات‌ها فیلد مخفی را پر می‌کنند؛ بی‌صدا موفق نشان بده
  if (form.website) {
    sent.value = true
    resetForm()
    return
  }

  sending.value = true

  try {
    const response = await fetch(`${API_BASE}/api/contact`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        name: form.name.trim(),
        phone: cleanPhone(),
        email: form.email.trim() || null,
        subject: form.subject,
        message: form.message.trim(),
      }),
    })

    let data = null

    try {
      data = await response.json()
    } catch {
      data = null
    }

    if (!response.ok) {
      // خطاهای اعتبارسنجی Laravel
      if (response.status === 422 && data?.errors) {
        Object.entries(data.errors).forEach(([field, messages]) => {
          serverErrors[field] = Array.isArray(messages) ? messages[0] : String(messages)
        })
      }

      throw new Error(data?.message || 'ارسال پیام انجام نشد. لطفاً دوباره تلاش کنید.')
    }

    sent.value = true
    resetForm()
  } catch (err) {
    serverError.value =
      err instanceof TypeError
        ? 'ارتباط با سرور برقرار نشد. اینترنت خود را بررسی کنید.'
        : err.message
  } finally {
    sending.value = false
  }
}
</script>

<style scoped>
.contact-page {
  --gold: #a38253;
  --gold-light: #e0c48c;
  --ink: #18181b;
  --muted: #71717a;
  --line: #e4e4e7;
  --line-soft: #eee9e0;

  min-height: 100vh;
  background: var(--color-canvas, #fbfbfd);
  color: #1a1a1a;
  overflow-x: clip;
}

/* هم‌عرض با هدر، فوتر و بقیه صفحه‌ها */
.contact-wrap {
  width: 100%;
  max-width: var(--site-max, 1360px);
  margin-inline: auto;
  padding-inline: var(--gutter, 28px);
  box-sizing: border-box;
}

/* =========================================================
   بنر تیره بالا (هم‌خانواده با هیرو و فوتر)
   ========================================================= */

.contact-hero {
  position: relative;
  overflow: hidden;
  padding: calc(var(--header-h, 80px) + 36px) 0 130px;
  background:
    radial-gradient(50% 70% at 90% 0%, rgba(199, 166, 106, 0.18) 0%, transparent 65%),
    radial-gradient(40% 60% at 0% 100%, rgba(199, 166, 106, 0.09) 0%, transparent 70%), #0a0908;
  color: #fff;
  text-align: center;
}

.contact-hero::after {
  content: '';
  position: absolute;
  width: 300px;
  height: 300px;
  bottom: -160px;
  left: 8%;
  border: 1px solid rgba(199, 166, 106, 0.14);
  border-radius: 50%;
  box-shadow: 0 0 0 40px rgba(199, 166, 106, 0.03);
  pointer-events: none;
}

.hero-inner {
  position: relative;
  z-index: 1;
}

.eyebrow {
  display: inline-block;
  margin-bottom: 14px;
  color: #c7a66a;
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: 3.5px;
  direction: ltr;
}

.contact-hero h1 {
  margin: 0 0 14px;
  font-size: clamp(28px, 4vw, 44px);
  font-weight: 800;
  line-height: 1.4;
}

.contact-hero h1 span {
  background: linear-gradient(135deg, #f3deb5 0%, #c7a66a 60%, #b08d52 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.contact-hero p {
  max-width: 540px;
  margin: 0 auto;
  color: #a3a3a3;
  font-size: 15px;
  line-height: 2;
}

/* =========================================================
   بدنه
   ========================================================= */

.contact-body {
  padding-bottom: var(--section-space, 80px);
}

/* کارت‌های ارتباطی روی بنر می‌آیند */
.info-grid {
  position: relative;
  z-index: 2;
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  margin: -84px 0 0;
  padding: 0;
  list-style: none;
}

.info-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  height: 100%;
  padding: 24px 18px;
  box-sizing: border-box;
  border: 1px solid var(--line-soft);
  border-radius: 18px;
  background: #fff;
  box-shadow: 0 10px 30px -14px rgba(0, 0, 0, 0.18);
  color: inherit;
  text-align: center;
  text-decoration: none;
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease,
    border-color 0.3s ease;
}

.info-icon {
  display: grid;
  place-items: center;
  width: 52px;
  height: 52px;
  border: 1px solid #eee5d8;
  border-radius: 16px;
  background: #fbf8f3;
  color: var(--gold);
  transition:
    background 0.3s ease,
    color 0.3s ease;
}

.info-text {
  display: flex;
  flex-direction: column;
  gap: 5px;
  min-width: 0;
}

.info-text small {
  color: var(--muted);
  font-size: 12px;
}

.info-text strong {
  font-size: 14px;
  font-weight: 700;
  line-height: 1.8;
  overflow-wrap: anywhere;
}

@media (hover: hover) {
  .info-card.is-link:hover {
    transform: translateY(-5px);
    border-color: #e3d5bd;
    box-shadow: 0 18px 36px -16px rgba(163, 130, 83, 0.4);
  }

  .info-card.is-link:hover .info-icon {
    background: var(--gold);
    color: #fff;
  }
}

.info-card:focus-visible {
  outline: 2px solid var(--gold);
  outline-offset: 3px;
}

/* ---------- فرم و نقشه ---------- */

.contact-main {
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
  gap: 24px;
  margin-top: 28px;
  align-items: stretch;
}

.panel {
  display: flex;
  flex-direction: column;
  min-width: 0;
  padding: 28px;
  border: 1px solid var(--line-soft);
  border-radius: 22px;
  background: #fff;
  box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
}

.panel-head {
  margin-bottom: 22px;
}

.panel-head h2 {
  margin: 0 0 6px;
  font-size: 19px;
  font-weight: 800;
}

.panel-head p {
  margin: 0;
  color: var(--muted);
  font-size: 13px;
  line-height: 1.9;
}

/* ---------- فرم ---------- */

.contact-form {
  position: relative;
  display: flex;
  flex: 1;
  flex-direction: column;
  gap: 20px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 7px;
  min-width: 0;
}

.field-full {
  grid-column: 1 / -1;
}

.field label {
  color: #52525b;
  font-size: 12.5px;
  font-weight: 600;
}

.field label b {
  color: #dc2626;
}

.optional {
  color: #a1a1aa;
  font-weight: 400;
}

.field input,
.field select,
.field textarea {
  width: 100%;
  box-sizing: border-box;
  padding: 0 14px;
  border: 1px solid var(--line);
  border-radius: 10px;
  background: #fafafa;
  color: #27272a;
  font-family: inherit;
  font-size: 13px;
  outline: none;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease,
    background 0.2s ease;
}

.field input,
.field select {
  height: 46px;
}

.field textarea {
  min-height: 140px;
  padding: 12px 14px;
  line-height: 1.9;
  resize: vertical;
}

.field input::placeholder {
  color: #b4b4bb;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
  border-color: var(--gold);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(163, 130, 83, 0.14);
}

.field.invalid input,
.field.invalid select,
.field.invalid textarea {
  border-color: #dc2626;
  background: #fffafa;
}

.field.invalid input:focus,
.field.invalid select:focus,
.field.invalid textarea:focus {
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
}

.field-error {
  margin: 0;
  color: #dc2626;
  font-size: 12px;
  line-height: 1.7;
}

.message-foot {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}

.counter {
  margin-inline-start: auto;
  color: #a1a1aa;
  font-size: 11.5px;
  white-space: nowrap;
}

.server-error {
  padding: 12px 14px;
  border: 1px solid #fecaca;
  border-radius: 10px;
  background: #fef2f2;
  color: #b91c1c;
  font-size: 13px;
  line-height: 1.8;
}

/* فیلد مخفی ضد ربات */
.hp-field {
  position: absolute;
  width: 1px;
  height: 1px;
  margin: -1px;
  padding: 0;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  clip-path: inset(50%);
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
}

.submit-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  min-height: 50px;
  margin-top: auto; /* دکمه به پایین کارت می‌چسبد، هم‌تراز با دکمه‌های نقشه */
  border: 1px solid var(--ink);
  border-radius: 12px;
  background: var(--ink);
  color: #fff;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition:
    background 0.25s ease,
    border-color 0.25s ease,
    transform 0.25s ease,
    box-shadow 0.25s ease;
}

.send-icon {
  /* آیکون ارسال در جهت راست‌به‌چپ برگردد */
  transform: scaleX(-1);
}

.submit-btn:hover:not(:disabled) {
  background: #27272a;
  border-color: #27272a;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.25);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: wait;
}

/* ---------- پیام موفقیت ---------- */

.success-box {
  display: flex;
  flex: 1;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 30px 10px;
  text-align: center;
}

.success-icon {
  display: grid;
  place-items: center;
  width: 70px;
  height: 70px;
  margin-bottom: 8px;
  border-radius: 50%;
  background: #f0fdf4;
  color: #16a34a;
  box-shadow: 0 0 0 8px rgba(34, 197, 94, 0.08);
}

.success-icon svg {
  width: 34px;
  height: 34px;
}

.success-box h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 800;
}

.success-box p {
  max-width: 320px;
  margin: 0 0 12px;
  color: var(--muted);
  font-size: 13px;
  line-height: 1.9;
}

.btn-outline-form {
  min-height: 44px;
  padding: 0 24px;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
  color: var(--ink);
  font-family: inherit;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition:
    border-color 0.25s ease,
    background 0.25s ease;
}

.btn-outline-form:hover {
  border-color: var(--gold);
  background: #fbf8f3;
}

/* ---------- نقشه ---------- */

.map-frame {
  position: relative;
  flex: 1;
  min-height: 340px;
  overflow: hidden;
  border: 1px solid var(--line-soft);
  border-radius: 16px;
  background: #f4f0e9;
}

.map-frame iframe {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border: 0;
}

.map-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-top: 16px;
}

.map-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 50px;
  padding: 0 14px;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
  color: var(--ink);
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
  transition:
    background 0.25s ease,
    border-color 0.25s ease,
    color 0.25s ease,
    transform 0.25s ease;
}

.map-btn:hover {
  border-color: var(--gold);
  background: #fbf8f3;
  transform: translateY(-2px);
}

.map-btn.primary {
  border-color: var(--ink);
  background: var(--ink);
  color: #fff;
}

.map-btn.primary:hover {
  border-color: #27272a;
  background: #27272a;
}

/* ---------- نوار لینک‌های کمکی و شبکه‌های اجتماعی ---------- */

.help-strip {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 18px 24px;
  margin-top: 28px;
  padding: 20px 24px;
  border: 1px solid #eee5d8;
  border-radius: 18px;
  background: #fbf8f3;
}

.help-group {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px 18px;
}

.help-group p {
  margin: 0;
  color: #52525b;
  font-size: 13.5px;
  font-weight: 600;
}

.help-links {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.help-links a {
  padding: 8px 16px;
  border: 1px solid #eee5d8;
  border-radius: 999px;
  background: #fff;
  color: var(--gold);
  font-size: 12.5px;
  font-weight: 700;
  text-decoration: none;
  transition:
    background 0.25s ease,
    color 0.25s ease,
    border-color 0.25s ease;
}

.help-links a:hover {
  border-color: var(--gold);
  background: var(--gold);
  color: #fff;
}

.social-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-inline-start: 20px;
  border-inline-start: 1px solid #e7decb;
  color: var(--muted);
  font-size: 12.5px;
  font-weight: 600;
}

.social-row a {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  border: 1px solid #eee5d8;
  border-radius: 12px;
  background: #fff;
  color: #52525b;
  transition:
    background 0.25s ease,
    color 0.25s ease,
    border-color 0.25s ease,
    transform 0.25s ease;
}

.social-row a svg {
  width: 19px;
  height: 19px;
}

.social-row a:hover {
  border-color: var(--gold);
  background: var(--gold);
  color: #fff;
  transform: translateY(-2px);
}

.submit-btn:focus-visible,
.map-btn:focus-visible,
.btn-outline-form:focus-visible,
.social-row a:focus-visible,
.help-links a:focus-visible {
  outline: 2px solid var(--gold);
  outline-offset: 3px;
}

@media (prefers-reduced-motion: reduce) {
  .info-card,
  .info-icon,
  .submit-btn,
  .map-btn,
  .social-row a,
  .help-links a,
  .field input,
  .field select,
  .field textarea {
    transition: none;
  }
}

/* =========================================================
   تبلت
   ========================================================= */
@media (max-width: 992px) {
  .info-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .contact-main {
    grid-template-columns: minmax(0, 1fr);
  }

  .map-frame {
    flex: none;
    height: 340px;
    min-height: 0;
  }

  .social-row {
    width: 100%;
    padding-inline-start: 0;
    padding-top: 14px;
    border-inline-start: 0;
    border-top: 1px dashed #e2d8c5;
  }

  .social-row span {
    margin-inline-end: auto;
  }
}

/* =========================================================
   موبایل
   ========================================================= */
@media (max-width: 640px) {
  .contact-hero {
    padding-bottom: 104px;
  }

  .contact-hero p {
    font-size: 13.5px;
    line-height: 1.95;
  }

  .info-grid {
    gap: 10px;
    margin-top: -64px;
  }

  /* کارت‌های کوچک‌تر */
  .info-card {
    gap: 10px;
    padding: 16px 10px;
    border-radius: 16px;
  }

  .info-icon {
    width: 44px;
    height: 44px;
    border-radius: 14px;
  }

  .info-text small {
    font-size: 11px;
  }

  .info-text strong {
    font-size: 12.5px;
    line-height: 1.7;
  }

  .contact-main {
    gap: 16px;
    margin-top: 18px;
  }

  .panel {
    padding: 20px 16px;
    border-radius: 18px;
  }

  .panel-head {
    margin-bottom: 18px;
  }

  .panel-head h2 {
    font-size: 17px;
  }

  .form-grid {
    grid-template-columns: minmax(0, 1fr);
    gap: 14px;
  }

  /* در iOS اگر اندازه فونت فیلد کمتر از ۱۶ باشد، صفحه هنگام فوکوس زوم می‌شود */
  .field input,
  .field select,
  .field textarea {
    font-size: 16px;
  }

  .map-frame {
    height: 280px;
  }

  .map-actions {
    grid-template-columns: 1fr;
  }

  .help-strip {
    margin-top: 18px;
    padding: 16px;
    border-radius: 16px;
  }

  .help-links a {
    padding: 7px 14px;
  }
}

/* موبایل خیلی کوچک */
@media (max-width: 400px) {
  .info-grid {
    grid-template-columns: minmax(0, 1fr);
  }

  /* کارت‌ها افقی و فشرده */
  .info-card {
    flex-direction: row;
    gap: 14px;
    padding: 14px 16px;
    text-align: right;
  }

  .info-text {
    align-items: flex-start;
  }
}
</style>