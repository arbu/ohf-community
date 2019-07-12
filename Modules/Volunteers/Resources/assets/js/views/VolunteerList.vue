<template>
    <div>
asd
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <router-link :to="{ name: 'volunteers-index', params: { scope: 'applied' } }" class="nav-link" active-class="active">
                    <i class="fas fa-envelope-open-text"></i> 
                    <span class="d-none d-sm-inline">Applicants</span>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{ name: 'volunteers-index', params: { scope: 'future' } }" class="nav-link" active-class="active">
                    <i class="fas fa-calendar-check"></i> 
                    <span class="d-none d-sm-inline">Upcoming</span>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{ name: 'volunteers-index', params: { scope: 'active' } }" class="nav-link" active-class="active">
                    <i class="fas fa-globe-africa"></i> 
                    <span class="d-none d-sm-inline">Active</span>
                </router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{ name: 'volunteers-index', params: { scope: 'previous' } }" class="nav-link" active-class="active">
                    <i class="fas fa-folder-open"></i> 
                    <span class="d-none d-sm-inline">Alumni</span>
                </router-link>
            </li>
        </ul>

        <div class="alert alert-warning mt-3" v-if="error != null">
            <p>{{ error }}</p>
            <button type="button" class="btn btn-warning btn-sm" @click="refresh"><i class="fa fa-sync"></i> Reload</button>
        </div>
        <loading-indicator v-if="!loaded"></loading-indicator>
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
                                <router-link :to="{ name: 'volunteers-show', params: { volunteer_id: volunteer.id } }">
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
    import api from '../services/volunteers';
    import volunteersMixin from '../mixins/volunteers';
    export default {
        mixins: [ volunteersMixin ],
        data() {
            return {
                loaded: false,
                volunteers: [],
                error: null,
            }
        },
        props: {
            scope: {
                default: 'active',
                required: false,
            }
        },
        created() {
            this.rememberScope(this.scope)
        },
        mounted() {
            this.refresh();
        },
        watch: {
            scope(val, oldVal) {
                this.rememberScope(this.scope)
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
                api.listVolunteers(scope)
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