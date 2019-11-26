<template>
    <div class="mb-3">

        <!-- Control buttons -->
        <div class="text-right">

            <!-- Reset button -->
            <b-button variant="warning" @click="resetFilter" v-if="filterChanged" size="sm">
                <icon name="eraser"></icon> {{ resetFilterText }}
            </b-button>

            <!-- Collapse button -->
            <b-button v-b-toggle.collapse-1 class="m-1" size="sm" variant="secondary">
                <template v-if="!visible">
                    <icon name="filter"></icon> {{ filterText }}
                </template>
                <template v-else>
                    <icon name="times"></icon> {{ hideFilterText }}
                </template>
            </b-button>
        </div>

        <!-- Collapsible area -->
        <b-collapse v-model="visible" id="collapse-1">
            <b-card>
                <b-form>
                    <b-form-row>

                        <!-- Receipt Number -->
                        <b-col cols="12" sm="6" md="1">
                            <b-form-group :label="labels.receipt_no">
                                <b-form-input type="number" v-model="filter.receipt_no" size="sm" debounce="500" min="1" @keydown.esc="filter.receipt_no = null"></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Date start -->
                        <b-col cols="12" sm="6" md="2">
                            <b-form-group :label="labels.date_from">
                                <b-form-input type="date" v-model="filter.date_start" :max="todayDate" size="sm"></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Date end -->
                        <b-col cols="12" sm="6" md="2">
                            <b-form-group :label="labels.date_to">
                                <b-form-input type="date" v-model="filter.date_end" :max="todayDate" size="sm"></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Amount from -->
                        <b-col cols="12" sm="6" md="2">
                            <b-form-group :label="labels.amount_from">
                                <b-form-input type="number" v-model="filter.amount_from" size="sm" debounce="500" min="1" @keydown.esc="filter.amount_from = null"></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Amount to -->
                        <b-col cols="12" sm="6" md="2">
                            <b-form-group :label="labels.amount_to">
                                <b-form-input type="number" v-model="filter.amount_to" size="sm" debounce="500" min="1" @keydown.esc="filter.amount_to = null"></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Type -->
                        <b-col cols="12" sm="6" md="3">
                            <b-form-group :label="labels.type">
                                <b-form-radio-group
                                    v-model="filter.type"
                                    :options="types"
                                ></b-form-radio-group>
                            </b-form-group>
                        </b-col>

                    </b-form-row>
                    <b-form-row>

                        <!-- Category -->
                        <b-col cols="12" sm="6" md="3">
                            <b-form-group :label="labels.category">
                                <b-form-select v-model="filter.category" :options="categories" size="sm">
                                    <template v-slot:first>
                                        <option :value="null">- {{ labels.category }} -</option>
                                    </template>
                                </b-form-select>
                            </b-form-group>
                        </b-col>

                        <!-- Project -->
                        <b-col cols="12" sm="6" md="3">
                            <b-form-group :label="labels.project">
                                <b-form-select v-model="filter.project" :options="projects" size="sm">
                                    <template v-slot:first>
                                        <option :value="null">- {{ labels.project }} -</option>
                                    </template>
                                </b-form-select>
                            </b-form-group>
                        </b-col>

                        <!-- Description -->
                        <b-col cols="12" sm="6" md="3">
                            <b-form-group :label="labels.description">
                                <b-form-input type="text" v-model="filter.description" size="sm" debounce="500" @keydown.esc="filter.description = null"></b-form-input>
                            </b-form-group>
                        </b-col>

                        <!-- Beneficiary -->
                        <b-col cols="12" sm="6" md="3">
                            <b-form-group :label="labels.beneficiary">
                                <b-form-input type="text" v-model="filter.beneficiary" size="sm" debounce="500" @keydown.esc="filter.beneficiary = null"></b-form-input>
                            </b-form-group>
                        </b-col>

                    </b-form-row>
                    <b-form-row>

                        <!-- "Registered today" -->
                        <b-col cols="auto">
                            <b-form-checkbox v-model="filter.today">{{ labels.registered_today }}</b-form-checkbox>
                        </b-col>

                         <!-- "No receipt" -->
                        <b-col cols="auto">
                            <b-form-checkbox v-model="filter.no_receipt">{{ labels.no_receipt }}</b-form-checkbox>
                        </b-col>

                    </b-form-row>
                </b-form>
            </b-card>
        </b-collapse>
    </div>
</template>

<script>
    const defaultFilter = {
        receipt_no: null,
        date_start: null,
        date_end: null,
        amount_from: null,
        amount_to: null,
        type: null,
        category: null,
        project: null,
        description: null,
        beneficiary: null,
        today: false,
        no_receipt: false,
    }
    const trimToNull = [
        'receipt_no',
        'amount_from',
        'amount_to',
        'description',
        'beneficiary'
    ]
    import Icon from '../../../../../../resources/js/components/Icon'
    export default {
        components: {
            Icon
        },
        props: {
            id: {
                type: String,
                required: false,
                default: 'accounting.transactions.filter'
            },
            labels: {
                type: Object,
                required: true
            },
            types: {
                type: Array,
                required: true
            },
            categories: {
                type: Array,
                required: true
            },
            projects: {
                type: Array,
                required: true
            },
            filterText: {
                type: String,
                required: false,
                default: 'Filter'
            },
            hideFilterText: {
                type: String,
                required: false,
                default: 'Hide filter'
            },
            resetFilterText: {
                type: String,
                required: false,
                default: 'Reset filter'
            }
        },
        data() {
            return {
                filter: {...defaultFilter},
                visible: sessionStorage.getItem(this.id + '.visible') ? sessionStorage.getItem(this.id + '.visible') == 'true' : false,
            }
        },
        methods: {
            emitFilter() {
                this.$emit('change', this.filter)
            },
            resetFilter() {
                this.filter = {...defaultFilter}
            }
        },
        computed: {
            filterChanged() {
                return JSON.stringify(this.filter) !== JSON.stringify(defaultFilter)
            },
            todayDate() {
                return new Date().toISOString().split('T')[0];
            }
        },
        watch: {
            filter: {
                deep: true,
                handler(val) {
                    for (let i = 0; i < trimToNull.length; i++) {
                        if (this.filter[trimToNull[i]] === '') {
                            this.filter[trimToNull[i]] = null
                            return
                        }
                    }
                    this.emitFilter()
                }
            },
            visible(val) {
                sessionStorage.setItem(this.id + '.visible', val)
            }
        }
    }
</script>