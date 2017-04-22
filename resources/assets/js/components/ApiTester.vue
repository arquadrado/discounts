<template>
    <div class="container">
        <div class="row api-tester">
            <div class="col-xs-12">
                <h1>API Tester</h1>
            </div>
        </div>
        <div class="row api-tester-description">
            <div class="col-xs-12">
                <h3>This is a simple page that allows to test the differente api routes</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
               
            </div>
        </div>
    </div>
</template>

<script>

import products from '../mocks/data/products.json'
import customers from '../mocks/data/customers.json'
import orderOne from '../mocks/example-orders/order1.json'
import orderTwo from '../mocks/example-orders/order2.json'
import orderThree from '../mocks/example-orders/order3.json'

    export default {
        data: () => {
            return {
                products: [],
                customers: [],
                orders: [],

            }
        },
        mounted() {
            this.products = products
            this.customers = customers
            this.orders.push(orderOne)
            this.orders.push(orderTwo)
            this.orders.push(orderThree)
           /* $.post('/api/order', (response) => {
                console.log(response, 'success')
            }).fail((response) => {
                console.log(response, 'fail')

            })*/
        }
    }
</script>
