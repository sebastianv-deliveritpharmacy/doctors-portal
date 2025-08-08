<template>
  <n-card :title="$t('adminUsers.title')">
    <!-- Search + Add -->
    <n-space justify="space-between" align="center" class="mb-4">
      <n-input
        v-model:value="searchTerm"
        :placeholder="$t('adminUsers.searchPlaceholder')"
        clearable
        style="max-width: 300px"
      />
      <n-button type="primary" @click="showAddUserModal = true">
        {{ $t('adminUsers.addUser') }}
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
        <h2 class="text-lg font-bold m-0">
          {{ editingUserId ? $t('adminUsers.editUser') : $t('adminUsers.addNewAdmin') }}
        </h2>     
      </template>

      <n-form
        ref="addUserFormRef"
        :model="newUser"
        :rules="rules"
        label-width="120px"
        size="large"
      >
        <n-form-item :label="$t('adminUsers.name')" path="name">
          <n-input v-model:value="newUser.name" :placeholder="$t('adminUsers.name')" />
        </n-form-item>

        <n-form-item :label="$t('adminUsers.email')" path="email">
          <n-input v-model:value="newUser.email" :placeholder="$t('adminUsers.email')" />
        </n-form-item>

        <n-form-item :label="$t('adminUsers.password')" path="password">
          <n-input
            v-model:value="newUser.password"
            type="password"
            :placeholder="$t('adminUsers.password')"
            show-password-on="click"
        />
        </n-form-item>

        <n-form-item :label="$t('adminUsers.confirmPassword')" path="password_confirmation">
          <n-input
            v-model:value="newUser.password_confirmation"
            type="password"
            :placeholder="$t('adminUsers.confirmPassword')"
            show-password-on="click"
        />
        </n-form-item>


        <n-space justify="end" class="mt-6">
          <n-button @click="showAddUserModal = false">{{ $t('adminUsers.cancel') }}</n-button>
          <n-button type="primary" :loading="addingUser" @click="submitAddUser">
            {{ $t('adminUsers.save') }}
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
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

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
  name: [{ required: true, message: t('adminUsers.validation.name'), trigger: ['input', 'blur'] }],
  email: [
    { required: true, message: t('adminUsers.validation.email'), trigger: ['input', 'blur'] },
    { type: 'email', message: t('adminUsers.validation.invalidEmail'), trigger: ['input', 'blur'] }
  ],
  password: [
    { required: false, min: 6, message: t('adminUsers.validation.passwordLength'), trigger: ['input', 'blur'] }
  ],
  password_confirmation: [
    {
      validator: (rule, value) => value === newUser.value.password,
      message: t('adminUsers.validation.passwordMatch'),
      trigger: ['input', 'blur']
    }
  ]
}

const columns = [
   { title: t('adminUsers.name'), key: 'name' },
  { title: t('adminUsers.email'), key: 'email' },
  {
    title: t('adminUsers.delete'),
    key: 'actions',
    render(row) {
      return h('div', { style: 'display: flex; gap: 8px;' }, [
        h(NButton, {
          size: 'small', type: 'info', secondary: true,
          onClick: () => openEditUserModal(row)
        }, { default: () => t('adminUsers.edit') }),

        h(NButton, {
          size: 'small', type: 'error', secondary: true,
          onClick: () => deleteUser(row.id)
        }, { default: () => t('adminUsers.delete') })
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
    message.error(t('adminUsers.errorLoad'))
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
      message.success(t('adminUsers.updateSuccess'))
    } else {
      await axios.post('/users/admins', payload)
      message.success(t('adminUsers.addSuccess'))
    }

    showAddUserModal.value = false
    fetchUsers()
  } catch (error) {
    message.error(t('adminUsers.errorSave'))
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
    title: t('adminUsers.deleteTitle'),
    content: t('adminUsers.deleteConfirm'),
    positiveText: t('adminUsers.delete'),
    negativeText: t('adminUsers.cancel'),
    positiveButtonProps: { type: 'error' },
    icon: () => h(AlertCircleOutline, { style: 'color: red; font-size: 22px;' }),
    onPositiveClick: async () => {
      try {
        await axios.delete(`/users/${id}`)
        message.success(t('adminUsers.deleteSuccess'))
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
