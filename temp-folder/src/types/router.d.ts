import 'vue-router'

declare module 'vue-router' {
  interface RouteMeta {
    // Add your custom meta fields
    public?: boolean
    requiresAuth?: boolean
    title?: string
  }
}