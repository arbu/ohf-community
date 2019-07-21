const prefix = '/api/volunteers';

export default {
    listVolunteers(scope) {
        return axios.get(prefix + '?scope=' + scope)
    },
    createVolunteer(data) {
        return axios.post(prefix, data);
    },
    getVolunteer(id) {
        return axios.get(prefix + '/' + id);
    },
    updateVolunteer(id, data) {
        return axios.put(prefix + '/' + id, data);
    },
    deleteVolunteer(id) {
        return axios.delete(prefix + '/' + id);
    },
}