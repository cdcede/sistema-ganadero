<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="medicine_entries"
      :search="search"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title
            ><v-icon>mdi-pill</v-icon> Ingresar Medicina</v-toolbar-title
          >
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                <v-icon>mdi-plus</v-icon> Registrar
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
                            <v-select
                              v-model="editedItem.medicine_id"
                              :items="medicines"
                              :item-text="'name'"
                              :item-value="'id'"
                              :rules="[v => !!v || 'Medicina es obligatorio']"
                              label="Selecciona la medicina*"
                              prepend-icon="mdi-needle"
                              dense
                            ></v-select>
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <v-text-field
                              v-model="editedItem.quantity"
                              :rules="[rules.required]"
                              label="Cantidad*"
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
                              :return-value.sync="editedItem.expiration_date"
                              transition="scale-transition"
                              offset-y
                              min-width="auto"
                            >
                              <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                  v-model="editedItem.expiration_date"
                                  label="Fecha de expiración*"
                                  prepend-icon="mdi-calendar"
                                  readonly
                                  clearable
                                  v-bind="attrs"
                                  v-on="on"
                                ></v-text-field>
                              </template>
                              <v-date-picker
                                v-model="editedItem.expiration_date"
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
                                  @click="$refs.menu.save(editedItem.expiration_date)"
                                >
                                  OK
                                </v-btn>
                              </v-date-picker>
                            </v-menu>
                          </v-col>                       
                          <v-col cols="12" sm="6" md="6">
                            <v-switch
                              v-model="editedItem.status"
                              inset
                              label="Inactivo/Activo"
                              dense
                            ></v-switch>
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
      <template v-slot:[`item.quantity`]="{ item }">
        <b>{{item.quantity}} ml</b>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
      <template v-slot:[`item.status`]="{ item }">
        <v-chip v-if="item.status === 1" :color="getColor(item.status)">Activo</v-chip>
        <v-chip v-else :color="getColor(item.status)">Inactivo</v-chip>
      </template>
      <template v-slot:no-data> NO HAY DATOS </template>
    </v-data-table>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    dialog: false,
    dialogDelete: false,
    loading: false,
    valid: false,
    menu: false,
    search: "",
    rules: {
      min: (v) => (v && v.length >=2 ) || "Minimo 2 caracteres",
      lettersOnly: (v) =>
          /^[a-zA-Z\s]*$/.test(v) || "Solo debe ingresar letras",
      required: (v) => !!v || "Campo es obligatorio",
    },
    headers: [
      {
        text: "ID",
        align: "start",
        sortable: false,
        value: "id",
      },
      { text: "Medicina", value: "medicine.name" },
      { text: "Cantidad", value: "quantity" },
      { text: "Fecha de expiración", value: "expiration_date" },
      { text: "Estado", value: "status" },
      { text: "Acciones", value: "actions", sortable: false },
    ],
    medicines: [],
    medicine_entries: [],
    editedIndex: -1,
    editedItem: {
      expiration_date: "",
      quantity: "",
      medicine_id: "",
      status: true,
    },
    defaultItem: {
      expiration_date: "",
      quantity: "",
      medicine_id: "",
      status: true,
    },
  }),
  mounted() {
    this.getData();
    this.getMedicines();
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Ingreso de Medicina" : "Editar Ingreso de Medicina";
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
    getColor(estado) {
      if (estado === 1) return "success";
      else return "error";
    },
    nameUppercase(v) {
      this.editedItem.name = this.editedItem.name.toUpperCase()
    },
    getMedicines() {
      axios.get("/get-medicines")
        .then((response) => {
          this.medicines = response.data.data;
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    getData() {
      axios
        .get("/medicine-entries")
        .then((response) => {
          this.medicine_entries = response.data.data;
          //console.log(this.medicines);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    editItem(item) {
      this.editedIndex = this.medicine_entries.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedIndex = this.medicine_entries.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      axios
        .delete("/medicine-entries/" + this.editedItem.id)
        .then((response) => {

          this.medicine_entries.splice(this.editedIndex, 1);
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
          this.$swal({
            icon: 'error',
            title: error.response.data.errors,
          });
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
        Object.assign(this.medicine_entries[this.editedIndex], this.editedItem);

        if (this.$refs.form.validate()) {
          this.loading = true;
          axios
            .put("/medicine-entries/" + this.editedItem.id, {
              expiration_date: this.editedItem.expiration_date,
              quantity: this.editedItem.quantity,
              medicine_id: this.editedItem.medicine_id,
              status: this.editedItem.status,
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
              this.loading = false;
              this.close();
            })
            .catch((error) => {});
        }
      } else {

        if (this.$refs.form.validate()) {
          this.loading = true;
          axios
            .post("/medicine-entries", {
              expiration_date: this.editedItem.expiration_date,
              quantity: this.editedItem.quantity,
              medicine_id: this.editedItem.medicine_id,
              status: this.editedItem.status,
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
              this.loading = false;
              this.close();
            })
            .catch((error) => {
                this.loading = false;
                this.$swal({
                    position: 'top-end',
                    title: 'Oops...',
                    icon: 'error',
                    title: error.response.data.errors.name[0],
                    showConfirmButton: false,
                    timer: 1500
                });
              /* this.errores = [];
                if (error.response.data.errors.descripcion) {
                    this.errores.push(error.response.data.errors.descripcion[0]);
                }

                if (error.response.data.errors.estado) {
                    this.errores.push(error.response.data.errors.estado[0]);
                } */
            });
        }
      }
    },
  },
};
</script>
