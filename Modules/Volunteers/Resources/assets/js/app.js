import VolunteerApp from './components/VolunteerApp.vue';
window.Vue.component('volunteer-app', VolunteerApp);

import VolunteerList from './components/VolunteerList.vue';
window.Vue.component('volunteer-list', VolunteerList);

import VolunteerDetails from './components/VolunteerDetails.vue';
window.Vue.component('volunteer-details', VolunteerDetails);

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
        { path: '/', name: 'volunteer-index', component: VolunteerList },
        { path: '/volunteer/:volunteer_id', name: 'volunteer-show', component: VolunteerDetails, props: true },
        { path: "*", component: PageNotFound }
    ]
})

const app = new Vue({
    el: '#app',
    router: router
});