<template>
  <div class="forgot-container">
    <n-card :title="t('forgot.title')" hoverable class="forgot-card">
      <n-form
        ref="formRef"
        :model="formValue"
        :rules="rules"
        size="large"
        @submit.prevent="submitRequest"
      >
        <n-form-item path="email" :label="t('forgot.emailLabel')">
          <n-input
            v-model:value="formValue.email"
            :placeholder="t('forgot.emailPlaceholder')"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-space vertical :size="24">
          <n-button
            type="primary"
            size="large"
            :block="true"
            :loading="loading"
            attr-type="submit"
            style="background-color: #2080f0;"
          >
            {{ t('forgot.sendLink') }}
          </n-button>

          <n-button text @click="router.push('/login')" style="text-align: center; width: 100%">
            {{ t('forgot.backToLogin') }}
          </n-button>
        </n-space>
      </n-form>
    </n-card>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import {
  NButton,
  NCard,
  NForm,
  NFormItem,
  NInput,
  NSpace,
  useMessage,
} from 'naive-ui'
import { useRouter } from 'vue-router'
import axios from '@/api/axios'
import { apiRoutes } from '@/api/apiRoutes'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const router = useRouter()
const message = useMessage()
const formRef = ref(null)
const loading = ref(false)

const formValue = ref({
  email: ''
})

const rules = {
  email: [
    {
      required: true,
      message: 'Please input your email',
      trigger: ['input', 'blur']
    },
    {
      type: 'email',
      message: 'Please enter a valid email address',
      trigger: ['input', 'blur']
    }
  ]
}

const submitRequest = async () => {
  loading.value = true
  try {
    const response = await axios.post(apiRoutes.forgotPassword, {
      email: formValue.value.email
    })

    message.success(response.data.message || 'Reset link sent successfully.')
  } catch (error) {
    const msg = error?.response?.data?.message || 'Failed to send reset link.'
    message.error(msg)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.forgot-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: #f5f5f5;
}

.forgot-card {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
}
</style>
