<template>
    <div class="row response" :class="{'success': success}">

        <div class="col-xs-12 discounts" v-if="success">
            <h3>Applied discounts</h3>
            <hr>
            <div v-for="discount in response.display_discounts" class="row discount">
                <div class="col-xs-12">
                    <span><strong>Description: </strong>{{ discount.description }}</span><span> | </span><span><strong>Value: </strong>{{ round(discount.value) }}</span>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-xs-4">
                    <span><strong>Total w/o discount: </strong>{{ totalWithoutDiscount }}</span>
                </div>

                <div class="col-xs-4">
                    <span><strong>Total discount: </strong>{{ totalDiscount }}</span>
                </div>

                <div class="col-xs-4">
                    <span><strong>Total: </strong>{{ totalWithDiscount }}</span>
                </div>
            </div>
        </div>
        <div class="col-xs-12 discounts" v-else>
            <h3>{{ response.responseJSON.error }}</h3>
        </div>

    </div>
</template>

<script>


import numbers from '../../../mixins/numbers.js'

    export default {
        props: ['response'],
        mixins: [numbers],
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

            totalWithoutDiscount () {
                return this.round(Number(this.response.total))
            },

            totalWithDiscount () {
                return this.round(Number(this.response.total) - Number(this.response.display_total_discount))
            },

            totalDiscount () {
                return this.round(this.response.display_total_discount)
            }

        },

        methods: {

        }

    }
</script>
