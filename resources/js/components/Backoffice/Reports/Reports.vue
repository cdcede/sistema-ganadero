<template>
  <v-container>
    <v-toolbar flat>
      <v-toolbar-title>
        <v-icon>mdi-chart-bar</v-icon> Reportes
      </v-toolbar-title>
      <v-divider class="mx-4" inset vertical></v-divider>
      <!-- <v-spacer></v-spacer>
      <v-btn
        tile
        color="grey lighten-4"
      >
        <v-icon left>
          mdi-file-excel
        </v-icon>
        Descargar Excel
      </v-btn> -->
    </v-toolbar>
    <v-row>
      <v-col
        cols="12"
        sm="4"
      >
        <v-autocomplete
          v-model="medicine_id"
          :items="medicines"
          :item-text="'name'"
          :item-value="'id'"
          label="Selecciona la medicina*"
          prepend-icon="mdi-medical-bag"
          dense
          clearable
          @change="getData"
          ></v-autocomplete>
      </v-col>
      <v-col
        cols="12"
        sm="4"
      >
        <v-autocomplete
          v-model="livestock_id"
          :items="livestocks"
          :item-text="'name_identification'"
          :item-value="'id'"
          label="Selecciona el ganado*"
          prepend-icon="mdi-cow"
          dense
          clearable
          @change="getData"
          ></v-autocomplete>
      </v-col>
      <v-col
        cols="12"
        sm="4"
      >
        <v-autocomplete
          v-model="breed_id"
          :items="breeds"
          :item-text="'name'"
          :item-value="'id'"
          label="Selecciona la raza*"
          prepend-icon="mdi-barn"
          dense
          clearable
          @change="getData"
          ></v-autocomplete>
      </v-col>
    </v-row>
    <v-row>.<v-col
        cols="12"
        sm="4"
      >
        <v-autocomplete
          v-model="user_id"
          :items="users"
          :item-text="'name'"
          :item-value="'id'"
          label="Selecciona el usuario*"
          prepend-icon="mdi-account"
          dense
          clearable
          @change="getData"
          ></v-autocomplete>
      </v-col>
      <!-- <v-col
        cols="12"
        sm="4"
      >
        <v-autocomplete
          v-model="category_id"
          :items="categories"
          :item-text="'name'"
          :item-value="'id'"
          label="Selecciona la categoría*"
          prepend-icon="mdi-shape"
          dense
          clearable
          @change="getData"
          ></v-autocomplete>
      </v-col>
      <v-col
        cols="12"
        sm="4"
      >
        <v-autocomplete
          v-model="mark_id"
          :items="marks"
          :item-text="'name'"
          :item-value="'id'"
          label="Selecciona la marca*"
          prepend-icon="mdi-archive"
          dense
          clearable
          @change="getData"
          ></v-autocomplete>
      </v-col> -->
    </v-row>
    <v-row>
      <v-col
        cols="12"
        sm="6"
        md="6"
      >
        <v-menu
          ref="menu"
          v-model="menu"
          :close-on-content-click="false"
          :return-value.sync="date_from"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="date_from"
              label="Fecha Desde"
              prepend-icon="mdi-calendar"
              readonly
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="date_from"
            no-title
            scrollable
            @change="validaRango();getData()"
          >
            <v-spacer></v-spacer>
            <v-btn
              text
              color="primary"
              @click="menu = false"
            >
              Cancel
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="$refs.menu.save(date_from)"
            >
              OK
            </v-btn>
          </v-date-picker>
        </v-menu>
      </v-col>
      <v-col
        cols="12"
        sm="6"
        md="6"
      >
        <v-menu
          ref="menu2"
          v-model="menu2"
          :close-on-content-click="false"
          :return-value.sync="date_to"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="date_to"
              label="Fecha Hasta"
              prepend-icon="mdi-calendar"
              readonly
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="date_to"
            no-title
            scrollable
            @change="validaRango();getData()"
          >
            <v-spacer></v-spacer>
            <v-btn
              text
              color="primary"
              @click="menu2 = false"
            >
              Cancel
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="$refs.menu2.save(date_to)"
            >
              OK
            </v-btn>
          </v-date-picker>
        </v-menu>
      </v-col>
    </v-row>
    <v-row class="mb-3">
      <v-spacer></v-spacer>
      <v-btn
        tile
        color="grey lighten-4"
        :href="'/excel-report?date_from='+this.date_from+'&date_to='+this.date_to+'&medicine_id='+this.medicine_id+'&livestock_id='+this.livestock_id+'&breed_id='+this.breed_id+'&mark_id='+this.mark_id+'&category_id='+this.category_id+'&user_id='+this.user_id"
        target="_blank"
        class="mr-2"
      >
        <v-icon left>
          mdi-file-excel
        </v-icon>
        Descargar Excel
      </v-btn>
      <!-- <v-btn
        tile
        color="grey lighten-4"
        :href="'/pdf-report?date_from='+this.date_from+'&date_to='+this.date_to+'&medicine_id='+this.medicine_id+'&livestock_id='+this.livestock_id+'&breed_id='+this.breed_id+'&mark_id='+this.mark_id+'&category_id='+this.category_id"
        target="_blank"
      >
        <v-icon left>
          mdi-file-pdf-box
        </v-icon>
        Descargar PDF
      </v-btn> -->
    </v-row>
    <v-data-table
      :headers="headers"
      :items="livestock_medicines"
      :search="search"
      sort-by="id"
      class="elevation-1"
    >
      <template v-slot:[`item.animal`]="{ item }">
        <a class="text-decoration-none">{{item.animal}}</a>
      </template>
      <template v-slot:[`item.dosis`]="{ item }">
        <b>{{item.dosis}}</b>
      </template>
      <template v-slot:[`item.fecha_aplicacion`]="{ item }">
        <b>{{item.fecha_aplicacion}}</b>
      </template>
      <template v-slot:no-data> NO HAY DATOS </template>
    </v-data-table>
  </v-container>
</template>

<script>
import moment from 'moment'
moment.locale('es');

export default {
  data: () => ({
    livestocks: [],
    medicines: [],
    breeds: [],
    categories: [],
    marks: [],
    users: [],
    livestock_medicines: [],
    menu: false,
    menu2: false,
    date_from: moment().format("YYYY-MM-DD"),
    date_to: moment().format("YYYY-MM-DD"),
    medicine_id: "",
    livestock_id: "",
    breed_id: "",
    mark_id: "",
    category_id: "",
    user_id: "",
    search: "",
    headers: [
      {
        text: "ID",
        align: "start",
        sortable: false,
        value: "id",
      },
      { text: "Ganado", value: "animal" },
      { text: "Raza", value: "raza" },
      { text: "Medicina", value: "medicina" },
      { text: "Dosis", value: "dosis" },
      /* { text: "Categoría", value: "categoria" },
      { text: "Marca", value: "marca" }, */
      { text: "Usuario", value: "usuario" },
      { text: "Fecha de aplicación", value: "fecha_aplicacion" },
    ],
  }),
  mounted() {
    this.getLivestocks();
    this.getMedicines();
    this.getBreeds();
    this.getMarks();
    this.getCategories();
    this.getUsers();
    this.getData();
  },
  computed: {

  },
  watch: {

  },
  methods: {
    validaRango(){
      if (this.date_from > this.date_to) {
        this.$swal({
            position: 'top-end',
            title: 'Oops...',
            icon: 'error',
            title: 'Rango de fechas inválido!',
            showConfirmButton: false,
            timer: 1500
        });
        this.date_from = moment().format("YYYY-MM-DD");
        this.date_to = moment().format("YYYY-MM-DD");
      }
    },
    downloadExcel() {
      axios.get("/excel-report")
        .then((response) => {
          console.log('reporte generado');
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    getData() {
      axios
        .get('/report-livestock-medicines?date_from='+this.date_from+'&date_to='+this.date_to+
        '&medicine_id='+this.medicine_id+'&livestock_id='+this.livestock_id+'&breed_id='+this.breed_id
        +'&mark_id='+this.mark_id+'&category_id='+this.category_id+'&user_id='+this.user_id)
        .then((response) => {
          this.livestock_medicines = response.data.data;
          //console.log(this.livestock-medicines);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    getUsers() {
      axios.get("/get-users")
        .then((response) => {
          this.users = response.data.data;
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    getLivestocks() {
      axios.post("/livestocks-by-name-id")
        .then((response) => {
          this.livestocks = response.data.data;
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    getMedicines() {
      axios
        .get("/medicines")
        .then((response) => {
          this.medicines = response.data.data;
          //console.log(this.livestock-medicines);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    getBreeds() {
      axios
        .get("/breeds")
        .then((response) => {
          this.breeds = response.data.data;
          //console.log(this.livestock-medicines);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    getMarks() {
      axios
        .get("/marks")
        .then((response) => {
          this.marks = response.data.data;
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    getCategories() {
      axios
        .get("/categories")
        .then((response) => {
          this.categories = response.data.data;
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
  },
};
</script>
