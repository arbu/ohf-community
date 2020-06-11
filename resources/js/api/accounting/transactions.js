import { api, route } from '@/api/baseApi'
export default {
    async list (walletId, params = {}) {
        params.wallet = walletId
        const url = route('api.accounting.wallets.transactions.index', params)
        return await api.get(url)
    },
    async find (id) {
        const url = route('api.accounting.transactions.show', id)
        return await api.get(url)
    },
    async store (walletId, data) {
        const url = route('api.accounting.wallets.transactions.store', walletId)
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
    async fetchSummary (walletId, params = {}) {
        params.wallet = walletId
        const url = route('api.accounting.wallets.transactions.summary', params)
        return await api.get(url)
    },
    async fetchExportData (walletId) {
        const url = route('api.accounting.wallets.transactions.export', walletId)
        return await api.get(url)
    },
    async fetchSettings () {
        const url = route('api.accounting.transactions.settings')
        return await api.get(url)
    },
    async fetchfilterClassifications () {
        const url = route('api.accounting.transactions.filterClassifications')
        return await api.get(url)
    },
}
