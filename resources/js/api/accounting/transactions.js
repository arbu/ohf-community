import { api, route } from '@/api/baseApi'
export default {
    async list (params) {
        const url = route('api.accounting.transactions.index', params)
        return await api.get(url)
    },
    async fetchCurrentWallet () {
        const url = route('api.accounting.transactions.currentWallet')
        return await api.get(url)
    },
    async fetchfilterClassifications () {
        const url = route('api.accounting.transactions.filterClassifications')
        return await api.get(url)
    },
    async updateReceiptPicture (transactionId, file) {
        const url = route('api.accounting.transactions.updateReceipt', transactionId)
        const formData = new FormData();
        formData.append('img', file)
        return await api.postFormData(url, formData)
    }
}
