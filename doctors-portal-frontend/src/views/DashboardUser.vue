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
            <div class="help-container">
              <span class="help-title">Need help? Contact us:</span>
              <ul class="help-list">
                <li>Phone: <a href="tel:+11231231233">832-939-8137</a></li>
                <li>Fax: 832 939 8128</li>
              </ul>
            </div>
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

        <div class="table-container">
          <div class="table-responsive">
            <n-data-table
              :columns="columns"
              :data="prescriptions"
              :bordered="true"
              :loading="isLoading"
              class="prescription-table"
            />
          </div>
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
import { ref, onMounted, h } from 'vue'
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
      patient_name: item.patient_name || '-',
      date_of_birth: item.date_of_birth || '-',
      insurance: item.insurance || '-',
      city: item.city || '-',
      source: item.source || '-',
      arrived_to_office_date: item.arrived_to_office_date || '-',
      shipment_id: item.shipment_id || '-'
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

const columns = [
  { 
    title: 'ID', 
    key: 'id',
    className: 'hide-on-mobile',
    width: 80
  },
  { 
    title: 'Patient', 
    key: 'patient_name',
    width: 150
  },
  { 
    title: 'Prescription', 
    key: 'name',
    width: 150
  },
  { 
    title: 'DOB', 
    key: 'date_of_birth',
    className: 'hide-on-mobile',
    width: 120
  },
  { 
    title: 'Insurance', 
    key: 'insurance',
    className: 'hide-on-mobile',
    width: 150
  },
  { 
    title: 'City', 
    key: 'city',
    className: 'hide-on-mobile',
    width: 120
  },
  { 
    title: 'Source', 
    key: 'source',
    className: 'hide-on-mobile',
    width: 120
  },
  { 
    title: 'Arrived', 
    key: 'arrived_to_office_date',
    render: (row) => row.arrived_to_office_date 
      ? new Date(row.arrived_to_office_date).toLocaleDateString() 
      : '-',
    width: 120
  },
  {
    title: 'Status',
    key: 'status',
    render: (row) => {
      const statusConfig = {
        confirmed_prescription_received: {
          label: 'Confirmed',
          color: '#e6f7ff',
          textColor: '#1890ff'
        },
        data_entry: {
          label: 'Data Entry',
          color: '#f6ffed',
          textColor: '#52c41a'
        },
        intake_processing: {
          label: 'Intake',
          color: '#fff7e6',
          textColor: '#fa8c16'
        },
        insurance_verification: {
          label: 'Insurance',
          color: '#fff1f0',
          textColor: '#f5222d'
        },
        copay_assistant: {
          label: 'Co-pay',
          color: '#f6faff',
          textColor: '#096dd9'
        },
        production: {
          label: 'Production',
          color: '#f9f0ff',
          textColor: '#722ed1'
        },
        collecting_copay: {
          label: 'Collecting',
          color: '#fff0f6',
          textColor: '#c41d7f'
        },
        delivery_confirmation: {
          label: 'Delivered',
          color: '#e6fffb',
          textColor: '#13c2c2'
        },
        default: {
          color: '#f0f0f0',
          textColor: '#595959'
        }
      }

      const config = statusConfig[row.status] || {
        ...statusConfig.default,
        label: row.status || 'Unknown'
      }

      return h(NTag, {
        style: {
          backgroundColor: config.color,
          color: config.textColor,
          fontWeight: '500',
          padding: '0 8px',
          whiteSpace: 'nowrap'
        },
        bordered: false
      }, { default: () => config.label })
    },
    width: 120
  }
]
</script>

<style scoped>
.dashboard-container {
  padding: 16px;
}

.greeting-card {
  padding: 16px;
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
  flex: 0 0 auto;
}

.help-container {
  background: white;
  padding: 12px;
  border-radius: 8px;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
  min-width: 200px;
}

.help-title {
  font-weight: bold;
  display: block;
  margin-bottom: 8px;
}

.help-list {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 14px;
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
  color: #666;
  font-size: 14px;
}

.table-container {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  margin-bottom: 16px;
}

.table-responsive {
  min-width: 800px;
}

.prescription-table :deep(.n-data-table-td) {
  white-space: nowrap;
  padding: 12px 8px !important;
}

/* Mobile styles */
@media (max-width: 768px) {
  .dashboard-container {
    padding: 12px;
  }
  
  .greeting-card-content {
    flex-direction: column;
  }
  
  .greeting-right {
    width: 100%;
    margin-top: 16px;
  }
  
  .help-container {
    width: 100%;
  }
  
  .hide-on-mobile {
    display: none;
  }
  
  .table-responsive {
    min-width: 600px;
  }
  
  .prescription-table :deep(.n-data-table-td) {
    padding: 8px 6px !important;
    font-size: 14px;
  }
}

/* Scrollbar styling */
.table-container::-webkit-scrollbar {
  height: 6px;
}

.table-container::-webkit-scrollbar-thumb {
  background: #d9d9d9;
  border-radius: 3px;
}
</style>