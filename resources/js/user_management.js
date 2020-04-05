import Vue from 'vue'

import FontAwesomeIcon from './components/common/FontAwesomeIcon'
Vue.component('font-awesome-icon', FontAwesomeIcon)

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

import RoleManagementPage from './pages/user_management/RoleManagementPage.vue'

Vue.config.productionTip = false

import i18n from './i18n'

new Vue({
    el: '#user-management-app',
    i18n,
    components: {
        RoleManagementPage
    }
});