<template>
  <div class="dashboard-container">
    <n-space vertical size="large">
      <!-- Greeting Section -->
      <n-card class="greeting-card">
        <div class="greeting-card-content">
          <!-- Left: Greeting -->
          <div class="greeting-left">
            <n-statistic label="" :value="`Hello, ${user.name}`" />
            <div class="subtext">Here you can see the status of your prescriptions</div>
          </div>

          <!-- Right: Need Help -->
          <div class="greeting-right">
            <span class="help-title">Need help? Contact us:</span>
            <ul class="help-list">
              <li>Phone: <a href="tel:+11231231233">832-939-8137</a></li>
              <li>Fax: 832 939 8128</li>
            </ul>
          </div>
        </div>
      </n-card>

      <!-- Search & Table -->
      <n-card>
        <n-space justify="space-between" align="center" style="margin-bottom: 16px">
          <n-input
            v-model:value="searchTerm"
            placeholder="Search prescriptions or patients..."
            clearable
            style="max-width: 300px"
          />
        </n-space>

        <div class="table-responsive">
          <n-data-table
            :columns="columns"
            :data="paginatedData"
            :bordered="true"
          />
        </div>

        <n-space justify="end" style="margin-top: 16px">
          <n-pagination
            v-model:page="currentPage"
            :page-size="pageSize"
            :item-count="filteredData.length"
            show-size-picker
            :page-sizes="[5, 10, 20]"
            @update:page-size="size => { pageSize = size; currentPage = 1 }"
          />
        </n-space>
      </n-card>
    </n-space>
  </div>
</template>

<script setup>
import { ref, onMounted, h, computed } from 'vue'
import {
  NCard,
  NStatistic,
  NSpace,
  useMessage,
  NDataTable,
  NTag,
  NInput,
  NPagination
} from 'naive-ui'
import { getCurrentUser } from '@/api/user'
import { fetchShipments } from '@/api/shipment'

const shipments = ref([])
const prescriptions = ref([])

const message = useMessage()
const user = ref({ name: '', email: '' })

const getUser = async () => {
  try {
    const response = await getCurrentUser()
    user.value = response.data
  } catch (error) {
    console.error('Failed to fetch user', error)
    message.error('Failed to fetch user data. Please login again.')
  }
}

const getShipments = async () => {
  try {
    const response = await fetchShipments()
    prescriptions.value = response.data.map((item, index) => ({
      id: item.id || index + 1,
      name: item.prescription_name || '—',
      status: item.status || 'unknown',
      date_shipped: item.date_shipped ? new Date(item.date_shipped).toLocaleString() : '—',
      delivered_at: item.delivered_at ? new Date(item.delivered_at).toLocaleString() : '-',
      patient_name: item.patient_name || '—',
      shipment_id: item.shipment_id || `-` // fallback/dummy value
    }))
  } catch (err) {
    console.error('Error fetching shipments:', err)
    message.error('Unable to load shipments.')
  }
}

onMounted(() => {
  const token = localStorage.getItem('access_token')
  if (token) {
    getUser()
    getShipments()
  }
})

const searchTerm = ref('')
const currentPage = ref(1)
const pageSize = ref(5)

const filteredData = computed(() => {
  if (!searchTerm.value) return prescriptions.value
  return prescriptions.value.filter(p =>
    p.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
    p.patient_name.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
})

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  const end = start + pageSize.value
  return filteredData.value.slice(start, end)
})

const columns = [
  {
    title: 'ID',
    key: 'id'
  },
    { title: 'Shipment ID', key: 'shipment_id' }, // ✅ New column

  {
    title: 'Patient',
    key: 'patient_name'
  },
  {
    title: 'Prescription',
    key: 'name'
  },
  {
    title: 'Status',
    key: 'status',
    render: (row) => {
      const status = row.status.toLowerCase()
      const statusColorMap = {
        active: 'success',
        delivered: 'success',
        shipped: 'info',
        'in transit': 'warning',
        pending: 'warning',
        returned: 'error',
        inactive: 'error',
        unknown: 'default'
      }
      const type = statusColorMap[status] || 'default'
      const text = row.status.charAt(0).toUpperCase() + row.status.slice(1)
      return h(NTag, { type }, { default: () => text })
    }
  },
  {
    title: 'Shipped At',
    key: 'shipped_at'
  },
  {
    title: 'Delivered At',
    key: 'delivered_at',
    render: row => row.delivered_at || '—'
  }
]
</script>

<style scoped>
.dashboard-container {
  padding: 24px;
}

.greeting-card {
  padding: 24px;
  background: linear-gradient(135deg, #f0f4ff, #dce7ff);
}

.greeting-card-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 16px;
}

.greeting-left {
  flex: 1;
  min-width: 200px;
}

.greeting-right {
  flex: 1;
  max-width: 200px;
  background: white;
  padding: 12px;
  border-radius: 8px;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
}

.help-title {
  font-weight: bold;
  font-size: 14px;
  display: block;
  margin-bottom: 8px;
}

.help-list {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 13px;
  color: #333;
}

.help-list li + li {
  margin-top: 4px;
}

.help-list a {
  color: #333;
  text-decoration: none;
}

.help-list a:hover {
  text-decoration: underline;
}

.subtext {
  margin-top: 8px;
  color: #888;
  font-size: 14px;
}

/* Responsive table wrapper */
.table-responsive {
  overflow-x: auto;
  width: 100%;
}

/* Ensure the table has minimum width so it can scroll */
.table-responsive ::v-deep(.n-data-table) {
  min-width: 800px;
}

/* Stack card content on small screens */
@media (max-width: 600px) {
  .greeting-card-content {
    flex-direction: column;
    align-items: stretch;
  }

  .greeting-right {
    max-width: 100%;
  }
}
</style>
