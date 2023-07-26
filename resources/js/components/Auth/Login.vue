<style scoped>
.transparente{
  opacity: 0.8;
  -moz-opacity: 0.8;
  filter: alpha(opacity=80);
  -khtml-opacity: 0.8;
}
</style>
<template>
  <v-col cols="12" lg="5" xl="6" class="ma-auto">
      <v-card shaped elevation="4" class="transparente">
        <v-btn
          fab
          large
          top
          class="info"
          href="/"
        >
          <v-icon>mdi-home</v-icon>
        </v-btn>
        <div class="pa-7 pa-sm-12">
          <v-row>
            <v-col cols="12" lg="12" xl="12">
              <h1 class="font-weight-bold mt-4 blue-grey--text text--darken-2 text-center">Iniciar Sesión</h1>
              <v-form
                ref="form"
                v-model="valid"
                lazy-validation
              >
                <v-text-field
                  v-model="email"
                  label="Email"
                  :rules="emailRules"
                  outlined
                  required
                  autofocus
                  dense
                ></v-text-field>
            
                <v-text-field
                  v-model="password"
                  label="Contraseña"
                  :rules="passwordRules"
                  outlined
                  dense
                  required
                  :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="show ? 'text' : 'password'"
                  @keyup.enter="login"
                  @click:append="show = !show"
                ></v-text-field>

                <!-- <div class="d-block d-sm-flex align-center mb-4 mb-sm-0">
                  <v-checkbox
                    v-model="remember"
                    label="Recuerdame?"
                  ></v-checkbox>
                  <div class="ml-auto">
                    <a href="javascript:void(0)" class="link">Olvido la contraseña?</a>
                  </div>
                </div> -->
                <v-btn
                  :disabled="!valid"
                  color="primary"
                  block
                  rounded
                  submit
                  @click="login"
                >
                  Ingresar
                </v-btn>
              </v-form>
            </v-col>
          </v-row>
        </div>
      </v-card>
    </v-col>   
</template>
<script>
export default {
  data(){
    return{
      show: false,
      valid: false,
      email: '',
      password: '',
      remember: false,
      passwordRules: [
        (v) => !!v || "Contraseña es obligatoria",
      ],
      emailRules: [
        (v) => !!v || "E-mail es obligatorio",
        (v) =>
          /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
          "Correo debe ser válido",
      ],
    }
  },
  methods: {
    login(){
      if (this.$refs.form.validate()) {

        axios.post('/login', {
        email: this.email,
        password: this.password,
        remember: this.remember
      })
        .then(response => {

          //console.log(response.data.message);
          window.location.href = '/home';

        }).catch(error => {

          this.$swal({
            icon: 'error',
            title: 'Oops...',
            text: error.response.data.errors.email[0],
            showConfirmButton: false,
            timer: 2000
          });
          //console.log(error.response.data.message);
          /* Swal.fire({
            icon: 'error',
            title: error.response.data.errors.username[0],
            showConfirmButton: false,
            timer: 1500
          }) */
        });
      }
    }
  }
}
</script>
