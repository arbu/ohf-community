<template>
    <div>
        <transaction-form
            :wallets="wallets"
            :wallet="wallet"
            :disabled="isBusy"
            @submit="storeDonation"
        />
    </div>
</template>

<script>
import transactionsApi from '@/api/accounting/transactions'
import TransactionForm from '@/components/accounting/TransactionForm'
import showSnackbar from '@/snackbar'
export default {
    components: {
        TransactionForm
    },
    props: {
        wallets: Array,
        wallet: Object,
    },
    data () {
        return {
            isBusy: false
        }
    },
    methods: {
        async storeDonation (formData) {
            this.isBusy = true
            try {
                let data = await transactionsApi.store(formData)
                showSnackbar(data.message)
                window.document.location = this.route('accounting.transactions.index')
            } catch (err) {
                alert(err)
            }
            this.isBusy = false
        }
    }
}
</script>
