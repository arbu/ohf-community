const scopes = ['applied', 'future', 'active', 'previous'];

export default {
    methods: {
        indexLink() {
            var scope = 'active';
            if (localStorage.volunteers_list_scope && scopes.includes(localStorage.volunteers_list_scope)) {
                scope = localStorage.volunteers_list_scope;
            }
            return { name: 'volunteers-index', params: { scope: scope } };
        },
        rememberScope(scope) {
            // console.log('remember scope: ' + scope)
            localStorage.volunteers_list_scope = scope;
        }
    }
}