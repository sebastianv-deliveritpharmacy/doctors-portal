<template>
  <div class="dashboard-container">
    <n-space vertical size="large">
      <!-- Greeting Section -->
      <n-card class="greeting-card">
        <div class="greeting-card-content">
          <div class="greeting-left">
            <n-statistic label="" :value="`You are currently viewing ${doctorName}`" />
            <div class="subtext">These are the prescriptions associated with this doctor</div>
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

      <!-- Table Section -->
      <n-card>
        <n-space justify="space-between" align="center" style="margin-bottom: 16px">
          <n-input
            v-model:value="searchTerm"
            placeholder="Search prescriptions or patients..."
            clearable
            style="max-width: 300px"
          />
          <n-space justify="space-between" align="center">
            <n-button type="primary"  @click="$router.push('/dashboard/doctors')">
              Go Back
            </n-button>
            <n-button type="primary" @click="openAddModal">
              Add Prescription
            </n-button>
          </n-space>
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
            @update:page-size="size => { pageSize = size; currentPage = 1; getPrescriptions() }"
            @update:page="page => { currentPage = page; getPrescriptions() }"
          />
        </n-space>
      </n-card>

      <!-- Add/Edit Modal -->
      <n-modal
        v-model:show="showAddEditModal"
        preset="card"
        style="width: 500px;"
      >
        <template #header>
            <h2>{{ editingPrescription.id ? 'Edit Prescription' : 'Create Prescription' }}</h2>
        </template>

        <n-form>
            <n-form-item label="Patient Name">
                <n-input v-model:value="editingPrescription.patient_name" placeholder="Patient Name" />
            </n-form-item>

            <n-form-item label="Prescription Name">
                <n-input v-model:value="editingPrescription.name" placeholder="Prescription Name" />
            </n-form-item>

            <n-form-item label="Rx Number">
                <n-input v-model:value="editingPrescription.rx_number" placeholder="Rx Number" />
            </n-form-item>

            <n-form-item label="Shipment ID">
                <n-input v-model:value="editingPrescription.shipment_id" placeholder="Shipment ID" />
            </n-form-item>

            <n-form-item label="Status" path="status">
                <n-select
                v-model:value="editingPrescription.status"
                :options="statusOptions"
                placeholder="Select a status"
                clearable
                />
            </n-form-item>

            <n-grid :cols="2" :x-gap="12">
                <n-grid-item>
                    <n-form-item label="Date Shipped">
                    <n-date-picker
                    v-model:value="editingPrescription.date_shipped"
                    type="datetime"
                    value-format="yyyy-MM-dd HH:mm:ss"
                    placeholder="Select date & time"
                    clearable
                  />

                    </n-form-item>
                </n-grid-item>
                <n-grid-item>
                    <n-form-item label="Deliverted At">
                    <n-date-picker
                      v-model:value="editingPrescription.delivered_at"
                      type="datetime"
                      value-format="yyyy-MM-dd HH:mm:ss"
                      placeholder="Select date & time"
                      clearable
                    />

                    </n-form-item>
                </n-grid-item>
            </n-grid>

            <n-space justify="end" class="mt-4">
                <n-button @click="showAddEditModal = false">Cancel</n-button>
                <n-button type="primary" @click="savePrescription">Save</n-button>
            </n-space>
        </n-form>

      </n-modal>
    </n-space>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, h, watch } from 'vue'
import { useRoute } from 'vue-router'
import {
  NCard, NStatistic, NSpace, useMessage,
  NDataTable, NTag, NInput, NPagination, NModal,
  NForm, NFormItem, NButton, NSelect,
  NDatePicker, NGrid
} from 'naive-ui'

import { fetchShipmentsByDoctor, createShipment, updateShipment } from '@/api/shipment'

const route = useRoute()
const message = useMessage()

const doctorId = route.params.id
const doctorName = ref('Doctor')
const prescriptions = ref([])
const totalItems = ref(0)

const showAddEditModal = ref(false)
const editingPrescription = ref({
  id: null,
  name: '',
  status: '',
  patient_name: '',
  shipment_id: '',
  rx_number: '',
  date_shipped: null,
  delivered_at: null,
  updated_at: null
})

const isLoading = ref(false)

const searchTerm = ref('')
const currentPage = ref(1)
const pageSize = ref(10)

watch([searchTerm, currentPage, pageSize], () => {
  getPrescriptions()
})

const statusOptions = [
  { label: 'Confirmed Prescription Received', value: 'confirmed_prescription_received' },
  { label: 'Data Entry', value: 'data_entry' },
  { label: 'Intake Processing', value: 'intake_processing' },
  { label: 'Insurance Verification & Authorization', value: 'insurance_verification' },
  { label: 'Co-pay Assistant or Foundation Assistant', value: 'copay_assistant' },
  { label: 'Production', value: 'production' },
  { label: 'Collecting Co-pay', value: 'collecting_copay' },
  { label: 'Delivery Confirmation', value: 'delivery_confirmation' }
]

const openAddModal = () => {
  editingPrescription.value = {
    id: null,
    name: '',
    status: '',
    patient_name: '',
    shipment_id: '',
    rx_number: '',
    date_shipped: null,
    activated_at: null
  }
  showAddEditModal.value = true
}

const openEditModal = (row) => {
  editingPrescription.value = {
    ...row
  }
  showAddEditModal.value = true
}

const savePrescription = async () => {
  try {
    if (
      editingPrescription.value.date_shipped &&
      editingPrescription.value.delivered_at &&
      new Date(editingPrescription.value.delivered_at).getTime() <= new Date(editingPrescription.value.date_shipped).getTime()
    ) {
      message.error('Delivered time must be after the shipped time.')
      return
    }

    const payload = {
      user_id: doctorId,
      patient_name: editingPrescription.value.patient_name,
      prescription_name: editingPrescription.value.name,
      status: editingPrescription.value.status,
      rx_number: editingPrescription.value.rx_number,
      shipment_id: editingPrescription.value.shipment_id || null,
      date_shipped: editingPrescription.value.date_shipped
        ? formatDateForBackend(editingPrescription.value.date_shipped)
        : null,
      delivered_at: editingPrescription.value.delivered_at
        ? formatDateForBackend(editingPrescription.value.delivered_at)
        : null
    }

    if (editingPrescription.value.id) {
      await updateShipment(editingPrescription.value.id, payload)
      message.success('Shipment updated!')
    } else {
      await createShipment(payload)
      message.success('Shipment created!')
    }

    showAddEditModal.value = false
    getPrescriptions()
  } catch (err) {
    console.error('Error saving shipment:', err)
    message.error('Could not save shipment.')
  }
}

function formatDateForBackend(timestamp) {
  const date = new Date(timestamp)
  const pad = (n) => n.toString().padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`
}

const getPrescriptions = async () => {
  try {
    isLoading.value = true
    const response = await fetchShipmentsByDoctor(doctorId, currentPage.value, pageSize.value, searchTerm.value)
    doctorName.value = response.doctorName || 'Doctor'
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
    message.error('Unable to load shipment updates.')
  } finally {
    isLoading.value = false
  }
}

onMounted(getPrescriptions)

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
    render: (row) => row.date_shipped ? new Date(row.date_shipped).toLocaleString() : '—'
  },
  {
    title: 'Delivered At',
    key: 'delivered_at',
    render: (row) => row.delivered_at ? new Date(row.delivered_at).toLocaleString() : '—'
  },
  {
    title: 'Actions',
    key: 'actions',
    render: (row) => h('div', { style: 'display: flex; gap: 8px;' }, [
      h(NButton, {
        size: 'small',
        type: 'info',
        secondary: true,
        onClick: () => openEditModal(row)
      }, { default: () => 'Edit' })
    ])
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
button.edit-btn-table {
  background: red!important;
  border: none;
  color: #2080f0;
  cursor: pointer;
  font-size: 14px;
}
 button.edit-btn-table:hover {
  text-decoration: underline;
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