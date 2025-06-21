import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import { useUserStore } from '../stores/useUser'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/Home.vue'),
    meta: { public: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/LoginForm.vue'),
    meta: { public: true }
  },
  {
    path: '/dashboard',
    name: 'DashboardLayout',
    component: () => import('@/layouts/DashboardLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'DashboardHome',
        component: () => import('@/views/DashboardRouter.vue') // 👈 new wrapper
      },
      {
        path: 'profile',
        name: 'Profile',
        component: () => import('@/views/DashboardProfile.vue')
      },
       {
        path: 'users',
        name: 'Users',
        component: () => import('@/views/DashboardAllUsers.vue')
      },
      {
        path: 'doctors',
        name: 'Doctors',
        component: () => import('@/views/DashboardDoctors.vue'),
        meta: { requiresAdmin: true } // 🔐 Only for admins
      },
      {
        path: 'settings',
        name: 'Settings',
        component: () => import('@/views/DashboardSettings.vue'),
        meta: { requiresAdmin: true } // 🔐 Only for admins

      },
      {
        path: 'doctors/:id/prescriptions',
        component: () => import('@/views/DoctorPrescriptionsView.vue'),
        meta: { requiresAdmin: true } // 🔐 Only for admins
      } 
    ]
    
  },
  {
    path: '/email-verified',
    name: 'Email Verified',
    component: () => import('@/views/EmailVerified.vue'),
  },
  {
    path: '/verify-2fa',
    name: 'Verify 2FA',
    component: () => import('@/views/Verify2Fa.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})


router.beforeEach(async (to, _from, next) => {
  const token = localStorage.getItem('access_token')
  const userStore = useUserStore()

  if (to.meta.requiresAuth) {
    if (!token) {
      return next('/login')
    }

    // Fetch user if not already in store
    if (!userStore.user) {
      try {
        // await userStore.fetchUser() // or userStore.setUser() depending on your store
      } catch (err) {
        localStorage.removeItem('access_token')
        return next('/login')
      }
    }
  }

  next()
})





export default router
