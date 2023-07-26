<template>
  <v-container>
    <v-card>
      <v-toolbar flat>
        <v-toolbar-title>
          <v-icon>mdi-table-eye</v-icon>
          Kardex
        </v-toolbar-title>
        <v-divider class="mx-4" inset vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn
          tile
          color="grey lighten-4"
          :href="'/kardex-report'"
          target="_blank"
          class="mr-2"
        >
          <v-icon left>
            mdi-file-excel
          </v-icon>
          Descargar Excel
        </v-btn>
      </v-toolbar>
      <v-text-field
        class="pa-6 ma-2"
        v-model="search"
        append-icon="mdi-magnify"
        label="Ingrese criterio de búsqueda..."
        solo
        single-line
        hide-details
      ></v-text-field>
      <v-data-table
        :headers="headers"
        :items="kardex"
        :search="search"
      >
        <template v-slot:[`item.ingreso`]="{ item }">
          <strong class="green--text text--lighten-1 font-weight-black">{{item.ingreso}} ml</strong>
        </template>
        <template v-slot:[`item.egreso`]="{ item }">
          <strong class="red--text text--lighten-1 font-weight-black">{{item.egreso}} ml</strong>
        </template>
        <template v-slot:[`item.stock`]="{ item }">
          <strong class="blue--text text--lighten-1 font-weight-black">{{item.stock}} ml</strong>
        </template>
        <template v-slot:no-data> NO HAY DATOS </template>
      </v-data-table>
    </v-card>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    search: "",
    headers: [
      {
        text: "ID",
        align: "start",
        sortable: false,
        value: "id",
      },
      { text: "Marca", value: "marca" },
      { text: "Medicina", value: "medicina" },
      { text: "Fecha de caducidad", value: "expiration_date" },
      { text: "Ingreso", value: "ingreso" },
      { text: "Egreso", value: "egreso" },
      { text: "Existencia", value: "stock" },
    ],
    kardex: [],
  }),
  mounted() {
    this.getData();
  },
  methods: {
    getColor(estado) {
      if (estado === 1) return "success";
      else return "error";
    },
    getData() {
      axios
        .get("/get-kardex")
        .then((response) => {
          this.kardex = response.data.data;
          //console.log(this.kardex);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
  },
};
</script>
