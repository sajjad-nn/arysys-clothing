<template>
  <header class="dashboard-header">

    <!-- Right -->
    <div class="header-right">

    <button
  class="menu-button"
  @click="$emit('toggle-sidebar')"
  aria-label="باز و بسته کردن منو"
>
  <svg
    v-if="props.sidebarOpen"
    viewBox="0 0 24 24"
    class="arrow-icon"
  >
    <path d="M9 6L15 12L9 18" />
  </svg>

  <svg
    v-else
    viewBox="0 0 24 24"
    class="arrow-icon"
  >
    <path d="M15 6L9 12L15 18" />
  </svg>
</button>

      <div class="welcome">
        <!-- <span>خوش آمدید</span>
        <strong>{{ userName }}</strong> -->
      </div>

      <!-- Main Menu -->
      <nav class="main-menu">

        <RouterLink to="/">
          خانه
        </RouterLink>

        <RouterLink to="/shop">
          فروشگاه
        </RouterLink>

        <RouterLink to="/articles">
          مقالات
        </RouterLink>

        <RouterLink to="/about">
          درباره ما
        </RouterLink>

        <RouterLink to="/contact">
          تماس با ما
        </RouterLink>

      </nav>

    </div>


    <!-- Left -->
    <div class="header-left">

      <!-- Search -->
      <button class="header-button">
        <svg viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="7"></circle>
          <path d="M16 16L21 21"></path>
        </svg>
      </button>


      <!-- Notification -->
      <button class="header-button">

        <svg viewBox="0 0 24 24">
          <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
          <path d="M10 21h4"></path>
        </svg>

        <span class="notification-dot"></span>

      </button>


      <!-- Profile -->
      <RouterLink
        to="/dashboard"
        class="profile"
      >

        <div class="profile-avatar">
          {{ userName.charAt(0) }}
        </div>

        <div class="profile-info">

          <strong>
            {{ userName }}
          </strong>

          <span>
            حساب کاربری
          </span>

        </div>

      </RouterLink>

    </div>

  </header>
</template>


<script setup>
const props = defineProps({
  userName: {
    type: String,
    default: 'کاربر'
  },

  sidebarOpen: {
    type: Boolean,
    default: true
  }
})

defineEmits(['toggle-sidebar'])
</script>


<style scoped>

.dashboard-header {
  width: 100%;
  height: 76px;

  padding: 0 24px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  direction: rtl;

  background: rgba(255, 255, 255, 0.72);

  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);

  border: 1px solid rgba(0, 0, 0, 0.06);

  border-radius: 18px;

  box-sizing: border-box;
}


/* RIGHT */

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}


/* MENU BUTTON */
.menu-button {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 12px;
  background: rgba(0, 0, 0, 0.04);
  cursor: pointer;
  transition: 0.25s;
}

.arrow-icon {
  width: 20px;
  height: 20px;
  fill: none;
  stroke: #111;
  stroke-width: 1.8;
  stroke-linecap: round;
  stroke-linejoin: round;
  transition: 0.25s;
}

.menu-button:hover {
  background: rgba(0, 0, 0, 0.08);
}


/* WELCOME */

.welcome {
  display: flex;
  flex-direction: column;

  gap: 3px;
}

.welcome span {
  font-size: 12px;
  color: #888;
}

.welcome strong {
  font-size: 16px;
  font-weight: 600;
  color: #111;
}


/* MAIN MENU */

.main-menu {
  display: flex;
  align-items: center;

  gap: 24px;

  margin-right: 30px;
}

.main-menu a {
  position: relative;

  color: #222;

  text-decoration: none;

  font-size: 13px;
  font-weight: 500;

  white-space: nowrap;

  padding-bottom: 5px;

  transition: 0.2s;
}

.main-menu a::after {
  content: "";

  position: absolute;

  right: 0;
  bottom: 0;

  width: 0;
  height: 2px;

  background: #111;

  transition: 0.25s;
}

.main-menu a:hover::after {
  width: 100%;
}

.main-menu a.router-link-active {
  font-weight: 700;
}

.main-menu a.router-link-active::after {
  width: 100%;
}


/* LEFT */

.header-left {
  display: flex;
  align-items: center;

  gap: 10px;
}


/* HEADER BUTTON */

.header-button {
  position: relative;

  width: 42px;
  height: 42px;

  display: flex;
  align-items: center;
  justify-content: center;

  border: none;
  border-radius: 12px;

  background: rgba(0, 0, 0, 0.04);

  cursor: pointer;

  transition: 0.25s;
}

.header-button:hover {
  background: rgba(0, 0, 0, 0.08);
}

.header-button svg {
  width: 19px;
  height: 19px;

  fill: none;

  stroke: #222;

  stroke-width: 1.7;

  stroke-linecap: round;
  stroke-linejoin: round;
}


/* NOTIFICATION */

.notification-dot {
  position: absolute;

  top: 9px;
  right: 9px;

  width: 6px;
  height: 6px;

  border-radius: 50%;

  background: #111;

  border: 1px solid white;
}


/* PROFILE */

.profile {
  display: flex;
  align-items: center;

  gap: 10px;

  margin-right: 8px;

  text-decoration: none;

  cursor: pointer;
}

.profile-avatar {
  width: 43px;
  height: 43px;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 50%;

  background: #111;

  color: white;

  font-size: 17px;
  font-weight: 600;
}

.profile-info {
  display: flex;
  flex-direction: column;

  gap: 3px;
}

.profile-info strong {
  font-size: 13px;
  font-weight: 600;

  color: #111;
}

.profile-info span {
  font-size: 10px;

  color: #888;
}


/* MOBILE */

@media (max-width: 900px) {

  .main-menu {
    display: none;
  }

}


@media (max-width: 768px) {

  .dashboard-header {
    height: 68px;

    padding: 0 14px;

    border-radius: 15px;
  }

  .welcome {
    display: none;
  }

  .profile-info {
    display: none;
  }

  .header-left {
    gap: 6px;
  }

  .profile {
    margin-right: 3px;
  }

  .header-button,
  .menu-button {
    width: 38px;
    height: 38px;
  }

}

</style>