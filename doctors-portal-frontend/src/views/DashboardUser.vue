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
                  <li>
                    Phone: <a href="tel:+11231231233">832-939-8137</a>
                  </li>
                  <li>
                    Fax: 832 939 8128
                  </li>
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
  
          <n-data-table
            :columns="columns"
            :data="paginatedData"
            :bordered="true"
          />
  
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
  
  const message = useMessage()
  const user = ref({ name: '', email: '' })
  
  const getUser = async () => {
    try {
      console.log("asdasdaads")
      const response = await getCurrentUser()
      user.value = response.data
    } catch (error) {
      console.error('Failed to fetch user222', error)
      message.error('Failed to fetch user data222. Please login again.')
    }
  }
  
  onMounted(() => {
  const token = localStorage.getItem('access_token')
  if (token) {
    getUser()
  }
})
  
  const prescriptions = ref([
    {
      id: 1,
      name: 'Amoxicillin',
      status: 'active',
      started_at: '2025-04-30 08:15',
      activated_at: '2025-04-30 10:30',
      patient_name: 'John Smith'
    },
    {
      id: 2,
      name: 'Ibuprofen',
      status: 'inactive',
      started_at: '2025-04-29 14:00',
      activated_at: null,
      patient_name: 'Emily Davis'
    },
    {
      id: 3,
      name: 'Metformin',
      status: 'processing',
      started_at: '2025-04-30 09:00',
      activated_at: null,
      patient_name: 'Carlos Martinez'
    },
    {
      id: 4,
      name: 'Losartan',
      status: 'active',
      started_at: '2025-04-28 11:45',
      activated_at: '2025-04-28 15:00',
      patient_name: 'Alice Johnson'
    }
  ])
  
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
        let type = 'default'
        let text = row.status
  
        if (row.status === 'active') type = 'success'
        else if (row.status === 'inactive') type = 'error'
        else if (row.status === 'processing') type = 'warning'
  
        return h(NTag, { type }, { default: () => text.charAt(0).toUpperCase() + text.slice(1) })
      }
    },
    {
      title: 'Started At',
      key: 'started_at'
    },
    {
      title: 'Activated At',
      key: 'activated_at',
      render: row => row.activated_at || '—'
    }
  ]
  </script>
  
  <style scoped>
  .dashboard-container {
    padding: 24px;
  }
  
  .greeting-card {
    padding: 24px;
    border-bottom: solid 1px #ebebeb;
    padding: 16px;
    background: linear-gradient(135deg, #f0f4ff, #dce7ff);
    text-align: left;
  }
  
  .subtext {
    margin-top: 8px;
    color: #888;
    font-size: 14px;
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
  box-shadow: 0 0 8px rgba(0,0,0,0.05);
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


  </style>