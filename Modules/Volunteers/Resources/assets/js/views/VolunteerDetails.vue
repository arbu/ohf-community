<template>
    <div>
        <div class="alert alert-warning mt-3" v-if="error != null">
            <p>{{ error }}</p>
            <button type="button" class="btn btn-warning btn-sm" @click="loadData"><i class="fa fa-sync"></i> Reload</button>
        </div>
        <loading-indicator v-if="!loaded"></loading-indicator>
        <template v-else>
            <h2>{{ volunteer.first_name }} {{ volunteer.last_name }}</h2>
            <p><strong>About:</strong> <i class="fas" :class="{ 'fa-male': volunteer.gender == 'm', 'fa-female': volunteer.gender == 'f', }"></i>
            {{ volunteer.date_of_birth }} (age {{ volunteer.age }}), 
            {{ volunteer.nationality }}</p>

            <p><strong>Address:</strong> {{ volunteer.street }} {{ volunteer.postcode }} {{ volunteer.city }} {{ volunteer.country }}</p>

            <p v-if="volunteer.emergency_contact != null">
                <strong>Emergency contact:</strong>
                {{ volunteer.emergency_contact }}
            </p>

            <p><strong>Langauges:</strong>
                <template v-for="(language, idx) in volunteer.languages">
                    {{ language }}<template v-if="idx + 1 < volunteer.languages.length">,</template>
                </template>
            </p>

            <div class="form-row">
                <div v-if="volunteer.email != null" class="col-auto mb-3">
                    <a :href="mailUrl(volunteer.email, volunteer.first_name + ' ' + volunteer.last_name)" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> {{ volunteer.email }}
                    </a>
                </div>
                <div v-if="volunteer.phone != null" class="col-auto mb-3">
                    <a :href="telUrl(volunteer.phone)" class="btn btn-primary">
                        <i class="fas fa-phone"></i> {{ volunteer.phone }}
                    </a>
                </div>
                <div v-if="volunteer.whatsapp != null" class="col-auto mb-3">
                    <a :href="whatsAppUrl(volunteer.whatsapp)" class="btn btn-primary">
                        <i class="fab fa-whatsapp"></i> {{ volunteer.whatsapp }}
                    </a>
                </div>
                <div v-if="volunteer.skype != null" class="col-auto mb-3">
                    <a :href="skypeUrl(volunteer.skype)" class="btn btn-primary">
                        <i class="fab fa-skype"></i> {{ volunteer.skype }}
                    </a>
                </div>
                
            </div>
           
            <div class="card mb-4" v-for="stay in volunteer.stays" :key="stay.id">
                <div class="card-header">
                    {{ stay.arrival }} - 
                    <template v-if="stay.departure != null">
                        {{ stay.departure }} ({{ Math.round(stay.num_days / 7) }} weeks)
                    </template>
                    <template v-else>
                        open-end
                    </template>   
                    <span v-if="stay.active" class="badge badge-pill badge-success">Active</span>
                </div>
                <div class="card-body">
                    Test
                    <!-- <h5 class="card-title">Special title treatment</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <a href="#" class="btn btn-primary">Go somewhere</a> -->
                </div>
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