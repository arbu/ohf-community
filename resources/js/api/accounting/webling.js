import { api, route } from '@/api/baseApi'
export default {
    async listPeriods () {
        const url = route('api.accounting.webling.index')
        return await api.get(url)
    },
    async fetchPrepare (periodId, from, to) {
        const params = {
            perioid: periodId,
            from: from,
            to: to
        }
        const url = route('api.accounting.webling.prepare', params)
        return await api.get(url)
    }
}
