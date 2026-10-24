<script setup>
defineProps({
    isRegister: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['switch'])
</script>

<template>
    <div class="auth-panel">

        <!-- Logo -->
        <div class="logo">
            <span>ARYA</span>SYS
        </div>

        <!-- Content -->
        <div class="content">

            <transition name="fade" mode="out-in">

                <div :key="isRegister">

                    <template v-if="!isRegister">
                        <h2>خوش برگشتید!</h2>

                        <p>
                            برای ادامه فعالیت و دسترسی به حساب
                            کاربری خود وارد شوید.
                        </p>

                        <button
                            class="switch-btn"
                            @click="emit('switch')"
                        >
                            ایجاد حساب
                        </button>
                    </template>

                    <template v-else>
                        <h2>به ما بپیوندید</h2>

                        <p>
                            حساب کاربری خود را بسازید و از
                            امکانات سایت استفاده کنید.
                        </p>

                        <button
                            class="switch-btn"
                            @click="emit('switch')"
                        >
                            ورود به حساب
                        </button>
                    </template>

                </div>

            </transition>

        </div>

    </div>
</template>

<style scoped>
.auth-panel {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

    padding: 45px;

    color: white;
    overflow: hidden;

    background:
        linear-gradient(
            145deg,
            #162d78,
            #0d1738 75%
        );
}

/* نور بالا */
.auth-panel::before {
    content: '';

    position: absolute;

    width: 300px;
    height: 300px;

    border-radius: 50%;

    background: rgba(82, 117, 255, 0.2);

    filter: blur(30px);

    top: -100px;
    right: -100px;
}

/* نور پایین */
.auth-panel::after {
    content: '';

    position: absolute;

    width: 200px;
    height: 200px;

    border-radius: 50%;

    background: rgba(82, 117, 255, 0.12);

    filter: blur(30px);

    bottom: -80px;
    left: -80px;
}

/* Logo */

.logo {
    position: relative;
    z-index: 1;

    font-size: 23px;
    font-weight: 800;

    letter-spacing: 1px;
}

.logo span {
    color: #6d8dff;
}

/* Content */

.content {
    position: relative;
    z-index: 1;

    margin-bottom: 40px;
}

.content h2 {
    font-size: 38px;
    line-height: 1.4;

    margin: 0 0 15px;
}

.content p {
    max-width: 330px;

    color: #b8c1db;

    line-height: 2;
    font-size: 14px;

    margin: 0 0 28px;
}

/* Switch Button */

.switch-btn {
    padding: 13px 28px;

    border-radius: 12px;

    border: 1px solid rgba(255, 255, 255, 0.3);

    background: rgba(255, 255, 255, 0.08);

    color: white;

    cursor: pointer;

    font-size: 14px;

    transition: 0.3s;
}

.switch-btn:hover {
    background: rgba(255, 255, 255, 0.16);

    transform: translateY(-2px);
}

/* Animation */

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Mobile */

@media (max-width: 800px) {

    .auth-panel {
        min-height: 220px;
        padding: 30px;
    }

    .content {
        margin: 0;
    }

    .content h2 {
        font-size: 26px;
        margin-bottom: 8px;
    }

    .content p,
    .switch-btn {
        display: none;
    }
}
</style>