<template>
  <n-card :title="$t('profile.title')">
    <n-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="120px"
      size="large"
    >
      <n-form-item :label="$t('profile.name')" path="name">
        <n-input v-model:value="form.name" :placeholder="$t('profile.placeholder.name')" />
      </n-form-item>

      <n-form-item :label="$t('profile.email')" path="email">
        <n-input v-model:value="form.email" :placeholder="$t('profile.placeholder.email')" />
      </n-form-item>

      <n-form-item :label="$t('profile.newPassword')" path="password">
        <n-input
          v-model:value="form.password"
          type="password"
          :placeholder="$t('profile.placeholder.newPassword')"
          show-password-on="click"
      />
      </n-form-item>

      <n-form-item :label="$t('profile.confirmPassword')" path="password_confirmation">
        <n-input
          v-model:value="form.password_confirmation"
          type="password"
          :placeholder="$t('profile.placeholder.confirmPassword')"
          show-password-on="click"
      />
      </n-form-item>

      <n-button type="primary" :loading="loading" @click="updateProfile">
        {{ $t('profile.update') }}
      </n-button>
    </n-form>
  </n-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { NCard, NForm, NFormItem, NInput, NButton, useMessage } from 'naive-ui'
import axios from '@/api/axios'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const message = useMessage()
const router = useRouter()

const formRef = ref(null)
const loading = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const rules = {
  name: [
    { required: true, message: t('profile.validation.name'), trigger: ['input', 'blur'] }
  ],
  email: [
    { required: true, message: t('profile.validation.email'), trigger: ['input', 'blur'] },
    { type: 'email', message: t('profile.validation.invalidEmail'), trigger: ['input', 'blur'] }
  ],
  password: [
    { min: 6, message: t('profile.validation.passwordMin'), trigger: ['input', 'blur'] }
  ],
  password_confirmation: [
    {
      validator: (rule, value) => value === form.value.password,
      message: t('profile.validation.passwordMatch'),
      trigger: ['input', 'blur']
    }
  ]
}

const userId = ref(null) // Store the current user ID

const getUserProfile = async () => {
  try {
    const response = await axios.get('/user'); // /api/user
    const user = response.data;
    userId.value = user.id;
    form.value.name = user.name;
    form.value.email = user.email;
  } catch (error) {
    console.error('Failed to fetch user', error);
    message.error(t('profile.fetchError'))
    router.push('/login'); // redirect if no valid session
  }
}

const updateProfile = async () => {
  try {
    loading.value = true;
    await formRef.value?.validate();

    // Prepare the payload
    const payload = {
      name: form.value.name,
      email: form.value.email,
    };
    if (form.value.password) {
      payload.password = form.value.password;
      payload.password_confirmation = form.value.password_confirmation;
    }

    await axios.put(`/user`, payload);

    message.success(t('profile.success'))
    form.value.password = '';
    form.value.password_confirmation = '';
  } catch (error) {
    console.error('Failed to update profile', error?.response?.data || error.message);
    message.error(t('profile.error'))
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  getUserProfile();
})
</script>
