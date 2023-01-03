<template>
    <div>
        <div class="row" v-if="ordersLoaded || orders.length == 0">
            <div class="col-md-12">
                <form class="card shadow-sm" name="settings-form" id="settings-form" method="post">
                    <div class="card-body" v-if="ordersLoaded">
                        Loading orders, please wait...
                    </div>
                    <div class="card-body" v-else>
                        No orders have been imported yet...
                    </div>
                </form>
            </div>
        </div>

        <div class="row" v-else v-for="(order, index) in orders">
            <div class="col-sm-12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Shopify Order ID</th>
                        <th scope="col">Order Details</th>
                        <th scope="col">Supplier Link</th>
                        <th scope="col">Supplier Last Price</th>
                        <th scope="col">Sold Price</th>
                        <th scope="col">Profit</th>
                        <th scope="col">Fulfillment Status (Shopify)</th>
                        <th scope="col">View/Fulfill in Shopify</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(lineItem, lindex) in order.line_items">
                        <td v-if="lindex == 0"><a :href="shop.url+ '/admin/orders/' + order.shopify_order_id" target="_blank">{{ order.name }}</a></td>
                        <td v-else><a v-if="lineItem.shopify_order_id != order.line_items[lindex - 1].shopify_order_id" :href="shop.url+ '/admin/orders/' + order.shopify_order_id" target="_blank">{{ order.name }}</a></td>
                        <td>
                            <div class="order_details">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="product-img">
                                            <img :src="lineItem.image" />
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="product-details">
                                            <h6>
                                                <a href="#">{{ lineItem.product_title }}</a>
                                            </h6>
                                            <div class="product-variant">
                                                <span>{{ lineItem.variant_title }}</span>
                                                <span>SKU: {{lineItem.sku}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><a :href="lineItem.source_url" target="_blank">{{ lineItem.source }}</a></td>
                        <td>{{lineItem.currency}}{{ lineItem.last_price }}</td>
                        <td>{{lineItem.currency}}{{ lineItem.sold_price }}</td>
                        <td>{{lineItem.currency}}{{ profit(lineItem.last_price, lineItem.sold_price) }}</td>
                        <td>{{order.fulfillment_status}}</td>
                        <td v-if="lindex == 0"><a :href="shop.url+ '/admin/orders/' + order.shopify_order_id" target="_blank">View/Edit order details</a></td>
                        <td v-else><a v-if="lineItem.shopify_order_id != order.line_items[lindex - 1].shopify_order_id" :href="shop.url+ '/admin/orders/' + order.shopify_order_id" target="_blank">View/Edit order details</a></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="8">
                            <div class="shipping-table-class">
                                <p>SHIPPING ADDRESS</p>
                                <div class="shipping-table-class-inner" v-if="order.client_name"><span>{{order.client_name}}</span><button @click="copyText(order.client_name)">copy</button></div>
                                <div class="shipping-table-class-inner" v-if="order.address1"><span>{{order.address1}}</span><button @click="copyText(order.address1)">copy</button></div>
                                <div class="shipping-table-class-inner" v-if="order.address2"><span>{{order.address2}}</span><button @click="copyText(order.address2)">copy</button></div>
                                <div class="shipping-table-class-inner" v-if="order.zip || order.city"><span>{{order.zip}}, {{order.city}}</span><button @click="copyText(order.zip + ', ' + order.city)">copy</button></div>
                                <div class="shipping-table-class-inner" v-if="order.country"><span>{{order.country}}</span><button @click="copyText(order.country)">copy</button></div>
                                <div class="shipping-table-class-inner" v-if="order.phone"><span>{{order.phone}}</span><button @click="copyText(order.phone)">copy</button></div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Diagnostics -->
<!--        <div class="row text-monospace">-->
<!--            <div class="col-sm-12">-->
<!--                <div class="card shadow-sm">-->
<!--                    <div class="card-header"><h4 class="mb-0">Diagnostics</h4></div>-->
<!--                    <div class="card-body">-->
<!--                        <p v-for="(d, index) in diagnostics"><strong>{{ d.name }}:</strong> {{ d.value }}</p>-->
<!--                        <p><strong>User-agent:</strong> {{ browser.userAgent }}</p>-->
<!--                        <p><strong>Platform:</strong> {{ browser.platform }}</p>-->
<!--                        <p><strong>Cookies Enabled:</strong> {{ browser.cookieEnabled }}</p>-->
<!--                        <p class="mb-0"><strong>Browser Language:</strong> {{ browser.language }}</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <modal ref="modal" :is_show_ok="is_ok"></modal>-->
        <input type="hidden" name="copytext" id="copytext" value="">
    </div>
</template>

<script>
    import Vue from 'vue';
    import VueToast from 'vue-toast-notification';
    import 'vue-toast-notification/dist/theme-sugar.css';

    Vue.use(VueToast);
    export default {
        props: {

        },
        components: {
        },
        data() {
            return {
                content: null,
                config: {},
                msgStatus: 'primary',
                msgText: 'Status: Ready to import',
                formEnabled: false,
                appSettings: {},
                browser: navigator,
                diagnostics: [],
                is_ok: false,
                ordersLoaded: false,
                orders: [],
                shop: [],
            };
        },
        computed: {
        },
        methods: {
            copyText(val){
                var element = document.getElementById('copytext');
                element.setAttribute('type', 'text');
                element.setAttribute('value', val);

                var copyText = document.getElementById('copytext');
                copyText.select();
                try {
                    document.execCommand("copy");
                    element.setAttribute('type', 'hidden');
                    let instance = Vue.$toast.open({ message: 'Copied!', type: 'default', position:
                    'bottom'});

                } catch (err) {
                    console.log(err);
                }
            },
            profit(sourcePrice, shopifyPrice) {
                let p = 0.00;
                p = parseFloat(parseFloat(shopifyPrice) - parseFloat(sourcePrice)).toFixed(2);
                return p;
            },
            getOrders() {
                this.ordersLoaded = true;
                axios.post('/orders/get-orders').then((res) => {

                    // Process errors, if any
                    if (res.data.errors && res.data.errors.length > 0)
                    {
                        console.log('Errors:\n' + res.data.errors.join("\n"));
                    }

                    // Success
                    else if (res.data.success)  {
                        this.ordersLoaded = false;
                        this.orders = res.data.data.orders;
                        this.shop = res.data.data.shop;
                    }
                });
                return true;
            },
        },
        mounted() {
            console.log('Component mounted.');
            this.getOrders();
        }
    }
</script>
