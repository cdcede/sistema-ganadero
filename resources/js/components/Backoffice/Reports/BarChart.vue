<template>
  <highcharts :options="chartOptions"></highcharts>
</template>
<script>
export default {
  data: () => ({
    chartOptions: {
      chart: {
        type: 'column'
      },
      title: {
        text: 'Top Animales con más dosis aplicadas'
      },
      xAxis: {
        categories: [],
        crosshair: true
      },
      yAxis: {
        min: 0,
        title: {
          text: 'Número de dosis'
        }
      },
      series: [{
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
        .get('/top-5-animal')
        .then((response) => {
          this.chartOptions.series[0].data = response.data.data;
          this.chartOptions.xAxis.categories = response.data.categories;
          //console.log(this.chartOptions);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
  },
}
</script>