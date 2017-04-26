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
                           <h5>Return all the orders</h5> 
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</template>

<script>



export default {
    props: ['endpoint'] 
}
</script>