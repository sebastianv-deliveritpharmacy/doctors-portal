<template>
  <div class="center-chart">
    <apexchart width="600" type="pie" :options="chartOptions" :series="series" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const series = ref([44, 55, 41, 17])

const chartOptions = computed(() => ({
  chart: {
    id: 'apexchart-example'
  },
  title: {
    text: t('pieChart.title'),
    align: 'center',
    margin: 40,
    style: {
      fontSize: '20px',
      fontWeight: 'bold',
      color: '#263238'
    }
  },
  colors: ['#2E93fA', '#46DA62', '#242E8A', '#E91E63'],
  dataLabels: {
    enabled: true
  },
  legend: {
    show: true,
    position: 'bottom'
  },
  labels: ['Lisinopril', 'Levotiroxina', 'Atorvastatina', 'Metformina']
}))
</script>

<style scoped>
.center-chart {
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
