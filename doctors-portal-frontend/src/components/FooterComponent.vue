<template>
    <n-layout-footer bordered class="footer">
      <n-space vertical align="center">
        <n-p>© {{ currentYear }} DeliverIt Pharmacy</n-p>
        <n-space>
          <n-button text @click="showContact = true">Contact Us</n-button>
          <n-button text @click="showTerms = true">Terms of Service</n-button>
        </n-space>
      </n-space>
    </n-layout-footer>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue'
  import { NLayoutFooter, NSpace, NButton, NP } from 'naive-ui'
  
  const showContact = ref(false)
  const showTerms = ref(false)
  
  const currentYear = computed(() => new Date().getFullYear())
  </script>
  
  <style scoped>
  .footer {
    padding: 24px;
    text-align: center;
    flex-shrink: 0;
  }
  </style>
  