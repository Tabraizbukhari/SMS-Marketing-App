require('./bootstrap');
require('alpinejs');
require('./backend.js');

import $ from 'jquery';
import select2 from 'select2';
import InfiniteLoading from 'vue-infinite-loading';

window.Vue = require('vue');
// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
const app = new Vue({
    el: '#app',
});

Vue.use(InfiniteLoading);