<template>
  <div class="dashboard-container">
    <n-space vertical size="large">
      <!-- Greeting Section -->
      <n-card class="greeting-card">
        <div class="greeting-card-content">
          <div class="greeting-left">
            <n-statistic label="" :value="`You are currently viewing ${doctorName}`" />
            <div class="subtext">  These prescriptions are linked to this doctor and refresh every 30 minutes from our system.</div>
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
            @update:page-size="size => { pageSize = size; currentPage = 1; getPrescriptions() }"
            @update:page="page => { currentPage = page; getPrescriptions() }"
          />
        </n-space>
      </n-card>
      <!-- Add/Edit Modal -->
      <!-- Add/Edit Modal -->
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
    <!-- Patient Name + DOB (same row) -->
    <n-grid :cols="24" :x-gap="12">
      <n-form-item-gi :span="12" label="Patient Name">
        <n-input v-model:value="editingPrescription.patient_name" placeholder="Patient Name" />
      </n-form-item-gi>
      <n-form-item-gi :span="12" label="Date of Birth">
        <n-date-picker
          v-model:value="editingPrescription.date_of_birth"
          type="date"
          value-format="yyyy-MM-dd"
          placeholder="Select date"
          clearable
        />
      </n-form-item-gi>
    </n-grid>

    <!-- Prescription Name + Source (same row) -->
    <n-grid :cols="24" :x-gap="12">
      <n-form-item-gi :span="12" label="Prescription Name">
        <n-input v-model:value="editingPrescription.name" placeholder="Prescription Name" />
      </n-form-item-gi>
      <n-form-item-gi :span="12" label="Source">
        <n-input v-model:value="editingPrescription.source" placeholder="Source" />
      </n-form-item-gi>
    </n-grid>

    <!-- Insurance + City (same row) -->
    <n-grid :cols="24" :x-gap="12">
      <n-form-item-gi :span="12" label="Insurance">
        <n-input v-model:value="editingPrescription.insurance" placeholder="Insurance Provider" />
      </n-form-item-gi>
      <n-form-item-gi :span="12" label="City">
        <n-input v-model:value="editingPrescription.city" placeholder="City" />
      </n-form-item-gi>
    </n-grid>

    <!-- Status (expanded text area) -->
    <n-form-item label="Status">
      <n-input
        v-model:value="editingPrescription.status"
        placeholder="Enter status manually"
        type="textarea"
        :autosize="{
          minRows: 3,
          maxRows: 6
        }"
        style="min-height: 100px;"
      />
    </n-form-item>

    <!-- Arrived to Office Date (full width) -->
    <n-form-item label="Arrived to Office Date">
      <n-date-picker
        v-model:value="editingPrescription.arrived_to_office_date"
        type="datetime"
        value-format="yyyy-MM-dd HH:mm:ss"
        placeholder="Select date"
        clearable
      />
    </n-form-item>

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
    date_of_birth: null,
    insurance: '',
    city: '',
    source: '',
    arrived_to_office_date: null,
    arrived_to_office_time: ''
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
      date_of_birth: null,
      insurance: '',
      city: '',
      source: '',
      arrived_to_office_date: null,
      arrived_to_office_time: ''
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
      const payload = {
        user_id: doctorId,
        patient_name: editingPrescription.value.patient_name,
        prescription_name: editingPrescription.value.name,
        status: editingPrescription.value.status,
        date_of_birth: editingPrescription.value.date_of_birth ? formatDateForBackend(editingPrescription.value.date_of_birth)
          : null,
        insurance: editingPrescription.value.insurance,
        city: editingPrescription.value.city,
        source: editingPrescription.value.source,
        arrived_to_office_date: editingPrescription.value.arrived_to_office_date
          ? formatDateForBackend(editingPrescription.value.arrived_to_office_date)
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

  const getPrescriptions = async () => {
    try {
      isLoading.value = true
      const response = await fetchShipmentsByDoctor(doctorId, currentPage.value, pageSize.value, searchTerm.value)
      doctorName.value = response.data.user_name || 'Doctor'
      prescriptions.value = response.data.data.data.map(item => ({
        id: item.id,
        name: item.prescription_name || '-',
        status: item.status || 'unknown',
        patient_name: item.patient_name || '-',
        date_of_birth: item.date_of_birth || '-',
        insurance: item.insurance || '-',
        city: item.city || '-',
        source: item.source || '-',
        arrived_to_office_date: item.arrived_to_office_date || '-',
        arrived_to_office_time: item.arrived_to_office_time || '-'
      }))
      totalItems.value = response.data.total
    } catch (err) {
      console.error('Error fetching shipments:', err)
      message.error('Unable to load shipment updates.')
    } finally {
      isLoading.value = false
    }
  }

  function formatDateForBackend(timestamp) {
    if (!timestamp) return null
    const date = new Date(timestamp)
    const pad = (n) => n.toString().padStart(2, '0')
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`
  }


  onMounted(getPrescriptions)

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
    width: 120,
    render: (row) => row.date_of_birth || '-'
  },
  { 
    title: 'Insurance', 
    key: 'insurance',
    className: 'hide-on-mobile',
    width: 120
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
    width: 150,
    render: (row) => row.arrived_to_office_date 
      ? new Date(row.arrived_to_office_date).toLocaleString() 
      : '-'
  },
  {
    title: 'Status',
    key: 'status',
    width: 180,
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
    }
  },
  {
    title: 'Actions',
    key: 'actions',
    width: 100,
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

.table-container {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
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
    padding: 16px;
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
  
  .greeting-card-content {
    flex-direction: column;
  }
  
  .greeting-right {
    width: 100%;
    margin-top: 16px;
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