import Vue from 'vue'

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

import AccountingTable from './components/AccountingTable'
import ImageViewer from '../../../../../resources/js/components/ImageViewer'

new Vue({
    el: '#accounting-app',
    components: {
        AccountingTable,
        ImageViewer
    }
});