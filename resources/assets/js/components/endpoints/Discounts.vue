<template>
    <div class="row endpoint">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-6">
                    <div class="row">
                        <div class="col-xs-12">
                           <h4><strong>{{ `${endpoint['name']} - ${endpoint['uri']}` }}</strong></h4> 
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <button class="toggle-order" @click="toggleOrder">
                                <span v-if="selectOrder">Compose a custom order</span>
                                <span v-else>Select a pre-existing order</span>
                            </button> 
                        </div>
                    </div>

                    <div v-if="selectOrder" class="row select-order">
                        <div class="col-xs-12">
                            <ul>
                                <li @click="setSelectedOrder(order)" class="order" :class="{active: isSelectedOrder(order.id) }" v-for="order in orders">
                                    <span>id: {{ order.id }}</span><br>
                                    <span>customer-id: {{ order['customer-id'] }}</span><br>
                                    <span>total: {{ order.total }}</span><br>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div v-else class="row compose-order">
                        <div class="col-xs-12 costumers">
                            <div class="row">
                                <div class="col-xs-12">
                                    <label for="customers">Customers</label>
                                    <select v-model="selectedCustomer" name="customers" id="customers">
                                        <option value="">Select a customer</option>
                                        <option v-for="customer in customers" :value="customer">{{ customer.name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-8 products">
                                    <label for="products">Products</label>
                                    <select v-model="selectedProduct" name="products" id="products">
                                        <option value="">Select a product</option>
                                        <option v-for="product in products" :value="product">{{ `${product.description} (${product.price})` }}</option>
                                    </select>
                                </div>
                                <div class="col-xs-4">
                                    <button :disabled="!hasSelectedProduct" @click="addProductToOrder">Add selected product</button>
                                    <button  @click="resetProducts">Reset products</button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="col-xs-6">
                    <order :customer="selectedCustomer" :order="selectedOrder" :products="products"></order>
                </div>
            </div>

            <div class="row submit-order">
                <div class="col-xs-12">
                    <button @click="submitOrder" :disabled="!canSubmit">Submit order</button>
                </div>
            </div>

            <div class="row response" v-if="response">
                <div class="col-xs-12">
                    <response :response="response"></response>
                </div>    
            </div>

        </div>

    </div>
</template>

<script>

import products from '../../mocks/data/products.json'
import customers from '../../mocks/data/customers.json'
import orderOne from '../../mocks/example-orders/order1.json'
import orderTwo from '../../mocks/example-orders/order2.json'
import orderThree from '../../mocks/example-orders/order3.json'

import Order from './discounts/Order.vue'
import Response from './discounts/Response.vue'

import numbers from '../../mixins/numbers.js'

    export default {

        props: ['endpoint'],

        mixins: [numbers],

        components: {
            'order': Order,
            'response': Response,
        },

        data: () => {

            return {
                selectOrder: true,

                selectedOrder: null,
                composedOrder: {
                    'customer_id': null,
                    'items': [],
                    'total': null
                },

                selectedProduct: null,
                selectedCustomer: null,

                products: products,
                customers: customers,
                orders: [],

                response: null
            }
        },

        computed: {

            hasSelectedProduct () {
                return this.selectedProduct !== null
            },

            canSubmit () {

                return  this.selectedOrder !== null && this.selectedOrder['customer_id'] !== null && this.selectedOrder['items'].length  
            },

            orderToSubmit () {
                return this.selectedOrder
            }

        },

        methods: {

            toggleOrder () {
                this.selectOrder = !this.selectOrder

                this.selectedOrder = this.selectOrder ? null : this.composedOrder

                if (this.response) {
                    this.response = null
                }
            },

            setSelectedOrder (order) {

                this.selectedOrder = order
                this.selectedCustomer = this.customers.reduce((reduced, customer) => {
                    if (customer.id == order['customer_id']) {
                        reduced = customer
                    }   

                    return reduced

                }, null)


            },

            addProductToOrder () {

                let added = false

                this.selectedOrder['items'].forEach((item) => {

                    if (item['product_id'] === this.selectedProduct.id) {

                        item.quantity++ 
                        item.total = this.round(Number(item.total) + Number(this.selectedProduct.price), 2) 
                        added = true

                    }
                })

                if (!added) {

                    this.selectedOrder['items'].push({
                        'product_id': this.selectedProduct.id,
                        'unit_price': this.selectedProduct.price,
                        'quantity': 1,
                        'total': this.selectedProduct.price
                    })
                }

                

                this.selectedOrder['total'] = this.selectedOrder['items'].reduce((reduced, item) => {

                        reduced += Number(item.total)

                        return reduced

                }, 0)

            },

            resetProducts () {
                this.selectedOrder.items = []
            },

            removeProductFromOrder () {

            },

            isSelectedOrder (id) {

                if (this.selectedOrder) {

                    return id == this.selectedOrder.id
                }

                return false

            },

            submitOrder () {

                $.ajax({
                    url: this.endpoint.uri,
                    method: this.endpoint.methods[0],
                    data: {
                        order: this.orderToSubmit
                    } 
                })
                .done((response, status) => {
                    this.response = response
                    this.response.status = status

                })
                .fail((response, status) => {

                    this.response = response
                    this.response.status = status

                })
            }

        },

        watch: {
            'selectedCustomer': function () {
                this.selectedOrder['customer_id'] = this.selectedCustomer.id
            }
        },

        mounted() {
            
             $.get('/api/orders', (response) => {
                this.orders = response 
            }).fail((response) => {
                console.log(response, 'fail')

            })


        }
    }
</script>
