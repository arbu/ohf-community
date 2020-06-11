import { api, route } from '@/api/baseApi'
export default {
    async listPeriods (walletId) {
        const url = route('api.accounting.webling.index', walletId)
        return await api.get(url)
    },
    async fetchPrepare (walletId, periodId, from, to) {
        const params = {
            wallet: walletId,
            period: periodId,
            from: from,
            to: to
        }
        const url = route('api.accounting.webling.prepare', params)
        return await api.get(url)
    },
    async store (walletId, periodId, payload) {
        const params = {
            wallet: walletId,
            period: periodId,
        }
        const url = route('api.accounting.webling.store', params)
        return await api.post(url, payload)
    }
}
