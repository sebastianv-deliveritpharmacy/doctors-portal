<template>
    <n-card title="Settings">
      <n-form :model="form" :rules="rules" label-width="120px" size="large" ref="formRef">
        <h3>CareTend API</h3>
        <n-form-item label="Client ID" path="client_id">
          <n-input v-model:value="form.client_id" placeholder="Enter Client ID" />
        </n-form-item>
  
        <n-form-item label="Secret Key" path="secret">
          <n-input
            v-model:value="form.secret"
            type="password"
            placeholder="Enter Secret Key"
            show-password-on="click"
          />
        </n-form-item>
  
        <n-space justify="end" class="mt-4">
          <n-button :loading="loading" type="primary" @click="handleSubmit">
            {{ existing ? 'Update' : 'Save' }}
          </n-button>
          <n-button v-if="existing" type="error" secondary @click="handleDelete">
            Delete
          </n-button>
        </n-space>
      </n-form>
    </n-card>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { NCard, NForm, NFormItem, NInput, NButton, NSpace, useMessage } from 'naive-ui';
  import axios from '@/api/axios';
  
  const message = useMessage();
  const formRef = ref(null);
  const form = ref({ client_id: '', secret: '' });
  const existing = ref(false);
  const loading = ref(false);
  
  const rules = {
    client_id: [{ required: true, message: 'Client ID is required', trigger: ['input', 'blur'] }],
    secret: [{ required: true, message: 'Secret is required', trigger: ['input', 'blur'] }]
  };
  
  const loadCredential = async () => {
    try {
      const res = await axios.get('/caretend-credential');
      if (res.data) {
        form.value = { client_id: res.data.client_id, secret: res.data.secret };
        existing.value = true;
      }
    } catch (error) {
      existing.value = false; // No credential yet
    }
  };
  
  const handleSubmit = async () => {
    try {
      await formRef.value?.validate();
      loading.value = true;
      if (existing.value) {
        await axios.put('/caretend-credential', form.value);
        message.success('Credential updated');
      } else {
        await axios.post('/caretend-credential', form.value);
        message.success('Credential saved');
        existing.value = true;
      }
    } catch (error) {
      message.error('Failed to save credential');
    } finally {
      loading.value = false;
    }
  };
  
  const handleDelete = async () => {
    try {
      await axios.delete('/caretend-credential');
      form.value = { client_id: '', secret: '' };
      existing.value = false;
      message.success('Credential deleted');
    } catch (error) {
      message.error('Failed to delete credential');
    }
  };
  
  onMounted(loadCredential);
  </script>
  
  <style scoped>
  .mt-4 {
    margin-top: 1.5rem;
  }
  </style>
  