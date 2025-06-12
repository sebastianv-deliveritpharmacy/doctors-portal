<template>
    <n-layout-header bordered class="header">
      <n-space justify="flex-end" align="center" class="header-content">
        <n-button type="primary" style="background-color: #2080f0;" @click="navigateToLogin">
          Doctor Login
        </n-button>
      </n-space>
    </n-layout-header>
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router'
  import { NLayoutHeader, NSpace, NButton } from 'naive-ui'
  
  const router = useRouter()
  const navigateToLogin = () => {
  const token = localStorage.getItem('access_token')

  if (token) {
    router.push('/dashboard')
  } else {
    router.push('/login')
  }
}

  </script>
  
  <style scoped>
  .header {
    padding: 0 24px;
    display: flex;
    height: 64px;
    align-items: center;
    justify-content: flex-end;
    flex-shrink: 0;
    background-color: var(--card-bg);
  }
  
  .header-content {
    width: 100%;
    max-width: 1440px;
  }
  
  .logo-img {
    width: 100px;
    object-fit: cover;
  }
  </style>
  