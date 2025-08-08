<template>
  <n-card :title="$t('doctors.title')">
    <!-- Search + Add -->
    <n-space justify="space-between" align="center" class="mb-4">
      <n-input
        v-model:value="searchTerm"
        :placeholder="$t('doctors.searchPlaceholder')"
        clearable
        style="max-width: 300px"
      />
      <n-button type="primary" @click="showAddUserModal = true">
          {{ $t('doctors.add') }}
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
        <h2 class="text-lg font-bold m-0">{{ editingUserId ? $t('doctors.edit') : $t('doctors.addNew') }}</h2>
      </template>

      <n-form
        ref="addUserFormRef"
        :model="newUser"
        :rules="rules"
        label-width="120px"
        size="large"
      >
        <n-form-item :label="$t('doctors.name')" path="name">
          <n-input v-model:value="newUser.name" :placeholder="$t('doctors.name')" />
        </n-form-item>

        <n-form-item :label="$t('doctors.email')" path="email">
          <n-input v-model:value="newUser.email" :placeholder="$t('doctors.email')" />
        </n-form-item>

        <n-form-item :label="$t('doctors.sheetId')" path="sheet">
          <n-input v-model:value="newUser.sheet_identifier" :placeholder="$t('doctors.sheetId')" />
        </n-form-item>

        <n-form-item :label="$t('doctors.password')" path="password">
          <n-input v-model:value="newUser.password" type="password" :placeholder="$t('doctors.password')" show-password-on="click" />
        </n-form-item>

        <n-form-item :label="$t('doctors.confirmPassword')" path="password_confirmation">
          <n-input v-model:value="newUser.password_confirmation" type="password" :placeholder="$t('doctors.confirmPassword')" show-password-on="click" />
        </n-form-item>

        <n-space justify="end" class="mt-6">
          <n-button @click="showAddUserModal = false">{{ $t('doctors.cancel') }}</n-button>
          <n-button type="primary" :loading="addingUser" @click="submitAddUser">
            {{ $t('doctors.save') }}
          </n-button>
        </n-space>
      </n-form>
    </n-modal>
  </n-card>
</template>

<script setup>
import { ref, computed, watch, onMounted, h } from 'vue'
import {
  NCard, NButton, NSpace, NDataTable, NModal, NForm, NFormItem,
  NInput, useMessage, useDialog, NPagination
} from 'naive-ui'
import { AlertCircleOutline } from '@vicons/ionicons5'
import axios from '@/api/axios'
import { useRouter } from 'vue-router'
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
const router = useRouter()

const newUser = ref({
  name: '',
  email: '',
  sheet_identifier: '',
  password: '',
  password_confirmation: ''
})

const rules = computed(() => ({
  name: [{ required: true, message: t('doctors.validation.name'), trigger: ['input', 'blur'] }],
  email: [
    { required: true, message: t('doctors.validation.email'), trigger: ['input', 'blur'] },
    { type: 'email', message: t('doctors.validation.invalidEmail'), trigger: ['input', 'blur'] }
  ],
  sheet_identifier: [{ required: true, message: t('doctors.validation.sheet'), trigger: ['input', 'blur'] }],
  password: editingUserId.value
    ? []
    : [{ required: true, min: 6, message: t('doctors.validation.passwordLength'), trigger: ['input', 'blur'] }],
  password_confirmation: editingUserId.value
    ? []
    : [{
        validator: (rule, value) => value === newUser.value.password,
        message: t('doctors.validation.passwordMatch'),
        trigger: ['input', 'blur']
      }]
}))

const columns = [
  { title: t('doctors.name'), key: 'name' },
  { title: t('doctors.email'), key: 'email' },
  {
    title: t('doctors.delete'),
    key: 'actions',
    render(row) {
      return h('div', { style: 'display: flex; gap: 8px;' }, [
        h(NButton, {
          size: 'small', type: 'info', secondary: true,
          onClick: () => openEditUserModal(row)
        }, { default: () => t('doctors.edit') }),

        h(NButton, {
          size: 'small', type: 'primary', secondary: true,
          onClick: () => viewPrescriptions(row.id)
        }, { default: () => t('doctors.prescriptions') }),

        h(NButton, {
          size: 'small', type: 'error', secondary: true,
          onClick: () => deleteUser(row.id)
        }, { default: () => t('doctors.delete') })
      ])
    }
  }
]


const fetchUsers = async () => {
  try {
    loading.value = true
    const response = await axios.get('/users')
    users.value = response.data
  } catch (error) {
    message.error(t('doctors.errorLoad'))
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
      sheet_identifier: newUser.value.sheet_identifier,
      ...(newUser.value.password ? {
        password: newUser.value.password,
        password_confirmation: newUser.value.password_confirmation
      } : {})
    }

    if (editingUserId.value) {
      await axios.put(`/users/${editingUserId.value}`, payload)
      message.success(t('doctors.updateSuccess'))
    } else {
      await axios.post('/users', { ...payload, is_admin: false })
      message.success(t('doctors.addSuccess'))
    }

    showAddUserModal.value = false
    fetchUsers()
  } catch (error) {
    message.error(t('doctors.errorSave'))
  } finally {
    addingUser.value = false
  }
}

const openEditUserModal = (user) => {
  editingUserId.value = user.id
  newUser.value = {
    name: user.name,
    email: user.email,
    sheet_identifier: user.sheet_identifier,
    password: '',
    password_confirmation: ''
  }
  showAddUserModal.value = true
}

const deleteUser = (id) => {
  dialog.warning({
  title: t('doctors.deleteTitle'),
  content: t('doctors.deleteConfirm'),
  positiveText: t('doctors.delete'),
  negativeText: t('doctors.cancel'),
    positiveButtonProps: { type: 'error' },
    icon: () => h(AlertCircleOutline, { style: 'color: red; font-size: 22px;' }),
    onPositiveClick: async () => {
      try {
        await axios.delete(`/users/${id}`)
        message.success(t('doctors.deleteSuccess'))
        fetchUsers()
      } catch {
        message.error(t('doctors.errorDelete'))
      }
    }
  })
}

const viewPrescriptions = (id) => {
  router.push(`/dashboard/doctors/${id}/prescriptions`)

}

const resetAddUserForm = () => {
  newUser.value = {
    name: '',
    email: '',
    sheet_identifier: '',
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
.mb-4 {
  margin-bottom: 16px;
}
.mt-4 {
  margin-top: 16px;
}
.mt-6 {
  margin-top: 24px;
}
.text-lg {
  font-size: 22px;
}
.font-bold {
  font-weight: bold;
}

/* Make table scrollable horizontally on small screens */
.table-responsive {
  overflow-x: auto;
  width: 100%;
}
.table-responsive ::v-deep(.n-data-table) {
  min-width: 600px;
}
</style>
