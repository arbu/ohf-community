<template>
    <div>
        <input type="file" ref="files" multiple @change="handleFilesUpload()" :accept="accept" class="d-none"/>
        <button :style="{ width: `${size}px`, height: `${size}px` }" class="btn" :class="dragOver && !uploading ? 'btn-dark' : 'btn-light'" @click="addFiles()" :disabled="uploading" ref="fileform">
            <icon :name="uploading ? 'spinner' : (dragOver ? 'upload' : 'plus-circle')" :spin="uploading"></icon>
            <template v-if="uploading">
                {{ uploadStatus }}
            </template>
        </button>
    </div>
</template>
<script>
    import { getAjaxErrorMessage } from '../../../../../../resources/js/utils'
    import Icon from './Icon'
    export default {
        components: {
            Icon
        },
        props: {
            uploadUrl: {
                required: true,
                type: String
            },
            size: {
                type: Number,
                default: 200,
                required: false
            },
            accept: {
                required: false,
                type: String,
                default: 'image/*'
            }
        },
        data(){
            return {
                files: [],
                uploadPercentage: -1,
                dragAndDropCapable: false,
                dragOver: false
            }
        },
        methods: {
            /**
             * Open file browser dialog
             */
            addFiles(){
                this.$refs.files.click();
            },
            /**
             * Handle selected files
             */
            handleFilesUpload(){
                // Add selected files to list of files to be uploaded
                let uploadedFiles = this.$refs.files.files;
                for (var i = 0; i < uploadedFiles.length; i++) {
                    var uploadedFile = uploadedFiles[i]
                    // Ensure only files are added which have not been added yet
                    if (this.files.filter(f => f.name == uploadedFile.name).length == 0) {
                        this.files.push(uploadedFile);
                    }
                }
                // Automatic submission
                this.submitFiles();
            },
            /**
             * Upload files to server
             */
            submitFiles(){
                // Create FormData object
                let formData = new FormData();
                for (var i = 0; i < this.files.length; i++) {
                    let file = this.files[i];
                    formData.append('files[' + i + ']', file);
                }
                // Send POST request
                this.uploadPercentage = 0;
                axios.post(this.uploadUrl,
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            },
                            // Update file upload percentage
                            onUploadProgress: progressEvent => {
                                this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
                            }
                        }
                    )
                    // Publish public URLs of newly uploaded files
                    .then(response => {
                        this.$emit('uploaded', response.data)
                    })
                    // Error handling
                    .catch(err => {
                        console.log(err);
                        window.alert(getAjaxErrorMessage(err));
                    })
                    // Cleanup
                    .then(() => {
                        this.files = [];
                        this.uploadPercentage = -1;
                    });
            },
            /**
             * Prevent navigation away from current window if uploading in progres
             */
            preventNav(event) {
                if (!this.uploading) return
                event.preventDefault()
                event.returnValue = ""
            },
            /**
             * Checks if drag and drop are supported
             */
            determineDragAndDropCapable(){
                var div = document.createElement('div');
                return ( ( 'draggable' in div )
                    || ( 'ondragstart' in div && 'ondrop' in div ) )
                    && 'FormData' in window
                    && 'FileReader' in window;
            },
        },
        computed: {
            /**
             * Determines if upload is in progress
             */
            uploading() {
                return this.uploadPercentage >= 0;
            },
            /**
             * Status text for upload
             */
            uploadStatus() {
                if (this.uploadPercentage == 100) {
                    return 'Processing...'
                }
                return `${this.uploadPercentage} %`
            }
        },
        beforeMount() {
            window.addEventListener("beforeunload", this.preventNav)
        },
        beforeDestroy() {
            window.removeEventListener("beforeunload", this.preventNav);
        },
        mounted(){
            this.dragAndDropCapable = this.determineDragAndDropCapable();
            if (this.dragAndDropCapable) {
                ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach(evt => {
                        this.$refs.fileform.addEventListener(evt, e => {
                            e.preventDefault();
                            e.stopPropagation();
                        })
                })
                this.$refs.fileform.addEventListener('drop', e => {
                    this.dragOver = false
                    if (!this.uploading) {
                        // Add dragged files
                        for (let i = 0; i < e.dataTransfer.files.length; i++) {
                            this.files.push(e.dataTransfer.files[i]);
                        }
                        // Automatic submission
                        this.submitFiles();
                    }
                });
                this.$refs.fileform.addEventListener('dragover', e => {
                    this.dragOver = true
                });
                this.$refs.fileform.addEventListener('dragleave', e => {
                    this.dragOver = false
                });
            }
        }
    }
</script>