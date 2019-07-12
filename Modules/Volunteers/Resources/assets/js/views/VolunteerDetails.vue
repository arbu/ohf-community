<template>
    <div>
        <div class="alert alert-warning mt-3" v-if="error != null">
            <p>{{ error }}</p>
            <button type="button" class="btn btn-warning btn-sm" @click="loadData"><i class="fa fa-sync"></i> Reload</button>
        </div>
        <loading-indicator v-if="!loaded"></loading-indicator>
        <template v-else>
            <h2>{{ volunteer.first_name }} {{ volunteer.last_name }}</h2>
            <i class="fas" :class="{ 'fa-male': volunteer.gender == 'm', 'fa-female': volunteer.gender == 'f', }"></i>
            {{ volunteer.date_of_birth }} (age {{ volunteer.age }}), 
            {{ volunteer.nationality }}

            <p>{{ volunteer.street }} {{ volunteer.postcode }} {{ volunteer.city }} {{ volunteer.country }}</p>

            <p v-if="volunteer.emergency_contact != null">
                Emergency contact: 
                {{ volunteer.emergency_contact }}
            </p>

            <p v-if="volunteer.email != null">
                <i class="fas fa-envelope"></i>
                <a :href="mailUrl(volunteer.email)">{{ volunteer.email }}</a>
            </p>
            <p v-if="volunteer.phone != null">
                <i class="fas fa-phone"></i>
                <a :href="telUrl(volunteer.phone)">{{ volunteer.phone }}</a>
            </p>
            <p v-if="volunteer.whatsapp != null">
                <i class="fab fa-whatsapp"></i>
                <a :href="whatsAppUrl(volunteer.whatsapp)">{{ volunteer.whatsapp }}</a>
            </p>
           
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped table-hover mt-4">
                    <thead>
                        <tr>
                            <th>Arrival</th>
                            <th>Departure</th>
                            <th class="text-right text-nowrap"># Weeks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="stay in volunteer.stays" :key="stay.id">
                            <td>{{ stay.arrival }}</td>
                            <td>
                                <template v-if="stay.departure != null">
                                    {{ stay.departure }}
                                </template>
                                <template v-else>
                                    open-end
                                </template>                            
                            </td>
                            <td class="text-right">
                                <template v-if="stay.departure != null">
                                    {{ Math.round(stay.num_days / 7) }}
                                </template>
                                <template v-else>
                                    -
                                </template>      
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p>
                <router-link :to="indexLink()">
                    <button type="button" class="btn btn-secondary">
                        <i class="fa fa-angle-left"></i> Back
                    </button>
                </router-link>
            </p>

        </template>
    </div>
</template>
<script>
    import api from '../services/volunteers';
    import commonMixin from '../mixins/common.js';
    import volunteersMixin from '../mixins/volunteers.js';
    export default {
        mixins: [ commonMixin, volunteersMixin ],
        data() {
            return {
                volunteer: null,
                loaded: false,
                error: null,                
            }
        },
        props: {
            'volunteer_id': {
                required: true,
            }
        },
        created() {
            this.loadData();
        },
        methods: {
            loadData() {
                this.loaded = false;
                this.error = null;
                this.volunteer = null;
                api.getVolunteer(this.volunteer_id)
                    .then(res => {
                        this.volunteer = res.data.data;
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