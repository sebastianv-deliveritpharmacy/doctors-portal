<template>
  <n-card title="Manage Admins">
    <!-- Search + Add -->
    <n-space justify="space-between" align="center" class="mb-4">
      <n-input
        v-model:value="searchTerm"
        placeholder="Search users..."
        clearable
        style="max-width: 300px"
      />
      <n-button type="primary" @click="showAddUserModal = true">
        Add User
      </n-button>
    </n-space>

    <!-- Table -->
    <div class="table-responsive">
      <n-data-table
        :columns="columns"
        :data="filteredUsers"
        :loading="loading"
        :bordered="true"
        striped
      />
    </div>

    <!-- Pagination -->
    <n-space justify="end" class="mt-4">
      <n-pagination
        v-model:page="currentPage"
        v-model:page-size="pageSize"
        :item-count="totalFiltered"
        show-size-picker
        :page-sizes="[5, 10, 20]"
      />
    </n-space>

    <!-- Modal -->
    <n-modal
      v-model:show="showAddUserModal"
      preset="card"
      style="width: 500px;"
    >
      <template #header>
        <h2 class="text-lg font-bold m-0">{{ editingUserId ? 'Edit User' : 'Add New User' }}</h2>
      </template>

      <n-form
        ref="addUserFormRef"
        :model="newUser"
        :rules="rules"
        label-width="120px"
        size="large"
      >
        <n-form-item label="Name" path="name">
          <n-input v-model:value="newUser.name" placeholder="User name" />
        </n-form-item>

        <n-form-item label="Email" path="email">
          <n-input v-model:value="newUser.email" placeholder="User email" />
        </n-form-item>

        <n-form-item label="Password" path="password">
          <n-input
            v-model:value="newUser.password"
            type="password"
            placeholder="Password"
            show-password-on="click"
          />
        </n-form-item>

        <n-form-item label="Confirm Password" path="password_confirmation">
          <n-input
            v-model:value="newUser.password_confirmation"
            type="password"
            placeholder="Confirm Password"
            show-password-on="click"
          />
        </n-form-item>

        <n-space justify="end" class="mt-6">
          <n-button @click="showAddUserModal = false">Cancel</n-button>
          <n-button type="primary" :loading="addingUser" @click="submitAddUser">
            Save
          </n-button>
        </n-space>
      </n-form>
    </n-modal>
  </n-card>
</template>

<script setup>
// Same logic as previous setup but fetches all users (not filtered by role)
import { ref, computed, watch, onMounted, h } from 'vue'
import {
  NCard, NButton, NSpace, NDataTable, NModal, NForm, NFormItem,
  NInput, useMessage, useDialog, NPagination
} from 'naive-ui'
import { AlertCircleOutline } from '@vicons/ionicons5'
import axios from '@/api/axios'

const users = ref([])
const loading = ref(false)
const showAddUserModal = ref(false)
const addingUser = ref(false)
const message = useMessage()
const dialog = useDialog()
const addUserFormRef = ref(null)
const editingUserId = ref(null)

const newUser = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const rules = {
  name: [{ required: true, message: 'Name is required', trigger: ['input', 'blur'] }],
  email: [
    { required: true, message: 'Email is required', trigger: ['input', 'blur'] },
    { type: 'email', message: 'Invalid email', trigger: ['input', 'blur'] }
  ],
  password: [
    { required: true, min: 6, message: 'Password must be at least 6 characters', trigger: ['input', 'blur'] }
  ],
  password_confirmation: [
    {
      validator: (rule, value) => value === newUser.value.password,
      message: 'Passwords do not match',
      trigger: ['input', 'blur']
    }
  ]
}

const columns = [
  { title: 'Name', key: 'name' },
  { title: 'Email', key: 'email' },
  {
    title: 'Actions',
    key: 'actions',
    render(row) {
      return h('div', { style: 'display: flex; gap: 8px;' }, [
        h(NButton, {
          size: 'small', type: 'info', secondary: true,
          onClick: () => openEditUserModal(row)
        }, { default: () => 'Edit' }),

        h(NButton, {
          size: 'small', type: 'error', secondary: true,
          onClick: () => deleteUser(row.id)
        }, { default: () => 'Delete' })
      ])
    }
  }
]

const fetchUsers = async () => {
  try {
    loading.value = true
    const response = await axios.get('users/admins')
    users.value = response.data
  } catch (error) {
    message.error('Failed to load users')
  } finally {
    loading.value = false
  }
}

const submitAddUser = async () => {
  try {
    await addUserFormRef.value?.validate()
    addingUser.value = true

    const payload = {
      name: newUser.value.name,
      email: newUser.value.email,
      ...(newUser.value.password ? {
        password: newUser.value.password,
        password_confirmation: newUser.value.password_confirmation
      } : {})
    }

    if (editingUserId.value) {
      await axios.put(`/users/${editingUserId.value}`, payload)
      message.success('User updated successfully!')
    } else {
      await axios.post('/users', payload)
      message.success('User added successfully!')
    }

    showAddUserModal.value = false
    fetchUsers()
  } catch (error) {
    message.error('Failed to save user')
  } finally {
    addingUser.value = false
  }
}

const openEditUserModal = (user) => {
  editingUserId.value = user.id
  newUser.value = {
    name: user.name,
    email: user.email,
    password: '',
    password_confirmation: ''
  }
  showAddUserModal.value = true
}

const deleteUser = (id) => {
  dialog.warning({
    title: 'Delete User',
    content: 'Are you sure you want to delete this user?',
    positiveText: 'Delete',
    negativeText: 'Cancel',
    positiveButtonProps: { type: 'error' },
    icon: () => h(AlertCircleOutline, { style: 'color: red; font-size: 22px;' }),
    onPositiveClick: async () => {
      try {
        await axios.delete(`/users/${id}`)
        message.success('User deleted successfully!')
        fetchUsers()
      } catch {
        message.error('Failed to delete user')
      }
    }
  })
}

const resetAddUserForm = () => {
  newUser.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  }
  editingUserId.value = null
}

watch(showAddUserModal, (visible) => {
  if (!visible) resetAddUserForm()
})

const searchTerm = ref('')
const currentPage = ref(1)
const pageSize = ref(5)

const filteredUsers = computed(() => {
  const filtered = users.value.filter(user =>
    user.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
    user.email.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
  const start = (currentPage.value - 1) * pageSize.value
  return filtered.slice(start, start + pageSize.value)
})

const totalFiltered = computed(() => {
  return users.value.filter(user =>
    user.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
    user.email.toLowerCase().includes(searchTerm.value.toLowerCase())
  ).length
})

watch(searchTerm, () => {
  currentPage.value = 1
})

onMounted(fetchUsers)
</script>

<style scoped>
.mb-4 { margin-bottom: 16px; }
.mt-4 { margin-top: 16px; }
.mt-6 { margin-top: 24px; }
.text-lg { font-size: 22px; }
.font-bold { font-weight: bold; }
.table-responsive { overflow-x: auto; width: 100%; }
.table-responsive ::v-deep(.n-data-table) { min-width: 600px; }
</style>
