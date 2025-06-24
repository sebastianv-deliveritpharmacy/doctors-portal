import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'  // Now importing the default export
import naive from 'naive-ui'
import VueApexCharts from 'vue3-apexcharts'

const app = createApp(App)

app.use(createPinia())
app.use(naive)
app.use(router)  // Using router directly
app.use(VueApexCharts as any)

app.mount('#app')