<template>
  <div class="chart-wrapper">
    <apexchart type="line" height="300" :options="chartOptions" :series="chartSeries" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ApexCharts from 'apexcharts'
import VueApexCharts from 'vue3-apexcharts'
import { useI18n } from 'vue-i18n'

const apexchart = VueApexCharts
const { t } = useI18n()

const chartSeries = ref([
  {
    name: t('chart.orders'),
    type: 'area',
    data: [55, 62, 48, 44, 70, 52, 61, 50]
  },
  {
    name: t('chart.earnings'),
    type: 'bar',
    data: [80, 85, 60, 30, 90, 45, 85, 40]
  },
  {
    name: t('chart.refunds'),
    type: 'line',
    data: [20, 18, 12, 10, 11, 25, 15, 30]
  }
])

const chartOptions = ref({
  chart: {
  height: 300,
  type: 'line',
  stacked: false,
  toolbar: { show: false },
  animations: {
    enabled: true,
    easing: 'easeinout',
    speed: 800,
    animateGradually: {
      enabled: true,
      delay: 150
    },
    dynamicAnimation: {
      enabled: true,
      speed: 350
    }
  }
},
  stroke: {
    width: [2, 0, 2],
    curve: 'smooth',
    dashArray: [0, 0, 6]
  },
  fill: {
    opacity: [0.2, 1, 1],
    type: ['solid', 'solid', 'solid']
  },
  colors: ['#3347FF', '#00B894', '#FF5C5C'],
  plotOptions: {
    bar: {
      columnWidth: '40%',
      borderRadius: 4
    }
  },
  dataLabels: { enabled: false },
  markers: { size: 0 },
  xaxis: {
    categories: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    labels: {
      style: { colors: '#888' }
    }
  },
  yaxis: {
    labels: {
      style: { colors: '#888' }
    }
  },
  grid: {
    borderColor: '#eee',
    xaxis: { lines: { show: false } }
  },
  legend: {
    position: 'bottom',
    horizontalAlign: 'center',
    markers: {
      width: 10,
      height: 10,
      radius: 50
    }
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'light'
  }
})
</script>

<style scoped>
.chart-wrapper {
  width: 100%;
  height: 300px;
}

</style>
