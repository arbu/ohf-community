<template>
    <div>
        <!-- Error alert -->
        <danger-alert-with-reload
            v-if="errorText != null"
            @reload="$root.$emit('bv::refresh::table', id)"
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
        <b-row align-v="center" class="mb-3" v-if="totalRows > 0">
            <b-col>
                <b-pagination
                    size="sm"
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    :aria-controls="id"
                    class="mb-0"
                ></b-pagination>
            </b-col>
            <b-col sm="auto" class="text-right">
                <small>{{ ((currentPage - 1) * perPage) + 1 }} - {{ Math.min(currentPage * perPage, totalRows) }} / {{ totalRows }}</small>
            </b-col>
        </b-row>

    </div>
</template>

<script>
    import Icon from '../../../../../../resources/js/components/Icon'
    import DangerAlertWithReload from '../../../../../../resources/js/components/DangerAlertWithReload'
    import { getAjaxErrorMessage } from '../../../../../../resources/js/utils'
    export default {
        components: {
            Icon,
            DangerAlertWithReload
        },
        props: {
            apiUrl: {
                type: String,
                required: true
            },
            labels: {
                type: Object,
                required: true
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
                id: 'accountingTransactionsTable',
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
                currentPage: 1,
                perPage: 25,
                totalRows: 0,
                sortBy: 'registered',
                sortDesc: true,
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
                        return items || []
                    })
                    .catch(err => {
                        this.errorText = getAjaxErrorMessage(err)
                        return []
                    })
            }
        }
    }
</script>