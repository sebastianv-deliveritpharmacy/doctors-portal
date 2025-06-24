<template>
  <div class="dashboard-container">
    <n-space vertical size="large">
      <!-- Greeting Section -->
      <n-card class="greeting-card">
        <div class="greeting-card-content">
          <div class="greeting-left">
            <n-statistic label="" :value="`Hello, ${user.name}`" />
            <div class="subtext">Here you can see the status of your prescriptions</div>
          </div>

          <div class="greeting-right">
            <span class="help-title">Need help? Contact us:</span>
            <ul class="help-list">
              <li>Phone: <a href="tel:+11231231233">832-939-8137</a></li>
              <li>Fax: 832 939 8128</li>
            </ul>
          </div>
        </div>
      </n-card>

      <!-- Search & Table Section -->
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
            :data="prescriptions"
            :bordered="true"
            :loading="isLoading"
          />
        </div>

        <n-space justify="end" style="margin-top: 16px">
          <n-pagination
            v-model:page="currentPage"
            :page-size="pageSize"
            :item-count="totalItems"
            show-size-picker
            :page-sizes="[5, 10, 20]"
            @update:page="getShipments"
            @update:page-size="size => { pageSize = size; currentPage = 1; getShipments(); }"
          />

        </n-space>
      </n-card>
    </n-space>
  </div>
</template>

<script setup>
import { ref, onMounted, h, watch } from 'vue'
import {
  NCard, NStatistic, NSpace, useMessage, NDataTable, NTag, NInput, NPagination
} from 'naive-ui'
import { getCurrentUser } from '@/api/user'
import { fetchShipments } from '@/api/shipment'

const message = useMessage()
const user = ref({ name: '', email: '' })
const prescriptions = ref([])
const isLoading = ref(false)

const currentPage = ref(1)
const pageSize = ref(20)
const totalItems = ref(0)
const searchTerm = ref('')

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
    isLoading.value = true
    const response = await fetchShipments(currentPage.value, pageSize.value, searchTerm.value)

    prescriptions.value = response.data.data.map(item => ({
      id: item.id,
      name: item.prescription_name || '-',
      status: item.status || 'unknown',
      date_shipped: item.date_shipped ? new Date(item.date_shipped).getTime() : null,
      delivered_at: item.delivered_at ? new Date(item.delivered_at).getTime() : null,
      patient_name: item.patient_name || '-',
      shipment_id: item.shipment_id || '-',
      rx_number: item.rx_number || '-'
    }))
    totalItems.value = response.data.total
  } catch (err) {
    console.error('Error fetching shipments:', err)
    message.error('Unable to load shipments.')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  const token = localStorage.getItem('access_token')
  if (token) {
    getUser()
    getShipments()
  }
})


watch(searchTerm, () => {
  currentPage.value = 1
  getShipments()
})

const columns = [
  { title: 'ID', key: 'id' },
  { title: 'Shipment ID', key: 'shipment_id' },
  { title: 'Patient', key: 'patient_name' },
  { title: 'Prescription', key: 'name' },
  {
    title: 'Status',
    key: 'status',
    render: (row) => {
      const statusMap = {
        confirmed_prescription_received: 'info',
        data_entry: 'default',
        intake_processing: 'warning',
        insurance_verification: 'warning',
        copay_assistant: 'info',
        production: 'primary',
        collecting_copay: 'warning',
        delivery_confirmation: 'success'
      }

      const labels = {
        confirmed_prescription_received: 'Confirmed Prescription Received',
        data_entry: 'Data Entry',
        intake_processing: 'Intake Processing',
        insurance_verification: 'Insurance Verification & Authorization',
        copay_assistant: 'Co-pay Assistant or Foundation Assistant',
        production: 'Production',
        collecting_copay: 'Collecting Co-pay',
        delivery_confirmation: 'Delivery Confirmation'
      }

      const type = statusMap[row.status] || 'default'
      const label = labels[row.status] || row.status

      return h(NTag, { type }, { default: () => label })
    }
  },
  {
    title: 'Shipped At',
    key: 'date_shipped',
    render: (row) => row.date_shipped
      ? new Date(row.date_shipped).toLocaleString()
      : '—'
  },
  {
    title: 'Delivered At',
    key: 'delivered_at',
    render: (row) => row.delivered_at
      ? new Date(row.delivered_at).toLocaleString()
      : '—'
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
.table-responsive {
  overflow-x: auto;
  width: 100%;
}
.table-responsive ::v-deep(.n-data-table) {
  min-width: 800px;
}
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
