import { api, route } from '@/api/baseApi'
export default {
    async list (params) {
        const url = route('api.fundraising.donations.index', params)
        return await api.get(url)
    },
    async store (donorId, data) {
        const url = route('api.fundraising.donations.store', donorId)
        return await api.post(url, data)
    },
    async update (id, data) {
        const url = route('api.fundraising.donations.update', id)
        return await api.put(url, data)
    },
    async delete (id) {
        const url = route('api.fundraising.donations.destroy', id)
        return await api.delete(url)
    }
}
