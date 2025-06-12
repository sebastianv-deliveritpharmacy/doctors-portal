<template>
    <Bar :data="data" :options="options" />
  </template>
  
  <script setup>
  import { Bar } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
  } from 'chart.js'
  
  // Register required chart.js components
  ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
  
  const data = {
    labels: ['January', 'February', 'March', 'April', 'May'],
    datasets: [
      {
        label: 'Prescriptions',
        backgroundColor: '#2080f0',
        data: [40, 20, 12, 39, 10]
      }
    ]
  }
  
  const options = {
    responsive: true,
    maintainAspectRatio: false,
  }
  </script>
  
  <style scoped>
  .chart-container {
    width: 100%;
    height: 450px;
  }
  </style>
  