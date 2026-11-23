<template>
  <div class="floating-social" dir="ltr">
    <Transition name="social-menu">
      <div v-if="isOpen" class="social-menu">
        <!-- Instagram -->
        <a
          href="https://www.instagram.com/YOUR_USERNAME"
          target="_blank"
          rel="noopener noreferrer"
          class="social-link instagram"
          aria-label="Instagram"
          title="Instagram"
        >
          <svg viewBox="0 0 24 24" fill="none">
            <rect
              x="3"
              y="3"
              width="18"
              height="18"
              rx="5"
              stroke="currentColor"
              stroke-width="2"
            />
            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2" />
            <circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" />
          </svg>
        </a>

        <!-- Telegram -->
        <a
          href="https://t.me/YOUR_USERNAME"
          target="_blank"
          rel="noopener noreferrer"
          class="social-link telegram"
          aria-label="Telegram"
          title="Telegram"
        >
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M21.8 4.2 18.5 20c-.25 1.12-.9 1.4-1.83.87l-5.05-3.72-2.44 2.35c-.27.27-.5.5-1.02.5l.36-5.14 9.36-8.45c.41-.36-.09-.56-.64-.2L5.66 13.45.66 11.9c-1.08-.34-1.1-1.08.23-1.61L20.45 2.7c.9-.33 1.7.22 1.35 1.5Z"
            />
          </svg>
        </a>

        <!-- WhatsApp -->
        <a
          href="https://wa.me/YOUR_PHONE_NUMBER"
          target="_blank"
          rel="noopener noreferrer"
          class="social-link whatsapp"
          aria-label="WhatsApp"
          title="WhatsApp"
        >
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M20.5 11.5a8.5 8.5 0 0 1-12.6 7.4L3 20l1.2-4.7a8.5 8.5 0 1 1 16.3-3.8Z" />
            <path
              d="M8.5 8.5c.4 2.3 2.7 4.6 5 5l1.2-1.2 2 1c-.2 1.5-1.4 2.2-2.8 2-3.5-.6-6.1-3.2-6.7-6.7-.2-1.4.5-2.6 2-2.8l1 2-1.2 1.2Z"
            />
          </svg>
        </a>
      </div>
    </Transition>

    <!-- Main Button -->
    <button
      class="social-main-button"
      :class="{ active: isOpen }"
      @click="isOpen = !isOpen"
      :aria-expanded="isOpen"
      aria-label="شبکه‌های اجتماعی"
    >
      <svg
        v-if="!isOpen"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <circle cx="18" cy="5" r="3" />
        <circle cx="6" cy="12" r="3" />
        <circle cx="18" cy="19" r="3" />
        <path d="m8.7 10.7 6.6-4.4M8.7 13.3l6.6 4.4" />
      </svg>

      <svg
        v-else
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
      >
        <path d="m18 6-12 12M6 6l12 12" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const isOpen = ref(false)
</script>

<style scoped>
.floating-social {
  position: fixed !important;
  right: 16px !important;
  bottom: calc(20px + env(safe-area-inset-bottom, 0px)) !important;
  z-index: 999999 !important;

  display: flex !important;
  flex-direction: column;
  align-items: center;
  gap: 12px;

  visibility: visible !important;
  opacity: 1 !important;
  pointer-events: auto !important;
}

.social-menu {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.social-link,
.social-main-button {
  width: 48px;
  height: 48px;
  flex-shrink: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
}

.social-link {
  background: #171717;
  color: #d6b36a;
  border: 1px solid rgba(214, 179, 106, 0.5);
}

.social-link svg {
  width: 23px;
  height: 23px;
}

.social-main-button {
  border: 1px solid #e3c27d;
  background: linear-gradient(135deg, #e6c77e, #b28b42);
  color: #171717;
  box-shadow: 0 6px 22px rgba(0, 0, 0, 0.25);
}

.social-main-button svg {
  width: 25px;
  height: 25px;
}

.social-link:hover {
  transform: translateY(-4px);
  background: #d6b36a;
  color: #111;
}

.social-main-button:hover {
  transform: scale(1.08);
}

.social-main-button.active {
  transform: rotate(90deg);
}

.social-menu-enter-active,
.social-menu-leave-active {
  transition: all 0.25s ease;
}

.social-menu-enter-from,
.social-menu-leave-to {
  opacity: 0;
  transform: translateY(15px);
}

@media (max-width: 768px) {
  .floating-social {
    right: 12px !important;
    bottom: calc(120px + env(safe-area-inset-bottom, 0px)) !important;
  }

  .social-link,
  .social-main-button {
    width: 46px;
    height: 46px;
  }
}
</style>
