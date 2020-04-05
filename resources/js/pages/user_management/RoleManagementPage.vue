<template>
    <div>
        <role-details
            v-if="role != null"
            :role="role"
            :user-list-api-url="userListApiUrl"
            @close="role = null"
        />
        <roles-table
            v-else
            :api-url="apiUrl"
            @selectRole="role = $event"
        />
    </div>
</template>

<script>
import RolesTable from '@/components/user_management/RolesTable.vue'
import RoleDetails from '@/components/user_management/RoleDetails.vue'
export default {
    components: {
        RolesTable,
        RoleDetails
    },
    props: {
        apiUrl: {
            type: String,
            required: true
        },
        userListApiUrl: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            role: null
        }
    }
}
</script>