<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="livestock_medicines"
      :search="search"
      sort-by="id"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title
            ><v-icon>mdi-medical-bag</v-icon> Medicina Aplicada</v-toolbar-title
          >
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                <v-icon>mdi-plus</v-icon> Aplicar Medicina
              </v-btn>
            </template>
            <v-card>
              <v-form ref="form" v-model="valid" lazy-validation>
                <v-card-title>
                  <span class="headline">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col>
                        <v-row>
                          <v-col
                            cols="12"
                            sm="6"
                          >
                            <v-autocomplete
                              v-model="editedItem.livestock_id"
                              :items="livestocks"
                              :item-text="'name_identification'"
                              :item-value="'id'"
                              :rules="[v => !!v || 'Ganado es obligatorio']"
                              label="Selecciona la ganado*"
                              prepend-icon="mdi-cow"
                              dense
                            ></v-autocomplete>
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                          >
                            <v-autocomplete
                              v-model="editedItem.medicine_id"
                              :items="medicines"
                              :item-text="getItemText"
                              :item-value="'id'"
                              return-object
                              :rules="[v => !!v || 'Medicina es obligatorio']"
                              label="Selecciona la medicina*"
                              prepend-icon="mdi-needle"
                              dense
                            ></v-autocomplete>
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <v-text-field
                              v-model="editedItem.dose"
                              :rules="[rules.required]"
                              label="Dosis*"
                              prepend-icon="mdi-flask-outline"
                              @keypress="onlyNumbers(event)"
                              suffix="ml"
                              required
                              dense
                            ></v-text-field>
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            md="6"
                          >
                            <v-menu
                              ref="menu"
                              v-model="menu"
                              :close-on-content-click="false"
                              :return-value.sync="editedItem.date"
                              transition="scale-transition"
                              offset-y
                              min-width="auto"
                            >
                              <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                  v-model="editedItem.date"
                                  label="Fecha de aplicación"
                                  :rules="[v => !!v || 'Fecha es obligatoria']"
                                  prepend-icon="mdi-calendar"
                                  readonly
                                  clearable
                                  v-bind="attrs"
                                  v-on="on"
                                ></v-text-field>
                              </template>
                              <v-date-picker
                                v-model="editedItem.date"
                                @change="validaRango()"
                                no-title
                                scrollable
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
                                  @click="$refs.menu.save(editedItem.date)"
                                >
                                  OK
                                </v-btn>
                              </v-date-picker>
                            </v-menu>
                          </v-col>
                        </v-row>
                      </v-col>
                    </v-row>
                  </v-container>
                  <small>*campos obligatorios</small>
                </v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="error" @click="close"> Cancelar </v-btn>
                  <v-btn :loading="loading" :disabled="loading" color="primary" @click="save"> Guardar </v-btn>
                </v-card-actions>
              </v-form>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="650px">
            <v-card>
              <v-card-title class="headline justify-center"
                >¿Está seguro que desea eliminar éste registro?</v-card-title
              >
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" text @click="closeDelete">Cancelar</v-btn>
                <v-btn color="primary" text @click="deleteItemConfirm">Confirmar</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
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
      </template>
      <template v-slot:[`item.dose`]="{ item }">
        <b>{{item.dose}} ml</b>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon v-if="user.roles[0].slug === 'admin' || user.roles[0].slug === 'superadmin'" small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
      <template v-slot:no-data> NO HAY DATOS </template>
    </v-data-table>
  </v-container>
</template>

<script>
import moment from 'moment'
moment.locale('es');
export default {
  props: ['user'],
  data: () => ({
    dialog: false,
    dialogDelete: false,
    loading: false,
    menu: false,
    valid: false,
    search: "",
    today: moment().format("YYYY-MM-DD"),
    rules: {
      min: (v) => (v && v.length >=2 ) || "Minimo 2 caracteres",
      lettersOnly: (v) =>
          /^[A-Za-z]+$/.test(v) || "Solo debe ingresar letras",
      required: (v) => !!v || "Campo es obligatorio",
    },
    headers: [
      {
        text: "ID",
        align: "start",
        sortable: false,
        value: "id",
      },
      { text: "Ganado", value: "livestock.name" },
      { text: "Identificación", value: "livestock.identification" },
      { text: "Medicina", value: "medicine_entry.medicine.name" },
      { text: "Dosis", value: "dose" },
      { text: "Fecha de aplicación", value: "date" },
      { text: "Acciones", value: "actions", sortable: false },
    ],
    livestock_medicines: [],
    medicines: [],
    livestocks: [],
    editedIndex: -1,
    editedItem: {
      date: "",
      dose: "",
      livestock_id: "",
      medicine_id: "",
    },
    defaultItem: {
      date: "",
      dose: "",
      livestock_id: "",
      medicine_id: "",
    },
  }),
  mounted() {
    this.getData();
    this.getMedicines();
    this.getLivestocks();
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo Registro" : "Editar Registro";
    },
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },
  methods: {
    onlyNumbers: function(evt) {
      evt = (evt) ? evt : window.event;
      let expect = evt.target.value.toString() + evt.key.toString();
      
      if (!/^[0-9]*\.?[0-9]*$/.test(expect)) {
        evt.preventDefault();
      } else {
        return true;
      }
    },
    validaRango(){
      if (this.editedItem.date > this.today) {
        this.$swal({
            position: 'top-end',
            title: 'Oops...',
            icon: 'error',
            title: 'La fecha de vacunación NO puede ser superior a la fecha actual',
            showConfirmButton: false,
            timer: 1500
        });
        this.editedItem.date = moment().format("YYYY-MM-DD");
      }
    },
    getColor(estado) {
      if (estado === 1) return "success";
      else return "error";
    },
    nameUppercase(v) {
      this.editedItem.name = this.editedItem.name.toUpperCase()
    },
    getItemText(item) {
      return `${item.medicine} | Stock: ${item.stock} ml`;
    },
    getMedicines() {
      axios.get("/get-medicines-entries")
        .then((response) => {
          this.medicines = response.data.data;
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
    getData() {
      axios
        .get("/livestock-medicines")
        .then((response) => {
          this.livestock_medicines = response.data.data;
          //console.log(this.livestock-medicines);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    editItem(item) {
      this.editedIndex = this.livestock_medicines.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedIndex = this.livestock_medicines.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      axios
        .delete("/livestock-medicines/" + this.editedItem.id)
        .then((response) => {

          this.livestock_medicines.splice(this.editedIndex, 1);
          this.getData();
          this.$swal({
            position: 'top-end',
            icon: 'success',
            title: response.data.message,
            showConfirmButton: false,
            timer: 1500
          });

        })
        .catch((error) => {

        });
      this.closeDelete();
    },
    close() {
      this.$refs.form.reset();
      this.dialog = false;
      this.loading = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    save() {
      if (this.editedIndex > -1) {
        Object.assign(this.livestock_medicines[this.editedIndex], this.editedItem);

        if (this.$refs.form.validate()) {
          this.loading = true;
          axios
            .put("/livestock-medicines/" + this.editedItem.id, {
              dose: this.editedItem.dose,
              date: this.editedItem.date,
              livestock_id: this.editedItem.livestock_id,
              medicine_id: this.editedItem.medicine_id,
            })
            .then((response) => {
              this.$swal({
                position: 'top-end',
                icon: 'success',
                title: response.data.message,
                showConfirmButton: false,
                timer: 1500
              });

              this.getData();
              this.getMedicines();
              this.loading = false;
              this.close();
            })
            .catch((error) => {
              this.loading = false;
              this.$swal({
                position: 'top-end',
                title: 'Oops...',
                icon: 'error',
                title: error.response.data.message,
                showConfirmButton: false,
                timer: 1500
              });
            });
        }
      } else {

        if (this.$refs.form.validate()) {
          this.loading = true;
          axios
            .post("/livestock-medicines", {
              dose: this.editedItem.dose,
              date: this.editedItem.date,
              livestock_id: this.editedItem.livestock_id,
              medicine_id: this.editedItem.medicine_id,
            })
            .then((response) => {
              this.$swal({
                position: 'top-end',
                icon: 'success',
                title: response.data.message,
                showConfirmButton: false,
                timer: 1500
              });

              this.getData();
              this.getMedicines();
              this.loading = false;
              this.close();
            })
            .catch((error) => {
              //console.log(error.response.data);
                this.loading = false;
                this.$swal({
                    position: 'top-end',
                    title: 'Oops...',
                    icon: 'error',
                    title: error.response.data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                //this.errores = [];
                if (error.response.data.errors) {
                  if (error.response.data.errors.date) {
                    this.$swal({
                      position: 'top-end',
                      title: 'Oops...',
                      icon: 'error',
                      title: error.response.data.errors.date[0],
                      showConfirmButton: false,
                      timer: 1500
                    });
                  }
                }

                /* if (error.response.data.errors.estado) {
                    this.errores.push(error.response.data.errors.estado[0]);
                } */
            });
        }
      }
    },
  },
};
</script>
