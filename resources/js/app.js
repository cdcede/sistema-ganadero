/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import vuetify from './vuetify';
import VueSweetalert2 from 'vue-sweetalert2';
import HighchartsVue from 'highcharts-vue';
import Highcharts from 'highcharts';
import HighchartsNoData from 'highcharts/modules/no-data-to-display';
// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';
Vue.use(VueSweetalert2);
Vue.use(HighchartsVue);
HighchartsNoData(Highcharts);

import VueNumber from 'vue-number-animation'
Vue.use(VueNumber)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
//Landing page
Vue.component('top-nav', require('./components/Landing/TopNav.vue').default);
Vue.component('landing-nav', require('./components/Landing/Nav.vue').default);
//Auth
Vue.component('login-form', require('./components/Auth/Login.vue').default);
//Backoffice
    //Users
Vue.component('users', require('./components/Backoffice/Users/Users.vue').default);
    //Livestock
Vue.component('livestocks', require('./components/Backoffice/Livestocks/Livestocks.vue').default);
    //Marks
Vue.component('marks', require('./components/Backoffice/Marks/Marks.vue').default);
    //Categories
Vue.component('categories', require('./components/Backoffice/Categories/Categories.vue').default);
    //Breeds
Vue.component('breeds', require('./components/Backoffice/Breeds/Breeds.vue').default);
    //Medicines
Vue.component('medicines', require('./components/Backoffice/Medicines/Medicines.vue').default);
Vue.component('medicine-entry', require('./components/Backoffice/Medicines/MedicineEntry.vue').default);
    //Livestock Medicines
Vue.component('livestock-medicines', require('./components/Backoffice/LivestockMedicines/LivestockMedicines.vue').default);
//Sidebar
Vue.component('sidebar', require('./components/Backoffice/Partials/Sidebar.vue').default);
//Dashboard
Vue.component('dashboard', require('./components/Backoffice/Dashboard.vue').default);
//Reports
Vue.component('reports', require('./components/Backoffice/Reports/Reports.vue').default);
Vue.component('pie-chart', require('./components/Backoffice/Reports/PieChart.vue').default);
Vue.component('bar-chart', require('./components/Backoffice/Reports/BarChart.vue').default);

Vue.component('kardex', require('./components/Backoffice/Kardex/Kardex.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    vuetify
});
