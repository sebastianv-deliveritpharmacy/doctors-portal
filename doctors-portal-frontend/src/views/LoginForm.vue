<template>
  <div class="login-container">
    <n-card :title="$t('login.title')" hoverable class="login-card">
      <n-form
        ref="formRef"
        :model="formValue"
        :rules="rules"
        size="large"
        @submit.prevent="login"
      >
        <n-form-item path="email" :label="$t('login.email')">
          <n-input
            v-model:value="formValue.email"
            :placeholder="$t('login.emailPlaceholder')"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-form-item path="password" :label="$t('login.password')">
          <n-input
            v-model:value="formValue.password"
            type="password"
            :placeholder="$t('login.passwordPlaceholder')"
            show-password-on="click"
            @keydown.enter.prevent
          />
        </n-form-item>

        <n-space vertical :size="24">
          <div class="login-links">
            <n-checkbox v-model:checked="rememberMe">
              {{ $t('login.rememberMe') }}
            </n-checkbox>

            <n-space size="small" justify="end">
              <n-button text @click="router.push('/forgot-password')">
                {{ $t('login.forgotPassword') }}
              </n-button>
              <n-button text @click="router.push('/sign-up')">
                {{ $t('login.signUp') }}
              </n-button>
            </n-space>
          </div>

          <n-button
            type="primary"
            size="large"
            :block="true"
            :loading="loading"
            attr-type="submit"
            style="background-color: #2080f0;"
          >
            {{ $t('login.signIn') }}
          </n-button>
        </n-space>

      </n-form>
    </n-card>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import {
  NButton,
  NCard,
  NCheckbox,
  NForm,
  NFormItem,
  NInput,
  NSpace,
  useMessage,
} from 'naive-ui'
import { useRouter } from 'vue-router'
import axios from '@/api/axios'
import { apiRoutes } from '@/api/apiRoutes'
import { useUserStore } from '@/stores/useUser'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const userStore = useUserStore()
const formRef = ref(null)
const message = useMessage()
const loading = ref(false)
const rememberMe = ref(false)

const formValue = ref({
  email: '',
  password: ''
})

// ✅ Use t() instead of $t here
const rules = {
  email: [
    {
      required: true,
      message: t('login.loginMsg.emailMsg'),
      trigger: ['input', 'blur']
    },
    {
      type: 'email',
      message: t('login.loginMsg.emailMsgValid'),
      trigger: ['input', 'blur']
    }
  ],
  password: [
    {
      required: true,
      message: t('login.loginMsg.emailPwd'),
      trigger: ['input', 'blur']
    },
    {
      min: 6,
      message: t('login.loginMsg.emailPwdCh'),
      trigger: ['input', 'blur']
    }
  ]
}

const router = useRouter()

const login = async () => {
  try {
    const response = await axios.post(apiRoutes.login, {
      email: formValue.value.email,
      password: formValue.value.password,
    });

    if (rememberMe.value) {
      localStorage.setItem('remember_me_email', formValue.value.email)
      localStorage.setItem('remember_me_password', formValue.value.password)
      localStorage.setItem('remember_me', 'true')
    } else {
      localStorage.removeItem('remember_me_email')
      localStorage.removeItem('remember_me_password')
      localStorage.removeItem('remember_me')
    }

    if (response.data.two_factor_required) {
      localStorage.setItem('2fa_user_id', response.data.user_id)
      router.push('/verify-2fa')
    } else {
      localStorage.setItem('access_token', response.data.token)
      userStore.setUser(response.data.user)
      router.push('/dashboard')
    }
  } catch (error) {
    let errorMessage = t('login.errorMessage')

    if (error?.response?.data?.errors) {
      const firstErrorKey = Object.keys(error.response.data.errors)[0]
      errorMessage = error.response.data.errors[firstErrorKey][0]
    } else if (error?.response?.data?.message) {
      errorMessage = error.response.data.message
    }

    message.error(errorMessage)
  }
}

onMounted(() => {
  const savedEmail = localStorage.getItem('remember_me_email')
  const savedPassword = localStorage.getItem('remember_me_password')

  if (savedEmail && savedPassword) {
    formValue.value.email = savedEmail
    formValue.value.password = savedPassword
    rememberMe.value = true
  }
})
</script>


<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: #f5f5f5;
}

.login-card {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
}

.login-links {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.login-card {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
  padding: 20px 24px; /* Extra breathing room */
}

.n-button[text] {
  padding: 0 8px;
}

</style>
