<template>
    <div class="container">
        <div class="row api-tester">
            <div class="col-xs-12">
                <h1><strong>API Tester</strong></h1>
            </div>
        </div>
        <div class="row api-tester-description">
            <div class="col-xs-12">
                <h5>This is a simple page that allows to test the differente api routes</h5>
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-12">
                <h3><strong>Endpoints</strong></h3>
            </div>
        </div>
        <component v-for="endpoint in endpoints" :is="endpoint.name" :key="endpoint.name" :endpoint="endpoint"></component>

    </div>
</template>

<script>

import Discounts from './endpoints/Discounts.vue'


    export default {
        components: {
            'discounts': Discounts
        },
        data: () => {
            return {
                endpoints: null

            }
        },
        mounted() {
            $.get('/api/', (response) => {
                this.endpoints = response 
            }).fail((response) => {
                console.log(response, 'fail')

            })

        }
    }
</script>
