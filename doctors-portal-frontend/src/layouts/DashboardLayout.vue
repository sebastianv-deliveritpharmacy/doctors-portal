<template>
  <n-layout has-sider class="full-height-layout">
   <n-layout-sider
    class="full-height-layout"
    bordered
    collapse-mode="width"
    :collapsed-width="64"
    :width="240"
    :show-trigger="!isMobile"
    :collapsed="isCollapsed"
    @update:collapsed="handleCollapse"
    style="position: relative;"
  >


      <router-link to="/">
        <img
          v-if="!isCollapsed"
          src="https://deliveritpharmacy.com/wp-content/uploads/2024/04/Logo-Resurrgaction-B-Vertical.png"
          alt="Deliverit Logo"
          class="logo-img"
          style="width: 120px; margin: 20px;"
        />
        <img
          v-else
          src="https://deliveritpharmacy.com/wp-content/uploads/2024/04/DPIC-emblem.png"
          alt="Deliverit Emblem"
          class="logo-img"
          style="width: 40px; margin: 12px auto; display: block;"
        />
    </router-link>


      <n-menu
        :value="activeKey"
        :options="menuOptions"
        @update:value="handleMenuSelect"
        :theme-overrides="menuThemeOverrides"
      />

      <!-- Partnerships section pinned to bottom -->
      <div
        v-if="!isCollapsed"
        style="
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
          padding: 16px;
          background: linear-gradient(135deg, #f0f4ff, #dce7ff);
          text-align: center;
          border-top: 1px solid #ccc;
        "
      >
        <div style="font-size: 18px; font-weight: bold; letter-spacing: 0.5px; color: #333; margin-bottom: 10px;">
          {{ $t('layout.partners') }}
        </div>
        <div style="display: flex; gap: 8px; justify-content: center;">
          <a href="https://deliveritpharmacy.com" target="_blank" rel="noopener">
            <img
              src="https://deliveritpharmacy.com/wp-content/uploads/2024/04/Logo-Resurrgaction-B-Vertical.png"
              alt="DeliverIt Pharmacy"
              style="width: 70px; height: 50px; object-fit: contain;"
            />
          </a>
          <a href="https://deliverithealth.com" target="_blank" rel="noopener">
            <img
              src="https://deliverithealth.com/wp-content/uploads/2024/02/dhc-portrait-logo-ong-embelem.png"
              alt="DeliverIt Health"
              style="width: 70px; height: 50px; object-fit: contain;"
            />
          </a>
        </div>
      </div>
    </n-layout-sider>

    <n-layout>
      <n-layout-content content-style="height: calc(100vh - var(--header-height));">
        <router-view />
      </n-layout-content>
    </n-layout>
  </n-layout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, h } from 'vue'
import { NIcon, useMessage } from 'naive-ui'
import { useRouter, useRoute } from 'vue-router'
import {
  BookOutline, PersonOutline, PeopleOutline,
  LogOutOutline, SettingsOutline, DocumentTextOutline, PeopleCircleOutline
} from '@vicons/ionicons5'
import axios from '@/api/axios'
import { useUserStore } from '@/stores/useUser'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const userStore = useUserStore()
const router = useRouter()
const route = useRoute()

const isCollapsed = ref(false)
const isMobile = ref(false)

const checkMobile = () => {
  isMobile.value = window.innerWidth < 768
  if (isMobile.value) {
    isCollapsed.value = true
  }
}
function handleCollapse(val) {
  if (!isMobile.value) {
    isCollapsed.value = val
  }
}


onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', checkMobile)
})

const superAdminMenu = computed(() => [
  {
    label: t('layout.menu.dashboard'),
    key: 'dashboard',
    to: '/dashboard',
    icon: renderIcon(BookOutline)
  },
  {
    label: t('layout.menu.admins'),
    key: 'admins',
    to: '/dashboard/admins',
    icon: renderIcon(PeopleOutline)
  },
  {
    label: t('layout.menu.doctors'),
    key: 'doctors',
    to: '/dashboard/doctors',
    icon: renderIcon(PeopleCircleOutline)
  },
  {
    label: t('layout.menu.profile'),
    key: 'profile',
    to: '/dashboard/profile',
    icon: renderIcon(PersonOutline)
  },
  {
    label: t('layout.menu.settings'),
    key: 'settings',
    to: '/dashboard/settings',
    icon: renderIcon(SettingsOutline)
  },
  {
    label: t('layout.menu.logout'),
    key: 'logout',
    to: '/logout',
    icon: renderIcon(LogOutOutline)
  }
])

const adminMenu = computed(() => [
  {
    label: t('layout.menu.dashboard'),
    key: 'dashboard',
    to: '/dashboard',
    icon: renderIcon(BookOutline)
  },
  {
    label: t('layout.menu.doctors'),
    key: 'doctors',
    to: '/dashboard/doctors',
    icon: renderIcon(PeopleOutline)
  },
  {
    label: t('layout.menu.profile'),
    key: 'profile',
    to: '/dashboard/profile',
    icon: renderIcon(PersonOutline)
  },
  {
    label: t('layout.menu.settings'),
    key: 'settings',
    to: '/dashboard/settings',
    icon: renderIcon(SettingsOutline)
  },
  {
    label: t('layout.menu.logout'),
    key: 'logout',
    to: '/logout',
    icon: renderIcon(LogOutOutline)
  }
])

const userMenu = computed(() => [
  {
    label: t('layout.menu.dashboard'),
    key: 'dashboard',
    to: '/dashboard',
    icon: renderIcon(BookOutline)
  },
  {
    label: t('layout.menu.profile'),
    key: 'profile',
    to: '/dashboard/profile',
    icon: renderIcon(PersonOutline)
  },
  {
    label: 'Referral Forms',
    key: 'referral',
    icon: renderIcon(DocumentTextOutline),
    path: 'https://deliveritpharmacy.com/referral-forms/',
    target: '_blank'
  },
  {
    label: t('layout.menu.settings'),
    key: 'settings',
    to: '/dashboard/settings',
    icon: renderIcon(SettingsOutline)
  },
  {
    label: t('layout.menu.logout'),
    key: 'logout',
    to: '/dashboard/logout',
    icon: renderIcon(LogOutOutline)
  }
])


const menuOptions = computed(() => {
  if (userStore.isSuperAdmin()) return superAdminMenu.value
  if (userStore.isAdmin()) return adminMenu.value
  return userMenu.value
})


function renderIcon(icon) {
  return () => h(NIcon, null, { default: () => h(icon) })
}

function handleMenuSelect(key) {
  const item = menuOptions.value.find(i => i.key === key)

  if (key === 'logout') {
    logout()
  } else if (item?.path) {
    window.open(item.path, item.target || '_self')
  } else if (item?.to) {
    router.push(item.to)
  }
}

const activeKey = ref(getActiveKeyFromRoute(route.path))

router.afterEach((to) => {
  activeKey.value = getActiveKeyFromRoute(to.path)
})

function getActiveKeyFromRoute(path) {
  if (path === '/dashboard') return 'dashboard'
  if (path.includes('/doctors')) return 'doctors'
  if (path.includes('/settings')) return 'settings'
  if (path.includes('/profile')) return 'profile'
  if (path.includes('/admins')) return 'admins'
  return ''
}

const menuThemeOverrides = {
  itemColorActive: '#2080f0',
  itemTextColorActive: '#ffffff',
  itemIconColorActive: '#ffffff',
  itemColorHover: '#4098fc',
  itemTextColorHover: '#ffffff',
  itemIconColorHover: '#ffffff',
  itemColorActiveHover: '#2080f0',
  itemTextColorActiveHover: '#ffffff',
  itemIconColorActiveHover: '#ffffff',
  itemColorActiveCollapsed: '#2080f0',
  itemTextColorActiveCollapsed: '#ffffff',
}

const message = useMessage()

const logout = async () => {
  try {
    await axios.post('/logout')
    localStorage.removeItem('access_token')
    userStore.clearUser()
    message.success('Logged out successfully')
    router.push('/')
  } catch (error) {
    console.error('Logout failed', error?.response?.data || error.message)
    message.error(t('layout.logoutError'))
  }
}
</script>

<style scoped>
.full-height-layout {
  min-height: 100vh;
}

:deep(.n-menu .n-menu-item-content) {
  padding-left: 18px !important;
}
</style>
