<template>
    <div>
        <!-- <div class="row mb-3 mb-sm-0">
            <div class="col col-auto">
                <div class="btn-group btn-group-sm mb-3" role="group" aria-label="Scopes">
                    <button class="btn btn-sm" :class="{ 'btn-dark': scope == 'applied', 'btn-secondary':  scope != 'applied' }" @click="scope='applied'">Applications</button>
                    <button class="btn btn-sm" :class="{ 'btn-dark': scope == 'future', 'btn-secondary':  scope != 'future' }" @click="scope='future'">Future</button>
                    <button class="btn btn-sm" :class="{ 'btn-dark': scope == 'active', 'btn-secondary':  scope != 'active' }" @click="scope='active'">Active</button>
                    <button class="btn btn-sm" :class="{ 'btn-dark': scope == 'previous', 'btn-secondary':  scope != 'previous' }" @click="scope='previous'">Previous</button>
                </div>
            </div>
            <div class="col col-auto">
                <button class="btn btn-sm btn-secondary" @click="refresh()" :disabled="!loaded">
                    <i class="fas fa-sync" :class="{ 'fa-spin': !loaded }"></i>
                </button>
            </div>
        </div> -->

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" :class="{ 'active': scope == 'applied' }" href="#" @click.stop="scope='applied'">
                    <i class="fas fa-envelope-open-text"></i> 
                    <span class="d-none d-sm-inline">Applicants</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" :class="{ 'active': scope == 'future' }" href="#" @click.stop="scope='future'">
                    <i class="fas fa-calendar-check"></i> 
                    <span class="d-none d-sm-inline">Upcoming</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" :class="{ 'active': scope == 'active' }" href="#" @click.stop="scope='active'">
                    <i class="fas fa-globe-africa"></i> 
                    <span class="d-none d-sm-inline">Active</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" :class="{ 'active': scope == 'previous' }" href="#" @click.stop="scope='previous'">
                    <i class="fas fa-folder-open"></i> 
                    <span class="d-none d-sm-inline">Alumni</span>
                </a>
            </li>
        </ul>
        <p>
            asd
        </p>

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
                        <th>Gender</th>
                        <th>Languages</th>
                        <th v-if="scope != 'active'">Arrival</th>
                        <th>Departure</th>
                        <th v-if="scope != 'active'" class="text-right">Number of days</th>
                        <th>Govt. reg.</th>
                        <th>Contribution paid</th>
                        <th>Feedback sheet received</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="volunteer in volunteers" :key="volunteer.id">
                        <td>
                            {{ volunteer.first_name }} {{ volunteer.last_name }}
                        </td>
                        <td>{{ volunteer.nationality }}</td>
                        <td>{{ volunteer.age }}</td>
                        <td>
                            <i class="fas" :class="{ 'fa-male': volunteer.gender == 'm', 'fa-female': volunteer.gender == 'f', }"></i>
                        </td>
                        <td>
                            <template v-for="language in volunteer.languages">
                                {{ language }}<br :key="language">
                            </template>
                        </td>
                        <td v-if="scope != 'active'">{{ volunteer.stay.arrival }}</td>
                        <td>
                            <template v-if="volunteer.stay.departure != null">
                                {{ volunteer.stay.departure }}
                            </template>
                            <template v-else>
                                open-end
                            </template>                            
                        </td>
                        <td v-if="scope != 'active'" class="text-right">{{ volunteer.stay.num_days }}</td>
                        <td>
                            {{ volunteer.stay.govt_reg_status }}
                        </td>
                        <td>
                            {{ volunteer.stay.financial_contribution_paid }}
                        </td>
                        <td>
                            {{ volunteer.stay.feedback_sheet_received }}
                        </td>
                        <td>
                            {{ volunteer.stay.fundraising_infos_received }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <p><small>{{ volunteers.length }} volunteers in total</small></p>
        </div>
        <div v-else-if="error == null" class="alert alert-info">
            No volunteers registrations found!
        </div>
    </div>
</template>
<script>
    const scopes = ['applied', 'future', 'active', 'previous'];
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
            if (localStorage.volunteers_list_scope && scopes.includes(localStorage.volunteers_list_scope)) {
                this.scope = localStorage.volunteers_list_scope;
            }
            this.refresh();
        },
        watch: {
            scope(val, oldVal) {
                localStorage.volunteers_list_scope = val;
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