<template>
  <div class="auth-container">
    <n-card :title="$t('signup.title')" hoverable class="auth-card">
      <n-form
        ref="formRef"
        :model="formValue"
        :rules="rules"
        size="large"
        @submit.prevent="register"
      >
        <n-form-item path="name" :label="$t('signup.name')">
          <n-input
            v-model:value="formValue.name"
            :placeholder="$t('signup.namePlaceholder')"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-form-item path="email" :label="$t('signup.email')">
          <n-input
            v-model:value="formValue.email"
            :placeholder="$t('signup.emailPlaceholder')"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-form-item path="password" :label="$t('signup.password')">
          <n-input
            v-model:value="formValue.password"
            type="password"
            :placeholder="$t('signup.passwordPlaceholder')"
            show-password-on="click"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-form-item path="password_confirmation" :label="$t('signup.passwordConfirm')">
          <n-input
            v-model:value="formValue.password_confirmation"
            type="password"
            :placeholder="$t('signup.passwordConfirmPlaceholder')"
            show-password-on="click"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-space vertical :size="24">
          <div style="display: flex; justify-content: space-between; align-items:center;">
            <n-checkbox v-model:checked="acceptTerms">
              <span v-html="$t('signup.acceptTermsHtml')"></span>
            </n-checkbox>

            <n-button text @click="router.push('/login')">
              {{ $t('signup.haveAccount') }}
            </n-button>
          </div>

          <n-button
            type="primary"
            size="large"
            :block="true"
            :loading="loading"
            attr-type="submit"
            style="background-color: #2080f0;"
          >
            {{ $t('signup.createAccount') }}
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
  NCheckbox,
  NForm,
  NFormItem,
  NInput,
  NSpace,
  useMessage
} from 'naive-ui'
import { useRouter } from 'vue-router'
import axios from '@/api/axios'
import { apiRoutes } from '@/api/apiRoutes'
import { useUserStore } from '@/stores/useUser'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const router = useRouter()
const userStore = useUserStore()
const message = useMessage()
const formRef = ref(null)
const loading = ref(false)
const acceptTerms = ref(false)

const formValue = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

// ✅ Validation messages pulled from i18n using t()
const rules = {
  name: [
    { required: true, message: t('signup.msg.nameRequired'), trigger: ['input', 'blur'] },
    { min: 2, message: t('signup.msg.nameMin'), trigger: ['input', 'blur'] }
  ],
  email: [
    { required: true, message: t('signup.msg.emailRequired'), trigger: ['input', 'blur'] },
    { type: 'email', message: t('signup.msg.emailInvalid'), trigger: ['input', 'blur'] }
  ],
  password: [
    { required: true, message: t('signup.msg.passwordRequired'), trigger: ['input', 'blur'] },
    { min: 6, message: t('signup.msg.passwordMin'), trigger: ['input', 'blur'] }
  ],
  password_confirmation: [
    {
      validator: (_rule, value) => value === formValue.value.password,
      message: t('signup.msg.passwordsMustMatch'),
      trigger: ['input', 'blur']
    }
  ]
}

const register = async () => {
  // Simple terms check before hitting API
  if (!acceptTerms.value) {
    message.warning(t('signup.msg.acceptTermsFirst'))
    return
  }

  try {
    loading.value = true

    const payload = {
      name: formValue.value.name,
      email: formValue.value.email,
      password: formValue.value.password,
      password_confirmation: formValue.value.password_confirmation
    }

    // Expect your backend route like: POST /auth/register
    const { data, status } = await axios.post(apiRoutes.signUp, payload)

    

     if (status === 201) {
      message.success(t('signup.msg.checkEmail'))
      router.push('/login') // make sure this route/page exists
      return
    }

    // Fallback: success without token (rare)
    message.success(t('signup.msg.success'))
    router.push('/login')
  } catch (error) {
    let errorMessage = t('signup.msg.genericError')

    if (error?.response?.data?.errors) {
      const firstKey = Object.keys(error.response.data.errors)[0]
      errorMessage = error.response.data.errors[firstKey][0]
    } else if (error?.response?.data?.message) {
      errorMessage = error.response.data.message
    }

    message.error(errorMessage)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh; /* in case you add header later */
  background: #f5f5f5;
  padding: 24px;
}
.auth-card {
  width: 100%;
  max-width: 420px;
  margin: 0 auto;
}
</style>
