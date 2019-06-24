
import VolunteerApp from './components/VolunteerApp.vue';
window.Vue.component('volunteer-app', VolunteerApp);

import VolunteerList from './components/VolunteerList.vue';
window.Vue.component('volunteer-list', VolunteerList);

const app = new Vue({
    el: '#app'
});