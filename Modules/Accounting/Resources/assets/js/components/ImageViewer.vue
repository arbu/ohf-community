<style scoped>
    .delete-button {
        position: absolute;
        top: 0px;
        right: 5px;
    }
</style>

<template>
    <div class="form-row">

        <!-- Iterate over thumbnails of current files -->
        <div class="col-auto mb-2" v-for="(path, idx) in paths" :key="path" style="position: relative;">

            <!-- Thumbnail -->
            <active-thumbnail
                :href="path"
                :size="imageSize"
            ></active-thumbnail>

            <!-- Delete button -->
            <delete-file-icon-button
                :path="path"
                :delete-url="deleteUrl"
                v-if="deleteUrl != null"
                class="delete-button"
                @deleted="removeUrl(idx)"
            ></delete-file-icon-button>

        </div>

        <div class="col-auto" v-if="loading">
            <button :style="{ width: `${imageSize}px`, height: `${imageSize}px` }" class="btn btn-light" :disabled="true">
                <icon name="spinner" :spin="true"></icon>
            </button>
        </div>

        <!-- Upload file button -->
        <div class="col-auto mb-2" v-if="uploadUrl != null && !loading">
            <upload-file-button
                :upload-url="uploadUrl"
                @uploaded="addUrls"
                :size="imageSize"
            ></upload-file-button>
        </div>
    </div>
</template>

<script>
    import { getAjaxErrorMessage } from '../../../../../../resources/js/utils'
    import ActiveThumbnail from './ActiveThumbnail'
    import UploadFileButton from './UploadFileButton'
    import DeleteFileIconButton from './DeleteFileIconButton'
    import Icon from './Icon'
    export default {
        components: {
            ActiveThumbnail,
            UploadFileButton,
            DeleteFileIconButton,
            Icon
        },
        props: {
            listUrl: {
                required: true,
                type: String
            },
            uploadUrl: {
                required: false,
                type: String,
                default: null
            },
            deleteUrl: {
                required: false,
                type: String,
                default: null
            }
        },
        data(){
            return {
                paths: [],
                loading: true,
                imageSize: 250
            }
        },
        methods: {
            /**
             * Add additional file URLs
             */
            addUrls(data) {
                this.paths.push(...data)
            },
            /**
             * Remove file URL by index
             */
            removeUrl(idx) {
                this.paths.splice(idx, 1);
            }
        },
        mounted(){
            // Load current pictures from supplied URLs
            axios.get(this.listUrl)
                .then(response => {
                    this.paths = response.data;
                })
                // Error handling
                .catch(err => {
                    console.log(err);
                    window.alert(getAjaxErrorMessage(err));
                })
                .then(() => this.loading = false)
        }
    }
</script>