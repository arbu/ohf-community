<template>
    <div>
        <b-button
            v-if="isFilterActive"
            size="sm"
            variant="primary"
            class="mb-3"
            @click="reset"
        >
            <font-awesome-icon icon="eraser" />
            <span class="d-none d-sm-inline">{{ $t('app.reset_filter') }}</span>
        </b-button>
        <b-button
            size="sm"
            variant="secondary"
            class="mb-3"
            @click="showModal"
        >
            <font-awesome-icon
                v-if="isBusy"
                icon="spinner"
                spin
            />
            <font-awesome-icon
                v-else
                icon="search"
            />
            <span class="d-none d-sm-inline">{{ $t(isFilterActive ? 'app.edit_filter' : 'app.filter_results') }}</span>
        </b-button>
        <b-modal
            :id="modalId"
            centered
            ok-only
            :title="$t('app.filter')"
            :ok-title="$t('app.apply')"
            @ok="submit"
        >
            <b-form-row>
                <b-col sm>
                    <b-form-group :label="$t('app.type')">
                        <b-form-radio-group
                            v-model="form.type"
                            :options="types"
                            stacked
                        />
                    </b-form-group>
                </b-col>
                <b-col sm>
                    <b-form-group :label="$t('accounting.receipt_no')">
                        <b-form-input
                            v-model="form.receipt_no"
                            type="number"
                            :min="1"
                        />
                    </b-form-group>
                </b-col>
            </b-form-row>
            <b-form-row>
                <b-col sm>
                    <b-form-group :label="$t('app.from')">
                        <b-form-datepicker
                            v-model="form.date_start"
                            :max="form.date_end ? form.date_end: today"
                            reset-button
                            :label-reset-button="$t('app.reset')"
                            :label-no-date-selected="$t('app.no_date')"
                        />
                    </b-form-group>
                </b-col>
                <b-col sm>
                    <b-form-group :label="$t('app.to')">
                        <b-form-datepicker
                            v-model="form.date_end"
                            :min="form.date_start"
                            :max="today"
                            reset-button
                            :label-reset-button="$t('app.reset')"
                            :label-no-date-selected="$t('app.no_date')"
                        />
                    </b-form-group>
                </b-col>
            </b-form-row>
            <b-form-row>
                <b-col sm>
                    <b-form-group :label="$t('app.category')">
                        <b-form-select
                            v-if="fixed_categories"
                            v-model="form.category"
                            :options="categories"
                        >
                            <template v-slot:first>
                                <b-form-select-option :value="null"></b-form-select-option>
                            </template>
                        </b-form-select>
                        <template v-else>
                            <b-form-input
                                v-model="form.category"
                                list="category-list"
                            />
                            <b-form-datalist
                                id="category-list"
                                :options="categories"
                            />
                        </template>
                    </b-form-group>
                </b-col>
                <b-col
                    v-if="secondary_categories != null"
                    sm
                >
                    <b-form-group :label="$t('app.secondary_category')">
                        <b-form-select
                            v-if="fixed_secondary_categories"
                            v-model="form.secondaryCategory"
                            :options="secondary_categories"
                        >
                            <template v-slot:first>
                                <b-form-select-option :value="null"></b-form-select-option>
                            </template>
                        </b-form-select>
                        <template v-else>
                            <b-form-input
                                v-model="form.secondary_category"
                                list="secondary-category-list"
                            />
                            <b-form-datalist
                                id="secondary-category-list"
                                :options="secondary_categories"
                            />
                        </template>
                    </b-form-group>
                </b-col>
                <b-col sm>
                    <b-form-group :label="$t('app.project')">
                        <b-form-select
                            v-if="fixed_projects"
                            v-model="form.project"
                            :options="projects"
                        >
                            <template v-slot:first>
                                <b-form-select-option :value="null"></b-form-select-option>
                            </template>
                        </b-form-select>
                        <template v-else>
                            <b-form-input
                                v-model="form.project"
                                list="project-list"
                            />
                            <b-form-datalist
                                id="project-list"
                                :options="projects"
                            />
                        </template>
                    </b-form-group>
                </b-col>
            </b-form-row>
            <b-form-row>
                <b-col
                    v-if="locations != null"
                    sm
                >
                    <b-form-group :label="$t('app.location')">
                        <b-form-select
                            v-if="fixed_locations"
                            v-model="form.location"
                            :options="locations"
                        >
                            <template v-slot:first>
                                <b-form-select-option :value="null"></b-form-select-option>
                            </template>
                        </b-form-select>
                        <template v-else>
                            <b-form-input
                                v-model="form.location"
                                list="location-list"
                            />
                            <b-form-datalist
                                id="location-list"
                                :options="locations"
                            />
                        </template>
                    </b-form-group>
                </b-col>
                <b-col
                    v-if="cost_centers"
                    sm
                >
                    <b-form-group :label="$t('accounting.cost_center')">
                        <b-form-select
                            v-if="fixed_cost_centers"
                            v-model="form.cost_center"
                            :options="cost_centers"
                        >
                            <template v-slot:first>
                                <b-form-select-option :value="null"></b-form-select-option>
                            </template>
                        </b-form-select>
                        <template v-else>
                            <b-form-input
                                v-model="form.cost_centers"
                                list="cost_center-list"
                            />
                            <b-form-datalist
                                id="cost_center-list"
                                :options="cost_centers"
                            />
                        </template>
                    </b-form-group>
                </b-col>
            </b-form-row>
            <b-form-row>
                <b-col sm>
                    <b-form-group :label="$t('accounting.beneficiary')">
                        <b-form-input
                            v-model="form.beneficiary"
                            list="beneficiary-list"
                        />
                        <b-form-datalist
                            id="beneficiary-list"
                            :options="beneficiaries"
                        />
                    </b-form-group>
                </b-col>
                <b-col sm>
                    <b-form-group :label="$t('app.description')">
                        <b-form-input
                            v-model="form.description"
                        />
                    </b-form-group>
                </b-col>
            </b-form-row>
            <b-form-row>
                <b-col sm>
                    <b-form-checkbox
                        v-model="form.today"
                    >
                        {{ $t('accounting.registered_today') }}
                    </b-form-checkbox>
                </b-col>
                <b-col sm>
                    <b-form-checkbox
                        v-model="form.no_receipt"
                    >
                        {{ $t('accounting.no_receipt') }}
                    </b-form-checkbox>
                </b-col>
            </b-form-row>
        </b-modal>
    </div>
</template>
<script>
import moment from 'moment'
import transactionsApi from '@/api/accounting/transactions'
export default {
    props: {
        value: {
            required: true
        }
    },
    data () {
        return {
            modalId: 'filterModal',
            isLoaded: false,
            isBusy: false,
            form: {
                type: null,
                receipt_no: null,
                date_start: null,
                date_end: null,
                category: null,
                secondary_category: null,
                project: null,
                location: null,
                cost_center: null,
                description: null,
                today: null,
                no_receipt: null
            },
            types: [
                {
                    value: null,
                    text: this.$t('app.any')
                },
                {
                    value: 'income',
                    text: this.$t('accounting.income'),
                },
                {
                    value: 'spending',
                    text: this.$t('accounting.spending')
                }
            ],
            fixed_categories: false,
            categories: [],
            fixed_secondary_categories: false,
            secondary_categories: [],
            fixed_projects: false,
            projects: [],
            fixed_locations: false,
            locations: [],
            fixed_cost_centers: false,
            cost_centers: [],
            beneficiaries: []
        }
    },
    computed: {
        isFilterActive () {
            return Object.values(this.value)
                .filter(e => e != null && e != '')
                .length > 0
        },
        today () {
            return moment().format(moment.HTML5_FMT.DATE)
        }
    },
    methods: {
        async showModal() {
            this.form = { ...this.value }
            try {
                if (!this.isLoaded) {
                    this.isBusy = true
                    let classifications = await transactionsApi.fetchfilterClassifications()
                    this.fixed_categories = classifications.fixed_categories
                    this.categories = classifications.categories
                    this.fixed_secondary_categories = classifications.fixed_secondary_categories
                    this.secondary_categories = classifications.secondary_categories
                    this.fixed_projects = classifications.fixed_projects
                    this.projects = classifications.projects
                    this.fixed_locations = classifications.fixed_locations
                    this.locations = classifications.locations
                    this.fixed_cost_centers = classifications.fixed_cost_centers
                    this.cost_centers = classifications.cost_centers
                    this.beneficiaries = classifications.beneficiaries
                    this.isBusy = false
                    this.isLoaded = true
                }
                this.$bvModal.show(this.modalId)
            } catch (err) {
                alert(err)
                this.isBusy = false
            }
        },
        submit (bvModalEvt) {
            this.$emit('submit', bvModalEvt, this.form)
        },
        reset () {
            this.$emit('reset')
        }
    }
}
</script>
