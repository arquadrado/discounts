<template>
    <div class="row endpoint">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-8">
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
                                    <select v-model="composedOrder['customer-id']" name="customers" id="customers">
                                        <option value="">Select a customer</option>
                                        <option v-for="customer in customers" :value="customer.id">{{ customer.name }}</option>
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
                                    <button @click="addProductToOrder">Add selected product</button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div v-if="!selectOrder" class="col-xs-4">
                    <h4>Composed Order</h4><br>
                    <span>customer-id: {{ composedOrder['customer-id'] }}</span><br>
                    <span>items: </span><br>
                    <ul>
                        <li v-for="item in composedOrder['items']">
                            <span>{{ `${item.description} - ${item.price}` }}</span>
                        </li>
                    </ul>    
                    <span>total: {{ composedOrder.total }}</span><br>
                </div>
            </div>

            <div class="row submit-order">
                <div class="col-xs-6">
                    <button @click="submitOrder" :disabled="!canSubmit">Submit order</button>
                </div>
                <div class="col-xs-3">
                    <span><strong>Discount: </strong>{{ discount }}</span>
                </div> 
                <div class="col-xs-3">
                    <span><strong>Total: </strong>{{ total }}</span>
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

    export default {

        props: ['endpoint'],

        data: () => {

            return {
                selectOrder: true,

                selectedOrder: null,
                composedOrder: {
                    'customer-id': null,
                    'items': [],
                    'total': null
                },

                selectedProduct: null,

                products: [],
                customers: [],
                orders: [],

                discount: 0,
                total: 0

            }
        },

        computed: {

            canSubmit () {

                if (this.selectOrder) {
                    return this.selectedOrder !== null
                }

                return this.composedOrder['customer-id'] !== null && this.composedOrder['items'].length  
            }

        },

        methods: {

            toggleOrder () {
                this.selectOrder = !this.selectOrder
            },

            setSelectedOrder (order) {

                this.selectedOrder = order

            },

            addProductToOrder () {
                this.composedOrder['items'].push(this.selectedProduct)

                this.composedOrder['total'] = this.composedOrder['items'].reduce((reduced, item) => {

                        reduced += Number(item.price)

                        return reduced

                }, 0)

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
                $.post(this.endpoint.uri, (response) => {

                    console.log(response, 'success') 

                    this.discount = response.discount
                    this.total = response.total

                }).fail((response) => {

                    console.log(response, 'fail')

                })
            }

        },

        mounted() {
            this.products = products
            this.customers = customers
            this.orders.push(orderOne)
            this.orders.push(orderTwo)
            this.orders.push(orderThree)

        }
    }
</script>
