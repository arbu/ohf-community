<template>
    <wallet-form
        :disabled="isBusy"
        @submit="storeWallet"
    />
</template>

<script>
import walletApi from '@/api/accounting/wallets'
import WalletForm from '@/components/accounting/WalletForm'
import showSnackbar from '@/snackbar'
export default {
    components: {
        WalletForm
    },
    data () {
        return {
            isBusy: false,
        }
    },
    methods: {
        async storeWallet (formData) {
            this.isBusy = true
            try {
                let data = await walletApi.store(formData)
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
