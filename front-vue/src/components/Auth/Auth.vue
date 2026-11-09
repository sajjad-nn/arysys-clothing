<script setup>
import { ref } from 'vue'
import AuthPanel from './AuthPanel.vue'
import LoginForm from './LoginForm.vue'
import RegisterForm from './RegisterForm.vue'

const isRegister = ref(false)
</script>

<template>
    <div class="auth-page">
        <div class="auth-card">

            <AuthPanel
                :is-register="isRegister"
                @switch="isRegister = !isRegister"
            />

            <div class="form-section">
                <LoginForm v-if="!isRegister" />
                <RegisterForm v-else />
            </div>

        </div>
    </div>
</template>

<style scoped>

.auth-page {
    width: 100%;
    flex: 1;
    min-height: 100%;
    box-sizing: border-box;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 110px 30px 40px;

    background:
        radial-gradient(circle at top right, #243b80, transparent 35%),
        radial-gradient(circle at bottom left, #111c42, transparent 40%),
        #070b17;

    direction: rtl;
    padding-bottom: 150px;
    
}
.auth-card {
    width: min(1050px, 100%);
    min-height: 650px;

    display: grid;
    grid-template-columns: 42% 58%;

    overflow: hidden;
    border-radius: 28px;

    background: #0f162b;
    border: 1px solid rgba(255,255,255,.08);

    box-shadow: 0 30px 80px rgba(0,0,0,.45);
    
}

.form-section {
    padding: 60px 80px;

    display: flex;
    align-items: center;
}

/* تبلت و موبایل */
@media (max-width: 800px) {
    .auth-page {
        padding: 100px 15px 30px;
    }

    .auth-card {
        display: block;
        min-height: auto;
    }

    .form-section {
        padding: 35px 25px;
    }
}

/* موبایل کوچک */
@media (max-width: 480px) {
    .auth-page {
        padding: 95px 12px 25px;
    }

    .form-section {
        padding: 30px 20px;
    }

    .auth-card {
        border-radius: 20px;
    }
}

</style>