<script setup>
const menuItems = [
    {
        title: 'داشبورد',
        icon: '⌂',
        path: '/dashboard'
    },
    {
        title: 'سفارش‌های من',
        icon: '▤',
        path: '/orders'
    },
    {
        title: 'علاقه‌مندی‌ها',
        icon: '♡',
        path: '/wishlist'
    },
    {
        title: 'آدرس‌های من',
        icon: '⌖',
        path: '/addresses'
    },
    {
        title: 'اطلاعات حساب',
        icon: '♙',
        path: '/profile'
    }
]
defineProps({
    isOpen: {
        type: Boolean,
        default: true
    }
})
</script>

<template>
    <aside
    class="sidebar"
    :class="{ 'is-closed': !isOpen }"
>

        <div class="sidebar-logo">
            <h2>Clothing</h2>
            <span>پنل کاربری</span>
        </div>

        <nav class="sidebar-menu">
            <RouterLink
                v-for="item in menuItems"
                :key="item.path"
                :to="item.path"
                class="menu-item"
                active-class="active"
            >
                <span class="menu-icon">
                    {{ item.icon }}
                </span>

                <span>{{ item.title }}</span>
            </RouterLink>
        </nav>

        <div class="sidebar-bottom">
            <button class="logout">
                <span class="menu-icon">↪</span>
                <span>خروج از حساب</span>
            </button>
        </div>

    </aside>
</template>

<style scoped>
.sidebar {
    transition:
        transform 0.3s ease,
        opacity 0.3s ease;
}

.sidebar.is-closed {
    transform: translateX(100%);
    opacity: 0;
    pointer-events: none;
}
.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    right: 0;
    top: 0;

    display: flex;
    flex-direction: column;

    background: #111827;
    color: white;

    padding: 25px 15px;
    box-sizing: border-box;
}

.sidebar-logo {
    padding: 0 15px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.sidebar-logo h2 {
    margin: 0;
    font-size: 24px;
}

.sidebar-logo span {
    display: block;
    margin-top: 6px;
    color: #9ca3af;
    font-size: 13px;
}

.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-top: 25px;
}

.menu-item,
.logout {
    display: flex;
    align-items: center;
    gap: 12px;

    width: 100%;
    padding: 13px 15px;

    color: #d1d5db;
    text-decoration: none;

    border: none;
    border-radius: 10px;

    background: transparent;
    font-family: inherit;
    font-size: 14px;

    cursor: pointer;
    box-sizing: border-box;

    transition: 0.2s;
}

.menu-item:hover,
.logout:hover {
    background: #1f2937;
    color: white;
}

.menu-item.active {
    background: #2563eb;
    color: white;
}

.menu-icon {
    width: 22px;
    text-align: center;
    font-size: 18px;
}

.sidebar-bottom {
    margin-top: auto;
}
</style>