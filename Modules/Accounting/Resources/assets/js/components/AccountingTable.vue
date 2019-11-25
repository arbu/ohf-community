<template>
    <div>
        <!-- Error alert -->
        <danger-alert-with-reload
            v-if="errorText != null"
            @reload="refresh()"
            :reload-text="reloadText"
        >
            {{ errorText }}
        </danger-alert-with-reload>

        <!-- Table -->
        <b-table
            :id="id"
            striped
            hover
            small
            bordered
            responsive
            :items="itemProvider"
            primary-key="id"
            :fields="fields"
            :api-url="apiUrl"
            :current-page="currentPage"
            :per-page="perPage"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :show-empty="true"
            :empty-text="emptyText"
            :empty-filtered-text="emptyFilteredText"
            :no-sort-reset="true"
        >
            <!-- Link on date column -->
            <template v-slot:cell(date)="data">
                <a :href="data.item.detail_url" v-if="data.item.detail_url != null">{{ data.value }}</a>
                <template v-else>{{ data.value }}</template>
            </template>

            <!-- Handling of busy state -->
            <template v-slot:table-busy>
                <div class="text-center my-2">
                    <icon name="spinner" :spin="true"></icon> {{ loadingText }}
                </div>
            </template>
        </b-table>

        <!-- Pagination -->
        <paginator
            :for-id="id"
            :total-rows="totalRows"
            :per-page="perPage"
            v-model="currentPage"
        ></paginator>

    </div>
</template>

<script>
    import Icon from '../../../../../../resources/js/components/Icon'
    import DangerAlertWithReload from '../../../../../../resources/js/components/DangerAlertWithReload'
    import Paginator from '../../../../../../resources/js/components/Paginator'
    import { getAjaxErrorMessage } from '../../../../../../resources/js/utils'
    export default {
        components: {
            Icon,
            DangerAlertWithReload,
            Paginator
        },
        props: {
            id: {
                type: String,
                required: false,
                default: 'accounting.transactions.table'
            },
            apiUrl: {
                type: String,
                required: true
            },
            labels: {
                type: Object,
                required: true
            },
            defaultSortBy: {
                required: false,
                type: String,
                default: 'registered'
            },
            defaultSortDesc: {
                required: false,
                type: Boolean,
                default: true,
            },
            itemsPerPage: {
                required: false,
                type: Number,
                default: 25
            },
            loadingText: {
                type: String,
                required: false,
                default: 'Loading...'
            },
            emptyText: {
                type: String,
                required: false,
                default: 'There are no records to show.'
            },
            emptyFilteredText: {
                type: String,
                required: false,
                default: 'There are no records matching your request.'
            },
            reloadText: {
                type: String,
                required: false,
                default: 'Reload'
            }
        },
        data() {
            return {
                fields: [
                    {
                        key: 'receipt_no',
                        label: `${this.labels['receipt']} #`,
                        sortable: true,
                        class: 'fit text-center',
                        tdClass: (value, key, item) => item.receipt_no != null && !item.has_receipt_pictures ? 'table-warning' : null
                    },
                    {
                        key: 'date',
                        label: this.labels['date'],
                        sortable: true,
                        class: 'fit'
                    },
                    {
                        key: 'amount',
                        label: this.labels['amount'],
                        sortable: false,
                        class: 'fit d-table-cell d-sm-none text-right',
                        tdClass: (value, key, item) => item.type == 'income' ? 'text-success' : item.type == 'spending' ? 'text-danger' : null
                    },
                    {
                        key: 'income',
                        label: this.labels['income'],
                        sortable: false,
                        class: 'fit d-none d-sm-table-cell text-right',
                        tdClass: 'text-success'
                    },
                    {
                        key: 'spending',
                        label: this.labels['spending'],
                        sortable: false,
                        class: 'fit d-none d-sm-table-cell text-right',
                        tdClass: 'text-danger'
                    },
                    {
                        key: 'category',
                        label: this.labels['category'],
                        sortable: true
                    },
                    {
                        key: 'project',
                        label: this.labels['project'],
                        sortable: true
                    },
                    {
                        key: 'description',
                        label: this.labels['description'],
                        sortable: true,
                        class: 'd-none d-sm-table-cell'
                    },
                    {
                        key: 'beneficiary',
                        label: this.labels['beneficiary'],
                        sortable: true,
                        class: 'd-none d-sm-table-cell'
                    },
                    {
                        key: 'registered',
                        label: this.labels['registered'],
                        sortable: true,
                        class: 'fit d-none d-md-table-cell'
                    },
                ],
                perPage: this.itemsPerPage,
                totalRows: 0,
                sortBy: sessionStorage.getItem(this.id + '.sortBy') ? sessionStorage.getItem(this.id + '.sortBy') : this.defaultSortBy,
                sortDesc: sessionStorage.getItem(this.id + '.sortDesc') ? sessionStorage.getItem(this.id + '.sortDesc') == 'true' : this.defaultSortDesc,
                currentPage: sessionStorage.getItem(this.id + '.currentPage') ? parseInt(sessionStorage.getItem(this.id + '.currentPage')) : 1,
                errorText: null
            }
        },
        methods: {
            itemProvider(ctx) {
                this.totalRows = 0
                this.errorText = null
                const params = '?page=' + ctx.currentPage + '&size=' + ctx.perPage + '&sortBy=' + ctx.sortBy + '&sortDesc=' + (ctx.sortDesc ? 1 : 0)
                return axios.get(ctx.apiUrl + params)
                    .then(res => {
                        // Retrieve data
                        const items = res.data.data

                        // Assign total rows value
                        if (res.data.meta && res.data.meta.total) {
                            this.totalRows = res.data.meta.total
                        }

                        // Remember sorting and pagination
                        sessionStorage.setItem(this.id + '.sortBy', ctx.sortBy)
                        sessionStorage.setItem(this.id + '.sortDesc', ctx.sortDesc)
                        sessionStorage.setItem(this.id + '.currentPage', ctx.currentPage)

                        return items || []
                    })
                    .catch(err => {
                        this.errorText = getAjaxErrorMessage(err)
                        return []
                    })
            },
            refresh() {

                // Reset cached state
                sessionStorage.removeItem(this.id + '.sortBy')
                sessionStorage.removeItem(this.id + '.sortDesc')
                sessionStorage.removeItem(this.id + '.currentPage')

                this.currentPage = 1
                this.sortBy = this.defaultSortBy
                this.sortDesc = this.defaultSortDesc

                // No need to emit a refresh as sortBy and sortDesc are synced and will trigger a refresh when changed
                // $root.$emit('bv::refresh::table', id)
            }
        },
        watch: {
            sortBy(val, oldVal) {
                // Reset page chen changing order
                this.currentPage = 1
            },
            sortDesc(val, oldVal) {
                // Reset page chen changing order direction
                this.currentPage = 1
            }
        }
    }
</script>