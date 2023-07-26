<template>
  <div>
    <v-navigation-drawer
      v-if="$vuetify.breakpoint.xsOnly"
      v-model="drawer"
      :clipped="clipped"
      absolute
      temporary
    >
      <v-list>
        <v-list-item>
          <v-btn text>
            Inicio
          </v-btn>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-app-bar
      :clipped-left="clipped"
      app
      dark
    >
      <v-app-bar-nav-icon v-if="$vuetify.breakpoint.xsOnly" @click.stop="drawer = !drawer" />
      <v-toolbar-title><v-img max-height="75" max-width="75" src="/images/logo.png"></v-img></v-toolbar-title>
      <a href="/home">Sistema Ganadero v1.0</a>
      <!-- <v-btn v-if="$vuetify.breakpoint.xsOnly!==true"  text active-class="bb">
        Inicio
      </v-btn> -->
      <v-spacer />
      <v-menu
        offset-y 
        bottom
        left
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            icon
            outlined
            v-bind="attrs"
            v-on="on"
          >
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

        <v-list dense>
          <v-list-item v-if="this.user == null" href="/login">
            <v-list-item-action>
              <v-icon>mdi-login</v-icon>
            </v-list-item-action>
            <v-list-item-title>Iniciar Sesión</v-list-item-title>
          </v-list-item>
          <v-list-item v-if="this.role == 'admin'" href="/home">
            <v-list-item-action>
              <v-icon>mdi-account-cog</v-icon>
            </v-list-item-action>
            <v-list-item-title>Administrador</v-list-item-title>
          </v-list-item>
          <v-list-item v-if="this.user" href="/logout">
            <v-list-item-action>
              <v-icon>mdi-exit-to-app</v-icon>
            </v-list-item-action>
            <v-list-item-title>Cerrar Sesion</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <!-- <v-avatar
        size="45"
      >
        <v-icon>
          mdi-account-circle
        </v-icon>
      </v-avatar> -->
    </v-app-bar>
  </div>
</template>
<script>
//import { mapGetters } from 'vuex'
export default {
  props: ['user', 'role'],
  data () {
    return {
      clipped: true,
      drawer: false,
    }
  },
  mounted(){
    //console.log(this.user)
  }
  /* computed: {
    ...mapGetters([
      'cart'
    ])
  } */
}
</script>
