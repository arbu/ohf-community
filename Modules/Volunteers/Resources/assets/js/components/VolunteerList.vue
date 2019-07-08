<template>
    <div>

        <!-- Nav tabs -->
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

        <div class="alert alert-warning mt-3" v-if="error != null">
            <p>{{ error }}</p>
            <button type="button" class="btn btn-warning btn-sm" @click="refresh"><i class="fa fa-sync"></i> Reload</button>
        </div>
        <div v-if="!loaded" class="text-center mt-2">
            Loading...
        </div>
        <template v-else-if="volunteers.length > 0">

            <p class="mt-1 mb-2"><small>
                <template v-if="scope == 'applied'">
                    {{ volunteers.length }} applicants
                </template>
                <template v-else-if="scope == 'future'">
                    {{ volunteers.length }} future volunteers
                </template>
                <template v-else-if="scope == 'active'">
                    {{ volunteers.length }} active volunteers
                </template>
                <template v-else-if="scope == 'previous'">
                    {{ volunteers.length }} former volunteers
                </template>
                <template v-else>
                    {{ volunteers.length }} volunteers
                </template>
            </small></p>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Nationality</th>
                            <th class="text-right">Age</th>
                            <th class="text-center">Gender</th>
                            <!-- <th>Languages</th> -->
                            <th v-if="scope != 'active'">Arrival</th>
                            <th>Departure</th>
                            <th v-if="scope != 'active'" class="text-right text-nowrap"># Weeks</th>
                            <th v-if="scope == 'future' || scope == 'active'">Govt. reg.</th>
                            <th v-if="scope == 'active'" class="text-center">Code signed</th>
                            <th v-if="scope == 'active'" class="text-center">Contribution paid</th>
                            <th v-if="scope == 'previous'" class="text-center">Debriefing info received</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="volunteer in volunteers" :key="'volunteer-'+volunteer.id">
                            <td >
                                {{ volunteer.id }}
                                <router-link :to="{ name: 'volunteer-show', params: { volunteer_id: volunteer.id } }">
                                    {{ volunteer.first_name }} {{ volunteer.last_name }}
                                </router-link>
                            </td>
                            <td>{{ volunteer.nationality }}</td>
                            <td class="text-right">{{ volunteer.age }}</td>
                            <td class="text-center">
                                <i class="fas" :class="{ 'fa-male': volunteer.gender == 'm', 'fa-female': volunteer.gender == 'f', }"></i>
                            </td>
                            <!-- <td>
                                <template v-for="language in volunteer.languages">
                                    {{ language }}<br :key="language">
                                </template>
                            </td> -->
                            <td v-if="scope != 'active'">{{ volunteer.stay.arrival }}</td>
                            <td>
                                <template v-if="volunteer.stay.departure != null">
                                    {{ volunteer.stay.departure }}
                                </template>
                                <template v-else>
                                    open-end
                                </template>                            
                            </td>
                            <td v-if="scope != 'active'" class="text-right">
                                <template v-if="volunteer.stay.departure != null">
                                    {{ Math.round(volunteer.stay.num_days / 7) }}
                                </template>
                                <template v-else>
                                    -
                                </template>      
                            </td>
                            <td v-if="scope == 'future' || scope == 'active'">
                                {{ volunteer.stay.govt_reg_status }}
                            </td>
                            <td v-if="scope == 'active'" class="text-center">
                                <i class="fa" :class="{ 'fa-check': volunteer.stay.code_of_conduct_signed, 'fa-times': !volunteer.stay.code_of_conduct_signed }"></i>
                            </td>
                            <td v-if="scope == 'active'" class="text-center">
                                <i class="fa" :class="{ 'fa-check': volunteer.stay.financial_contribution_paid, 'fa-times': !volunteer.stay.financial_contribution_paid }"></i>
                            </td>
                            <td v-if="scope == 'previous'" class="text-center">
                                <i class="fa" :class="{ 'fa-check': volunteer.stay.debriefing_info_received, 'fa-times': !volunteer.stay.debriefing_info_received }"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
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
                axios.get('/api/volunteers?scope=' + scope)
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