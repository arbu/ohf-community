<template>
    <div class="mb-3">
        <div class="text-right">
            <b-button v-b-toggle.collapse-1 class="m-1" size="sm" :variant="filterChanged && !visible ? 'warning' : 'secondary'">
                <template v-if="!visible">
                    <icon name="search"></icon> Filter results
                </template>
                <template v-else>
                    <icon name="times"></icon> Hide filter
                </template>
            </b-button>
        </div>
        <b-collapse v-model="visible" id="collapse-1">
            <b-card>
                <b-form inline>
                    <b-form-group label="Type">
                        <b-form-radio-group
                            v-model="filter.type"
                            :options="types"
                        ></b-form-radio-group>
                    </b-form-group>
                    <b-form-group label="Date from">
                        <b-form-input type="date" v-model="filter.date_start" :max="today" size="sm"></b-form-input>
                    </b-form-group>
                    <b-form-group label="Date to">
                        <b-form-input type="date" v-model="filter.date_end" :max="today" size="sm"></b-form-input>
                    </b-form-group>
                    <b-form-group label="Category">
                        <b-form-select v-model="filter.category" :options="categories" size="sm">
                            <template v-slot:first>
                                <option :value="null">- Category -</option>
                            </template>
                        </b-form-select>
                    </b-form-group>
                    <b-form-group label="Project">
                        <b-form-select v-model="filter.project" :options="projects" size="sm">
                            <template v-slot:first>
                                <option :value="null">- Project -</option>
                            </template>
                        </b-form-select>
                    </b-form-group>
                    <b-button variant="secondary" @click="resetFilter" :disabled="!filterChanged" size="sm">
                        <icon name="undo"></icon> Reset
                    </b-button>
                </b-form>
            </b-card>
        </b-collapse>
    </div>
</template>

<script>
    const defaultFilter = {
        type: null,
        date_start: null,
        date_end: null,
        category: null,
        project: null
    }
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
                return  JSON.stringify(this.filter) !== JSON.stringify(defaultFilter)
            },
            today() {
                return new Date().toISOString().split('T')[0];
            }
        },
        watch: {
            filter: {
                deep: true,
                handler(val) {
                    this.emitFilter()
                }
            },
            visible(val) {
                console.log(val)
                sessionStorage.setItem(this.id + '.visible', val)
            }
        }
    }
</script>