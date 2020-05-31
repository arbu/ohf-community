import { api, route } from '@/api/baseApi'
export default {
    async listPeriods () {
        const url = route('api.accounting.webling.index')
        return await api.get(url)
    },
    async fetchPrepare (periodId, from, to) {
        const params = {
            period: periodId,
            from: from,
            to: to
        }
        const url = route('api.accounting.webling.prepare', params)
        return await api.get(url)
    },
    async store (periodId, payload) {
        const params = {
            period: periodId,
        }
        const url = route('api.accounting.webling.store', params)
        return await api.post(url, payload)
    }
}
