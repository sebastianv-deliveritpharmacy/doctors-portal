<template>
  <div class="reset-container">
    <n-card title="Reset Password" hoverable class="reset-card">
      <n-form
        ref="formRef"
        :model="formValue"
        :rules="rules"
        size="large"
        @submit.prevent="submitReset"
      >
        <n-form-item path="email" label="Email">
          <n-input
            v-model:value="formValue.email"
            placeholder="Enter your email"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-form-item path="password" label="New Password">
          <n-input
            v-model:value="formValue.password"
            type="password"
            placeholder="Enter new password"
            show-password-on="click"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-form-item path="password_confirmation" label="Confirm Password">
          <n-input
            v-model:value="formValue.password_confirmation"
            type="password"
            placeholder="Confirm new password"
            show-password-on="click"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-button
          type="primary"
          :loading="loading"
          attr-type="submit"
          block
          style="background-color: #2080f0;"
        >
          Reset Password
        </n-button>
      </n-form>
    </n-card>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  NCard,
  NForm,
  NFormItem,
  NInput,
  NButton,
  useMessage,
} from 'naive-ui'
import axios from '@/api/axios'

const route = useRoute()
const router = useRouter()
const formRef = ref(null)
const message = useMessage()
const loading = ref(false)

const formValue = ref({
  email: '',
  password: '',
  password_confirmation: '',
})

const rules = {
  email: [
    { required: true, message: 'Email is required', trigger: ['input', 'blur'] },
    { type: 'email', message: 'Invalid email', trigger: ['input', 'blur'] },
  ],
  password: [
    { required: true, message: 'Password is required', trigger: ['input', 'blur'] },
    { min: 6, message: 'Password must be at least 6 characters', trigger: ['input', 'blur'] },
  ],
  password_confirmation: [
    { required: true, message: 'Please confirm your password', trigger: ['input', 'blur'] },
  ],
}

const submitReset = async () => {
  loading.value = true

  try {
    await axios.post('/reset-password', {
      token: route.params.token,
      ...formValue.value,
    })

    message.success('Password reset successfully. You can now log in.')
    router.push('/login')
  } catch (error) {
    const err = error?.response?.data?.message || 'Something went wrong'
    message.error(err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.reset-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: #f5f5f5;
}

.reset-card {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
}
</style>
