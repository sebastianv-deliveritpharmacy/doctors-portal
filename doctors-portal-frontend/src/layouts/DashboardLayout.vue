<template>
  <n-layout has-sider class="full-height-layout">
    <n-layout-sider
      class="full-height-layout"
      bordered
      collapse-mode="width"
      :collapsed-width="64"
      :width="240"
      show-trigger
      v-model:collapsed="isCollapsed"
      style="position: relative;"
    >
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
          Partnered With
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
import { ref, computed } from 'vue'
import { h } from 'vue'
import { NIcon, useMessage } from 'naive-ui'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { BookOutline, PersonOutline, PeopleOutline, LogOutOutline, SettingsOutline, DocumentTextOutline } from '@vicons/ionicons5'
import axios from '@/api/axios'
import { useUserStore } from '@/stores/useUser'

const userStore = useUserStore()
const isCollapsed = ref(false)

const router = useRouter()
const route = useRoute()

const adminMenu = [
  {
    label: 'Dashboard',
    key: 'dashboard',
    to: '/dashboard',
    icon: renderIcon(BookOutline)
  },
  {
    label: 'Doctors',
    key: 'doctors',
    to: '/dashboard/doctors',
    icon: renderIcon(PeopleOutline)
  },
  {
    label: 'Profile',
    key: 'profile',
    to: '/dashboard/profile',
    icon: renderIcon(PersonOutline)
  },
  {
    label: 'Settings',
    key: 'settings',
    to: '/dashboard/settings',
    icon: renderIcon(SettingsOutline)
  },
  {
    label: 'Logout',
    key: 'logout',
    to: '/logout',
    icon: renderIcon(LogOutOutline)
  }
]

const userMenu = [
  {
    label: 'Dashboard',
    key: 'dashboard',
    to: '/dashboard',
    icon: renderIcon(BookOutline)
  },
  {
    label: 'Profile',
    key: 'profile',
    to: '/dashboard/profile',
    icon: renderIcon(PersonOutline)
  },
  {
    label: 'Referral Forms',
    key: 'referral',
    icon: renderIcon(DocumentTextOutline),
    path: 'https://deliveritpharmacy.com/referral-forms/',
    target: '_blank' // optional: opens in a new tab
  },
  {
    label: 'Logout',
    key: 'logout',
    to: '/dashboard/logout',
    icon: renderIcon(LogOutOutline)
  }
]


const menuOptions = computed(() =>
  userStore.isAdmin() ? adminMenu : userMenu
)

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

// Auto update active key when route changes
router.afterEach((to) => {
  activeKey.value = getActiveKeyFromRoute(to.path)
})

// Helper to extract the key from the current route
function getActiveKeyFromRoute(path) {
  if (path === '/dashboard') return 'dashboard'
  if (path.includes('/doctors')) return 'doctors'
  if (path.includes('/settings')) return 'settings'
  if (path.includes('/profile')) return 'profile'
  return ''
}



// Define your theme overrides here
const menuThemeOverrides = {
  itemColorActive: '#2080f0',            // Background when active
  itemTextColorActive: '#ffffff',         // Text color when active
  itemIconColorActive: '#ffffff',         // Icon color when active
  itemColorHover: '#4098fc',              // Background when hovering (not active)
  itemTextColorHover: '#ffffff',          // Text color when hovering
  itemIconColorHover: '#ffffff',          // Icon color when hovering
  itemColorActiveHover: '#2080f0',        // 🔥 Background stay same while hover active
  itemTextColorActiveHover: '#ffffff',    // 🔥 Text stay white while hovering active
  itemIconColorActiveHover: '#ffffff',    // 🔥 Icon stay white while hovering active
  itemColorActiveCollapsed: '#2080f0',      // 🔥 Active background when collapsed
  itemTextColorActiveCollapsed: '#ffffff',  // 🔥 Text color when collapsed and active
}

const message = useMessage()

const logout = async () => {
  try {
    await axios.post('/logout'); // 👈 Call your Laravel logout route
    localStorage.removeItem('access_token'); // Clear token
    userStore.clearUser() // 👈 reset user
    message.success('Logged out successfully');
    router.push('/'); // Redirect to login page
    return;
  } catch (error) {
    console.error('Logout failed', error?.response?.data || error.message);
    message.error('Failed to logout');
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