<template>
    <i class="fa" :class="trueFalseIconClasses(value)"></i>
</template>
<script>
export default {
    props: {
        value: {
            type: Boolean,
            required: true
        }
    },
    methods: {
        trueFalseIconClasses(value) {
            if (value) {
                return ['fa-check', 'text-success'];
            }
            return 'fa-times';
        }
    }
}
</script>

