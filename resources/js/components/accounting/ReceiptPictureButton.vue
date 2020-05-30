<template>
    <b-button
        v-if="receiptPictures"
        :href="receiptPictures[0]"
        size="sm"
        variant="secondary"
        data-lity
    >
        <font-awesome-icon icon="image" />
    </b-button>
    <span v-else>
        <input
            ref="fileUpload"
            type="file"
            accept="image/*,application/pdf"
            class="d-none"
            @change="onFileSelect"
        />
        <b-button
            v-if="isBusy"
            disabled
            size="sm"
            variant="light"
        >
            <font-awesome-icon icon="spinner" spin />
            <template v-if="showLables">
                {{ $t('app.please_wait') }}
            </template>
        </b-button>
        <b-button
            v-else
            size="sm"
            variant="warning"
            @click="openFileSelector"
        >
            <font-awesome-icon icon="plus-circle" />
            <template v-if="showLables">
                {{ $t('accounting.choose_picture_of_receipt') }}
            </template>
        </b-button>
    </span>
</template>

<script>
import { showSnackbar } from '@/utils'
import transactionsApi from '@/api/accounting/transactions'
export default {
    props: {
        id: {
            type: Number,
            required: true
        },
        value: {
            required: false
        },
        showLables: Boolean
    },
    data () {
        return {
            receiptPictures: this.value,
            isBusy: false
        }
    },
    methods: {
        openFileSelector () {
            this.$refs.fileUpload.click()
        },
        onFileSelect (evt) {
            if (evt.target.files.length > 0) {
                this.uploadFile(evt.target.files[0])
            }
        },
        async uploadFile(file) {
            this.isBusy = true
            try {
                let data = await transactionsApi.updateReceiptPicture(this.id, file)
                this.receiptPictures = data.receipt_pictures
                this.$emit('input', data.receipt_pictures)
                showSnackbar(data.message)
            } catch (err) {
                alert(err)
            }
            this.isBusy = false
        }
    }
}
</script>
