<template>
    <validation-observer
        ref="observer"
        v-slot="{ handleSubmit }"
        slim
    >
        <b-form
            v-if="loaded"
            @submit.stop.prevent="handleSubmit(onSubmit)"
        >

            <!-- Wallet -->
            <div v-if="!transaction && wallets.length > 1">
                <b-form-group
                    :label="$t('accounting.receipt_no')"
                >
                    <b-form-select
                        v-model="form.wallet_id"
                        :options="walletOptions"
                        required
                        @change="setNewReceiptNumber"
                    />
                </b-form-group>
            </div>

            <b-form-row>

                <!-- Receipt No -->
                <b-col sm="auto">
                    <validation-provider
                        :name="$t('accounting.receipt_no')"
                        vid="receipt_no"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('accounting.receipt_no')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-input
                                v-model="form.receipt_no"
                                type="number"
                                :min="1"
                                :step="1"
                                required
                                autocomplete="off"
                                :state="getValidationState(validationContext)"
                            />
                        </b-form-group>
                    </validation-provider>
                </b-col>

                <!-- Date -->
                <b-col sm="auto">
                    <validation-provider
                        :name="$t('app.date')"
                        vid="date"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('app.date')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-datepicker
                                v-model="form.date"
                                :max="today"
                                required
                                :state="getValidationState(validationContext)"
                            />
                        </b-form-group>
                    </validation-provider>
                </b-col>

            </b-form-row>

            <b-form-row>

                <!-- Type -->
                <b-col sm="auto" class="pb-3">
                    <validation-provider
                        :name="$t('app.type')"
                        vid="type"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('app.type')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-radio-group
                                v-model="form.type"
                                :options="types"
                                required
                                :state="getValidationState(validationContext)"
                            />
                        </b-form-group>
                    </validation-provider>
                </b-col>

                <!-- Amount -->
                <b-col sm>
                    <validation-provider
                        :name="$t('app.amount')"
                        vid="amount"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('app.amount')"
                            :description="$t('app.write_decimal_point_as_comma')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-input
                                v-model="form.amount"
                                type="number"
                                :min="0"
                                step="any"
                                autocomplete="off"
                                required
                                :state="getValidationState(validationContext)"
                            />
                        </b-form-group>
                    </validation-provider>
                </b-col>

                <!-- Beneficiary -->
                <b-col sm>
                    <validation-provider
                        :name="$t('accounting.beneficiary')"
                        vid="beneficiary"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('accounting.beneficiary')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-input
                                v-model="form.beneficiary"
                                autocomplete="off"
                                required
                                list="beneficiary-list"
                                :state="getValidationState(validationContext)"
                            />
                        </b-form-group>
                        <b-form-datalist
                            id="beneficiary-list"
                            :options="beneficiaries"
                        />
                    </validation-provider>
                </b-col>
            </b-form-row>

            <b-form-row>

                <!-- Category -->
                <b-col sm>
                    <validation-provider
                        :name="$t('app.category')"
                        vid="category"
                        :rules="{
                            required: true,
                            oneOf: fixed_categories ? categories : false
                        }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('app.category')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-select
                                v-if="fixed_categories"
                                v-model="form.category"
                                :options="categories"
                                required
                                :state="getValidationState(validationContext)"
                            >
                                <template v-slot:first>
                                    <b-form-select-option :value="null"></b-form-select-option>
                                </template>
                            </b-form-select>
                            <template v-else>
                                <b-form-input
                                    v-model="form.category"
                                    list="category-list"
                                    required
                                    :state="getValidationState(validationContext)"
                                />
                                <b-form-datalist
                                    id="category-list"
                                    :options="categories"
                                />
                            </template>
                        </b-form-group>
                    </validation-provider>
                </b-col>

                <!-- Secondary category -->
                <b-col
                    v-if="secondary_categories != null"
                    sm
                >
                    <validation-provider
                        :name="$t('app.secondary_category')"
                        vid="secondary_category"
                        :rules="{
                            oneOf: fixed_secondary_categories ? secondary_categories : false
                        }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('app.secondary_category')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-select
                                v-if="fixed_secondary_categories"
                                v-model="form.secondary_category"
                                :options="secondary_categories"
                                :state="getValidationState(validationContext)"
                            >
                                <template v-slot:first>
                                    <b-form-select-option :value="null"></b-form-select-option>
                                </template>
                            </b-form-select>
                            <template v-else>
                                <b-form-input
                                    v-model="form.secondary_category"
                                    list="secondary_category-list"
                                    :state="getValidationState(validationContext)"
                                />
                                <b-form-datalist
                                    id="secondary_category-list"
                                    :options="secondary_categories"
                                />
                            </template>
                        </b-form-group>
                    </validation-provider>
                </b-col>

                <!-- Projects -->
                <b-col sm>
                    <validation-provider
                        :name="$t('app.project')"
                        vid="project"
                        :rules="{
                            oneOf: fixed_projects ? projects : false
                        }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('app.project')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-select
                                v-if="fixed_projects"
                                v-model="form.project"
                                :options="projects"
                                :state="getValidationState(validationContext)"
                            >
                                <template v-slot:first>
                                    <b-form-select-option :value="null"></b-form-select-option>
                                </template>
                            </b-form-select>
                            <template v-else>
                                <b-form-input
                                    v-model="form.project"
                                    list="project-list"
                                    :state="getValidationState(validationContext)"
                                />
                                <b-form-datalist
                                    id="project-list"
                                    :options="projects"
                                />
                            </template>
                        </b-form-group>
                    </validation-provider>
                </b-col>

            </b-form-row>
            <b-form-row v-if="locations != null || cost_centers != null">

                <!-- Location -->
                <b-col
                    v-if="locations != null"
                    sm
                >
                    <validation-provider
                        :name="$t('app.location')"
                        vid="location"
                        :rules="{
                            oneOf: fixed_locations ? locations : false
                        }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('app.location')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-select
                                v-if="fixed_locations"
                                v-model="form.location"
                                :options="locations"
                                :state="getValidationState(validationContext)"
                            >
                                <template v-slot:first>
                                    <b-form-select-option :value="null"></b-form-select-option>
                                </template>
                            </b-form-select>
                            <template v-else>
                                <b-form-input
                                    v-model="form.location"
                                    list="location-list"
                                    :state="getValidationState(validationContext)"
                                />
                                <b-form-datalist
                                    id="location-list"
                                    :options="locations"
                                />
                            </template>
                        </b-form-group>
                    </validation-provider>
                </b-col>

                <!-- Cost center -->
                <b-col
                    v-if="cost_centers != null"
                    sm
                >
                    <validation-provider
                        :name="$t('accounting.cost_center')"
                        vid="cost_center"
                        :rules="{
                            oneOf: fixed_cost_centers ? cost_centers : false
                        }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('accounting.cost_center')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-select
                                v-if="fixed_cost_centers"
                                v-model="form.cost_center"
                                :options="cost_centers"
                                :state="getValidationState(validationContext)"
                            >
                                <template v-slot:first>
                                    <b-form-select-option :value="null"></b-form-select-option>
                                </template>
                            </b-form-select>
                            <template v-else>
                                <b-form-input
                                    v-model="form.cost_center"
                                    list="cost_center-list"
                                    :state="getValidationState(validationContext)"
                                />
                                <b-form-datalist
                                    id="cost_center-list"
                                    :options="cost_centers"
                                />
                            </template>
                        </b-form-group>
                    </validation-provider>
                </b-col>

            </b-form-row>

            <!-- Description -->
            <validation-provider
                :name="$t('app.description')"
                vid="description"
                :rules="{ required: true }"
                v-slot="validationContext"
            >
                <b-form-group
                    :label="$t('app.description')"
                    :state="getValidationState(validationContext)"
                    :invalid-feedback="validationContext.errors[0]"
                >
                    <b-form-input
                        v-model="form.description"
                        autocomplete="off"
                        required
                        :state="getValidationState(validationContext)"
                    />
                </b-form-group>
            </validation-provider>

            <!-- TODO Receipt pictures -->

            <b-form-row>

                <!-- Wallet owner -->
                <b-col sm="4">
                    <validation-provider
                        :name="$t('accounting.wallet_owner')"
                        vid="wallet_owner"
                        :rules="{ }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('accounting.wallet_owner')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-input
                                v-model="form.wallet_owner"
                                autocomplete="off"
                                :state="getValidationState(validationContext)"
                            />
                        </b-form-group>
                    </validation-provider>
                </b-col>

                <!-- Remarks -->
                <b-col sm="8">
                    <validation-provider
                        :name="$t('app.remarks')"
                        vid="remarks"
                        :rules="{ }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('app.remarks')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-input
                                v-model="form.remarks"
                                autocomplete="off"
                                :state="getValidationState(validationContext)"
                            />
                        </b-form-group>
                    </validation-provider>
                </b-col>

            </b-form-row>

            <p class="d-flex justify-content-between align-items-start">

                <!-- Submit -->
                <b-button
                    type="submit"
                    variant="primary"
                    :disabled="disabled"
                >
                    <font-awesome-icon icon="check" />
                    {{ transaction ? $t('app.update') : $t('app.add') }}
                </b-button>

                <!-- Delete -->
                <b-button
                    v-if="transaction && transaction.can_delete"
                    variant="link"
                    :disabled="disabled"
                    class="text-danger"
                    @click="onDelete"
                >
                    {{ $t('app.delete') }}
                </b-button>

            </p>
        </b-form>
        <p v-else>
            {{ $t('app.loading') }}
        </p>
    </validation-observer>
</template>

<script>
import transactionsApi from '@/api/accounting/transactions'
import moment from 'moment'
export default {
    props: {
        wallets: Array,
        wallet: Object,
        transaction: {
            type: Object,
            required: false
        },
        disabled: Boolean
    },
    data () {
        return {
            loaded: false,
            form: this.transaction ? {
                    receipt_no: this.transaction.receipt_no,
                    date: this.transaction.date,
                    type: this.transaction.type,
                    amount: this.transaction.amount,
                    beneficiary: this.transaction.beneficiary,
                    category: this.transaction.category,
                    secondary_category: this.transaction.secondary_category,
                    project: this.transaction.project,
                    location: this.transaction.project,
                    cost_center: this.transaction.cost_center,
                    description: this.transaction.description,
                    wallet_owner: this.transaction.wallet_owner,
                    remarks: this.transaction.remarks,
                } : {
                    receipt_no: this.wallets.length == 1 ? this.wallets[0].new_receipt_no : null,
                    date: moment().format(moment.HTML5_FMT.DATE),
                    type: null,
                    amount: null,
                    beneficiary: null,
                    category: null,
                    secondary_category: null,
                    project: null,
                    location: null,
                    cost_center: null,
                    description: null,
                    wallet_owner: null,
                    remarks: null,
                    wallet_id: this.wallets.length > 1 ? (this.wallet ? this.wallet.id : null) : this.wallets[0].id
            },
            types: [
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
        today () {
            return moment().format(moment.HTML5_FMT.DATE)
        },
        walletOptions () {
            return this.wallets.map(w => ({ value: w.id, text: w.name }))
        }
    },
    created () {
        this.fetchClassifications()
    },
    mounted () {
        if (!this.transaction && this.wallets.length > 1) {
            this.setNewReceiptNumber()
        }
    },
    methods: {
        async fetchClassifications () {
            try {
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
                if (this.form.wallet_owner == null) {
                    this.form.wallet_owner = classifications.auth_user_name
                }
                this.loaded = true
            } catch (err) {
                alert(err)
            }
        },
        getValidationState ({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
        onSubmit () {
            this.$emit('submit', this.form)
        },
        onDelete () {
            if (confirm(this.$t('accounting.confirm_delete_transaction'))) {
                this.$emit('delete')
            }
        },
        setNewReceiptNumber() {
            const walletId = this.form.wallet_id
            const wallet = this.wallets.filter(w => w.id == walletId)[0]
            this.form.receipt_no = wallet.new_receipt_no
        }
    }
}
</script>
