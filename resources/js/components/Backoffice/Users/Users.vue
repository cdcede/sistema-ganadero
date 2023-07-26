<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="users"
      :search="search"
      sort-by="id"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title
            ><v-icon>mdi-account-group</v-icon> Usuarios</v-toolbar-title
          >
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" persistent max-width="600px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                <v-icon>mdi-plus</v-icon> Nuevo Usuario
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
                              v-model="editedItem.first_name"
                              :rules="[v => !!v || 'Nombre es obligatorio', rules.lettersOnly, rules.min]"
                              label="Nombre*"
                              prepend-icon="mdi-card-account-details"
                              required
                              dense
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <v-text-field
                              v-model="editedItem.last_name"
                              :rules="[v => !!v || 'Apellido es obligatorio', rules.lettersOnly, rules.min]"
                              label="Apellido*"
                              prepend-icon="mdi-card-account-details"
                              required
                              dense
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <v-text-field
                              v-model="editedItem.username"
                              :rules="[v => !!v || 'Usuario es obligatorio']"
                              label="Usuario*"
                              prepend-icon="mdi-account-circle"
                              required
                              dense
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <v-text-field
                              v-model="editedItem.email"
                              :rules="emailRules"
                              label="Correo*"
                              prepend-icon="mdi-email"
                              required
                              dense
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="12" md="12" v-if= "formTitle === 'Editar Usuario'">
                            <v-checkbox
                              v-model="editPassword.edit"
                              label="Modificar Contraseña"
                            ></v-checkbox>
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <v-text-field v-if="editPassword.edit === true || editedItem.pass !== false"
                              v-model="editedItem.password"
                              :rules="passwordRules"
                              label="Contraseña*"
                              required
                              prepend-icon="mdi-lock"
                              :append-icon="showP ? 'mdi-eye' : 'mdi-eye-off'"
                              :type="showP ? 'text' : 'password'"
                              @click:append="showP = !showP"
                              dense
                            ></v-text-field>
                            <!-- <v-progress-linear
                              :color="score.color"
                              :value="score.value"
                            ></v-progress-linear> -->
                          </v-col>
                          <v-col cols="12" sm="6" md="6">
                            <v-text-field v-if="editPassword.edit === true || editedItem.pass !== false"
                              v-model="editedItem.password_confirmation"
                              :rules="confirmPasswordRules.concat(passwordConfirmationRule)"
                              label="Confirmar contraseña*"
                              required
                              prepend-icon="mdi-lock"
                              :append-icon="showCP ? 'mdi-eye' : 'mdi-eye-off'"
                              :type="showCP ? 'text' : 'password'"
                              @click:append="showCP = !showCP"
                              dense
                            ></v-text-field>
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                          >
                            <v-select
                              v-model="editedItem.role_id"
                              :items="roles"
                              :item-text="'slug'"
                              :item-value="'id'"
                              :rules="[v => !!v || 'Rol es obligatorio']"
                              label="Selecciona el Rol*"
                              prepend-icon="mdi-account-lock-outline"
                              dense
                            ></v-select>
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
                  <!-- {{editedItem}} -->
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
      <template v-slot:[`item.rol`]="{ item }">
        <v-chip v-if="item.rol === 'superadmin'" color="success">{{item.rol}}</v-chip>
        <v-chip v-if="item.rol === 'admin'" color="secondary">{{item.rol}}</v-chip>
        <v-chip v-if="item.rol === 'trabajador'" color="info">{{item.rol}}</v-chip>
      </template>
      <template v-slot:[`item.actions`]="{ item }">
        <v-icon small class="mr-2" @click="editItem(item, editPassword)"> mdi-pencil </v-icon>
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
//const zxcvbn = require('zxcvbn');
import zxcvbn from "zxcvbn";
export default {
  data: () => ({
    dialog: false,
    dialogDelete: false,
    loader: null,
    loading: false,
    valid: false,
    showP: false,
    showCP: false,
    editPassword: {edit:false, pass: false},
    search: "",
    /* nameRules: [
      (v) => !!v || "Nombre es obligatorio",
      (v) => (v && v.length <= 10) || "Name must be less than 10 characters",
    ], */
    rules: {
      min: (v) => (v && v.length >=2 ) || "Minimo 2 caracteres",
      lettersOnly: (v) =>
          /^[A-Za-z]+$/.test(v) || "Solo debe ingresar letras",
    },    
    emailRules: [
      (v) => !!v || "Correo es obligatorio",
      (v) =>
        /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
        "Correo debe ser válido",
    ],
    passwordRules: [
      v => !!v || 'Contraseña es obligatorio',
      //v => v.length >= 8 || 'Usa 8 caracteres o mas para tu contraseña',
      v => {
        const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;
        //const pattern = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/;
        return (
          pattern.test(v) ||
          "Minimo 8 caracteres con al menos una mayuscula, un numero y un caracter especial."
        );
      }
      //v => zxcvbn(v).score >= 3 || 'Por favor usa una contraseña mas fuerte. Intenta una combinacion de letras, numeros y simbolos.',
    ],
    confirmPasswordRules: [
        (v) => !!v || 'Confirmar contraseña',
      ],
    headers: [
      {
        text: "ID",
        align: "start",
        sortable: false,
        value: "id",
      },
      { text: "Usuario", value: "username" },
      { text: "Nombre", value: "first_name" },
      { text: "Apellido", value: "last_name" },
      { text: "Email", value: "email" },
      { text: "Rol", value: "rol" },
      { text: "Estado", value: "status" },
      { text: "Acciones", value: "actions", sortable: false },
    ],
    users: [],
    roles: [],
    editedIndex: -1,
    editedItem: {
      username: "",
      first_name: "",
      last_name: "",
      email: "",
      password: "",
      password_confirmation: "",
      role_id: "",
      status: true,
    },
    defaultItem: {
      username: "",
      first_name: "",
      last_name: "",
      email: "",
      password: "",
      password_confirmation: "",
      role_id: "",
      status: true,
    },
  }),
  mounted() {
    //console.log( zxcvbn('Tr0ub4dour&3'));
    this.getData();
    this.getRoles();
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo Usuario" : "Editar Usuario";
    },
    passwordConfirmationRule() {
      return () =>
        this.editedItem.password === this.editedItem.password_confirmation || "Las contraseñas NO coinciden.";
    },
    /* score() {
      const result = zxcvbn(this.editedItem.password);
      
      switch (result.score) {
        case 4:
          return {
            color: "light-blue",
            value: 100
          };
        case 3:
          return {
            color: "light-green",
            value: 75
          };
        case 2:
          return {
            color: "yellow",
            value: 50
          };
        case 1:
          return {
            color: "orange",
            value: 25
          };
        default:
          return {
            color: "red",
            value: 0
          };
      }
    }, */
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
    getRoles() {
        axios.post("/roles")
          .then((response) => {
            this.roles = response.data.data;
          })
          .catch((err) => {
            //console.log(err.response.data.message);
          });
      },
    getData() {
      axios
        .get("/users")
        .then((response) => {
          this.users = response.data.data;
          //console.log(this.users);
        })
        .catch((err) => {
          //console.log(err.response.data.message);
        });
    },
    editItem(item) {
      this.editedIndex = this.users.indexOf(item);
      this.editedItem = Object.assign({}, item, this.editPassword);
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedIndex = this.users.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      axios
        .delete("/users/" + this.editedItem.id)
        .then((response) => {

          this.users.splice(this.editedIndex, 1);
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
        Object.assign(this.users[this.editedIndex], this.editedItem);

        if (this.$refs.form.validate()) {
          this.loading = true;
          axios
            .put("/users/" + this.editedItem.id, {
              username: this.editedItem.username,
              first_name: this.editedItem.first_name,
              last_name: this.editedItem.last_name,
              email: this.editedItem.email,
              password: this.editedItem.password,
              password_confirmation: this.editedItem.password_confirmation,
              role_id: this.editedItem.role_id,
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
            .post("/users", {
              username: this.editedItem.username,
              first_name: this.editedItem.first_name,
              last_name: this.editedItem.last_name,
              email: this.editedItem.email,
              password: this.editedItem.password,
              password_confirmation: this.editedItem.password_confirmation,
              role_id: this.editedItem.role_id,
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
