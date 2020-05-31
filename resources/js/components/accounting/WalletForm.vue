<template>
    <validation-observer
        ref="observer"
        v-slot="{ handleSubmit }"
        slim
    >
        <b-form
            @submit.stop.prevent="handleSubmit(onSubmit)"
        >
            <b-row>
                <b-col md="8" class="mb-4">

                    <!-- Name -->
                    <validation-provider
                        :name="$t('app.name')"
                        vid="name"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group
                            :label="$t('app.name')"
                            :state="getValidationState(validationContext)"
                            :invalid-feedback="validationContext.errors[0]"
                        >
                            <b-form-input
                                v-model="form.name"
                                required
                                :autofocus="!wallet"
                                autocomplete="off"
                                :state="getValidationState(validationContext)"
                            />
                        </b-form-group>
                    </validation-provider>

                    <b-form-checkbox
                        v-model="form.is_default"
                    >
                        {{ $t('app.default') }}
                    </b-form-checkbox>

                </b-col>
                <b-col md="4" class="mb-4">

                    <!-- Roles -->
                    <b-card
                        v-if="roles"
                        :header="$t('app.roles_with_access')"
                    >
                        <p><em>{{ $t('app.specifying_no_role_will_allow_access_by_any') }}</em></p>
                        <b-form-checkbox-group
                            v-model="form.roles"
                            :options="roleOptions"
                            stacked
                        />
                        <em v-if="roles.length == 0">
                            {{ $t('app.no_roles_defined') }}
                        </em>
                    </b-card>

                </b-col>

            </b-row>

            <p class="d-flex justify-content-between align-items-start">

                <!-- Submit -->
                <b-button
                    type="submit"
                    variant="primary"
                    :disabled="disabled"
                >
                    <font-awesome-icon icon="check" />
                    {{ wallet ? $t('app.update') : $t('app.add') }}
                </b-button>

                <!-- Delete -->
                <b-button
                    v-if="wallet && wallet.can_delete"
                    variant="link"
                    :disabled="disabled"
                    class="text-danger"
                    @click="onDelete"
                >
                    {{ $t('app.delete') }}
                </b-button>

            </p>
        </b-form>
    </validation-observer>
</template>

<script>
import rolesApi from '@/api/user_management/roles'
export default {
    props: {
        wallet: {
            type: Object,
            required: false
        },
        disabled: Boolean
    },
    data () {
        return {
            form: this.wallet ? {
                name: this.wallet.name,
                is_default: this.wallet.is_default,
                roles: this.wallet.roles,
            } : {
                name: null,
                is_default: false,
                roles: [],
            },
            roles: null
        }
    },
    computed: {
        roleOptions () {
            return this.roles.map(role => ({
                value: role.id,
                text: role.name
            }))
        }
    },
    created () {
        this.fetchRoles()
    },
    methods: {
        async fetchRoles () {
            // TODO @can('viewAny', App\Role::class)
            let data = await rolesApi.list()
            this.roles = data.data
        },
        getValidationState ({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
        onSubmit () {
            this.$emit('submit', this.form)
        },
        onDelete () {
            if (confirm(this.$t('accounting.confirm_delete_wallet'))) {
                this.$emit('delete')
            }
        }
    }
}
</script>
