import VolunteerApp from './components/VolunteerApp.vue';
window.Vue.component('volunteer-app', VolunteerApp);

import VolunteerList from './components/VolunteerList.vue';
window.Vue.component('volunteer-list', VolunteerList);

import VueRouter from 'vue-router'
Vue.use(VueRouter)

const router = new VueRouter({
    base: '/volunteers',
    routes: [
        { path: '/', component: VolunteerList },
    ]
})

const app = new Vue({
    el: '#app',
    router: router
});