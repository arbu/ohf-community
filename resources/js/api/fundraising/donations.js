import { api, route } from '@/api/baseApi'
export default {
    async list (params) {
        const url = route('api.fundraising.donations.index', params)
        return await api.get(url)
    },
    async update (id, data) {
        const url = route('api.fundraising.donations.update', id)
        return await api.put(url, data)
    },
    async delete (id) {
        const url = route('api.fundraising.donations.destroy', id)
        return await api.delete(url)
    },
    async listChannels () {
        const url = route('api.fundraising.donations.channels')
        return await api.get(url)
    },
    async listCurrencies () {
        const url = route('api.fundraising.donations.currencies')
        return await api.get(url)
    }
}
