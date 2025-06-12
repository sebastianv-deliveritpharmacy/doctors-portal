<template>
  <n-config-provider>
    <n-dialog-provider>
      <n-message-provider>
        <router-view />
      </n-message-provider>
    </n-dialog-provider>
  </n-config-provider>
</template>


<script setup>
import { NConfigProvider, NDialogProvider, NMessageProvider } from 'naive-ui'
</script>

<style>
html, body, .n-config-provider {
  height: 100%;
  margin: 0;
}

#app {
  height: 100vh;
  margin: 0;
}

.n-card.n-card--bordered {
  border: 0;
}

.n-layout-scroll-container {
  width: 100%;
}
</style>
