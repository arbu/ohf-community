
const updateRole = async function(role, data) {
    return axios.put(role.links.self, data)
        .then(res => res.data)
}

export default { updateRole }
