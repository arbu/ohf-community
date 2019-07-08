<template>
    <div>
        <div class="alert alert-warning mt-3" v-if="error != null">
            <p>{{ error }}</p>
            <button type="button" class="btn btn-warning btn-sm" @click="refresh"><i class="fa fa-sync"></i> Reload</button>
        </div>
        <div v-if="!loaded" class="text-center mt-2">
            <i class="fas fa-spinner fa-pulse"></i> 
            Loading...
        </div>
        <template v-else>
            Details {{ volunteer_id }}
            <h2>{{ volunteer.first_name }}</h2>
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
        mounted() {
            axios.get('/api/volunteers/' + this.volunteer_id)
                .then(res => {
                    this.volunteer = res.data.data;
                    console.log(this.volunteer)
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