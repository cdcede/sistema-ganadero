<template>
  <div>
    <v-navigation-drawer
      v-model="drawer"
      :mini-variant="miniVariant"
      :clipped="clipped"
      fixed
      app
    >
    <v-list>
      <v-list-item
        href="/home"
        exact
      >
        <v-list-item-action>
          <v-icon>mdi-view-dashboard</v-icon>
        </v-list-item-action>
        <v-list-item-content>
          Inicio
        </v-list-item-content>
      </v-list-item>
      <v-list-group
        prepend-icon="mdi-cow"
        no-action
      >
        <template v-slot:activator>
          <v-list-item-content>
            <v-list-item-title>Ganado</v-list-item-title>
          </v-list-item-content>
        </template>

        <v-list-item href="/ganado">
          <v-list-item-title>Registrar ganado</v-list-item-title>
          <v-list-item-icon>
            <v-icon>mdi-pencil-plus</v-icon>
          </v-list-item-icon>
        </v-list-item>
      </v-list-group>
      <v-list-group
        prepend-icon="mdi-hospital-box"
        no-action
      >
        <template v-slot:activator>
          <v-list-item-content>
            <v-list-item-title>Medicinas</v-list-item-title>
          </v-list-item-content>
        </template>

        <v-list-item v-if="user.roles[0].slug === 'superadmin' || user.roles[0].slug === 'admin'" href="/medicinas">
          <v-list-item-title>Registro Medicinas</v-list-item-title>
          <v-list-item-icon>
            <v-icon>mdi-pencil-plus</v-icon>
          </v-list-item-icon>
        </v-list-item>
        <v-list-item v-if="user.roles[0].slug === 'superadmin' || user.roles[0].slug === 'admin'" href="/ingresar-medicina">
          <v-list-item-title>Ingresar Medicina</v-list-item-title>
          <v-list-item-icon>
            <v-icon>mdi-pill</v-icon>
          </v-list-item-icon>
        </v-list-item>
        <v-list-item href="/aplicar-medicina">
          <v-list-item-title>Aplicar Medicina</v-list-item-title>
          <v-list-item-icon>
            <v-icon>mdi-needle</v-icon>
          </v-list-item-icon>
        </v-list-item>
      </v-list-group>
      <v-list-item
        href="/kardex"
        exact
      >
        <v-list-item-action>
          <v-icon>mdi-table-eye</v-icon>
        </v-list-item-action>
        <v-list-item-content>
          Kardex
        </v-list-item-content>
      </v-list-item>
      <v-list-group
        v-if="user.roles[0].slug === 'superadmin' || user.roles[0].slug === 'admin'"
        prepend-icon="mdi-application-cog"
        no-action
      >
        <template v-slot:activator>
          <v-list-item-content>
            <v-list-item-title>Administrador</v-list-item-title>
          </v-list-item-content>
        </template>

        <v-list-item href="/usuarios" link>
          <v-list-item-title>Usuarios</v-list-item-title>
          <v-list-item-icon>
            <v-icon>mdi-account-cowboy-hat</v-icon>
          </v-list-item-icon>
        </v-list-item>
        <v-list-item href="/reportes">
          <v-list-item-title>Reportes</v-list-item-title>
          <v-list-item-icon>
            <v-icon>mdi-chart-bar</v-icon>
          </v-list-item-icon>
        </v-list-item>
        <v-list-group
          no-action
          sub-group
        >
          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>Parametros</v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item
            href="/marcas"
            link
          >
            <v-list-item-title>Marcas</v-list-item-title>

            <v-list-item-icon>
              <v-icon>mdi-archive</v-icon>
            </v-list-item-icon>
          </v-list-item>
          <v-list-item
            href="/categorias"
            link
          >
            <v-list-item-title>Categorías</v-list-item-title>

            <v-list-item-icon>
              <v-icon>mdi-shape</v-icon>
            </v-list-item-icon>
          </v-list-item>
          <v-list-item
            href="/razas"
            link
          >
            <v-list-item-title>Razas</v-list-item-title>

            <v-list-item-icon>
              <v-icon>mdi-barn</v-icon>
            </v-list-item-icon>
          </v-list-item>
        </v-list-group>
      </v-list-group>
    </v-list>
    </v-navigation-drawer>
    <v-app-bar
      :clipped-left="clipped"
      fixed
      app
      >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer" />

      <a href= "/"><v-img max-height="75" max-width="75" src="/images/logo.png"></v-img></a>
      Sistema Ganadero v1.0
      <v-spacer />
      <v-menu
        offset-y   
        bottom
        left
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            icon
            v-bind="attrs"
            v-on="on"
          >
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

        <v-list dense>
          <!-- <v-list-item >
            <v-list-item-action>
              <v-icon>mdi-account</v-icon>
            </v-list-item-action>
            <v-list-item-title>Perfil</v-list-item-title>
          </v-list-item> -->
          <v-list-item href="/logout">
            <v-list-item-action>
              <v-icon>mdi-exit-to-app</v-icon>
            </v-list-item-action>
            <v-list-item-title>Cerrar Sesión</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <!-- <v-avatar
        color="indigo"
        size="45"
      >
        <v-icon dark>
          mdi-account-circle
        </v-icon>
      </v-avatar> -->
    </v-app-bar>
  </div>
</template>
<script>
export default {
  props: ['user'],
  data () {
    return {
      clipped: true,
      drawer: true,
      items: [
        {
          icon: 'mdi-apps',
          title: 'Welcome',
          to: '/'
        },
        {
          icon: 'mdi-chart-bubble',
          title: 'Inspire',
          to: '/inspire'
        }
      ],
      miniVariant: false,
      title: 'Sistema Ganadero v1.0'
    }
  },
  mounted(){
    //console.log(this.user)
  },
  methods:{
    onClickImage() {
      window.location.href('/')
    },
  },
}
</script>
