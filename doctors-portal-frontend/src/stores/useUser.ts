// stores/useUser.ts
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => {
    let user = null
    try {
      const raw = localStorage.getItem('user')
      user = raw && raw !== 'undefined' ? JSON.parse(raw) : null
    } catch (e) {
      console.warn('Failed to parse user from localStorage:', e)
      user = null
    }

    return {
      user: user as null | {
        name: string
        email: string
        role: string
      }
    }
  },

  actions: {
   setUser(userData: any) {
    const roleName = userData.roles?.[0]?.name || null
    this.user = {
      ...userData,
      role: roleName
    }
    localStorage.setItem('user', JSON.stringify(this.user))
  },

    clearUser() {
      this.user = null
      localStorage.removeItem('user')
    },

    isAdmin() {
      return this.user?.role === 'admin' || this.user?.role === 'super_admin'
    },

    isSuperAdmin() {
      return this.user?.role === 'super_admin'
    }
  }
})
