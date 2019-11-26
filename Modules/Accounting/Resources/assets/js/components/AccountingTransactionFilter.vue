<template>
    <div class="mb-3">
        <div class="text-right">
            <b-button variant="warning" @click="resetFilter" v-if="filterChanged" size="sm">
                <icon name="eraser"></icon> Reset filter
            </b-button>
            <b-button v-b-toggle.collapse-1 class="m-1" size="sm" variant="secondary">
                <template v-if="!visible">
                    <icon name="search"></icon> Filter results
                </template>
                <template v-else>
                    <icon name="times"></icon> Hide filter
                </template>
            </b-button>
        </div>
        <b-collapse v-model="visible" id="collapse-1">
            <b-card body-class="pb-2">
                <b-form>
                    <b-form-row>
                        <b-col cols="12" sm="4" lg="3">
                            <b-form-group label="Receipt No">
                                <b-form-input type="number" v-model="filter.receipt_no" size="sm" debounce="500" min="1" @keydown.esc="filter.receipt_no = null"></b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col cols="6" sm="4" lg="3">
                            <b-form-group label="Date from">
                                <b-form-input type="date" v-model="filter.date_start" :max="todayDate" size="sm"></b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col cols="6" sm="4" lg="3">
                            <b-form-group label="Date to">
                                <b-form-input type="date" v-model="filter.date_end" :max="todayDate" size="sm"></b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col cols="6" lg="auto">
                            <b-form-group label="Type">
                                <b-form-radio-group
                                    v-model="filter.type"
                                    :options="types"
                                    stacked
                                ></b-form-radio-group>
                            </b-form-group>
                        </b-col>
                        <b-col cols="6" lg="auto">
                            <b-form-group label="Options">
                                <b-form-checkbox v-model="filter.today">Registered today</b-form-checkbox>
                                <b-form-checkbox v-model="filter.no_receipt">No receipt</b-form-checkbox>
                            </b-form-group>
                        </b-col>
                    </b-form-row>
                    <b-form-row>
                        <b-col cols="12" sm="6" md="3">
                            <b-form-group label="Category">
                                <b-form-select v-model="filter.category" :options="categories" size="sm">
                                    <template v-slot:first>
                                        <option :value="null">- Category -</option>
                                    </template>
                                </b-form-select>
                            </b-form-group>
                        </b-col>
                        <b-col cols="12" sm="6" md="3">
                            <b-form-group label="Project">
                                <b-form-select v-model="filter.project" :options="projects" size="sm">
                                    <template v-slot:first>
                                        <option :value="null">- Project -</option>
                                    </template>
                                </b-form-select>
                            </b-form-group>
                        </b-col>
                        <b-col cols="12" sm="6" md="3">
                            <b-form-group label="Description">
                                <b-form-input type="text" v-model="filter.description" size="sm" debounce="500" @keydown.esc="filter.description = null"></b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col cols="12" sm="6" md="3">
                            <b-form-group label="Beneficiary">
                                <b-form-input type="text" v-model="filter.beneficiary" size="sm" debounce="500" @keydown.esc="filter.beneficiary = null"></b-form-input>
                            </b-form-group>
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
            categories: {
                type: Array,
                required: true,
            },
            projects: {
                type: Array,
                required: true,
            }
        },
        data() {
            return {
                filter: {...defaultFilter},
                visible: sessionStorage.getItem(this.id + '.visible') ? sessionStorage.getItem(this.id + '.visible') == 'true' : false,
                types: [
                    { value: null, text: 'Any' },
                    { value: 'income', text: 'Income' },
                    { value: 'spending', text: 'Spending' }
                ]
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