<template>
    <div>
        <!-- Error message -->
        <error-alert
            v-if="error"
            :message="error"
        />

        <!-- Heading -->
        <h2 class="display-4">{{ $t('app.edit_role', { name: role.name }) }}</h2>

        <!-- Name input -->
        <b-form-group
            :label="$t('app.name')"
            label-for="role-name"
            :invalid-feedback="nameInvalidFeedback"
            :state="nameState"
        >
            <b-form-input
                id="role-name"
                v-model="name"
                :state="nameState"
                trim
                autocomplete="off"
                @keydown.enter="save()"
            />
        </b-form-group>

        <!-- Sumit button -->
        <b-button
            variant="primary"
            key="submit-edit"
            :disabled="busy"
            @click="save()"
        >
            <font-awesome-icon icon="check"/>
            {{ $t('app.submit') }}
        </b-button>

        <!-- Cancel button -->
        <b-button
            variant="secondary"
            key="cancel-edit"
            :disabled="busy"
            @click="$emit('cancel')"
        >
            <font-awesome-icon icon="times-circle"/>
            {{ $t('app.cancel') }}
        </b-button>
    </div>
</template>

<script>
import api from '@/api/user_management'
import { getAjaxErrorMessage, showSnackbar } from '@/utils'
import { BButton, BFormInput, BFormGroup } from 'bootstrap-vue'
import ErrorAlert from '@/components/alerts/ErrorAlert'
export default {
    components: {
        BButton,
        BFormInput,
        BFormGroup,
        ErrorAlert
    },
    props: {
        role: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {
            name: this.role.name,
            busy: false,
            error: null
        }
    },
    computed: {
        nameState() {
            return this.name != this.role.name ? (this.name.length > 0 ? true : false) : null
        },
        nameInvalidFeedback() {
            return this.$t('validation.required', { attribute: this.$t('validation.attributes.name') })
        }
    },
    methods: {
        save() {
            this.error = false
            this.busy = true
            api.updateRole(this.role, {
                    name: this.name
                })
                .then(data => {
                    showSnackbar(data.message)
                    this.$emit('save', {
                        name: this.name
                    })
                })
                .catch(err => this.error = getAjaxErrorMessage(err))
                .finally(() => this.busy = false)
        }
    }
}
</script>