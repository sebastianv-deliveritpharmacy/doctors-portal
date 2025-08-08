<template>
  <n-card :title="$t('settings.title')">
    <!-- Language Switcher -->
    <n-space vertical align="start" class="mb-4">
      <span class="switcher-title">{{ $t('settings.languageTitle') }}</span>
      <n-space>
        <n-button
          @click="switchLanguage('en')"
          :type="locale === 'en' ? 'primary' : 'default'"
        >
          English
        </n-button>
        <n-button
          @click="switchLanguage('es')"
          :type="locale === 'es' ? 'primary' : 'default'"
        >
          Español
        </n-button>
      </n-space>
    </n-space>

    <!-- CareTend Form (Only for Super Admin) -->
    <template v-if="isSuperAdmin">
      <n-form
        :model="form"
        :rules="rules"
        label-width="120px"
        size="large"
        ref="formRef"
      >
        <h3 class="section-title">{{ $t('settings.caretend') }}</h3>

        <n-form-item :label="$t('settings.clientId')" path="client_id">
          <n-input
            v-model:value="form.client_id"
            :placeholder="$t('settings.placeholderClientId')"
          />
        </n-form-item>

        <n-form-item :label="$t('settings.secretKey')" path="secret">
          <n-input
            v-model:value="form.secret"
            type="password"
            :placeholder="$t('settings.placeholderSecretKey')"
            show-password-on="click"
          />
        </n-form-item>

        <n-space justify="end" class="mt-4">
          <n-button :loading="loading" type="primary" @click="handleSubmit">
            {{ existing ? $t('settings.update') : $t('settings.save') }}
          </n-button>
          <n-button
            v-if="existing"
            type="error"
            secondary
            @click="handleDelete"
          >
            {{ $t('settings.delete') }}
          </n-button>
        </n-space>
      </n-form>
    </template>
    
  </n-card>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useMessage } from 'naive-ui'
import axios from '@/api/axios'
import { useI18n } from 'vue-i18n'
import { useUserStore } from '@/stores/useUser'

const userStore = useUserStore()
const isSuperAdmin = computed(() => userStore.isSuperAdmin())

const { t, locale } = useI18n()
const message = useMessage()

const formRef = ref(null)
const form = ref({ client_id: '', secret: '' })
const existing = ref(false)
const loading = ref(false)

const rules = {
  client_id: [
    { required: true, message: t('settings.requiredClientId'), trigger: ['input', 'blur'] }
  ],
  secret: [
    { required: true, message: t('settings.requiredSecret'), trigger: ['input', 'blur'] }
  ]
}

function switchLanguage(lang) {
  locale.value = lang
  localStorage.setItem('language', lang)
}

const loadCredential = async () => {
  // Don’t call the API if the user isn’t a super admin
  if (!isSuperAdmin.value) return
  try {
    const res = await axios.get('/caretend-credential')
    if (res.data) {
      form.value = { client_id: res.data.client_id, secret: res.data.secret }
      existing.value = true
    } else {
      existing.value = false
    }
  } catch {
    existing.value = false
  }
}

const handleSubmit = async () => {
  if (!isSuperAdmin.value) {
    message.error(t('settings.insufficientPermissions') || 'Insufficient permissions.')
    return
  }
  try {
    await formRef.value?.validate()
    loading.value = true
    if (existing.value) {
      await axios.put('/caretend-credential', form.value)
      message.success(t('settings.successUpdate'))
    } else {
      await axios.post('/caretend-credential', form.value)
      message.success(t('settings.successSave'))
      existing.value = true
    }
  } catch {
    message.error(t('settings.errorSave'))
  } finally {
    loading.value = false
  }
}

const handleDelete = async () => {
  if (!isSuperAdmin.value) {
    message.error(t('settings.insufficientPermissions') || 'Insufficient permissions.')
    return
  }
  try {
    await axios.delete('/caretend-credential')
    form.value = { client_id: '', secret: '' }
    existing.value = false
    message.success(t('settings.successDelete'))
  } catch {
    message.error(t('settings.errorDelete'))
  }
}

onMounted(loadCredential)
</script>

<style scoped>
/* Spacing helpers to match your existing style */
.mb-4 {
  margin-bottom: 1rem;
}
.mt-4 {
  margin-top: 1.5rem;
}

/* Language switcher title */
.switcher-title {
  font-weight: 600;
  font-size: 0.95rem;
  line-height: 1.2;
}

/* Section title for the CareTend block */
.section-title {
  margin: 0 0 0.75rem 0;
  font-weight: 600;
  font-size: 1.05rem;
}

/* Permission notice */
.guard-note {
  margin-top: 0.75rem;
}
</style>
