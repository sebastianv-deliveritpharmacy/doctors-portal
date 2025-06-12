<!-- Verify2FA.vue -->
<template>
  <div class="verify-wrapper">
    <img
      src="https://deliveritpharmacy.com/wp-content/uploads/2024/04/Logo-Resurrgaction-B-Vertical.png"
      alt="DeliverIt Logo"
      class="logo"
    />

    <n-card class="verify-card" title="Two-Factor Authentication">
      <n-space vertical size="large" align="center">
        <n-p>Please enter the 6-digit code sent to your email:</n-p>
        <n-input v-model:value="code" placeholder="Enter code" maxlength="6" style="width: 200px" />
        <n-space>
          <n-button type="primary" @click="verify">Verify</n-button>
          <n-button tertiary @click="resend">Resend Code</n-button>
        </n-space>
      </n-space>
    </n-card>

  </div>
    <FooterComponent />
</template>

<script setup>
import { ref } from 'vue'
import axios from '@/api/axios'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/useUser'
import FooterComponent from '@/components/FooterComponent.vue'
import { useMessage } from 'naive-ui'

const router = useRouter()
const code = ref('')
const userId = localStorage.getItem('2fa_user_id')
const userStore = useUserStore()
const message = useMessage()

if (!userId) {
  router.push('/login')
}

const verify = async () => {
  try {
    const response = await axios.post('/verify-2fa', {
      code: code.value,
      user_id: userId
    })

    localStorage.setItem('access_token', response.data.token)
    userStore.setUser(response.data.user)
    localStorage.removeItem('2fa_user_id')

    // Clean up "remember me" if not selected
    if (localStorage.getItem('remember_me') !== 'true') {
      localStorage.removeItem('remember_me_email')
      localStorage.removeItem('remember_me_password')
      localStorage.removeItem('remember_me')
    }

    message.success('Verification successful!')
    router.push('/dashboard')
  } catch (err) {
    message.error('Invalid or expired code')
  }
}

const resend = async () => {
  try {
    await axios.post('/resend-2fa', {
      user_id: userId
    })
    message.success('Code resent to your email')
  } catch (err) {
    message.error('Failed to resend code')
  }
}
</script>

<style scoped>
.verify-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: #f7f8fa;
  padding: 40px 16px;
}

.logo {
  width: 120px;
  margin-bottom: 32px;
}

.verify-card {
  max-width: 400px;
  width: 100%;
  margin-bottom: 40px;
}
</style>
