
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import moment from 'moment';
import { Form, HasError, AlertError } from 'vform'
import Swal from 'sweetalert2'
import VueProgressBar from 'vue-progressbar'

import Gate from './gate';
//prototype the gate class
Vue.prototype.$gate = new Gate(window.user);


window.form = Form;
window.Swal = Swal;

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component('pagination', require('laravel-vue-pagination'));



import VueRouter from 'vue-router';
Vue.use(VueRouter);

let dashbord = require('./components/dashbord.vue').default;
//let page_not_found = require('./components/page_not_found.vue').default;

Vue.use(VueProgressBar,{
  color: '#bffaf3',
  failedColor: '#874b4b',
  thickness: '5px'
})

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  onOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})

window.Toast = Toast;

const routes = [
    { path: '/dashbord', component: dashbord },
    { path: '/profile', component: require('./components/profile.vue').default },
    { path: '/users', component: require('./components/users.vue').default},
    { path: '/developer', component:require('./components/developer.vue').default }
  ]


  const router = new VueRouter({
    routes // short for `routes: routes`
  });

  Vue.filter('uppercase', function(text){
    return text.charAt(0).toUpperCase() + text.slice(1);
  });

  Vue.filter('dateformate', function(_date){
    return _date.moment().format('MMMM Do YYYY');
  });

  /*custom event*/
  let Fire = new Vue();
  window.Fire = Fire;

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


Vue.component(
  'passport-clients',
  require('./components/passport/Clients.vue').default
);

Vue.component(
  'passport-authorized-clients',
  require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
  'passport-personal-access-tokens',
  require('./components/passport/PersonalAccessTokens.vue').default
);

Vue.component(
    'not-found',
    require('./components/page_not_found.vue').default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    data: {
      search:''
    },
    methods:{
      search_data: _.debounce(() => {
        Fire.$emit('search');
      },2000)
      
    }
});