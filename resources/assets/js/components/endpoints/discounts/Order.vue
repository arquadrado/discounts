<template>
    <div class="row final-order">
        <div v-if="hasOrder" class="col-xs-12">
            
            <div class="row">
                <div class="col-xs-12">
                    <h3>Order details</h3><br>
                </div> 
            </div>     
            
            <div class="row">
                <div class="col-xs-12">

                    <div class="client-details">
                        <h4>Client information</h4><br>
                        <span><strong>Name: </strong> {{ customer.name }}</span><br>
                        <span><strong>Since: </strong> {{ customer.since }}</span><br>
                        <span><strong>Revenue: </strong> {{ customer.revenue }}</span><br>
                    </div>

                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-xs-12">
                    <div class="products">
                    <h4>Items</h4><br>
                        <ul>
                            <li>
                                <div class="row">
                                    <div class="col-xs-3">Product</div>
                                    <div class="col-xs-3">Unit price</div>
                                    <div class="col-xs-3">Quantity</div>
                                    <div class="col-xs-3">Total</div>
                                </div>
                            </li>
                            
                            <li v-for="item in order.items">
                                <div class="row">
                                    <div class="col-xs-3">{{ resolveProductName(item) }}</div>
                                    <div class="col-xs-3">{{ item['unit_price'] }}</div>
                                    <div class="col-xs-3">{{ item.quantity }}</div>
                                    <div class="col-xs-3">{{ item.total }}</div>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-xs-8"><strong>Total</strong></div>
                <div class="col-xs-4">{{ order.total }}</div>
            </div>

        </div>
        <div v-else class="col xs-12">
            <h3>There's no selected order</h3>
        </div>



    </div>
</template>

<script>

import numbers from '../../../mixins/numbers.js'


    export default {
        props: ['customer', 'order', 'products'],
        mixins: [numbers],
        components: {
            
        },
        data: () => {
            return {
                

            }
        },

        computed: {
            hasOrder () {
                return this.customer && this.order
            }
        },

        methods: {
            resolveProductName (item) {
                return this.products.reduce((reduced, product) => {
                    if (product.id === item['product_id']) {
                        reduced = product.description
                    }
                    return reduced
                }, null)
            }
        }

    }
</script>
