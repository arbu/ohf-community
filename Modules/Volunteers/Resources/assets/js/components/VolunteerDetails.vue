<template>
    <div>
        <div class="alert alert-warning mt-3" v-if="error != null">
            <p>{{ error }}</p>
            <button type="button" class="btn btn-warning btn-sm" @click="refresh"><i class="fa fa-sync"></i> Reload</button>
        </div>
        <div v-if="!loaded" class="text-center mt-2">
            Loading...
        </div>
        <template v-else>
            <h2>{{ volunteer.first_name }} {{ volunteer.last_name }}</h2>
            <i class="fas" :class="{ 'fa-male': volunteer.gender == 'm', 'fa-female': volunteer.gender == 'f', }"></i>
            {{ volunteer.date_of_birth }} (age {{ volunteer.age }}), 
            {{ volunteer.nationality }}
           
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped table-hover mt-4">
                    <thead>
                        <tr>
                            <th>Arrival</th>
                            <th>Departure</th>
                            <th class="text-right text-nowrap"># Weeks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="stay in volunteer.stays" :key="stay.id">
                            <td>{{ stay.arrival }}</td>
                            <td>
                                <template v-if="stay.departure != null">
                                    {{ stay.departure }}
                                </template>
                                <template v-else>
                                    open-end
                                </template>                            
                            </td>
                            <td class="text-right">
                                <template v-if="stay.departure != null">
                                    {{ Math.round(stay.num_days / 7) }}
                                </template>
                                <template v-else>
                                    -
                                </template>      
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </template>        
    </div>
</template>
<script>
    export default {
        data() {
            return {
                volunteer: null,
                loaded: false,
                error: null,                
            }
        },
        props: ['volunteer_id'],
        created() {
            axios.get('/api/volunteers/' + this.volunteer_id)
                .then(res => {
                    this.volunteer = res.data.data;
                })
                .catch(err => {
                    this.error = err;
                })
                .then(() => {
                    this.loaded = true;
                });
        }        
    }
</script>