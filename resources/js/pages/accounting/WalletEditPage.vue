<template>
    <div v-if="wallet">
        <wallet-form
            :wallet="wallet"
            :disabled="isBusy"
            @submit="updateWallet"
            @delete="deleteWallet"
        />
    </div>
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import walletApi from '@/api/accounting/wallets'
import WalletForm from '@/components/accounting/WalletForm'
import showSnackbar from '@/snackbar'
export default {
    components: {
        WalletForm
    },
    props: {
        id: {
            required: true,
        }
    },
    data () {
        return {
            wallet: null,
            isBusy: false,
        }
    },
    mounted () {
        this.fetchData()
    },
    methods: {
        async fetchData () {
            try {
                let data = await walletApi.find(this.id)
                this.wallet = data.data
            } catch (err) {
                alert(err)
            }
        },
        async updateWallet (formData) {
            this.isBusy = true
            try {
                let data = await walletApi.update(this.id, formData)
                showSnackbar(data.message)
                window.document.location = this.route('accounting.wallets.index')
            } catch (err) {
                alert(err)
            }
            this.isBusy = false
        },
        async deleteWallet () {
            this.isBusy = true
            try {
                let data = await walletApi.delete(this.id)
                showSnackbar(data.message)
                window.document.location = this.route('accounting.wallets.index')
            } catch (err) {
                alert(err)
            }
            this.isBusy = false
        }
    }
}
</script>
