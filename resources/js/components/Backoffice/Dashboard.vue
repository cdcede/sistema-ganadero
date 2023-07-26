<style scoped>
  .bold {
    font-weight: bold;
    font-size: 25px;
  }
  .transition {
    transition: all 0.3s ease-in;
  }
</style>
<template>
    <v-container fluid>
      <v-row dense>
        <v-col cols="12" md="3" sm="3">
          <v-card
            elevation="2"
            outlined
            class="indigo darken-1"
            dark
          >
            <v-card-title>Total de animales</v-card-title>
            <v-card-subtitle>Número de animales registrados.</v-card-subtitle>
            <center>
              <number
              class="bold transition"
              ref="number1"
              :from="0"
              :to="livestocks"
              :duration="3"
              easing="Power1.easeOut"/>
            </center>
            <v-card-actions>
              <v-icon x-large>mdi-cow</v-icon>
              <v-spacer />
              <v-btn color="primary" href="/ganado">Ver más</v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12" md="3" sm="3">
          <v-card
            elevation="2"
            outlined
            class="green darken-3"
            dark
          >
            <v-card-title>Medicinas aplicadas</v-card-title>
            <v-card-subtitle>Número de medicinas aplicadas.</v-card-subtitle>
            <center>
              <number
              class="bold transition"
              ref="number1"
              :from="0"
              :to="livestockMedicines"
              :duration="3"
              easing="Power1.easeOut"/>
            </center>
            <v-card-actions>
              <v-icon x-large>mdi-needle</v-icon>
              <v-spacer />
              <v-btn color="primary" href="/aplicar-medicina"><v-icon>mdi-eye</v-icon></v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12" md="3" sm="3">
          <v-card
            elevation="2"
            outlined
            class="teal darken-3"
            dark
          >
            <v-card-title>Total de usuarios</v-card-title>
            <v-card-subtitle>Número de usuarios registrados.</v-card-subtitle>
            <center>
              <number
              class="bold transition"
              ref="number1"
              :from="0"
              :to="users"
              :duration="3"
              easing="Power1.easeOut"/>
            </center>
            <v-card-actions>
              <v-icon x-large>mdi-account</v-icon>
              <v-spacer />
              <v-btn color="primary" href="/usuarios"><v-icon>mdi-plus-circle</v-icon></v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12" md="3" sm="3">
          <v-card
            elevation="2"
            outlined
            class="orange darken-2"
            dark
          >
            <v-card-title>Registro de medicinas</v-card-title>
            <v-card-subtitle>Número de medicinas registradas.</v-card-subtitle>
            <center>
              <number
              class="bold transition"
              ref="number1"
              :from="0"
              :to="medicines"
              :duration="3"
              easing="Power1.easeOut"/>
            </center>
            <v-card-actions>
              <v-icon x-large>mdi-account</v-icon>
              <v-spacer />
              <v-btn color="primary" href="/medicinas"><v-icon>mdi-plus</v-icon></v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <!-- <v-col cols="4">
          <v-card
            elevation="2"
            outlined
          >
            <v-card-title>Total de usuarios</v-card-title>
            <v-card-subtitle>Número de usuarios registrados.</v-card-subtitle>
            <center>
              <number
              class="bold transition"
              ref="number1"
              :from="0"
              :to="50"
              :duration="5"
              easing="Power1.easeOut"/>
            </center>
            <v-card-actions>
              <v-icon x-large>mdi-account</v-icon>
              <v-spacer />
              <v-btn color="primary"><v-icon>mdi-plus-box</v-icon></v-btn>
            </v-card-actions>
          </v-card>
        </v-col> -->
      </v-row>
      <v-row>
        <v-col cols="12" md="6" sm="6">
          <pie-chart></pie-chart>
        </v-col>
        <v-col cols="12" md="6" sm="6">
          <bar-chart></bar-chart>
        </v-col>
      </v-row>
    </v-container>
</template>

<script>
  export default {
    data: () => ({
      livestocks: 0,
      medicines: 0,
      users: 0,
      livestockMedicines: 0,
      
    }),
    mounted(){
      this.getDashboard();
    },
    methods:{
      theFormat(number) {
        return number.toFixed(2);
      },
      getDashboard() {
        axios.get("/show-dashboard")
        .then((response) => {
          this.livestocks = response.data.data.livestocks;
          this.medicines = response.data.data.medicines;
          this.users = response.data.data.users;
          this.livestockMedicines = response.data.data.livestockMedicines;
        })
        .catch((err) => {
            //console.log(err.response.data.message);
        });
      },
    }
  }
</script>
