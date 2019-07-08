import VolunteerApp from './components/VolunteerApp.vue';
window.Vue.component('volunteer-app', VolunteerApp);

import VolunteerList from './components/VolunteerList.vue';
window.Vue.component('volunteer-list', VolunteerList);

import VueRouter from 'vue-router'
Vue.use(VueRouter)

const PageNotFound = Vue.component("page-not-found", {
    template: "",
    created: function() {
        // Redirect outside the app using plain old javascript
        window.location.href = "/not-found.html";
    }
});

const router = new VueRouter({
    base: '/volunteers',
    mode: 'history',
    routes: [
        { path: '/', component: VolunteerList },
        { path: "*", component: PageNotFound }
    ]
})

const app = new Vue({
    el: '#app',
    router: router
});