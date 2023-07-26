<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="categories"
      :search="search"
      sort-by="id"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title
            ><v-icon>mdi-shape</v-icon> Categorias</v-toolbar-title
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
                          <v-col cols="12" sm="6" md="6">
                            <v-text-field
                              v-model="editedItem.name"
                              :rules="[rules.required, rules.min, rules.lettersOnly]"
                              label="Nombre*"
                              @keyup="nameUppercase"
                              prepend-icon="mdi-text-box"
                              required
                              dense
                            ></v-text-field>
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
      { text: "Nombre", value: "name" },
      { text: "Estado", value: "status" },
      { text: "Acciones", value: "actions", sortable: false },
    ],
    categories: [],
    editedIndex: -1,
    editedItem: {
      name: "",
      status: true,
    },
    defaultItem: {
      name: "",
      status: true,
    },
  }),
  mounted() {
    this.getData();
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nueva Categoria" : "Editar Categoria";
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
    getColor(estado) {
      if (estado === 1) return "success";
      else return "error";
    },
    nameUppercase(v) {
      this.editedItem.name = this.editedItem.name.toUpperCase()
    },
    getData() {
      axios
        .get("/categories")
        .then((response) => {
          this.categories = response.data.data;
          //console.log(this.categories);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    editItem(item) {
      this.editedIndex = this.categories.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedIndex = this.categories.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      axios
        .delete("/categories/" + this.editedItem.id)
        .then((response) => {

          this.categories.splice(this.editedIndex, 1);
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
        Object.assign(this.categories[this.editedIndex], this.editedItem);

        if (this.$refs.form.validate()) {
          this.loading = true;
          axios
            .put("/categories/" + this.editedItem.id, {
              name: this.editedItem.name,
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
            .post("/categories", {
              name: this.editedItem.name,
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
