<template>
    <b-form
        v-if="loaded"
        @submit.stop.prevent="onSubmit"
    >
        <!-- File format -->
        <b-form-group
            :label="$t('app.file_format')"
        >
            <b-form-radio-group
                v-model="form.format"
                :options="formats"
                stacked
            />
        </b-form-group>

        <!-- Columns -->
        <b-form-group
            :label="$t('app.columns')"
        >
            <b-form-radio-group
                v-model="form.columns"
                :options="columnsSelection"
                stacked
            />
        </b-form-group>

        <!-- Grouping -->
        <b-form-group
            :label="$t('app.grouping')"
        >
            <b-form-radio-group
                v-model="form.grouping"
                :options="groupings"
                stacked
            />
        </b-form-group>

        <!-- Selection -->
        <b-form-group
            v-if="selections"
            :label="$t('app.selection')"
        >
            <b-form-radio-group
                v-model="form.selection"
                :options="selections"
                stacked
            />
        </b-form-group>

        <!-- Submit -->
        <p>
            <b-button
                type="submit"
                variant="primary"
                :disabled="isBusy"
            >
                <font-awesome-icon icon="download" />
                {{ $t('app.export') }}
            </b-button>
        </p>
    </b-form>
    <p v-else>
        {{ $t('app.loading') }}
    </p>
</template>

<script>
import transactionsApi from '@/api/accounting/transactions'
import { postRequest } from '@/utils/form'
export default {
    data () {
        return {
            loaded: false,
            form: {
                format: null,
                columns: null,
                grouping: null,
                selection: null
            },
            isBusy: false,
            formats: null,
            columnsSelection: null,
            groupings: null,
            selections: null
        }
    },
    async created () {
        try {
            let data = await transactionsApi.fetchExportData()
            this.form.format = data.format
            this.form.columns = data.columns
            this.form.grouping = data.grouping
            this.form.selection = data.selection
            this.formats = Object.entries(data.formats).map(e => ({
                value: e[0],
                text: e[1]
            }))
            this.columnsSelection = Object.entries(data.columnsSelection).map(e => ({
                value: e[0],
                text: e[1]
            }))
            this.groupings = Object.entries(data.groupings).map(e => ({
                value: e[0],
                text: e[1]
            }))
            this.selections = data.selections ? Object.entries(data.selections).map(e => ({
                value: e[0],
                text: e[1]
            })) : null
            this.loaded = true
        } catch (err) {
            alert(err)
        }
    },
    methods: {
        async onSubmit () {
            this.isBusy = true
            try {
                postRequest(this.route('api.accounting.doExport'), this.form)
            } catch (err) {
                alert(err)
            }
            this.isBusy = false
        }
    }
}
</script>
