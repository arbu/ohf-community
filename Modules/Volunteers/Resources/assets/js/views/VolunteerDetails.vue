<template>
    <div>
        <div class="alert alert-warning mt-3" v-if="error != null">
            <p>{{ error }}</p>
            <button type="button" class="btn btn-warning btn-sm" @click="loadData"><i class="fa fa-sync"></i> Reload</button>
        </div>
        <loading-indicator v-if="!loaded"></loading-indicator>
        <template v-else>
            <h2>{{ volunteer.first_name }} {{ volunteer.last_name }}</h2>

            <volunteer-detail-item>
                <template v-slot:label>About:</template>
                <i class="fas" :class="{ 'fa-male': volunteer.gender == 'm', 'fa-female': volunteer.gender == 'f', }"></i>
                            {{ volunteer.date_of_birth }} (age {{ volunteer.age }}), 
                            {{ volunteer.nationality }}
            </volunteer-detail-item>

            <volunteer-detail-item>
                <template v-slot:label>Address:</template>
                {{ volunteer.street }}<br>
                {{ volunteer.postcode }} {{ volunteer.city }}<br>
                {{ volunteer.country }}
            </volunteer-detail-item>

            <volunteer-detail-item v-if="volunteer.emergency_contact != null">
                <template v-slot:label>Emergency contact:</template>
                <span class="pre-formatted">{{ volunteer.emergency_contact }}</span>
            </volunteer-detail-item>

            <volunteer-detail-item>
                <template v-slot:label>Langauges:</template>
                <template v-for="(language, idx) in volunteer.languages">
                    {{ language }}<template v-if="idx + 1 < volunteer.languages.length">,</template>
                </template>
            </volunteer-detail-item>

            <volunteer-detail-item>
                <template v-slot:label>Passport/ID number:</template>
                <template v-if="volunteer.passport_id_number != null">
                    {{ volunteer.passport_id_number }}
                </template>
                <template v-else>
                    <template v-if="editPassportIdNumber">
                        <div class="input-group">
                            <input type="text" class="form-control" :disabled="passportIdNumberUpdate" v-model="passport_id_number" :class="{ 'is-invalid': passportIdNumberError != null }" placeholder="Passport/ID number" ref="passport_id_number" @keyup.enter="updatePassportIdNumber" @keyup.esc="editPassportIdNumber = false">
                            <div class="input-group-append" id="button-addon4">
                                <button class="btn btn-outline-success" type="button" @click="updatePassportIdNumber" :disabled="passportIdNumberUpdate || !validPasswordIdNumber"><i class="fa fa-check"></i></button>
                                <button class="btn btn-outline-secondary" type="button" @click="editPassportIdNumber = false" :disabled="passportIdNumberUpdate"><i class="fa fa-times-circle"></i></button>
                            </div>
                            <div class="invalid-feedback" v-if="passportIdNumberError != null">
                                {{ passportIdNumberError }}
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <span class="text-warning">Missing</span>
                        [<a href="javascript:;" @click="editPassportIdNumber = true">Add</a>]
                    </template>                    
                </template>
            </volunteer-detail-item>

            <volunteer-detail-item v-if="volunteer.govt_reg_number != null">
                <template v-slot:label>Govt. Volunteer registration number:</template>
                {{ volunteer.govt_reg_number }} (expires {{ volunteer.govt_reg_expiry }})
            </volunteer-detail-item>

            <volunteer-detail-item>
                <template v-slot:label>Criminal record validated:</template>
                <true-false-icon :value="volunteer.criminal_record_validated"></true-false-icon>
            </volunteer-detail-item>

            <volunteer-detail-item>
                <template v-slot:label>Driving license:</template>
                <true-false-icon :value="volunteer.has_driving_license"></true-false-icon>
            </volunteer-detail-item>

            <volunteer-detail-item v-if="volunteer.qualifications != null">
                <template v-slot:label>Qualifications:</template>
                <span class="pre-formatted">{{ volunteer.qualifications }}</span>
            </volunteer-detail-item>

            <volunteer-detail-item v-if="volunteer.previous_experience != null">
                <template v-slot:label>Previous experience:</template>
                <span class="pre-formatted">{{ volunteer.previous_experience }}</span>
            </volunteer-detail-item>

            <volunteer-detail-item v-if="volunteer.remarks != null">
                <template v-slot:label>Remarks:</template>
                <span class="pre-formatted">{{ volunteer.remarks }}</span>
            </volunteer-detail-item>

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

                    <p>Status: {{ stay.status }}</p>

                    <volunteer-detail-item>
                        <template v-slot:label>govt_reg_status</template>
                        {{ stay.govt_reg_status }}
                    </volunteer-detail-item>
                    <volunteer-detail-item>
                        <template v-slot:label>code_of_conduct_signed</template>
                        {{ stay.code_of_conduct_signed }}
                    </volunteer-detail-item>
                    <volunteer-detail-item>
                        <template v-slot:label>financial contribution</template>
                        {{ stay.financial_contribution }} Paid? {{ stay.financial_contribution_paid }}
                    </volunteer-detail-item>
                    <volunteer-detail-item>
                        <template v-slot:label>debriefing_info_received</template>
                        {{ stay.debriefing_info_received }}
                    </volunteer-detail-item>

                    <volunteer-detail-item>
                        <template v-slot:label>Responsibilities:</template>
                        <template v-for="(responsibility, idx) in stay.responsibilities">
                            {{ responsibility }}<template v-if="idx + 1 < stay.responsibilities.length">,</template>
                        </template>
                    </volunteer-detail-item>

                    <volunteer-detail-item>
                        <template v-slot:label>remarks</template>
                        {{ stay.remarks }}
                    </volunteer-detail-item>

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
    import VolunteerDetailItem from '../components/VolunteerDetailItem.vue';
    import TrueFalseIcon from '../components/TrueFalseIcon.vue';
    import api from '../services/volunteers';
    import commonMixin from '../mixins/common.js';
    import volunteersMixin from '../mixins/volunteers.js';
    export default {
        mixins: [ commonMixin, volunteersMixin ],
        components: {
            'volunteer-detail-item': VolunteerDetailItem,
            'true-false-icon': TrueFalseIcon,
        },
        data() {
            return {
                volunteer: null,
                loaded: false,
                error: null,
                editPassportIdNumber: false,
                passportIdNumberError: null,     
                passportIdNumberUpdate: false,
                passport_id_number: null,
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
            },
            updatePassportIdNumber() {
                if (!this.validPasswordIdNumber) {
                    return
                }
                this.passportIdNumberUpdate = true
                this.passportIdNumberError = null
                api.updateVolunteer(this.volunteer_id, {
                    'passport_id_number': this.passport_id_number,
                })
                .then((res) => {
                    this.editPassportIdNumber = false
                    this.passportIdNumberError = null
                    this.volunteer = res.data.data;
                })
                .catch(err => {
                    this.passportIdNumberError = this.extractResponseErrorMessage(err, 'passport_id_number')
                })
                .then(() => {
                    this.passportIdNumberUpdate = false
                })
            }
        },
        watch: {
            editPassportIdNumber(val, oldVal) {
                if (val) {
                    this.$nextTick(() => this.$refs.passport_id_number.focus())
                } else {
                    this.passportIdNumberError = null
                    this.passport_id_number = null
                }
            }
        },
        computed: {
            validPasswordIdNumber() {
                return this.passport_id_number != null && this.passport_id_number.length > 0
            },
        }
    }
</script>