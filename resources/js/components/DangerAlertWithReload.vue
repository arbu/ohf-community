<template>
    <b-alert variant="danger" :show="true">
        <b-row align-v="center">
            <b-col>
                <i class="fa fa-times-circle"></i> <slot></slot>
            </b-col>
            <b-col sm="auto">
                <b-button
                    variant="danger"
                    size="sm"
                    @click="$emit('reload')"
                    class="float-right"
                >
                    <i class="fa fa-redo"></i> {{ reloadText }}
                </b-button>
            </b-col>
        </b-row>
    </b-alert>
</template>

<script>
export default {
    props: {
        reloadText: {
            type: String,
            required: false,
            default: 'Reload'
        }
    }
}
</script>