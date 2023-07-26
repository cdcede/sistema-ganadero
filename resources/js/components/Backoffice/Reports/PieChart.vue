<template>
  <highcharts :options="chartOptions"></highcharts>
</template>
<script>
export default {
  data: () => ({
    chartOptions: {
      title: {
        text: 'Número de dosis por medicina'
      },
      series: [{
        type: 'pie',
        name: 'Cantidad',
        data: []
      }]
    }
  }),
  mounted() {
    this.getData();
  },
  methods: {
    getData() {
      axios
        .get('/dose-per-medicine')
        .then((response) => {
          this.chartOptions.series[0].data = response.data.data;
          //console.log(this.chartOptions.series[0].data);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
  },
}
</script>