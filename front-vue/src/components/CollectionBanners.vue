
<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const containerRef = ref(null)
const womenRef = ref(null)
const menRef = ref(null)

const GAP = 14
const RADIUS = 28
const BEND = 20
const STEP_WIDTH = 120
const STEP_RATIO = 0.5

function roundedPath(points) {
  const n = points.length
  let d = ''

  points.forEach(([x, y, r], i) => {
    const [px, py] = points[(i - 1 + n) % n]
    const [nx, ny] = points[(i + 1) % n]

    const inX = Math.sign(px - x)
    const inY = Math.sign(py - y)
    const outX = Math.sign(nx - x)
    const outY = Math.sign(ny - y)

    const startX = x + inX * r
    const startY = y + inY * r
    const endX = x + outX * r
    const endY = y + outY * r

    const cross = -inX * outY + inY * outX
    const sweep = cross > 0 ? 1 : 0

    d += `${i === 0 ? 'M' : 'L'}${startX} ${startY}A${r} ${r} 0 0 ${sweep} ${endX} ${endY}`
  })

  return `${d}Z`
}

function updateShapes() {
  const box = containerRef.value
  const women = womenRef.value
  const men = menRef.value

  if (!box || !women || !men) return

  if (window.matchMedia('(max-width: 768px)').matches) {
    women.style.clipPath = ''
    men.style.clipPath = ''
    return
  }

  const W = box.clientWidth
  const H = box.clientHeight

  const stepW = Math.min(STEP_WIDTH, W * 0.12)
  const half = GAP / 2

  const xUpper = W / 2 - stepW / 2
  const xLower = W / 2 + stepW / 2
  const stepY = Math.round(H * STEP_RATIO)
  const outer = BEND + GAP

  const left = roundedPath([
    [0, 0, RADIUS],
    [xUpper - half, 0, RADIUS],
    [xUpper - half, stepY, outer],
    [xLower - half, stepY, BEND],
    [xLower - half, H, RADIUS],
    [0, H, RADIUS],
  ])

  const right = roundedPath([
    [xUpper + half, 0, RADIUS],
    [W, 0, RADIUS],
    [W, H, RADIUS],
    [xLower + half, H, RADIUS],
    [xLower + half, stepY - GAP, outer],
    [xUpper + half, stepY - GAP, BEND],
  ])

  men.style.clipPath = `path('${left}')`
  women.style.clipPath = `path('${right}')`
}

let observer = null

onMounted(() => {
  updateShapes()

  observer = new ResizeObserver(updateShapes)
  observer.observe(containerRef.value)
})

onUnmounted(() => {
  observer?.disconnect()
})
</script>

<template>
  <section class="collection-section" dir="rtl">
    <div ref="containerRef" class="collection-banners">

      <!-- Women Banner -->
      <article ref="womenRef" class="collection-banner women-banner">

        <div class="banner-image"></div>

        <div class="banner-content">
          <span class="en-title">NEW COLLECTION</span>
          <h2>کالکشن زنانه</h2>
          <p>زیبایی در سادگی، خاص بودن در انتخاب</p>

          <RouterLink to="/shop" class="banner-btn">
            مشاهده محصولات
            <span class="btn-arrow" aria-hidden="true">←</span>
          </RouterLink>
        </div>

      </article>

      <!-- Men Banner -->
      <article ref="menRef" class="collection-banner men-banner">

        <div class="banner-image"></div>

        <div class="banner-content">
          <span class="en-title">NEW COLLECTION</span>
          <h2>کالکشن مردانه</h2>
          <p>استایلی متفاوت برای انتخابی متفاوت</p>

          <RouterLink to="/shop" class="banner-btn">
            مشاهده محصولات
            <span class="btn-arrow" aria-hidden="true">←</span>
          </RouterLink>
        </div>

      </article>

    </div>
  </section>
</template>

<style scoped>

/* =================================
   Collection Section
================================= */

.collection-section {
  width: 100%;
  margin: 110px auto;
  padding: 65px 0;

  background: #f7f5f0;

  box-sizing: border-box;
  overflow: hidden;
}

/* =================================
   Banners Container
================================= */

.collection-banners {
  position: relative;

  width: 82%;
  max-width: 1360px;

  height: clamp(400px, 33vw, 470px);

  margin: 0 auto;
}

/* =================================
   Common Banner Styles
================================= */

.collection-banner {
  position: absolute;
  inset: 0;

  display: flex;
  align-items: center;

  padding: 40px 55px;

  box-sizing: border-box;
  overflow: hidden;

  color: #fff;
}

/* =================================
   Image Layer
================================= */

.banner-image {
  position: absolute;
  inset: 0;
  z-index: 0;

    background-size: contain;
  background-position: center;
  background-repeat: no-repeat;

  transform: scale(1);
  transition: transform 0.7s ease;
  will-change: transform;
}

/* Women Image */

.women-banner .banner-image {
  background-image:
    linear-gradient(
      90deg,
      rgba(20, 20, 20, 0.08) 0%,
      rgba(20, 20, 20, 0.25) 45%,
      rgba(20, 20, 20, 0.62) 100%
    ),
    url('../asset/images/women-banner.webp');
}

/* Men Image */

.men-banner .banner-image {
  background-image:
    linear-gradient(
      90deg,
      rgba(20, 20, 20, 0.58) 0%,
      rgba(20, 20, 20, 0.22) 55%,
      rgba(20, 20, 20, 0.05) 100%
    ),
    url('../asset/images/men-banner.webp');
}

/* Hover Zoom */

@media (hover: hover) {
  .collection-banner:hover .banner-image {
    transform: scale(1.07);
  }
}

/* =================================
   Banner Positions
================================= */

.women-banner {
  justify-content: flex-start;
}

.men-banner {
  justify-content: flex-end;
}

/* =================================
   Banner Content
================================= */

.banner-content {
  position: relative;
  z-index: 2;

  width: 100%;
  max-width: 390px;

  display: flex;
  flex-direction: column;

  gap: 13px;

  direction: rtl;
}

/* Women Text */

.women-banner .banner-content {
  align-items: flex-start;
  text-align: right;
}

/* Men Text */

.men-banner .banner-content {
  align-items: flex-end;
  text-align: left;
  direction: ltr;
}

/* =================================
   English Subtitle
================================= */

.en-title {
  display: block;
  margin: 0;

  font-size: 11px;
  font-weight: 600;
  letter-spacing: 3px;

  color: rgba(255, 255, 255, 0.85);
}

/* =================================
   Persian Title
================================= */

.banner-content h2 {
  margin: 0;

  font-size: clamp(25px, 2.2vw, 34px);
  font-weight: 800;

  line-height: 1.7;

  color: #fff;
}

/* =================================
   Description
================================= */

.banner-content p {
  margin: 0;

  font-size: 14px;
  font-weight: 400;

  line-height: 2;

  color: rgba(255, 255, 255, 0.9);
}

/* =================================
   Banner Button
================================= */

.banner-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;

  margin-top: 8px;
  padding: 11px 23px;

  background: #e8d4ad;
  color: #29251f;

  border: 1px solid #e8d4ad;
  border-radius: 9px;

  font-size: 13px;
  font-weight: 600;
  text-decoration: none;

  transition: all 0.3s ease;
}

.banner-btn:hover {
  background: #fff;
  color: #29251f;
  border-color: #fff;

  transform: translateY(-3px);
}

/* Button Arrow */

.btn-arrow {
  display: inline-block;

  font-size: 17px;
  line-height: 1;

  transition: transform 0.3s ease;
}

.banner-btn:hover .btn-arrow {
  transform: translateX(-4px);
}

/* =================================
   Tablet
================================= */

@media (max-width: 1050px) {

  .collection-section {
    padding: 55px 0;
  }

  .collection-banners {
    width: 88%;
    height: 380px;
  }

  .collection-banner {
    padding: 30px;
  }

  .banner-content {
    max-width: 300px;
    gap: 10px;
  }

  .banner-content h2 {
    font-size: 25px;
  }

  .banner-content p {
    font-size: 12px;
  }

}

/* =================================
   Mobile
================================= */

@media (max-width: 768px) {

  .collection-section {
    margin: 75px auto;
    padding: 35px 0;
  }

  .collection-banners {
    width: 90%;
    height: auto;

    display: grid;
    grid-template-columns: 1fr;

    gap: 18px;
  }

  .collection-banner {
    position: relative;
    inset: auto;

    width: 100%;
    min-height: 250px;

    padding: 30px 20px;

    border-radius: 22px;

    clip-path: none !important;

    justify-content: center;
  }

  .women-banner,
  .men-banner {
    background-position: center;
  }

  /* Both Banners Centered */

  .women-banner .banner-content,
  .men-banner .banner-content {
    max-width: 100%;

    align-items: center;
    justify-content: center;

    text-align: center;
    direction: rtl;

    gap: 9px;
  }

  .en-title {
    font-size: 10px;
  }

  .banner-content h2 {
    font-size: 24px;
  }

  .banner-content p {
    font-size: 12px;
    line-height: 2;
  }

  .banner-btn {
    min-width: 150px;

    margin-top: 8px;
    padding: 10px 19px;

    font-size: 12px;
  }

}

/* =================================
   Small Mobile
================================= */

@media (max-width: 480px) {

  .collection-section {
    padding: 28px 0;
    margin: 55px auto;
  }

  .collection-banners {
    width: 90%;
    gap: 16px;
  }

  .collection-banner {
    min-height: 230px;

    padding: 25px 16px;

    border-radius: 18px;
  }

  .banner-content h2 {
    font-size: 22px;
  }

  .banner-content p {
    font-size: 11px;
  }

  .banner-btn {
    min-width: 145px;
    padding: 9px 17px;
  }

}

</style>
