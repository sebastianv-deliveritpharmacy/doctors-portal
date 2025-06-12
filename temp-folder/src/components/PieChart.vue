
<template>
  <div class="center-chart">
    <apexchart width="600" type="pie" :options="chartOptions" :series="series">
    </apexchart>
  </div>
</template>

<script>
export default {
  name: "Apex charts",
  data: () => ({
    chartOptions: {
      chart: {
        id: "apexchart-example",
      },
      title: {
        text: "Completed prescription by %",
        align: "center",
        margin: 40,
        style: {
          fontSize: "20px",
          fontWeight: "bold",
          color: "#263238",
        },
      },
        colors: ['#2E93fA', '#46DA62', '#242E8A', '#E91E63'], // Matched in order
      dataLabels: {
         enabled: true,
      },
      legend: {
        show: true,
        position: 'bottom',
      },
        labels: ["Lisinopril", "Levothyroxine", "Atorvastatin", "Metformin"],
    },
    series: [44, 55, 41, 17],
  }),
};
</script>