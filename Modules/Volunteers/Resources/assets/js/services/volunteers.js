const prefix = '/api/volunteers';

export default {
    listVolunteers(scope) {
        return axios.get(prefix + '?scope=' + scope)
    },
    getVolunteer(id) {
        return axios.get(prefix + '/' + id);
    }
}