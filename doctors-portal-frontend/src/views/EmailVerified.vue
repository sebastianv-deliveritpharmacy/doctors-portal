<template>
  <n-layout class="email-verified-layout">
    <HeaderComponent />

    <n-layout-content class="email-verified-content">
      <n-space vertical justify="center" align="center" style="width: 100%;">
        <n-card :title="t('emailVerified.title')" style="max-width: 400px; width: 100%;">
          <p>{{ t('emailVerified.message') }}</p>
          <n-button @click="$router.push('/login')">{{ t('emailVerified.button') }}</n-button>
        </n-card>
      </n-space>
    </n-layout-content>

    <FooterComponent />
  </n-layout>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import HeaderComponent from '@/components/HeaderComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

const { t } = useI18n()
</script>

<style scoped>
.email-verified-layout {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.email-verified-content {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 24px;
}
</style>
