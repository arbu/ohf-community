<template>
    <div>
        <template v-if="editMode">
            <div class="input-group">
                <input type="text" class="form-control" :disabled="processing" v-model="value" :class="{ 'is-invalid': error != null }" :placeholder="placeholder" ref="passport_id_number" @keyup.enter="store" @keyup.esc="editMode = false">
                <div class="input-group-append" id="button-addon4">
                    <button class="btn btn-outline-success" type="button" @click="store" :disabled="processing || !isValid"><i class="fa fa-check"></i></button>
                    <button class="btn btn-outline-secondary" type="button" @click="editMode = false" :disabled="processing"><i class="fa fa-times-circle"></i></button>
                </div>
                <div class="invalid-feedback" v-if="error != null">
                    {{ error }}
                </div>
            </div>
        </template>
        <template v-else>
            <span class="text-warning">Missing</span>
            [<a href="javascript:;" @click="editMode = true">Update</a>]
        </template>    
    </div>
</template>
<script>
import commonMixin from '../mixins/common.js';
export default {
    mixins: [ commonMixin ],
    props: {
        fieldname: {
            required: true,
            type: String
        },
        placeholder: {
            required: true,
            type: String
        },
        callback: {
            required: true,
            type: Function
        }
    },
    data() {
        return {
            editMode: false,
            error: null,     
            processing: false,
            value: null,
        }
    },
    methods: {
        store() {
            if (!this.isValid) {
                return
            }
            this.processing = true
            this.error = null
            this.callback({
                [this.fieldname]: this.value,
            })
            .then(res => {
                this.editMode = false
                this.error = null
                this.$emit('updated', res.data.data)
            })
            .catch(err => {
                this.error = this.extractResponseErrorMessage(err, this.fieldname)
            })
            .then(() => {
                this.processing = false
            })
        }
    },
    watch: {
        editMode(val, oldVal) {
            if (val) {
                this.$nextTick(() => this.$refs.passport_id_number.focus())
            } else {
                this.error = null
                this.value = null
            }
        }
    },
    computed: {
        isValid() {
            return this.value != null && this.value.length > 0
        },
    }
}
</script>

