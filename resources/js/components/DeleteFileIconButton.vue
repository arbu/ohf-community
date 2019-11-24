<template>
    <button class="btn btn-link text-dark" @click="deleteFile()" :disabled="processing">
        <icon name="times-circle" :spin="processing"></icon>
    </button>
</template>

<script>
    import { getAjaxErrorMessage } from '../utils'
    import Icon from './Icon'
    export default {
        components: {
            Icon
        },
        props: {
            deleteUrl: {
                required: true,
                type: String
            },
            path: {
                required: true,
                type: String
            }
        },
        data(){
            return {
                processing: false
            }
        },
        methods: {
            deleteFile() {
                if (window.confirm('Delete this file?')) {
                    this.processing = true;
                    axios.post(this.deleteUrl, {
                            path: this.path
                        })
                        .then(response => {
                            this.$emit('deleted')
                        })
                        // Error handling
                        .catch(err => {
                            console.log(err);
                            window.alert(getAjaxErrorMessage(err));
                        })
                        .then(() => {
                            this.processing = false;
                        })
                }
            }
        }
    }
</script>