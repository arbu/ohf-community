import { api, route } from '@/api/baseApi'
export default {
    async listPeriods () {
        const url = route('api.accounting.webling.index')
        return await api.get(url)
    }
}
