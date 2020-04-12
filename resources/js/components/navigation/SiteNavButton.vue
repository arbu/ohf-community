<template>
    <button
        v-if="name == 'delete'"
        class="btn btn-danger d-none d-md-inline-block"
        @click="deleteAction"
    >
        <font-awesome-icon :icon="button.icon" />
        {{ button.caption }}
    </button>
    <!--  TODO
    <button
        v-if="name == 'delete'"
        class="btn btn-link text-light d-md-none"
        @click="deleteAction"
    >
        <font-awesome-icon :icon="button.icon" />
    </button>
    -->
    <a
        v-else-if="name == 'action'"
        :href="button.url"
        class="btn btn-primary d-none d-md-inline-block"
    >
        <font-awesome-icon :icon="button.icon" />
        {{ button.caption }}
    </a>
    <a
        v-else-if="name == 'back'"
        :href="button.url"
        class="btn btn-secondary d-none d-md-inline-block"
    >
        <font-awesome-icon :icon="button.icon" />
        {{ button.caption }}
    </a>
        <!--
            TODO
         @php
            if (isset($button['attributes'])) {
                $attributes = collect($button['attributes'])
                    ->map(fn ($v, $k) => $k . '="' . $v . '"')
                    ->implode(' ');
            } else {
                $attributes = '';
            }
        @endphp -->
            <!-- {!! $attributes !!} -->
    <a
        v-else
        :href="button.url"
        class="btn btn-secondary d-none d-md-inline-block"
        :target="name == 'help' ? '_blank' : null"
    >
        <font-awesome-icon :icon="button.icon" />
        {{ button.caption }}
    </a>
    <!-- TODO
    <a
        v-else
        :href="button.url"
        class="btn text-light d-md-none"
        :title="button.caption"
        :target="name == 'help' ? '_blank' : null"
    >
        <font-awesome-icon :icon="button.icon" />
    </a> -->
</template>

<script>
export default {
    props: {
        button: {
            type: Object,
            required: true
        },
        name: {
            type: String,
            required: true
        }
    },
    methods: {
        deleteAction() {
            if (confirm(this.button.confirmation)) {
                postRequest(this.button.url, {}, 'delete')
            }
        }
    }
}
</script>