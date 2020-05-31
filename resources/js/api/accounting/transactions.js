import { api, route } from '@/api/baseApi'
export default {
    async list (walletId, params = {}) {
        params.wallet_id = walletId
        const url = route('api.accounting.transactions.index', params)
        return await api.get(url)
    },
    async find (id) {
        const url = route('api.accounting.transactions.show', id)
        return await api.get(url)
    },
    async store (data) {
        const url = route('api.accounting.transactions.store')
        return await api.post(url, data)
    },
    async update (id, data) {
        const url = route('api.accounting.transactions.update', id)
        return await api.put(url, data)
    },
    async delete (id) {
        const url = route('api.accounting.transactions.destroy', id)
        return await api.delete(url)
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
    },
    async undoExternalBooking (transactionId) {
        const url = route('api.accounting.transactions.undoBooking', transactionId)
        return await api.put(url)
    },
    async fetchSummary (params = {}) {
        const url = route('api.accounting.transactions.summary', params)
        return await api.get(url)
    },
    async fetchExportData () {
        const url = route('api.accounting.export')
        return await api.get(url)
    }
}
