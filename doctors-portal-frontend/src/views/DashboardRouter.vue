<script setup>
import { useUserStore } from '@/stores/useUser'
import DashboardAdmin from './DashboardAdmin.vue'
import DashboardUser from './DashboardUser.vue'

const userStore = useUserStore()
</script>

<template>
  <component :is="userStore.isAdmin() ? DashboardAdmin : DashboardUser" />
</template>
