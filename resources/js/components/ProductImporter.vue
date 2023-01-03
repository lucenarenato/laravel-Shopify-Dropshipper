<template>
    <div>
        <div class="row justify-content-center import-page">
            <div class="col-md-9">
                <form class="mb-3" @submit.prevent="getProduct" name="ad-import-form" id="ad-import-form" method="post">
                    <div class="input-group mb-3 shadow-sm">
                        <div class="input-group-prepend">
                            <button class="btn btn-secondary" type="button" id="ad-paste-btn" data-toggle="tooltip" data-placement="bottom" title="Paste from Clipboard" :disabled="!importEnabled" @click.prevent="resetImportStatus"><i class="fas fa-paste fa-lg"></i><span class="sr-only">Paste</span></button>
                        </div>
                        <input name="ad-product-url" id="ad-product-url" v-model="productURL" type="url" :class="'form-control form-control-lg ' + urlValidClass"
                               :placeholder="page_contents.ad_product_url_placeholder ? page_contents.ad_product_url_placeholder : 'e.g., https://www.amazon.com/dp/B07B7QHGR6/'" aria-label="e.g., https://www.amazon.com/dp/B07B7QHGR6/"
                               aria-describedby="ad-get-product-btn" :readonly="!importEnabled" maxlength="2000" required @keyup="resetImportStatus">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-lg btn-amazon" type="submit" id="ad-get-product-btn" :disabled="!importEnabled">
                                <span v-if="importInProgress"><span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> </span>{{page_contents.ad_product_btn_title ? page_contents.ad_product_btn_title : 'Get Product'}}</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="custom-control custom-switch mb-3 text-left">
                                <input type="checkbox" class="custom-control-input" id="ad-amazon-affiliate" role="checkbox" :value="amazonAffiliate" v-model="amazonAffiliate" :checked="amazonAffiliate" @change="checkStore()"
                                :disabled="productInfo['source'] == 'Walmart'">
                                <label class="custom-control-label" for="ad-amazon-affiliate">
                                    {{page_contents.ad_amazon_affiliate_label_text ? page_contents.ad_amazon_affiliate_label_text : 'Import for Amazon Affiliate Program'}}
                                    </label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div id="ReportAnErrorForm">
                                <report-an-error-form :shop_data="shop_data" :page_contents="page_contents"></report-an-error-form>
                            </div>
                        </div>
                    </div>
                </form>
                <div :class="'text-' + importStatus + ' mb-3 importMsg'" v-html="importMsg"></div>
            </div>
        </div>
        <div class="row" v-if="productInfo.title">
            <div class="col-md-12">
                <form class="card shadow-sm" name="add-product-form" id="add-product-form" method="post">
                    <div class="card-header">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="ad-product-info-tab" data-toggle="tab" href="#ad-product-info-pan" role="tab" aria-controls="ad-product-info-pan" aria-selected="true">Product Detail</a>

                                <a v-if="productInfo['source'] == 'Amazon'" class="nav-item nav-link" id="ad-product-stats-tab" data-toggle="tab" href="#ad-product-stats-pan" role="tab" aria-controls="ad-product-stats-pan" aria-selected="false">Product Stats</a>

                                <a class="nav-item nav-link" id="ad-product-description-tab" data-toggle="tab" href="#ad-product-description-pan" role="tab" aria-controls="ad-product-description-pan" aria-selected="false">Description</a>
                                <a :class="{ 'nav-item': true, 'nav-link': true, 'disabled': !productInfo.images || !productInfo.images.length}" id="ad-product-images-tab" data-toggle="tab" href="#ad-product-images-pan" role="tab" aria-controls="ad-product-images-pan" aria-selected="false">Images <span v-if="productInfo.images && productInfo.images.length" class="badge badge-pill badge-secondary">{{productInfo.unique_images.length }}</span></a>
                                <a v-if="productInfo.variants && productInfo.variants.length" :class="{ 'nav-item': true, 'nav-link': true, 'disabled': !productInfo.variants || !productInfo.variants.length}" id="ad-product-variants-tab" data-toggle="tab" href="#ad-product-variants-pan" role="tab" aria-controls="ad-product-variants-pan" aria-selected="false">Variants <span v-if="productInfo.variants && productInfo.variants.length" class="badge badge-pill badge-secondary">{{ productInfo.variants.length }}</span></a>
                                <a class="nav-item nav-link" id="ad-product-affiliate-tab" data-toggle="tab" href="#ad-product-affiliate-pan" role="tab" aria-controls="ad-product-affiliate-pan" aria-selected="false" v-if="productInfo['source'] == 'Amazon' && amazonAffiliate">Affiliate Details</a>

                                <a v-if="productInfo['source'] == 'Amazon'" class="nav-item nav-link" id="ad-auto-update-tab" data-toggle="tab" href="#ad-auto-update-pan" role="tab" aria-controls="ad-auto-update-pan" aria-selected="false">Auto Update Setting</a>

                                <a class="nav-item nav-link" id="ad-import-product-tab" data-toggle="tab" href="#ad-import-product-pan" role="tab" aria-controls="ad-import-product-pan" aria-selected="false">Reviews <span class="text-feature">(new)</span></a>
                            </div>
                        </nav>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="ad-product-info-pan" role="tabpanel" aria-labelledby="ad-product-info-tab">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div v-if="productInfo.images && productInfo.images.length">
                                            <a href="#" @click.prevent="mainImageModal(-1)"><img :src="productInfo.images[productInfo.main_image].src" :alt="productInfo.title + ' [thumbnail]'" class="img-fluid mb-3"></a>
                                            <p class="text-center"><a href="#" @click.prevent="mainImageModal(-1)" class="btn"><i class="far fa-image"></i> Change main image</span></a></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="form-group">
                                            <label for="ad-product-title">Title</label>
                                            <input type="text" class="form-control" id="ad-product-title" v-model="productInfo.title" required>
                                        </div>

                                        <div class="row">

                                        <div class="col-sm-12 12 col-lg-3">
                                        <div class="form-group" v-if="collections && collections.length">
                                            <label for="ad-product-collections">Collections</label>
                                            <select class="form-control" id="ad-product-collections" v-model="productInfo.collection">
                                                <option value="0">Select collection:</option>
                                                <option v-for="collection in collections" :value="collection.id">{{ collection.title }}</option>
                                            </select>
                                        </div>
                                        </div>
                                       <div class="col-sm-12 col-md-12 col-lg-3">
                                        <div class="form-group">
                                            <label for="ad-product-tags">Tags</label>
                                            <input type="text" class="form-control" id="ad-product-tags" v-model="productInfo.tags" placeholder="Comma separated tags">
                                        </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-3">
                                        <div class="form-group">
                                            <label for="ad-product-type">Product Type</label>
                                            <input type="text" class="form-control" id="ad-product-type" v-model="productInfo.type">
                                        </div>
                                        </div>
                                            <div class="col-sm-12 col-md-12 col-lg-3">
                                                <div class="form-group">
                                                    <label for="ad-product-type" title="This set the product either as Active in your live store after imported or as draft (not visible in live store) ">Import As(Status)</label>
                                                    <select title="This set the product either as Active in your live store after imported or as draft (not visible in live store) " class="form-control" id="ad-product-collections" v-model="productInfo.status">
                                                        <option value="active" selected>Active</option>
                                                        <option value="draft" selected>Draft</option>                             </select>
                                                </div>
                                            </div>
                                     </div>
                                        <div class="row" v-if="productInfo['source'] == 'Walmart'">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ad-shipping-type">Shipping Type</label>
                                                    <input type="text" class="form-control" aria-label="Shipping Type" name="ad-shipping-type" id="ad-shipping-type" v-model="productInfo.shipping_type" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="ad-shipping-price">Shipping Price</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">{{productInfo.currency_symbol}}</span>
                                                        </div>
                                                        <input type="number" class="form-control" aria-label="Shipping Price" name="ad-shipping-price" id="ad-shipping-price" :value="parseFloat(productInfo.shipping_price).toFixed(2)" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" v-if="productInfo.variants.length === 0">
                                            <div class="col-sm-12 col-md-12 col-lg-3">
                                                <label for="ad-source-price">{{ productInfo['source'] }} Price (with shipping)</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{productInfo.currency_symbol}}</span>
                                                    </div>
                                                    <input type="number" class="form-control" :aria-label="productInfo['source'] + ' Price'" name="ad-source-price" id="ad-source-price" :value="productInfo.source_price" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-3" v-if="productInfo.variants.length === 0">
                                                <label for="ad-shopify-price">My Price</label>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{productInfo.currency_symbol}}</span>
                                                    </div>
                                                    <input type="number" class="form-control" aria-label="My Price" name="ad-shopify-price" id="ad-shopify-price" v-model="productInfo.shopify_price" step="0.01" min="0.00" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-3" v-if="productInfo['source'] != 'Amazon' || !amazonAffiliate">
                                                Profit
                                                <div class="input-group mb-3 pt-2" :class="{'text-danger': profit(-1) < 0, 'text-success': profit(-1) > 0}">{{productInfo.currency_symbol}}{{ profit(-1) }}</div>
                                            </div>
                                        </div>
                                        <!--                                        <div class="row" v-if="productInfo['source'] != 'Amazon' || !amazonAffiliate">-->
                                        <div class="row">
                                            <div class="col-sm-6"  v-if="productInfo.variants.length === 0">
                                                <div class="form-group">
                                                    <label for="ad-product-inventory">Inventory</label>
                                                    <input type="number" class="form-control" id="ad-product-inventory" min="0" v-model="productInfo.inventory" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-12" v-if="productInfo['source'] == 'Amazon'">
                                                <div class="custom-control custom-switch pt-3">
                                                    <input type="checkbox" class="custom-control-input" id="ad-prime-eligible" v-model="productInfo.prime_eligible">
                                                    <label class="custom-control-label" for="ad-prime-eligible">Prime Eligible</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div  v-if="productInfo['source'] == 'Amazon'" class="tab-pane fade" id="ad-product-stats-pan" role="tabpanel" aria-labelledby="ad-product-stats-tab">
                                <div class="row">
                                    <div class="col-sm-12" >
                                        <div style="margin-bottom: 10px;">
                                        <h2 class="font-weight-bold" style="color: #2d2f69; margin-bottom: 0px;">Product Stats</h2>
                                        <p style="color: #800080; margin-bottom: 0px;">At this moment stats are available only for Amazon products.</p>
                                        </div>

                                        <div  style="margin-bottom: 10px;">
                                        <p style="margin-bottom: 0px; font-size: 16px;"> <b>Estimated Monthly Sales*: </b>
                                            <span v-if="productInfo.monthly_sales_estimate" style="color: #800080;" > {{ productInfo.monthly_sales_estimate }} </span>
                                            <span v-else style="color:red">not available</span>
                                        </p>

                                        <p style="color: #800080; margin-bottom: 0px;">*These are projections based on available data and algorithms.</p>
                                        </div>

                                        <div class="row-inline"  style="margin-bottom: 10px;">
                                        <p style="display:inline-block; font-size: 16px; margin-bottom: 0px;"><b>Best Seller Rank: </b></p>
                                        <ul id="seller_rank">
                                            <li style="color: #800080;" v-for="(item,index) in productInfo['saller_ranks']">
                                                #{{ item.count }} in <a :href="item.link" target="_blank" style="color: #800080;"> {{ item.name }} </a>
                                                  <span v-if="index < (productInfo['saller_ranks'].length - 1)">&nbsp; &gt;</span>
                                            </li>
                                        </ul>
                                        </div>
                                        <div  style="margin-bottom: 10px;  font-size: 16px; ">
                                        <p><b> Reviews: </b> <span style="color: #800080;"> {{productInfo.reviews_summary ? productInfo.reviews_summary.reviews_total : 0}} Reviews - ({{productInfo.reviews_summary ? productInfo.reviews_summary.rating : 0}} / 5) </span> </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-8 col-lg-9" >
                                        <div class="amazon_history_graph_section" v-if="productInfo['source'] == 'Amazon'">
                                            <h5 class="font-weight-bold">Historical Product Price</h5>
                                            <div v-if="productInfo['history_is_graph']">
                                                <amazon-price-history :displayData="productInfo.history.amazon_price_history.price" :currency_symbol="productInfo.from_currency_symbol" :label="productInfo.history.amazon_price_history.date"></amazon-price-history>
                                            </div>
                                            <div v-else>
                                                <p>Historical data is not available for this product</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="ad-product-description-pan" role="tabpanel" aria-labelledby="ad-product-description-tab">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <trumbowyg v-model="productInfo.description" :config="config" class="form-control" name="productInfo.description"></trumbowyg>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade ad-scrollable ad-image-select-container" id="ad-product-images-pan" role="tabpanel" aria-labelledby="ad-product-images-tab">
                                <ul class="list-unstyled mb-0">
                                    <li v-for="(image, index) in productInfo.unique_images"><a href="#" @click.prevent="selectProductImage(index)" :class="{'ad-selected': image.selected}"><i class="fas fa-check fa-lg"></i><img :src="image.src" :alt="productInfo.title + ' [image ' + Number(index + 1) + ']'"></a></li>
                                </ul>
                                <!--                                <ul class="list-unstyled mb-0">-->
                                <!--                                    <li v-for="(image, index) in productInfo.images"><a href="#" @click.prevent="selectProductImage(index)" :class="{'ad-selected': image.selected}"><i class="fas fa-check fa-lg"></i><img :src="image.src" :alt="productInfo.title + ' [image ' + Number(index + 1) + ']'"></a></li>-->
                                <!--                                </ul>-->
                            </div>
                            <div class="tab-pane fade ad-scrollable" id="ad-product-variants-pan" role="tabpanel" aria-labelledby="ad-product-variants-tab" v-if="productInfo.variants && productInfo.variants.length">
                                <table class="table table-striped" v-if="productInfo.variants && productInfo.variants.length">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="text-center">Select</th>
                                        <!--                                            <th scope="col" v-if="productInfo['source'] == 'Amazon'">ASIN</th>-->
                                        <!--                                            <th scope="col" v-if="productInfo['source'] == 'Walmart'">Product ID</th>-->
                                        <th v-for="(attr, attrIndex) in productInfo.variants[0].attributes" scope="col">{{ attr.dimension }}</th>
                                        <th scope="col">{{ productInfo['source'] }} {{ productInfo['locale'] }} Price</th>
                                        <th scope="col">
                                            <div class="form-group row mb-0">
                                                <label for="product-percent-diff" class="col-sm-12 pb-0 col-form-label">Price % Difference</label>
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        <input type="number" class="form-control" aria-label="My product price % difference" name="product-percent-diff" id="product-percent-diff" value="" v-on:change="applyPercentDiffAll()" :disabled="amazonAffiliate">
                                                    </div>
                                                </div>
                                            </div>
                                        </th>
                                        <th scope="col">My Price</th>
                                        <th scope="col">Profit</th>
                                        <th scope="col">Quantity</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(variant, index) in productInfo.variants">
                                        <td>
                                            <div class="form-group form-check text-center">
                                                <input type="checkbox" class="form-check-input" :id="variant.source_id + '-check'" checked="checked" aria-checked="checked" v-model="variant.checked">
                                                <label class="form-check-label sr-only" :for="variant.source_id + '-check'">Variant {{ index }}</label>
                                                <a href="#" @click.prevent="mainImageModal(index)"><img :src="productInfo.images[variant.main_image].src" :alt="variant.title + ' [thumbnail]'" class="img-fluid" style="max-width: 60px; height: auto;"></a><br>
                                                <a href="#" @click.prevent="mainImageModal(index)" class="btn">Change<span class="sr-only"> variant image</span></a>
                                            </div>
                                        </td>
                                        <!--                                            <td>{{ variant.source_id }}</td>-->
                                        <td v-for="(attrTD, indexTD) in productInfo.variants[0].attributes">
                                            {{variant.attributes[indexTD].value }}
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="">{{productInfo.currency_symbol}}</span>
                                                </div>
                                                <span> &nbsp; {{variant.source_price}}</span>
                                                <!--                                                <input type="number" class="form-control" :aria-label="'Variant ' + variant.source_id + ' Price'" :name="'variant-' + variant.source_id + '-source-price'" :id="'variant-' + variant.source_id + '-source-price'" :value="variant.source_price" readonly>-->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <input type="number"   class="form-control" :aria-label="'Variant ' + variant.source_id + ' price % difference'" :name="'variant-' + variant.source_id + '-percent-diff'" :id="'variant-' + variant.source_id + '-percent-diff'" :value="getPercentDiff(index)" :disabled="amazonAffiliate" v-on:change="applyPercentDiff(index)">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group" style="width: 12rem;">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{productInfo.currency_symbol}}</span>
                                                </div>
                                                <input type="number"  class="form-control" :aria-label="'Variant ' + variant.source_id + ' Price'" :name="'variant-' + variant.source_id + '-shopify-price'" :id="'variant-' + variant.source_id + '-shopify-price'" v-model="variant.shopify_price" step="0.01" min="0.00" v-on:change="updatePercentDiff(index)" :disabled="amazonAffiliate">
                                            </div>
                                        </td>
                                        <td :class="{'text-danger': profit(index) < 0, 'text-success': profit(index) > 0}" ><div class="pt-1">{{productInfo.currency_symbol}}{{ profit(index) }}</div></td>
                                        <td>
                                            <input type="number" class="form-control" :aria-label="'Variant ' + variant.source_id + ' Quantity'" :name="variant.source_id + '-quantity'" :id="'variant-' + variant.source_id + '-quantity'" v-model="variant.inventory" :disabled="amazonAffiliate">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="ad-product-affiliate-pan" role="tabpanel" aria-labelledby="ad-product-affiliate-tab" v-if="productInfo['source'] == 'Amazon' && amazonAffiliate">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h3 class="mb-3">Purchase Button Style</h3>
                                        <div class="row mb-3">
                                            <div class="col-1 my-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="ad-aa-button-opt" id="ad-aa-button-opt1" value="0" v-model="amazonAffiliateOpt" :checked="(parseInt(amazonAffiliateOpt) === 0)">
                                                    <label class="form-check-label sr-only" for="ad-aa-button-opt1">Text Link</label>
                                                </div>
                                            </div>
                                            <div class="col-11 pt-4 form-inline">
                                                <label class="mr-2" for="ad-aa-button-label">Text Link:</label>
                                                <input type="text" class="form-control" id="ad-aa-button-label" v-model="amazonAffiliateText" maxlength="100">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-1 my-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="ad-aa-button-opt" id="ad-aa-button-opt2" value="1" v-model="amazonAffiliateOpt" :checked="(parseInt(amazonAffiliateOpt) === 1)" @change="amazonAffiliateText='/images/amazon_buy_btn1.png'">
                                                    <label class="form-check-label sr-only" for="ad-aa-button-opt2">Amazon Affiliate Button Style 1</label>
                                                </div>
                                            </div>
                                            <div class="col-11 pt-4">
                                                <img src="/images/amazon_buy_btn1.png" alt="Amazon Affiliate Button Style 1" class="img-fluid" style="max-width: 200px">
                                            </div>
                                        </div>
                                        <div class="row mb-5">
                                            <div class="col-1 my-auto">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="ad-aa-button-opt" id="ad-aa-button-opt3" value="2" v-model="amazonAffiliateOpt" :checked="(parseInt(amazonAffiliateOpt) === 2)" @change="amazonAffiliateText='/images/amazon_buy_btn2.png'">
                                                    <label class="form-check-label sr-only" for="ad-aa-button-opt3">Amazon Affiliate Button Style 2</label>
                                                </div>
                                            </div>
                                            <div class="col-11 pt-4">
                                                <img src="/images/amazon_buy_btn2.png" alt="Amazon Affiliate Button Style 2" class="img-fluid" style="max-width: 200px">
                                            </div>
                                        </div>
                                        <!--                                        <h3 class="mb-4">More Options</h3>-->
                                        <!--                                        <div class="custom-control custom-switch mb-3">-->
                                        <!--                                            <input type="checkbox" class="custom-control-input" id="ad-aa-display-prices-chk">-->
                                        <!--                                            <label class="custom-control-label" for="ad-aa-display-prices-chk">Display Product Prices</label>-->
                                        <!--                                        </div>-->
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="alert alert-info" role="alert"><strong>NOTE:</strong> The Amazon Associates feature is available for the following Shopify Templates or themes: Debut, Simple, Pop, Boundless, Venture, Jumpstart, Supply, Narrative, Brooklyn and Minimal.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="ad-auto-update-pan" role="tabpanel" aria-labelledby="ad-auto-update-tab">
                                <auto-update :value="autoupdate"
                                             type="import"
                                             err_msg=""
                                             show_note="true"
                                             :note_msg="autoupdate_note_msg"
                                             :autoupdate_status_disabled="autoupdate_status_disabled"
                                ></auto-update>
                            </div>
                            <div class="tab-pane fade" id="ad-import-product-pan" role="tabpanel" aria-labelledby="ad-import-product-tab">
                                <div class="import-product-main">
                                    <div class="import-product-title">
                                        <h2>Import Product Reviews</h2>
                                    </div>
                                    <div class="import-product-switch">
                                        <div class="custom-control custom-switch mb-3 col-md-12">
                                            <input type="checkbox" id="ad-product-switch" role="checkbox" class="custom-control-input" v-model="review.is_import" :disabled="is_review_disabled">
                                            <label for="ad-product-switch" class="custom-control-label"> <span v-if="review.is_import">On</span><span v-else>Off</span>
                                            </label>
                                        </div>
                                    </div>
                                    <p class="review-inst a">For Instructions on how to set up the reviews in your Products page, go to 'Settings' > 'Reviews' tab.</p>
                                    <div class="">
                                        <h5>Select option from criteria below.</h5>
                                        <div class="import-product-input-group">
                                            <div class="">
                                                <input type="radio" id="ad-all-review" role="checkbox" name="import-product" class="import-product-input-type" value="0" v-model="review.import_type">
                                                <label for="ad-all-review" class="import-product-label">Import: All Reviews &nbsp;&nbsp;(100 max.)
                                                </label>
                                            </div>
                                            <div class="">
                                                <input type="radio" id="ad-review-image" role="checkbox" name="import-product" class="import-product-input-type" value="1" v-model="review.import_type">
                                                <label for="ad-review-image" class="import-product-label">Import only Reviews with Images() &nbsp;&nbsp;(100 max.)
                                                </label>
                                            </div>
                                            <div class="">
                                                <input type="radio" id="ad-4-5-star" role="checkbox" name="import-product" class="import-product-input-type" value="2" v-model="review.import_type">
                                                <label for="ad-4-5-star" class="import-product-label">Import only 4 and 5 star&nbsp;&nbsp;(100 max.)
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-success btn-lg mr-2" @click.prevent="addProduct()"><i class="fas fa-plus"></i> Add To My Store</button>
                        <button type="button" class="btn btn-secondary btn-lg" @click.prevent="clearProduct()"><i class="fas fa-times"></i> Clear</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="!productInfo.title">

        <product-services :page_contents="page_contents"></product-services>


        <!--       START:: search top sold products in amazon-->

        <product-search :categories="bestsellerCategory" :page_contents="page_contents"></product-search>

        <!--       END:: search top sold products in amazon-->

        </div>

        <!-- Set Main Image Modal -->
        <div class="modal fade" id="ad-image-modal" tabindex="-1" role="dialog" aria-labelledby="ad-image-modal-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ad-image-modal-title">Set main image</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ad-scrollable ad-image-select-container" id="ad-image-modal-body">
                        <ul class="list-unstyled mb-0">
                            <li v-for="(image, index) in productInfo.images" v-if="mainImageTarget < 0 || mainImageTarget === image.variant"><a href="#" @click.prevent="setMainImage(index)" :class="{'ad-selected': mainImageTarget < 0 && productInfo.main_image === index || mainImageTarget > -1 && productInfo.variants[mainImageTarget].main_image === index}"><i class="far fa-image fa-lg"></i><img :src="image.src" :alt="mainImageTarget.title + ' [image ' + Number(index + 1) + ']'"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <!-- Diagnostics -->
        <!--        <div class="row text-monospace">-->
        <!--            <div class="col-sm-12">-->
        <!--                <div class="card shadow-sm">-->
        <!--                    <div class="card-header"><h4 class="mb-0">Diagnostics</h4></div>-->
        <!--                    <div class="card-body">-->
        <!--                        <p v-if="this.lastProcessedURL"><strong>Request URL:</strong> {{ this.lastProcessedURL }}</p>-->
        <!--                        <p v-for="(d, index) in diagnostics"><strong>{{ d.name }}:</strong> {{ d.value }}</p>-->
        <!--                        <p><strong>User-agent:</strong> {{ browser.userAgent }}</p>-->
        <!--                        <p><strong>Platform:</strong> {{ browser.platform }}</p>-->
        <!--                        <p><strong>Cookies Enabled:</strong> {{ browser.cookieEnabled }}</p>-->
        <!--                        <p class="mb-0"><strong>Browser Language:</strong> {{ browser.language }}</p>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->

<!--        <amazon-price-history :displayData="series" :label="xaxis"></amazon-price-history>-->

        <modal ref="modal" :is_show_ok="is_ok" :is_job_msg="is_job_msg" :job_msg="job_msg" :is_app_review="is_app_review" :is_poll_script="is_poll_script"  @update="showAppReview"></modal>

        <!--        AppReviewModal-->
        <app-review-modal  ref="app_review" :is_clicked="is_clicked"></app-review-modal>
        <!--        plan upgrade modal-->
        <plan-upgrade-modal ref="plan_upgrade"></plan-upgrade-modal>
    </div>
</template>

<script>
import Trumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/ui/trumbowyg.css';
import AutoUpdate from "./AutoUpdate";
import ProductSearch from "./ProductSearch";
import AppReviewModal from "./AppReviewModal";
import AmazonPriceHistory from "./Charts/AmazonPriceHistory";
import ReportAnErrorForm from "./ReportAnErrorForm";
import ProductServices from "./ProductServices";

export default {
    props: ['page_contents'],
    components: {
        Trumbowyg, AutoUpdate,ProductSearch, AppReviewModal, AmazonPriceHistory,ReportAnErrorForm, ProductServices
    },
    data() {
        return {
            is_app_review: false,
            is_clicked: false,
            is_ok: false,
            is_job_msg: false,
            job_msg: 'image',
            content: null,
            config: {},
            urlValidClass: '',
            importStatus: 'primary',
            importMsg: '',
            importEnabled: true,
            importInProgress: false,
            productInfo: {},
            customCollections: {},
            mainImageTarget: -1,
            amazonAffiliate: false,
            is_Affiliate_disabled: false,
            amazonAffiliateText: 'Buy from Amazon',
            amazonAffiliateOpt: 0,
            productURL: '',
            lastProcessedURL: '',
            browser: navigator,
            diagnostics: [],
            associates: [],
            source: '',
            planMsgs: '',
            currency: '',
            currency_symbol: '',
            autoupdate: {
                status: false,
                frequency: 24,
                save_shopify: false
            },
            bestsellerCategory: [],
            review:{
                is_import: true,
                import_type: 0,
                asin: '',
                domain: '',
                status: 'No',
                reviews: [],
                is_show_front: false
            },
            is_review_disabled: false,
            series:[30, 40, 35, 50, 49, 60, 70, 91, 125],
            xaxis: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999],
            shop_data : [],
            autoupdate_note_msg : 'Note: The Auto Update function has been paused for a redesign. We will provide updates through main page banner. <br>' +
                'Only the "Update Now" option will be available from the MY PRODUCTS > Auto Update Settings page.',
            autoupdate_status_disabled : true,
            is_poll_script : false


        };
    },
    computed: {
    },
    methods: {
        showAppReview(){
            this.$refs.app_review.open();
        },
        getCollections() {


            axios.post('/get-collections').then((res) => {

                // Process errors, if any
                if (res.data.errors && res.data.errors.length > 0)
                {
                    console.log('Errors:\n' + res.data.errors.join("\n"));
                }

                // Success
                else if (res.data.success)  {
                    this.collections = res.data.collections;
                    this.shop_data = res.data.shope_data;

                    console.log("shop-data",this.shop_data);

                    this.associates = res.data.associates;
                    this.planMsgs = res.data.plan;
                    this.bestsellerCategory = res.data.bestseller_category;

                    if( this.planMsgs.current_plan_id == 1 ){
                        if( this.planMsgs.regular_pr_err_msg !== '' && this.planMsgs.affiliate_pr_err_msg !== ''){
                            this.$refs.plan_upgrade.open(this.planMsgs.total_products, this.planMsgs.total_affiliate_products, this.planMsgs.current_plan_name,this.planMsgs.current_plan_id);
                        }
                    }else{
                        if( this.planMsgs.regular_pr_err_msg !== '' && !this.amazonAffiliate){
                            this.$refs.plan_upgrade.open(this.planMsgs.total_products, this.planMsgs.total_affiliate_products, this.planMsgs.current_plan_name,this.planMsgs.current_plan_id);
                        }
                    }
                }

                // Unknown server error (catch all)
                else {
                    console.log('Could not retrieve custom collections due to server error. Please try again or contact support for help.');
                    return false;
                }
            });

            return true;
        },
        addProduct() {
            try{
                if (!this.checkStore()) {
                    return false;
                }
                this.is_poll_script = false;

                // Display progress
                this.$refs.modal.open('Please wait...', 'Adding product', false, '', 100);
                let parser = document.createElement('a');
                parser.href = this.productURL;

                // add affiliate data and button
                let affiliate = {
                    'country': parser.hostname.replace(/^(www\.)?(amazon|amz)\.(com\.|co\.)?/i, '').toUpperCase(),
                    'status':  this.amazonAffiliate,
                    'button_style':  this.amazonAffiliateOpt,
                    'button_text':  this.amazonAffiliateText,
                };
                let params = {
                    'product_info': JSON.stringify(this.productInfo),
                    'affiliate': JSON.stringify(affiliate),
                    'autoupdate': JSON.stringify(this.autoupdate),
                    'review': JSON.stringify(this.review)
                };

                console.log("params",this.productInfo);

                axios.post('/add-product', params).then((res) => {
                    // Process errors, if any
                    if (res.data.errors && res.data.errors.length > 0)
                    {
                        let errorMsg = "<p class=\"mb-0\">Could not add product:</p><ul>\n";

                        for (let i = 0; i < res.data.errors.length; i++) {
                            errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                        }

                        errorMsg += "</ul>\n";

                        this.$refs.modal.open('Error', errorMsg, true);

                        console.log('Errors:\n' + res.data.errors.join("\n"));
                    }


                    // Success
                    else if (res.data.success)  {
                        // add button in model

                        let urls = res.data.data;
                        this.is_job_msg = urls['isjobmsg'];
                        this.job_msg = urls['jobmsg'];
                        this.is_app_review = urls['is_show_review_popup'];
                        this.is_clicked = urls['is_clicked'];
                        this.is_poll_script = false;
                        let a  = '<div class="d-flex"><a class="btn model-btn" href="'+ urls['view'] +'" target="_blank">View<br> Product</a><a class="btn model-btn" href="'+ urls['edit'] +'" target="_blank" style="margin: 0px 10px 0px 10px;">Edit Product<br><span style="color:#7373b4;"> (shopify)</span></a><a class="btn model-btn" href="'+ urls['my_product'] +'">Go to<br> My Products</a></div>';


                        setTimeout(() => { this.$refs.modal.open('Product has been added succesfully', a, true); }, 1000);


                        // setTimeout(() => { this.$refs.modal.open('Success', 'Product has been added succesfully.', true); }, 1000);
                        //setTimeout(() => { this.$refs.modal.close(); }, 1000);
                        this.clearProduct();
                        this.productURL = '';
                        this.getCollections();
                    }

                    // Unknown server error (catch all)
                    else {
                        this.$refs.modal.open('Error', 'Could not add product due to server error. Please try again or contact support for help.', true);
                        // console.log(res);
                        return false;
                    }
                });

                // console.log(this.productInfo);
                return true;
            }catch(res){
                this.$refs.modal.open('Error', 'Could not add product due to server error. Please try again or contact support for help.', true);
                // console.log(res);
                return false;

            }
        },
        clearProduct() {
            this.lastProcessedURL = '';
            this.productInfo = {};
            this.diagnostics = [];
            this.resetImportStatus();
            this.importMsg = '';
        },
        profit(index) {
            let p = 0.00;

            if (index < 0)
            {
                p = parseFloat(parseFloat(this.productInfo.shopify_price) - parseFloat(this.productInfo.source_price)).toFixed(2);
            }
            else
            {
                p = parseFloat(parseFloat(this.productInfo.variants[index].shopify_price) - parseFloat(this.productInfo.variants[index].source_price)).toFixed(2);
            }

            return p;
        },
        mainImageModal(index) {
            console.log(index);
            // Set target image container object
            this.mainImageTarget = index;

            // Display image selector modal
            $('#ad-image-modal').modal('show');
        },
        setMainImage(index) {
            // Set main image
            if (this.mainImageTarget < 0) {
                this.productInfo.main_image = index;
            } else {
                this.productInfo.variants[this.mainImageTarget].main_image = index;
            }

            // Hide modal
            $('#ad-image-modal').modal('hide');
        },
        setImportStatus(status, msg, validClass = '', enabled = true, inProgress = false) {
            this.importStatus     = status;
            this.importMsg        = msg;
            this.urlValidClass    = validClass;
            this.importEnabled    = enabled;
            this.importInProgress = inProgress;
        },
        resetImportStatus()
        {
            console.log("rady to import");
            if (!this.lastProcessedURL || this.productURL != this.lastProcessedURL) {
                this.setImportStatus('primary', 'Status: Ready to import');
            }
        },
        selectProductImage(index)
        {
            this.productInfo.unique_images[index].selected = !this.productInfo.unique_images[index].selected;
        },
        getProduct() {

            let base = this;
            try {
                if (this.importEnabled) {

                    this.clearProduct();

                    // Switch to product info tab
                    $('a[href="#ad-product-info-pan"]').tab('show');

                    // check associate id is available or not

                    if (this.amazonAffiliate) {
                        if (!this.checkStore()) {
                            return false;
                        }else{
                            this.setImportStatus('primary', 'Status: Fetching product information. Please wait...', '', false, true);
                            this.lastProcessedURL = this.productURL = this.productURL.trim();
                        }

                    } else if( this.planMsgs['regular_pr_err_msg'] !== '' ){
                        this.setImportStatus('danger', this.planMsgs['regular_pr_err_msg'], 'is-invalid');
                        this.$refs.modal.open('Info', this.planMsgs['regular_pr_err_msg'], true);
                        return false;
                    }

                    let importSource = '';
                    let importParams = {};

                    this.setImportStatus('primary', 'Status: Fetching product information. Please wait...', '', false, true);
                    this.lastProcessedURL = this.productURL = this.productURL.trim();

                    let parser = document.createElement('a');
                    parser.href = this.productURL;

                    let amazonMatch = /^(www\.)?(amazon|amz)\.(com|com\.au|com\.br|ca|cn|fr|de|in|it|co\.jp|com\.mx|es|co\.uk)/i;
                    let walmartMatch = /^(www\.)?walmart\.com/i;

                    console.log('hostname: ' + parser.hostname.replace('www.', ''));

                    // Check URL
                    if (!parser.host || parser.host == window.location.host ||
                        (parser.protocol != 'http:' && parser.protocol != 'https:')) {
                        this.setImportStatus('danger', 'Invalid URL entered. Please make sure the URL starts with https://.', 'is-invalid');
                        return false;
                    }

                    // Valid Amazon URL
                    else if (amazonMatch.test(parser.hostname)) {
                        this.is_review_disabled = false;
                        this.review.is_import = true;
                        this.is_Affiliate_disabled = false;
                        this.source = 'amazon';
                         console.log('Matched Amazon URL');

                        // Get locale
                        let amazonLocale = parser.hostname.replace(/^(www\.)?(amazon|amz)\.(com\.|co\.)?/i, '').toUpperCase();

                        if (amazonLocale == 'COM') {
                            amazonLocale = 'US';
                        }else if( amazonLocale == 'UK' ){
                            amazonLocale = 'GB';
                        }

                        // Check ASIN
                        let idMatch = parser.pathname.match('/([a-zA-Z0-9]{10})(?:[/?]|$)');

                        if (!idMatch) {
                            this.urlValidClass = 'is-invalid';
                            this.setImportStatus('danger', 'Could not extract product ASIN from product URL. Please double check your entry or try another URL.', 'is-invalid');
                            return false;
                        }

                        let dhost = parser.host;
                        this.review.domain = dhost.replace("www.", "");

                        importSource = 'amazon';
                        importParams = {
                            locale: amazonLocale,
                            include_variants: true,
                            id: idMatch[1],
                            currency: base.currency,
                            hostname: parser.hostname.replace('www.', ''),
                            currency_symbol : base.currency_symbol
                        };
                    }

                    // Walmart URL
                    else if (walmartMatch.test(parser.hostname)) {
                        this.is_review_disabled = false;
                        this.is_Affiliate_disabled = true;
                        this.review.is_import = true;
                        this.source = 'walmart';
                        // console.log('Matched Walmart URL');

                        // Check Walmart Product ID
                        let idMatch = parser.pathname.match('/([0-9]{5,12})(?:[/?]|$)');

                        if (!idMatch) {
                            this.urlValidClass = 'is-invalid';
                            this.setImportStatus('danger', 'Could not extract product ID from product URL. Please double check your entry or try another URL.', 'is-invalid');
                            return false;
                        }

                        // console.log('Walmart Product ID: ' + idMatch[1]);
                        this.review.domain = 'walmart.com';
                        importSource = 'walmart';
                        importParams = {
                            product_id: idMatch[1],
                            currency: base.currency,
                            currency_symbol : base.currency_symbol
                        };
                    }

                    // Valid URL but neither Amazon/Walmart
                    else {
                        this.setImportStatus('danger', 'Sorry but we cannot get products <strong>' + parser.hostname + '</strong> at this time. Press the <strong><i class="fas fa-external-link-alt"></i> Store Shortcuts</strong> button at the top for the list of supported online stores.', 'is-invalid');
                        return false;
                    }

                    // Get Product from API
                    if (importSource) {
                        this.review.asin = (importSource === 'walmart') ? importParams['product_id'] : importParams['id'];

                        let parser = document.createElement('a');
                        // get currency of locale
                        let l = (importSource === 'walmart') ? 'us' : importParams['locale'].toLowerCase();

                        if( (importSource === 'amazon') ){
                            l = ( importParams['locale'].toLowerCase() === 'uk' ) ? 'us' : l;
                        }

                        console.log("l",l);
                        console.log("locale",importParams['locale']);

                            importParams['from_locale'] = l;
                            axios.post('/get-' + importSource + '-product', importParams).then((res) => {


                                console.log("GetProductsInormation::res",res);


                                if (res.data.diagnostics) {
                                    this.diagnostics = res.data.diagnostics;
                                }

                                // Process errors, if any
                                // if (!res.data.success) {
                                //     this.setImportStatus('danger', 'Could not fetch product due to server error. Please try again or contact support for help.', '');
                                //     return false;
                                // }

                                console.log("success",res.data.errors);

                                if (res.data.success!==true && res.data.errors['api_rate_limit']) {
                                    let message = res.data.errors['api_rate_limit'];
                                    let errorMsg = "<p class=\"mb-0\">"+message+"</p>\n";
                                    this.setImportStatus('danger', message, 'is-invalid');
                                    this.productInfo = {};
                                    this.diagnostics = [];
                                    this.$refs.modal.open('Info', message, true);
                                    return false;

                                        //
                                        // let message = res.data.errors['api_rate_limit'];
                                        // console.log("res", res.data.errors);
                                        // this.$refs.modal.open('Info', message, true);

                                }
                               else if (res.data.errors && res.data.errors.length > 0) {

                                        let errorMsg = "<p class=\"mb-0\">Could not fetch product:</p><ul>\n";

                                        for (let i = 0; i < res.data.errors.length; i++) {
                                            errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                                        }

                                        errorMsg += "</ul>\n";

                                        this.setImportStatus('danger', errorMsg, 'is-invalid');


                                    // console.log('Errors:\n' + res.data.errors.join("\n"));
                                }

                                // Success
                                else if (res.data.product) {

                                 console.log("imported product data ",res.data.product);
                                    this.is_poll_script = false;


                                    // Assign object properties, so that they are reactive
                                    for (let prop in res.data.product) {
                                        if (res.data.product.hasOwnProperty(prop)) {
                                            this.$set(this.productInfo, prop, res.data.product[prop]);
                                        }
                                    }

                                    this.is_Affiliate_disabled = (res.data.product.source=="Amazon") ? false : true;
                                    this.productInfo['source_url'] = this.lastProcessedURL;

                                    this.setImportStatus('success', 'Status: Product import completed successfully.', 'is-valid')
                                }

                                // Unknown server error (catch all)
                                else {
                                    this.setImportStatus('danger', 'Could not fetch product due to server error. Please try again or contact support for help.', '');
                                    // console.log(res.data);
                                    return false;
                                }
                            }).catch(error => {
                                    console.log("error",error);
                                });

                    }
                    //
                    // return true;
                }
                return true;
            }catch(res){
                console.log(res);
            }
        },
        getPercentDiff(index) {
            if (this.productInfo.variants[index].source_price != 0) {
                let sp = parseFloat(this.productInfo.variants[index].source_price);
                let tp = parseFloat(this.productInfo.variants[index].shopify_price);
                return parseFloat((tp - sp) / sp * 100).toFixed(2);
            }
            return '0.00';
        },
        applyPercentDiffAll() {
            let el = document.getElementById('product-percent-diff');
            let val = el.value;

            if (!isNaN(val)) {
                // update main variant price

                let p = parseFloat(val).toFixed(2);
                let pVal = parseFloat(p);
                let sp = parseFloat(this.productInfo.source_price);
                this.productInfo.shopify_price = parseFloat(sp + (sp / 100) * pVal).toFixed(2)

                // update all variants
                for (let i = 0; i < this.productInfo.variants.length; i++) {
                    el.value = parseFloat(val).toFixed(2);
                    document.getElementById('variant-' + this.productInfo.variants[i].source_id + '-percent-diff').value = parseFloat(val).toFixed(2);
                    this.applyPercentDiff(i);
                }
            }
        },
        applyPercentDiff(index) {
            let el = document.getElementById('variant-' + this.productInfo.variants[index].source_id + '-percent-diff');
            let p = el.value;
            if (!isNaN(p)) {
                let pVal = parseFloat(p);
                let sp = parseFloat(this.productInfo.variants[index].source_price);
                this.productInfo.variants[index].shopify_price = parseFloat(sp + (sp / 100) * pVal).toFixed(2);

                console.log("P::",this.productInfo.variants[index].shopify_price);

                el.value = parseFloat(p).toFixed(2);
            }
        },
        updatePercentDiff(index) {
            let pField = document.getElementById('variant-' + this.productInfo.variants[index].source_id + '-percent-diff');
            let sp = parseFloat(this.productInfo.variants[index].source_price);
            let tp = parseFloat(this.productInfo.variants[index].shopify_price);

            if (sp != 0) {
                pField.value = parseFloat(((tp - sp) / sp) * 100).toFixed(2);
            }
            else {
                pField.value = '0.00';
            }

            console.log("pField::",pField.value);
        },
        sleep(milliseconds) {
            let timeStart = new Date().getTime();
            while (true) {
                let elapsedTime = new Date().getTime() - timeStart;
                if (elapsedTime > milliseconds) {
                    break;
                }
            }
        },
        // check amazon or walmart product affiliate
        checkStore(){

            console.log("Check-store");

            this.is_poll_script = false;

            let base = this;
            if( this.productURL !== '' && this.amazonAffiliate){
                let parser = document.createElement('a');
                parser.href = this.productURL;
                let amazonMatch = /^(www\.)?(amazon|amz)\.(com|com\.au|com\.br|ca|cn|fr|de|in|it|co\.jp|com\.mx|es|co\.uk)/i;
                let walmartMatch = /^(www\.)?walmart\.com/i;
                if( this.planMsgs['affiliate_pr_err_msg'] !== '' ){
                    this.productInfo = {};
                    this.diagnostics = [];
                    this.setImportStatus('danger', this.planMsgs['affiliate_pr_err_msg'], 'is-invalid');
                    this.$refs.modal.open('Info', this.planMsgs['affiliate_pr_err_msg'], true);
                    return false;
                } else if (walmartMatch.test(parser.hostname)) {
                    // base.amazonAffiliate = false;
                    let errorMsg = "<p class=\"mb-0\">Affiliates Import is not available for Walmart products. For Walmart products please use regular import</p><ul>\n";
                    this.setImportStatus('danger', errorMsg, 'is-invalid');
                    this.productInfo = {};
                    this.diagnostics = [];
                    this.$refs.modal.open('Info', errorMsg, true);
                    return false;
                }else if(amazonMatch.test(parser.hostname)){
                    if( this.amazonAffiliate ){
                        let country = parser.hostname.replace(/^(www\.)?(amazon|amz)\.(com\.|co\.)?/i, '').toUpperCase();
                        let c = ( country === 'COM' ) ? 'US' : country;
                        c = ( country === 'UK' ) ? 'GB' : c;
                        if(!this.associates.includes(c)){
                            this.is_ok = true;
                            let msg = 'You have not saved your Amazon Affiliate/Associate ID for this Amazon Marketplace (Country). Please go to Settings section and add it. Otherwise use regular product Import.';
                            this.setImportStatus('danger', msg, 'is-invalid');
                            this.productInfo = {};
                            this.diagnostics = [];
                            this.$refs.modal.open('Error', msg, true);
                            return false;
                        }else{
                            this.setImportStatus('primary', 'Status: Ready to import', '');
                        }
                    }
                }
            }else if( this.planMsgs['regular_pr_err_msg'] !== '' && !this.amazonAffiliate ){
                this.productInfo = {};
                this.diagnostics = [];
                this.setImportStatus('danger', this.planMsgs['regular_pr_err_msg'], 'is-invalid');
                this.$refs.modal.open('Info', this.planMsgs['regular_pr_err_msg'], true);
                return false;
            } else{
                this.setImportStatus('primary', 'Status: Ready to import', '');
            }
            return true;
        }
    },
    mounted() {
        this.is_poll_script = false;
        this.getCollections();

        // window.Echo.channel('events')
        //     .listen('WalmartProductFetchRealTimeMessage', (e) => console.log('WalmartProductFetchRealTimeMessage: ' + e.message));
        //
        // Echo.channel('events')
        //     .listen('WalmartProductFetchRealTimeMessage', (e) => console.log('WalmartProductFetchRealTimeMessage: ' + e.message));

    },
}
</script>
