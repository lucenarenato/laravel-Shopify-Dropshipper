<template>
    <div class="ad-products mt-4">

        <div  class="">
            <div class="row">
<!--                <div class="col-sm-12 col-md-12">-->
<!--                    <h5 class="card-title">Filters</h5>-->
<!--                </div>-->
                <div class="col-sm-12 12 col-lg-3">
                    <div class="form-group">
                        <label for="ad-product-collections">Search</label>
                        <input type="text" class="form-control" placeholder="Filter products" v-model="filterBySearch" v-on:keyup.enter="handleFiltersBySearch($event)">
                    </div>
                </div>
                <div class="col-sm-12 12 col-lg-3">
                    <div class="form-group">
                        <label for="ad-product-collections">Collections</label>
                        <select class="form-control" id="ad-product-collections" v-model="filterByCollection"  @change="handleFiltersByCollection()">
                            <option value="0">{{collections.length > 0 ? 'Select collection:' : 'Collection not found'}} </option>
                            <option v-for="collection in collections" :value="collection.id">{{ collection.title }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 12 col-lg-3">
                    <div class="form-group">
                        <label for="ad-product-collections">Tags</label>
                        <select class="form-control" id="ad-product-collections" v-model="filterBytag"  @change="handleFiltersByTag()">
                            <option value="0">{{tags.length > 0 ? 'Select tag:' : 'Tags not found'}}</option>
                            <option v-for="tag in tags" :value="tag">{{ tag }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 12 col-lg-3">
                    <div class="rm-filter-btn">
                        <button class="btn btn-outline-dark" type="button" :disabled="ClearFilterStatus" @click="onClearAllFilters()"> {{ClearFilterText}} </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="row" v-if="productsLoaded || products.length == 0">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body" v-if="productsLoaded">
                        Loading products, please wait...
                    </div>
                    <div class="card-body" v-else>
                        No products have been imported yet...
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
        <div class="row"  v-for="(product, index) in products">
            <div class="col-md-12">
                <form class="card shadow-sm" :name="'product-' + index + '-form'" id="'product-' + index + '-form'" method="post">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <div style="text-align: center;">
                                    <a :href="product.edit_url" data-toggle="tooltip" title="Edit Product in Shopify" target="_blank"><img :src="product.image_url" :alt="product.title + ' [thumbnail]'" class="img-fluid mb-3 product-image"></a>
                                </div>
                                <div style="text-align: center;">
                                    <a :href="product.edit_url" data-toggle="tooltip" title="Edit Product in Shopify" target="_blank">Edit in Shopify</a>
                                </div>
                            </div>
                            <div class="col-sm-10">
                                <h2 class="ad-shortened-title"><a :href="product.product_url" data-toggle="tooltip" title="View Product in Store" target="_blank">{{ product.title }}</a></h2>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <p class="mb-1" v-if="product.associates['status']" style="color:#321fdb;">Affiliate Product</p>
                                        <p class="mb-1"><span class="shipping-type-color">Shipping Type:</span> {{ product.shipping_type }}</p>
                                        <p class="mb-1">Variants: <a href="#" @click.prevent="getProductsVariants(product.id,product.shopify_id,index)">{{ product.variants_count }}</a></p>

                                        <p class="mb-1" v-if="!product.associates['status']">Shopify Price: <a href="#" @click.prevent="getProductsVariants(product.id,product.shopify_id,index)">{{ getShopifyPriceRange(index) }}</a></p>
                                        <p class="mb-1" v-if="!product.associates['status']">Cost: <a href="#" @click.prevent="getProductsVariants(product.id,product.shopify_id,index)">{{ getSourcePriceRange(index) }}</a></p>
                                        <p class="mb-1">Supplier Link: <a :href="product.source_url" target="_blank">View on {{ product.source }}</a> <i class="fas fa-external-link-alt"></i></p>
                                    </div>
                                    <div class="col-sm-4">
                                        <p  v-for="(sales, sindex, i) in product.saller_ranks" :key="sindex">
                                            <span > #{{sales.count}} in
                                                <span v-if="sales.link">
                                                    <a :href="sales.link" target="_blank"> {{ sales.name }} </a>
                                                </span>
                                                <span v-else>
                                                    {{sales.name}}
                                                </span>
                                            </span>
                                        </p>

                                        <!--                                        <p v-if="typeof product.review_status != 'undefined'">Reviews:-->
                                        <!--                                            <span v-if="product.reviews != null || typeof product.reviews.is_import != 'undefined'" >-->
                                        <!--                                                <button v-if="product.reviews.is_import" type="button" class="a" @click="updateReview(product.reviews)" data-toggle="modal" data-target="#review-modal">Yes</button>-->
                                        <!--                                                <a v-else class="a">No</a>-->
                                        <!--                                            </span>-->
                                        <!--                                            <a v-else class="a">No</a>-->
                                        <!--                                        </p>-->
                                        <!--                                        <p v-else>Reviews: <a class="a">No</a></p>-->
                                        <div class="product-review-main">
                                            <span>Reviews:</span>

                                            <button v-if="product.review_status  == 'Yes'" type="button" class="a" @click="updateReview(products[index].id)" data-toggle="modal" data-target="#review-modal">Yes
                                            </button>

                                            <div class="show-review-switch" v-if="product.review_status  == 'Yes'">
                                                <div class="custom-control custom-switch ml-3 col-md-12">
                                                    <input type="checkbox" :id="`ad-product-review-switch` + index" role="checkbox" class="custom-control-input" v-model="product.is_show_front" @change="changeShowReview(product.id, index)">
                                                    <label :for="`ad-product-review-switch` + index" class="custom-control-label">
                                                    </label>
                                                </div>
                                            </div>

                                            <a v-else class="a">{{ product.review_status }}</a>
                                        </div>
                                    </div>

                                    <div class="col-sm-4 ad-product-actions">
                                        <div class="dropdown">
                                            <button :id="'product-' + index + '-menu'" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                                                ...
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-sm-right" :aria-labelledby="'product-' + index + '-menu'">
                                                <a class="dropdown-item" type="button" :href="product.edit_url" target="_blank"><i class="fas fa-edit mr-1"></i> Edit in Shopify</a>
                                                <button class="dropdown-item" type="button" @click.prevent="editProduct(index)"><i class="fas fa-edit mr-1"></i> Edit Prices in Amazone</button>
                                                <button class="dropdown-item text-danger" type="button" @click.prevent="deleteProduct(index)"><i class="fas fa-trash-alt mr-1"></i> Delete Product</button>
                                            </div>
                                        </div>
                                        <div class="ad-product-actions-feature">
                                            <a></a>
                                            <button type="button" class="a" @click="setAutoUpdate(product.id, product.autoupdate);" data-toggle="modal" data-target="#auto-update-modal">Auto Update Settings</button>
                                            <p>Last updated: {{ product.updated_at }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div :class="'row chng_price collapse collapse' + index" aria-expanded="false">


                                <div v-if="variantsLoaded || variants.length == 0" class="col-md-12">
                                    <div class="card shadow-sm">
                                        <div class="card-body" v-if="variantsLoaded">
                                            Loading variants, please wait...
                                        </div>
                                        <div class="card-body" v-else>
                                            No variants have been imported yet...
                                        </div>
                                    </div>
                                </div>


                            <div v-else class="col-sm-12">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12"></div>
                                    <div class="input-group col-lg-6 col-md-6 col-sm-12 justify-content-end">
                                        <label class="pr-3 col-form-label">Change price for all:</label>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <input type="number" class="form-control col-lg-4 col-xl-4" :aria-label="'My product ' + index + ' price % difference'" :name="'product-' + index + '-percent-diff'" :id="'product-' + index + '-percent-diff'" value="" v-on:change="applyPercentDiffAll(index)" :disabled="product.associates['status']">
                                    </div>
                                </div>
                                <table class="table table-striped" v-if="variants && variants.length">
                                    <thead>
                                    <tr>

                                        <th scope="col">Image</th>
                                        <th scope="col" v-if="attributes['key']" v-for="(cnt, cntindex) in attributes['key']">{{cnt}}</th>
                                        <!--                                            <th scope="col" v-if="product.source == 'Amazon'">ASIN</th>-->
                                        <!--                                        <th scope="col" v-if="product.source == 'Walmart'">Product ID</th>-->
                                        <!--                                            <th v-for="(option, optionIndex) in product.options" scope="col">{{ option.value }}</th>-->
                                        <th scope="col">{{ product.source }} {{ product.locale }} Price</th>
                                        <th scope="col">
                                            <div class="form-group row mb-0">
                                                <label :for="'product-' + index + '-percent-diff'" class="col-sm-7 pb-0 col-form-label">Price Difference(%)</label>
                                            </div>
                                        </th>
                                        <th scope="col">My Price</th>
                                        <th scope="col">Profit</th>
                                        <th scope="col">Quantity</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(variant, vIndex) in variants">
                                        <td><img :src="variantImageURL(index, variant.image_id, variant.db_image)" :alt="variant.title + ' [thumbnail]'" class="img-fluid" style="max-width: 110px; max-height: 110px;"></td>
                                        <!--                                            <td>{{ variant.sku }}</td>-->
                                        <td v-for="(attr, attrindex) in attributes[variant.sku]">{{attr}}</td>
                                        <!--                                            <td v-for="(option, optionIndex) in product.options">{{ variant[option.value] }}</td>-->

                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="">{{currency}}</span>
                                                </div>
                                                <span v-if="source_prices.length > 0"> &nbsp; {{source_prices[vIndex]}} </span>
                                                <span v-else> &nbsp;{{ product.min_source_price }}</span>
                                                <!--                                                <input type="number" class="form-control" :aria-label="'Variant ' + variant.sku + ' Price'" :name="'variant-' + variant.sku + '-source-price'" :id="'variant-' + variant.sku + '-source-price'" :value="product.source_prices[vIndex]" readonly>-->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <input v-if="variant.sku" type="number" class="form-control" :aria-label="'Variant ' + variant.sku + ' price % difference'" :name="'variant-' + variant.sku + '-percent-diff'" :id="'variant-' + variant.sku + '-percent-diff'" :value="getPercentDiff(index, vIndex)" v-on:change="applyPercentDiff(index, vIndex)" :disabled="product.associates['status']">

                                                <input v-else type="number" class="form-control" :aria-label="'Variant ' + vIndex + ' price % difference'" :name="'variant-' +index + '-' + vIndex + '-percent-diff'" :id="'variant-' + index + '-' + vIndex + '-percent-diff'" :value="getPercentDiff(index, vIndex)" v-on:change="applyPercentDiff(index, vIndex)" :disabled="product.associates['status']">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{currency}}</span>
                                                </div>
                                                <input type="number" class="form-control" :aria-label="'Variant ' + variant.sku + ' Price'" :name="'variant-' + variant.sku + '-shopify-price'" :id="'variant-' + variant.sku + '-shopify-price'" v-model="variant.price" step="0.01" min="0.00" v-on:change="updatePercentDiff(index, vIndex)" :disabled="product.associates['status']">
                                            </div>
                                        </td>
                                        <td :class="{'text-danger': profit(product.min_source_price, variant.price) < 0, 'text-success': profit(product.min_source_price, variant.price) > 0}">
                                            <div class="pt-1" v-if="source_prices.length > 0">{{currency}}{{ profit(source_prices[vIndex], variant.price) }}</div>

                                            <div class="pt-1" v-else>{{currency}}{{ profit(product.min_source_price, variant.price) }}</div>
                                        </td>
                                        <td>{{variant.inventory_quantity}}
                                            <!--                                            <input type="number" class="form-control" :aria-label="'Variant ' + variant.sku + ' Quantity'" :name="variant.sku + '-quantity'" :id="'variant-' + variant.sku + '-quantity'" :value="variant.inventory_quantity" readonly>-->
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-success btn-lg mr-2" @click.prevent="saveVariants(index)"><i class="fas fa-save"></i> Update Variants</button>
                                        <button type="button" class="btn btn-secondary btn-lg" @click.prevent="editProduct(index)"><i class="fas fa-times"></i> Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <pagination :data="productsPaginationData" @pagination-change-page="getProducts"></pagination>


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
        <!--        review modal-->
        <div class="modal fade" id="review-modal" tabindex="-1" role="dialog" aria-labelledby="ad-review-title" aria-hidden="true">
            <div class="modal-dialog review-modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ad-review-title">Reviews</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ad-image-select-container" id="ad-review-modal-body">
                        <review ref="review_model"></review>
                    </div>
                </div>
            </div>
        </div>
        <!-- auto update modal -->
        <div class="modal fade" id="auto-update-modal" tabindex="-1" role="dialog" aria-labelledby="ad-image-modal-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ad-auto-update-title">Set Auto Update Setting</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ad-image-select-container" id="ad-auto-update-body">
                        <auto-update :value="autoupdate"
                                     type="manager"
                                     :err_msg="plan_err_msg"
                                      show_note="true"
                                     :note_msg="autoupdate_note_msg"
                                     :autoupdate_status_disabled="autoupdate_status_disabled"
                        >
                        </auto-update>
                    </div>
                    <div class="modal-footer d-flex">
                        <button type="button" class="btn btn-primary" style="color: #ffffff;" @click="updateAutoSetting()">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <modal ref="modal"></modal>
    </div>
</template>

<script>
import AutoUpdate from "./AutoUpdate";
import Review from "./Review";
import pagination from 'laravel-vue-pagination';

export default {
    props: {

    },
    components: {
        AutoUpdate, Review,pagination
    },
    data() {
        return {
            show_review_modal: false,
            modalReview: [],
            plan_err_msg: '',
            autoupdate: {
                status: true,
                frequency: 0, //24
                product_id: '',
                save_shopify: false,
            },
            autoupdate_note_msg : 'Note: The Auto Update function has been paused for a redesign. <br>' +
                'Only the "Update Now" option will be available',
            content: null,
            config: {},
            msgStatus: 'primary',
            msgText: 'Status: Ready to import',
            productsLoaded: false,
            variantsLoaded: false,
            products: [],
            variants : [],
            source_prices : [],
            images : [],
            showEditProduct: -1,
            browser: navigator,
            diagnostics: [],
            is_affiliate: false,
            attributes: [],
            collections :[],
            productsPaginationData:{},
            CollectionsCategory: {},
            tags : [],
            filterByCollection : "0",
            filterBySearch: '',
            filterBytag : "0",
            ClearFilterText : "No filters applied",
            ClearFilterStatus : true,
            autoupdate_status_disabled : false
        };
    },
    computed: {
        filteredProduct: function () {
                 console.log("===computed===");
        }
    },
    methods: {

        updateReview(id){
            this.show_review_modal = true;
            this.$refs.review_model.getReviews(id);

        },
        setAutoUpdate(product_id, autoupdate){
            this.getPlanValidity();
            document.body.className += ' modal-open-custom';
            document.getElementById('auto-update-modal').style.display = 'block';
            this.autoupdate.frequency = parseInt(autoupdate.frequency);
            this.autoupdate.status = autoupdate.status == true;
            this.autoupdate.product_id = product_id;
            this.autoupdate_status_disabled = false;
            this.autoupdate.save_shopify = (typeof autoupdate.save_shopify != 'undefined') ? autoupdate.save_shopify : false;

        },
        variantImageURL(productIndex, imageID, imageURL) {
            var t = 0;
            for (let i = 0; i < this.images.length; i++) {
                if (this.images[i].id == imageID) {
                    t = 1;
                    return this.images[i].src;
                }
            }
            return ( t === 0 && imageURL !== '') ? imageURL : this.products[productIndex].image_url;
        },
        editProduct(index) {
            if (this.showEditProduct > -1) {
                $('.collapse' + this.showEditProduct).collapse('hide');
            }

            $('.collapse' + index).collapse('toggle');

            this.showEditProduct = index;
        },
        saveVariants(index) {
            // Display progress
            this.$refs.modal.open('Please wait...', 'Updating variants', false, '', 100);

            let params = {
                'product_id': this.products[index].id,
                'variants': JSON.stringify(this.variants)
            };

            axios.post('/products/save-variants', params).then((res) => {

                // Process errors, if any
                if (res.data.errors && res.data.errors.length > 0)
                {
                    let errorMsg = "<p class=\"mb-0\">Could not save variants:</p><ul>\n";

                    for (let i = 0; i < res.data.errors.length; i++) {
                        errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                    }

                    errorMsg += "</ul>\n";

                    this.$refs.modal.open('Error', errorMsg, true);

                    console.log('Errors:\n' + res.data.errors.join("\n"));
                }

                // Success
                else if (res.data.success)  {
                    setTimeout(() => { this.$refs.modal.open('Success', 'Variants have been updated successfully.', true); }, 1000);

                    // Reset min/max prices for this product
                    this.products[index].min_price = this.products[index].variants[0].price;
                    this.products[index].max_price = this.products[index].variants[0].price;

                    for (let v = 0; v < this.products[index].variants.length; v++) {
                        this.products[index].min_price = Math.min(this.products[index].min_price, this.products[index].variants[v].price);
                        this.products[index].max_price = Math.max(this.products[index].max_price, this.products[index].variants[v].price);
                    }
                }

                // Unknown server error (catch all)
                else {
                    this.$refs.modal.open('Error', 'Could not update variants due to server error. Please try again or contact support for help.', true);
                    console.log(res);
                    return false;
                }
            });

            return true;
        },
        profit(sourcePrice, shopifyPrice) {
            let p = 0.00;
            p = parseFloat(parseFloat(shopifyPrice) - parseFloat(sourcePrice)).toFixed(2);
            return p;
        },
        deleteProduct(index) {

            // Display progress
            this.$refs.modal.open('Please wait...', 'Deleting product', false, '', 100);

            axios.post('/products/delete-product', { 'product_id': this.products[index].id}).then((res) => {

                // Process errors, if any
                if (res.data.errors && res.data.errors.length > 0)
                {
                    let errorMsg = "<p class=\"mb-0\">Could not delete product:</p><ul>\n";

                    for (let i = 0; i < res.data.errors.length; i++) {
                        errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                    }

                    errorMsg += "</ul>\n";

                    this.$refs.modal.open('Error', errorMsg, true);

                    console.log('Errors:\n' + res.data.errors.join("\n"));
                }

                // Success
                else if (res.data.success)  {
                    setTimeout(() => { this.$refs.modal.open('Success', 'Product has been deleted.', true); }, 1000);
                    //setTimeout(() => { this.$refs.modal.close(); }, 1000);
                    this.products.splice(index, 1);
                }

                // Unknown server error (catch all)
                else {
                    this.$refs.modal.open('Error', 'Could not delete product due to server error. Please try again or contact support for help.', true);
                    console.log('Could not delete product due to server error. Please try again or contact support for help.');
                    return false;
                }
            });

            return true;

        },
        getShopifyPriceRange(index) {
            if (this.variants.length == 1 || parseFloat(this.products[index].min_price) == parseFloat(this.products[index].max_price)) {
                return this.currency + parseFloat(this.products[index].min_price).toFixed(2);
            }

            return this.currency + parseFloat(this.products[index].min_price).toFixed(2) + ' - ' +this.currency + parseFloat(this.products[index].max_price).toFixed(2);
        },
        getSourcePriceRange(index) {
            if (this.variants.length == 1 || parseFloat(this.products[index].min_source_price) == parseFloat(this.products[index].max_source_price)) {
                return this.currency + parseFloat(this.products[index].min_source_price).toFixed(2);
            }

            return this.currency + parseFloat(this.products[index].min_source_price).toFixed(2) + ' - ' + this.currency + parseFloat(this.products[index].max_source_price).toFixed(2);
        },
        getPercentDiff(index, vIndex) {

            if (this.source_prices[vIndex] != 0) {
                let sp = parseFloat(this.source_prices[vIndex]);
                if(isNaN(sp)){
                    sp = parseFloat(this.products[index].min_source_price);
                }
              //  let tp = parseFloat(variant.price);
                let tp = parseFloat(this.variants[vIndex].price);
                return parseFloat((tp - sp) / sp * 100).toFixed(2);
            }
            return '0.00';
        },
        applyPercentDiffAll(index) {
            let el = document.getElementById('product-' + index + '-percent-diff');
            let val = el.value;

            if (!isNaN(val)) {
                for (let i = 0; i < this.variants.length; i++) {
                    el.value = parseFloat(val).toFixed(2);
                    document.getElementById('variant-' + this.variants[i].sku + '-percent-diff').value = parseFloat(val).toFixed(2);
                    this.applyPercentDiff(index, i);
                }
            }
        },
        applyPercentDiff(index, vIndex) {
            let el = (this.variants[vIndex].sku) ? document.getElementById('variant-' + this.variants[vIndex].sku + '-percent-diff') : document.getElementById('variant-' + index + '-' + vIndex + '-percent-diff');

            let p = el.value;

            if (!isNaN(p)) {
                let pVal = parseFloat(p);
                let sp = parseFloat(this.source_prices[vIndex]);

                if(isNaN(sp)){
                    sp = parseFloat(this.products[index].min_source_price);
                }

                this.variants[vIndex].price = parseFloat(sp + (sp / 100) * pVal).toFixed(2);
                el.value = parseFloat(p).toFixed(2);
            }
        },
        updatePercentDiff(index, vIndex) {
            let pField = document.getElementById('variant-' + this.variants[vIndex].sku + '-percent-diff');

            let sp = parseFloat(this.source_prices[vIndex]);
            let tp = parseFloat(this.variants[vIndex].price);

            if (sp != 0) {
                pField.value = parseFloat(((tp - sp) / sp) * 100).toFixed(2);
            }
            else {
                pField.value = '0.00';
            }
        },
        handleFiltersBySearch: function() {
            this.ClearFilterText = "Clear all filters";
            this.ClearFilterStatus = false;
            this.getProducts(1);
        },
        handleFiltersByCollection: function() {
            this.ClearFilterText = "Clear all filters";
            this.ClearFilterStatus = false;
            this.getProducts(1);
        },
        handleFiltersByTag: function() {
            this.ClearFilterText = "Clear all filters";
            this.ClearFilterStatus = false;
            this.getProducts(1);
        },
        getProducts(page=1) {

          if (typeof page === 'undefined') {
                page = 1;
            }

            this.productsLoaded = true;

            let filterBySearch = "";
            if(this.filterBySearch.length >=3) {
                filterBySearch = this.filterBySearch;
            }


            let filterData = {
                "collection": this.filterByCollection,
                "search": filterBySearch,
                "tag": this.filterBytag,
            };

            let params = {
                "filterData" : filterData
            };

            axios.post('/products/get-products?page=' + page,params).then((res) => {

                console.log("My product::Res",res);

                // Process errors, if any
                if (res.data.errors && res.data.errors.length > 0)
                {
                    console.log('Errors:\n' + res.data.errors.join("\n"));
                }

                // Success
                else if (res.data.success)  {
                    this.products = res.data.products;
                    this.productsPaginationData = res.data.productsPagination;
                    this.currency = res.data.currency;
                    this.productsLoaded = false;
                }

                // Unknown server error (catch all)
                else {
                    console.log('Could not retrieve products due to server error. Please try again or contact support for help.');
                    return false;
                }
            });

            return true;
        },
        getProductsVariants(product_id,shopify_id,index){


            if (this.showEditProduct > -1) {
                $('.collapse' + this.showEditProduct).collapse('hide');
            }

            $('.collapse' + index).collapse('toggle');

            this.showEditProduct = index;

            let params = {
                "product_id" : product_id,
                "shopify_id" : shopify_id
            };

            this.variantsLoaded = true;

            axios.post('/products/get-products-variants',params).then((res) => {

                console.log("Variants::Res",res);

                // Process errors, if any
                if (res.data.errors && res.data.errors.length > 0)
                {
                    console.log('Errors:\n' + res.data.errors.join("\n"));
                }

                // Success
                else if (res.data.success)  {
                    this.attributes = res.data.attributes;
                    this.variants = res.data.variants;
                    this.images = res.data.images;
                    this.source_prices = res.data.source_prices;
                    this.variantsLoaded = false;
                }

                // Unknown server error (catch all)
                else {
                    console.log('Could not retrieve products due to server error. Please try again or contact support for help.');
                    return false;
                }
            });



            return true;
        },
        getPlanValidity(){
            axios.get('/check-plan-validity').then((res) => {
                console.log("Plan::Res",res);
                // Process errors, if any
                if (res.data.errors && res.data.errors.length > 0)
                {
                    console.log('Errors:\n' + res.data.errors.join("\n"));
                }

                // Success
                else if (res.data.success)  {
                    this.plan_err_msg = res.data.plan.err_msg;
                }

                // Unknown server error (catch all)
                else {
                    console.log('Could not retrieve Plan due to server error. Please try again or contact support for help.');
                    return false;
                }
            });
            return true;
        },
        getCollections() {
            axios.post('/get-collections-category').then((res) => {

               if(res.data.success)  {
                    this.collections = res.data.collections;
                }
                // Unknown server error (catch all)
                else {
                    console.log('Could not retrieve custom collections due to server error. Please try again or contact support for help.');
                    return false;
                }
            });

            return true;
        },
        getTags() {
            axios.post('/get-shopify-tags').then((res) => {

                if(res.data.success)  {
                    this.tags = res.data.tags;

                    console.log("TAGS",this.tags);
                }
                // Unknown server error (catch all)
                else {
                    console.log('Could not retrieve custom collections due to server error. Please try again or contact support for help.');
                    return false;
                }
            });

            return true;
        },
        updateAutoSetting(){
            let base = this;
            let params = {
                'auto_update': this.autoupdate,
            };

            $('#auto-update-modal').modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();

            if(this.autoupdate.frequency == 0){
                this.$refs.modal.open('Please wait...', 'Updating product price', false, '', 100);
            }

            axios.post('/products/update-autosetting', params).then((res) => {
                // Process errors, if any
                if (res.data.errors && res.data.errors.length > 0)
                {
                    console.log('Errors:\n' + res.data.errors.join("\n"));
                }
                // Success
                else if (res.data.success)  {
                    location.reload();
                    // if(base.autoupdate.frequency == 0){
                    //     location.reload();
                    // }else{
                    //     $('#ad-modal').modal('hide');
                    //     this.productsLoaded = true;
                    //     document.body.classList.remove('modal-open-custom');
                    //     base.getProducts();
                    // }
                }

                // Unknown server error (catch all)
                else {
                    console.log(res);
                    return false;
                }
            });
            return true;
        },
        changeShowReview(id, index){
            let base = this;
            let params = {
                'id': id,
                'is_show_front': this.products[index].is_show_front
            };

            axios.post('/change-show-review', params).then((res) => {
                // Process errors, if any
                if (res.data.errors && res.data.errors.length > 0)
                {
                    console.log('Errors:\n' + res.data.errors.join("\n"));
                }
                // Success
                else if (res.data.success)  {
                    // location.reload();
                }

                // Unknown server error (catch all)
                else {
                    console.log(res);
                    return false;
                }
            });
            // return true;
        },
        onClearAllFilters: function() {

            this.filterByCollection = "0";
            this.filterBySearch = '';
            this.filterBytag = '0';

            this.ClearFilterText = "No filters applied";

            this.ClearFilterStatus = true;
            this.getProducts(1);

        },
    },
    mounted() {
        console.log('Component mounted.');
        this.getCollections();
        this.getTags();
        this.getProducts(1);
        $('#auto-update-modal').on('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open-custom');
        })
    }
}
</script>
