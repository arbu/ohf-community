import Vue from 'vue'

import FontAwesomeIcon from '@/components/common/FontAwesomeIcon'
Vue.component('font-awesome-icon', FontAwesomeIcon)

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

// import store from '@/store'

import i18n from '@/plugins/i18n'
// import router from '@/router/accounting'

import '@/plugins/vee-validate'

import ziggyMixin from '@/mixins/ziggyMixin'
Vue.mixin(ziggyMixin)

Vue.config.productionTip = false

// import AccountingApp from '@/app/AccountingApp'
import TransactionsIndexPage from '@/pages/accounting/TransactionsIndexPage'
import TransactionShowPage from '@/pages/accounting/TransactionShowPage'
import TransactionEditPage from '@/pages/accounting/TransactionEditPage'
import TransactionCreatePage from '@/pages/accounting/TransactionCreatePage'
import TransactionSummaryPage from '@/pages/accounting/TransactionSummaryPage'
import ExportPage from '@/pages/accounting/ExportPage'
import WalletIndexPage from '@/pages/accounting/WalletIndexPage'
import WalletEditPage from '@/pages/accounting/WalletEditPage'
import WalletCreatePage from '@/pages/accounting/WalletCreatePage'
import WeblingIndexPage from '@/pages/accounting/WeblingIndexPage'

new Vue({
    el: '#accounting-app',
    // store,
    // router,
    i18n,
    components: {
        // AccountingApp
        TransactionsIndexPage,
        TransactionShowPage,
        TransactionEditPage,
        TransactionCreatePage,
        TransactionSummaryPage,
        ExportPage,
        WalletIndexPage,
        WalletEditPage,
        WalletCreatePage,
        WeblingIndexPage
    }
});
