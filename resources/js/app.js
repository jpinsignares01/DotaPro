/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

// Importar Plugins
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import Multiselect from 'vue-multiselect';
import VueSweetalert2 from 'vue-sweetalert2';
import moment from 'moment';
import 'moment/locale/es'


// Importar Estilos
import 'vue-multiselect/dist/vue-multiselect.min.css';
import 'sweetalert2/dist/sweetalert2.min.css';

// Inicializamos plugin
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(VueSweetalert2)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Inicializamos los componentes globales
Vue.component('multiselect', Multiselect);
//
Vue.component('index-component', require('./components/IndexComponent.vue').default);

//Date filer
moment.locale('es');
Vue.filter('date_format', function (value) {
    return moment(value).format('DD/MM/YYYY');
})

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
