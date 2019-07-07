<template>
    <div>
        <div v-if="volunteer != null">
            <h2>{{ volunteer.first_name }} {{ volunteer.last_name }}</h2>
            <i class="fas" :class="{ 'fa-male': volunteer.gender == 'm', 'fa-female': volunteer.gender == 'f', }"></i>
            {{ volunteer.date_of_birth }} (age {{ volunteer.age }}), 
            {{ volunteer.nationality }}

            <button type="button" class="btn btn-warning btn-sm" @click="volunteer = null"><i class="fa fa-times"></i> Close</button>
        </div>
        <volunteer-list v-else @volunteerSelected="showVolunteer"></volunteer-list>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                volunteer: null,
            }
        },
        mounted() {
            
        },
        methods: {
            showVolunteer(volunteer) {
                this.volunteer = volunteer;
            }
        }
    }
</script>