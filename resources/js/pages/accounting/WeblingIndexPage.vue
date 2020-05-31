<template>
    <div v-if="periods">
        <template v-if="periods.length > 0">
            <p>
                {{ $t('accounting.please_choose_month_with_unbooked_transactions_in_open_booking_period') }}
            </p>
            <div
                v-for="period in periods"
                :key="period.id"
            >
                <h2>{{ period.title }}</h2>
                <b-list-group
                    v-if="period.months.length > 0"
                    class="mb-4 mt-3"
                >
                    <b-list-group-item
                        v-for="month in period.months"
                        :key="month.date"
                        :href="route('accounting.webling.prepare', {
                            period: period.id,
                            from: montStartDate(month.date),
                            to: monthEndDate(month.date)
                        })"
                        class="d-flex justify-content-between align-items-center"
                    >
                        <span>{{ monthNameDate(month.date) }}</span>
                        <small>{{ month.transactions }} {{ $t('accounting.transactions') }}</small>
                    </b-list-group-item>
                </b-list-group>
                <b-alert
                    v-else
                    show
                    variant="info"
                >
                    {{ $t('accounting.no_months_with_unbooked_transactions_found') }}
                </b-alert>
            </div>
        </template>
        <b-alert
            v-else
            show
            variant="info"
        >
            {{ $t('accounting.no_open_periods_found') }}
        </b-alert>
    </div>
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import moment from 'moment'
import weblingApi from '@/api/accounting/webling'
export default {
    data() {
        return {
            periods: null
        }
    },
    mounted () {
        this.fetchData()
    },
    methods: {
        async fetchData () {
            try {
                let data = await weblingApi.listPeriods()
                this.periods = data.periods
            } catch (err) {
                alert(err)
            }
        },
        monthNameDate(value) {
            return moment(value).format('MMMM YYYY')
        },
        montStartDate(value) {
            return moment(value).startOf('month').format(moment.HTML5_FMT.DATE)
        },
        monthEndDate(value) {
            return moment(value).endOf('month').format(moment.HTML5_FMT.DATE)
        }
    }
}
</script>
