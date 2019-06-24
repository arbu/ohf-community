<template>
    <div>
        <div class="row">
            <div class="col col-auto">
                <div class="btn-group btn-group-sm mb-3" role="group" aria-label="Scopes">
                    <button class="btn btn-sm" :class="{ 'btn-dark': scope == 'active', 'btn-secondary':  scope != 'active' }" @click="scope='active'">Active</button>
                    <button class="btn btn-sm" :class="{ 'btn-dark': scope == 'future', 'btn-secondary':  scope != 'future' }" @click="scope='future'">Future</button>
                    <button class="btn btn-sm" :class="{ 'btn-dark': scope == 'applied', 'btn-secondary':  scope != 'applied' }" @click="scope='applied'">Applications</button>
                </div>
            </div>
            <div class="col col-auto">
                <button class="btn btn-sm btn-secondary" @click="refresh()" :disabled="!loaded">
                    <i class="fas fa-sync" :class="{ 'fa-spin': !loaded }"></i>
                </button>
            </div>
        </div>
        <div class="alert alert-warning" v-if="error != null">
            {{ error }}
        </div>
        <div v-if="!loaded" class="text-center">
            <!-- <i class="fas fa-spinner fa-pulse"></i>  -->
            Loading...
        </div>
        <div v-else-if="volunteers.length > 0" class="table-responsive">
            <table class="table table-sm table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Nationality</th>
                        <th>Age</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="volunteer in volunteers" :key="volunteer.id">
                        <td>{{ volunteer.first_name }} {{ volunteer.last_name }}</td>
                        <td>{{ volunteer.nationality }}</td>
                        <td>{{ volunteer.age }}</td>
                        <td>{{ volunteer.stays[0].arrival }}</td>
                        <td>{{ volunteer.stays[0].departure }}</td>
                        <td>{{ volunteer.stays[0].num_days }}</td>
                    </tr>
                </tbody>
            </table>
            <p><small>{{ volunteers.length }} volunteers in total</small></p>
        </div>
        <div v-else-if="error == null" class="alert alert-warning">
            No volunteers registered!
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                loaded: false,
                scope: 'active',
                volunteers: [],
                error: null,
            }
        },
        mounted() {
            this.refresh();
        },
        watch: {
            scope(val, oldVal) {
                this.loadData(val);
            }
        },
        methods: {
            refresh() {
                this.loadData(this.scope);
            },
            loadData(scope) {
                this.loaded = false;
                this.error = null;
                axios.get('api/volunteers?scope=' + scope)
                    .then(res => {
                        this.volunteers = res.data.data;
                    })
                    .catch(err => {
                        this.error = err;
                    })
                    .then(() => {
                        this.loaded = true;
                    });
            }
        }
    }
</script>