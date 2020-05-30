<template>
    <alert-with-retry
        v-if="errorText"
        :value="errorText"
        @retry="fethData"
    />
    <div v-else-if="transaction">
        <b-list-group
            flush
            class="mb-2"
        >
            <two-col-list-group-item
                v-if="transaction.receipt_no"
                :title="$t('accounting.receipt')"
                :text="transaction.receipt_no"
            >
                {{ transaction.receipt_no }}
                <div
                    class="mt-2"
                >
                    <template v-if="transaction.receipt_pictures">
                        <a
                            v-for="picture_url in transaction.receipt_pictures"
                            :key="picture_url"
                            :href="picture_url"
                            data-lity
                        >
                            <thumbnail-image
                                :src="picture_url"
                                :size="150"
                            />
                        </a>
                    </template>
                    <receipt-picture-button
                        v-else
                        v-model="transaction.receipt_pictures"
                        :id="transaction.id"
                        show-lables
                    />
                </div>
            </two-col-list-group-item>
            <two-col-list-group-item
                :title="$t('app.date')"
                :text="dateFormat(transaction.data)"
            />
            <two-col-list-group-item
                :title="transaction.type == 'income' ? $t('accounting.income') : $t('accounting.spending')"
            >
                <span :class="{
                    'text-success': transaction.type == 'income',
                    'text-danger': transaction.type == 'spending'
                }">
                    {{ numberFormat(transaction.amount) }}
                </span>
            </two-col-list-group-item>
            <two-col-list-group-item
                :title="$t('app.category')"
                :text="transaction.category"
            />
            <two-col-list-group-item
                v-if="transaction.secondary_category"
                :title="$t('app.secondary_category')"
                :text="transaction.secondary_category"
            />
            <two-col-list-group-item
                v-if="transaction.project"
                :title="$t('app.project')"
                :text="transaction.project"
            />
            <two-col-list-group-item
                v-if="transaction.location"
                :title="$t('app.location')"
                :text="transaction.location"
            />
            <two-col-list-group-item
                v-if="transaction.cost_center"
                :title="$t('accounting.cost_center')"
                :text="transaction.cost_center"
            />
            <two-col-list-group-item
                :title="$t('app.description')"
                :text="transaction.description"
            />
            <two-col-list-group-item
                :title="$t('accounting.beneficiary')"
                :text="transaction.beneficiary"
            />
            <two-col-list-group-item
                v-if="transaction.wallet_owner"
                :title="$t('accounting.wallet_owner')"
                :text="transaction.wallet_owner"
            />
            <two-col-list-group-item
                v-if="transaction.remarks"
                :title="$t('app.remarks')"
                :text="transaction.remarks"
            />
            <two-col-list-group-item
                v-if="transaction.booked"
                :title="$t('accounting.booked')"
            >
                <template v-if="transaction.can_book_externally && transaction.external_id">
                    Webling:
                    <a
                        v-if="transaction.external_url"
                        :href="transaction.external_ur"
                        target="_blank"
                    >
                        {{ transaction.external_id }}
                    </a>
                    <template v-else>
                        {{ transaction.external_id }}
                    </template>
                </template>
                <template v-else>
                    {{ $t('app.yes') }}
                </template>
                <p
                    v-if="transaction.can_undo_booking"
                    class="mb-0 mt-2"
                >
                    <b-button
                        variant="outline-danger"
                        size="sm"
                        :disabled="isBusy"
                        @click="undoBooking"
                    >
                        <font-awesome-icon icon="undo" />
                        {{ $t('accounting.undo_booking') }}
                    </b-button>
                </p>
            </two-col-list-group-item>
            <two-col-list-group-item
                :title="$t('app.registered')"
            >
                {{ dateTimeFormat(transaction.created_at) }}
                <span v-if="transaction.audit_user_name">
                    ({{ transaction.audit_user_name }})
                </span>
            </two-col-list-group-item>
        </b-list-group>
    </div>
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import numeral from 'numeral'
import moment from 'moment'
import transactionsApi from '@/api/accounting/transactions'
import AlertWithRetry from '@/components/alerts/AlertWithRetry'
import TwoColListGroupItem from '@/components/ui/TwoColListGroupItem'
import ReceiptPictureButton from '@/components/accounting/ReceiptPictureButton'
import ThumbnailImage from '@/components/ui/ThumbnailImage'
import showSnackbar from '@/snackbar'
export default {
    components: {
        AlertWithRetry,
        TwoColListGroupItem,
        ReceiptPictureButton,
        ThumbnailImage
    },
    props: {
        id: {
            required: true
        }
    },
    data () {
        return {
            transaction: null,
            errorText: null,
            isBusy: false
        }
    },
    computed: {
    },
    async created () {
        this.fethData()
    },
    methods: {
        async fethData () {
            try {
                let data = await transactionsApi.find(this.id)
                this.transaction = data.data
            } catch (err) {
                this.errorText = err
            }
        },
        numberFormat (value) {
            return numeral(value).format('0,0.00')
        },
        dateFormat (value) {
            return moment(value).format('LL')
        },
        dateTimeFormat (value) {
            return moment(value).format('LLL')
        },
        async undoBooking () {
            if (confirm(this.$t('accounting.really_undo_booking'))) {
                this.isBusy = true
                try {
                    let data = await transactionsApi.undoExternalBooking(this.id)
                    this.transaction = data.data
                    showSnackbar(data.message)
                } catch (err) {
                    alert(err)
                }
                this.isBusy = false
            }
        }
    }
}
</script>
