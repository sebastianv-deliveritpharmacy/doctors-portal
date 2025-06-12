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
        is_admin: number
      }
    }
  },

  actions: {
    setUser(userData: any) {
      this.user = userData
      localStorage.setItem('user', JSON.stringify(userData))
    },

    clearUser() {
      this.user = null
      localStorage.removeItem('user')
    },

    isAdmin() {
      return this.user?.is_admin == 1
    }
  }
})
