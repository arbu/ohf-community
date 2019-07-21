// Main app
import VolunteerApp from './VolunteerApp.vue';
window.Vue.component('volunteer-app', VolunteerApp);

// Components
import LoadingIndicator from './components/LoadingIndicator.vue';
window.Vue.component('loading-indicator', LoadingIndicator);

// Views
import VolunteerList from './views/VolunteerList.vue';
import VolunteerDetails from './views/VolunteerDetails.vue';

import VueRouter from 'vue-router'
Vue.use(VueRouter)

const PageNotFound = Vue.component("page-not-found", {
    template: "",
    created: function() {
        // Redirect outside the app using plain old javascript
        window.location.href = "/not-found.html";
    }
});

const defaultScope = 'active'
const allowedScopes = ['active','future','applied','previous']

const router = new VueRouter({
    base: '/volunteers',
    mode: 'history',
    routes: [
        { path: '/', redirect: '/' + defaultScope },
        { path: '/:scope(' + allowedScopes.join('|') + ')', name: 'volunteers-index', component: VolunteerList, props: true },
        { path: '/volunteer/:volunteer_id', name: 'volunteers-show', component: VolunteerDetails, props: true },
        { path: "*", component: PageNotFound }
    ]
})

import NProgress from 'nprogress';
NProgress.configure({ showSpinner: false });
// router.beforeResolve((to, from, next) => {
//     if (to.name) {
//         NProgress.start()
//     }
//     next()
// })

// router.afterEach((to, from) => {
//     NProgress.done()
// })

// before a request is made start the nprogress
axios.interceptors.request.use(config => {
    NProgress.start()
    return config
})
  
// before a response is returned stop nprogress
axios.interceptors.response.use(response => {
    NProgress.done()
    return response
}, error => {
    NProgress.done()
    return Promise.reject(error);
})

const app = new Vue({
    el: '#app',
    router: router
});