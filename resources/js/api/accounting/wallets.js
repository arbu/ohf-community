import { api, route } from '@/api/baseApi'
export default {
    async list () {
        const url = route('api.accounting.wallets.index')
        return await api.get(url)
    },
    async find (id) {
        const url = route('api.accounting.wallets.show', id)
        return await api.get(url)
    },
    async store (data) {
        const url = route('api.accounting.wallets.store')
        return await api.post(url, data)
    },
    async update (id, data) {
        const url = route('api.accounting.wallets.update', id)
        return await api.put(url, data)
    },
    async delete (id) {
        const url = route('api.accounting.wallets.destroy', id)
        return await api.delete(url)
    }
}
