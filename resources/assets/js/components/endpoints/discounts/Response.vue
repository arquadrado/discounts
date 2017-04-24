<template>
    <div class="row response" :class="{'success': success}">
        
        <div class="col-xs-12 discounts">
            <h3>Applied discounts</h3>
            <hr>
            <div v-for="discount in response.discounts" class="row discount">
                <div class="col-xs-12">
                    <span>{{ discount }}</span>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-xs-4">
                    <span><strong>Total w/o discount: </strong>{{ response.total }}</span>
                </div>

                <div class="col-xs-4">
                    <span><strong>Total discount: </strong>{{ response.discount }}</span>
                </div>

                <div class="col-xs-4">
                    <span><strong>Total: </strong>{{ totalWithDiscount }}</span>
                </div>
            </div>
        </div>

    </div>
</template>

<script>




    export default {
        props: ['response'],
        components: {
            
        },
        data: () => {
            return {
                

            }
        },

        computed: {

            success () {
                return this.response.status === 'success'
            },

            totalWithDiscount () {
                return Number(this.response.total) - Number(this.response.discount)
            }
        },

        methods: {
            
        }

    }
</script>
