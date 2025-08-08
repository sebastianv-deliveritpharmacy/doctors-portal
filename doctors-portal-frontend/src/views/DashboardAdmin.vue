<template>
  <div class="dashboard-container">
    <div class="greeting-card">
      <h2 class="greeting-text">{{ $t('dashboard.greeting', { name: user.name }) }}</h2>
      <div class="subtext">{{ $t('dashboard.subtext') }}</div>
      <div class="stats-overview">
        <div v-for="card in cards" :key="card.title" class="stat-card">
          <div class="card-top">
            <div>
              <div class="stat-title">{{ card.title }}</div>
              <div class="stat-value">{{ card.value }}</div>
              <div
                class="stat-change"
                :class="{
                  positive: card.change > 0,
                  negative: card.change < 0,
                  neutral: card.change === 0
                }"
              >
                {{ card.change > 0 ? '↑' : card.change < 0 ? '↓' : '+' }}
                {{ Math.abs(card.change).toFixed(2) }}%
              </div>
            </div>
            <div class="stat-icon" :style="{ backgroundColor: card.iconBg }">
              <svg
                v-if="card.icon === 'checkmark'"
                xmlns="http://www.w3.org/2000/svg"
                fill="white"
                viewBox="0 0 24 24"
                width="20"
                height="20"
              >
                <path d="M9 16.17l-3.88-3.88L4 13.41l5 5 9-9-1.41-1.41z" />
              </svg>
              <component v-else :is="card.icon" class="icon" />
            </div>
          </div>
          <a :href="card.link" class="stat-link">{{ card.linkText }}</a>
        </div>
      </div>
    </div>

    <div class="chart-card">
      <ChartComponent />
      <PieChart />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { DollarSign, ShoppingBag, Users, CreditCard } from 'lucide-vue-next'
import ChartComponent from '@/components/ChartComponent.vue'
import PieChart from '@/components/PieChart.vue'
import { getCurrentUser } from '@/api/user'
import { fetchDashboardStats } from '@/api/dashboard'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const user = ref({ name: '', email: '' })

const cards = ref([
  {
    title: t('dashboard.cards.doctors.title'),
    value: 0,
    change: 0,
    icon: Users,
    iconBg: '#00B140',
    link: '#',
    linkText: t('dashboard.cards.doctors.linkText')
  },
  {
    title: t('dashboard.cards.prescriptions.title'),
    value: 0,
    change: 0,
    icon: ShoppingBag,
    iconBg: '#0073e6',
    link: '#',
    linkText: t('dashboard.cards.prescriptions.linkText')
  },
  {
    title: t('dashboard.cards.completed.title'),
    value: 0,
    change: 0,
    icon: 'checkmark',
    iconBg: '#f59e0b',
    link: '#',
    linkText: t('dashboard.cards.completed.linkText')
  },
  {
    title: t('dashboard.cards.month.title'),
    value: 0,
    change: 0,
    icon: CreditCard,
    iconBg: '#9b59b6',
    link: '#',
    linkText: t('dashboard.cards.month.linkText')
  }
])

const getUser = async () => {
  try {
    const response = await getCurrentUser()
    user.value = response.data
  } catch (error) {
    console.error('Failed to fetch user', error)
  }
}

const loadDashboardStats = async () => {
  try {
    const res = await fetchDashboardStats()
    const stats = res.data
    cards.value[0].value = stats.active_doctors.count
    cards.value[0].change = stats.active_doctors.change
    cards.value[1].value = stats.prescriptions_today.count
    cards.value[1].change = stats.prescriptions_today.change
    cards.value[2].value = stats.completed_today.count
    cards.value[2].change = stats.completed_today.change
    cards.value[3].value = stats.this_month.count
    cards.value[3].change = stats.this_month.change
  } catch (err) {
    console.error('Failed to load dashboard stats', err)
  }
}

onMounted(() => {
  const token = localStorage.getItem('access_token')
  if (token) {
    getUser()
    loadDashboardStats()
  }
})
</script>

<style scoped>
.dashboard-container {
  padding: 24px;
  background: #f7f8fc;
}

.greeting-card {
  padding: 20px;
  background: linear-gradient(135deg, #f0f4ff, #dce7ff);
  border-radius: 10px;
  margin-bottom: 20px;
  border: 1px solid #ebebeb;
}

.greeting-text {
  margin: 0;
  font-size: 24px;
  font-weight: bold;
}

.subtext {
  margin-top: 8px;
  color: #888;
  font-size: 14px;
}

.stats-overview {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-bottom: 30px;
  margin-top: 30px;
}

.stat-card {
  flex: 1 1 calc(25% - 20px);
  background-color: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.card-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.stat-title {
  font-size: 12px;
  font-weight: 500;
  color: #888;
  text-transform: uppercase;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  margin: 4px 0;
  color: #222;
}

.stat-change {
  font-size: 13px;
  font-weight: 600;
}

.stat-change.positive {
  color: #00a76f;
}

.stat-change.negative {
  color: #e53935;
}

.stat-change.neutral {
  color: #999;
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.icon {
  width: 20px;
  height: 20px;
}

.stat-link {
  margin-top: 14px;
  font-size: 14px;
  font-weight: 500;
  color: #3366cc;
  text-decoration: none;
}

.stat-link:hover {
  text-decoration: underline;
}

.chart-card {
  padding: 24px;
  min-height: 300px;
  background-color: white;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Responsive layout */
@media (max-width: 1024px) {
  .stat-card {
    flex: 1 1 calc(50% - 20px);
  }
}

@media (max-width: 600px) {
  .stat-card {
    flex: 1 1 100%;
  }
}
</style>
