<template>
    <alert-with-retry
        v-if="errorText"
        :value="errorText"
        @retry="fethData"
    />
    <div v-else-if="transaction">
        <transaction-form
            :transaction="transaction"
            :disabled="isBusy"
            @submit="updateItem"
            @delete="deleteItem"
        />
    </div>
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import transactionsApi from '@/api/accounting/transactions'
import AlertWithRetry from '@/components/alerts/AlertWithRetry'
import TransactionForm from '@/components/accounting/TransactionForm'
import showSnackbar from '@/snackbar'
export default {
    components: {
        AlertWithRetry,
        TransactionForm
    },
    props: {
        id: {
            required: true
        }
    },
    data () {
        return {
            transaction: null,
            errorText: null,
            isBusy: false
        }
    },
    async created () {
        this.fethData()
    },
    methods: {
        async fethData () {
            try {
                let data = await transactionsApi.find(this.id)
                this.transaction = data.data
            } catch (err) {
                this.errorText = err
            }
        },
        async updateItem (formData) {
            this.isBusy = true
            try {
                let data = await transactionsApi.update(this.id, formData)
                showSnackbar(data.message)
                window.document.location = this.route('accounting.transactions.show', this.id)
            } catch (err) {
                alert(err)
            }
            this.isBusy = false
        },
        async deleteItem () {
            this.isBusy = true
            try {
                let data = await transactionsApi.delete(this.id)
                showSnackbar(data.message)
                window.document.location = this.route('accounting.wallets.transactions.index', data.wallet_id)
            } catch (err) {
                alert(err)
            }
            this.isBusy = false
        }
    }
}
</script>
