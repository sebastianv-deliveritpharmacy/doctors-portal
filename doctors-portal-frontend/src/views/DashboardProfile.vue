<template>
  <n-card title="Your Profile">
    <n-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="120px"
      size="large"
    >
      <n-form-item label="Name" path="name">
        <n-input v-model:value="form.name" placeholder="Your name" />
      </n-form-item>

      <n-form-item label="Email" path="email">
        <n-input v-model:value="form.email" placeholder="Your email" />
      </n-form-item>

      <n-form-item label="New password" path="password">
        <n-input
          v-model:value="form.password"
          type="password"
          placeholder="New password"
          show-password-on="click"
        />
      </n-form-item>

      <n-form-item label="Confirm password" path="password_confirmation">
        <n-input
          v-model:value="form.password_confirmation"
          type="password"
          placeholder="Confirm password"
          show-password-on="click"
        />
      </n-form-item>

      <n-button type="primary" :loading="loading" @click="updateProfile">
        Update Profile
      </n-button>
    </n-form>
  </n-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { NCard, NForm, NFormItem, NInput, NButton, useMessage } from 'naive-ui'
import axios from '@/api/axios'
import { useRouter } from 'vue-router'

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
    { required: true, message: 'Name is required', trigger: ['input', 'blur'] },
  ],
  email: [
    { required: true, message: 'Email is required', trigger: ['input', 'blur'] },
    { type: 'email', message: 'Invalid email format', trigger: ['input', 'blur'] },
  ],
  password: [
    { min: 6, message: 'Password must be at least 6 characters', trigger: ['input', 'blur'] },
  ],
  password_confirmation: [
    {
      validator: (rule, value) => value === form.value.password,
      message: 'Passwords do not match',
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
    message.error('Failed to fetch user data');
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

    message.success('Profile updated successfully!');
    form.value.password = '';
    form.value.password_confirmation = '';
  } catch (error) {
    console.error('Failed to update profile', error?.response?.data || error.message);
    message.error('Failed to update profile. Please check the form.');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  getUserProfile();
})
</script>
