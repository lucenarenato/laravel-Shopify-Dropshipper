<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <form class="card shadow-sm" name="settings-form" id="settings-form" method="post">
                    <div class="card-header">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="settings-plan-tab" data-toggle="tab" href="#settings-plan-pan" role="tab" aria-controls="settings-plan-pan" aria-selected="true">Subscription Plan</a>
                                <a class="nav-item nav-link" id="settings-associate-tab" data-toggle="tab" href="#settings-associate-pan" role="tab" aria-controls="settings-associate-pan" aria-selected="false">Amazon Associate</a>
                                <a class="nav-item nav-link" id="settings-reviews-tab" data-toggle="tab" href="#settings-reviews-pan" role="tab" aria-controls="settings-reviews-pan" aria-selected="false">Reviews <span class="text-feature">(new)</span></a>
<!--                                <a class="nav-item nav-link" id="settings-advanced-tab" data-toggle="tab" href="#settings-advanced-pan" role="tab" aria-controls="settings-advanced-pan" aria-selected="false">Advanced</a>-->
                            </div>
                        </nav>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="settings-plan-pan" role="tabpanel" aria-labelledby="settings-plan-tab">
                                <div class="ad-plan-container">
                                    <div :class="'ad-plan-col ' + currentPlanClass(1)">
                                        <ul>
                                            <li class="header" style="background-color: #6c6c6c">Freemium</li>
                                            <li class="grey">Free</li>
                                            <li>10 Product Imports</li>
                                            <li>10 Amazon Associate Imports</li>
                                            <li>250 Product Variants<sup>*</sup></li>
                                            <li class="grey" v-if="plan == 1">Current Plan</li>
                                            <li class="grey" v-else style="padding:13%;">
<!--                                                <button class="btn btn-block btn-primary" type="button" :disabled="!buttonsEnabled || is_disable_free_plan === 1" @click.prevent="setPlan(1)">Select Plan</button>-->
                                            </li>
                                        </ul>
                                    </div>
                                    <div :class="'ad-plan-col ' + currentPlanClass(2)">
                                        <ul>
                                            <li class="header" style="background-color: #337AB7">Pro</li>
                                            <li class="grey">$4.99 / month</li>
                                            <li>15 Product Imports</li>
                                            <li>Unlimited Amazon Associate Imports</li>
                                            <li>375 Product Variants<sup>*</sup></li>
                                            <li class="grey" v-if="plan == 2">Current Plan</li>
                                            <li class="grey" v-else><button class="btn btn-block btn-primary" type="button" :disabled="!buttonsEnabled" @click.prevent="setPlan(2)">Select Plan</button></li>
                                        </ul>
                                    </div>
                                    <div :class="'ad-plan-col ' + currentPlanClass(3)">
                                        <ul>
                                            <li class="header" style="background-color: #976C30">Gold</li>
                                            <li class="grey">$9.99 / month</li>
                                            <li>100 Product Imports</li>
                                            <li>Unlimited Amazon Associate Imports</li>
                                            <li>2,500 Product Variants<sup>*</sup></li>
                                            <li class="grey" v-if="plan == 3">Current Plan</li>
                                            <li class="grey" v-else><button class="btn btn-block btn-primary" type="button" :disabled="!buttonsEnabled" @click.prevent="setPlan(3)">Select Plan</button></li>
                                        </ul>
                                    </div>
                                    <div :class="'ad-plan-col ' + currentPlanClass(4)">
                                        <ul>
                                            <li class="header" style="background-color: #C84C48">Ultimate</li>
                                            <li class="grey">$25.99 / month</li>
                                            <li>500 Product Imports</li>
                                            <li>Unlimited Amazon Associate Imports</li>
                                            <li>12,500 Product Variants<sup>*</sup></li>
                                            <li class="grey" v-if="plan == 4">Current Plan</li>
                                            <li class="grey" v-else><button class="btn btn-block btn-primary" type="button" :disabled="!buttonsEnabled" @click.prevent="setPlan(4)">Select Plan</button></li>
                                        </ul>
                                    </div>
                                    <div :class="'ad-plan-col ' + currentPlanClass(5)">
                                        <ul>
                                            <li class="header">Ultimate Plus</li>
                                            <li class="grey">$99.99 / month</li>
                                            <li>5,000 Product Imports</li>
                                            <li>Unlimited Amazon Associate Imports</li>
                                            <li>125,000 Product Variants<sup>*</sup></li>
                                            <li class="grey" v-if="plan == 5">Current Plan</li>
                                            <li class="grey" v-else><button class="btn btn-block btn-primary" type="button" :disabled="!buttonsEnabled" @click.prevent="setPlan(5)">Select Plan</button></li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="mb-0 pl-2"><sup>*</sup>Maximum 25 per product</p>
                            </div>
                            <div class="tab-pane fade" id="settings-associate-pan" role="tabpanel" aria-labelledby="settings-associate-tab">
                                <div class="row">
                                    <div class="col-sm-6">
<!--                                        <h3>General</h3>-->
<!--                                        <div class="form-group">-->
<!--                                            <label for="ad-aa-button-label">Amazon Associate Purchase Button Label</label>-->
<!--                                            <input type="text" class="form-control" id="ad-aa-button-label" value="Buy from Amazon" maxlength="100" v-model="AmazonAssociateBtn" required>-->
<!--                                        </div>-->
<!--                                        <button class="btn btn-success" type="button" :disabled="!buttonsEnabled" @click.prevent="setAmazonAssociateBtn()">Save Changes</button>-->
<!--                                        <hr>-->
                                        <h3>Add Amazon Associate ID</h3>
                                        <div class="form-group">
                                            <label for="ad-aa-locale">Country</label>
                                            <select class="form-control" id="ad-aa-locale" required v-model="AmazonAssociateLocale">
                                                <option value="US">United States</option>
                                                <option value="AU">Australia</option>
                                                <option value="BR">Brazil</option>
                                                <option value="CA">Canada</option>
                                                <option value="CN">China</option>
                                                <option value="FR">France</option>
                                                <option value="DE">Germany</option>
                                                <option value="IN">India</option>
                                                <option value="IT">Italy</option>
                                                <option value="JP">Japan</option>
                                                <option value="MX">Mexico</option>
                                                <option value="ES">Spain</option>
                                                <option value="GB">United Kingdom</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="ad-aa-id">Associate ID</label>
                                            <input type="text" class="form-control" id="ad-aa-id" maxlength="250" v-model="AmazonAssociateId" required>
                                        </div>
                                        <button class="btn btn-success" type="button" :disabled="!buttonsEnabled" @click.prevent="addAmazonAssociate()">Add Associate ID</button>
                                        <div v-if="associates && associates.length">
                                            <h3 class="mt-3">Saved Amazon Associate IDs</h3>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Country</th>
                                                        <th scope="col">Associate ID</th>
                                                        <th scope="col"><span class="sr-only">Action</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="associate in associates">
                                                        <td>{{ associate.locale }}</td>
                                                        <td>{{ associate.associate_id }}</td>
                                                        <td class="text-right"><button class="btn btn-danger" type="button" :disabled="!buttonsEnabled" @click.prevent="deleteAmazonAssociate(associate.id)">Delete</button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="alert alert-info" role="alert"><strong>NOTE:</strong> The Amazon Associates feature is available for the following Shopify Templates or themes: Debut, Simple, Pop, Boundless, Venture, Jumpstart, Supply, Narrative, Brooklyn and Minimal.</p>
                                        <div class="alert alert-info" role="alert"><strong>Steps to activate Amazon Associate app feature:</strong>
                                            <ol class="pl-3">
                                                <li>Get your Associate ID by enrolling into Amazon Associate/Affiliate Program.</li>
                                                <li>Select the Country of the Amazon Marketplace you enrolled for the Associate Program.</li>
                                                <li>Enter the Associate ID and press the Save Changes button.</li>
                                                <li>Deactivate the Dynamic Checkout button from the Shopify product page for your theme.<br>
                                                    <strong>NOTE:</strong> You have the option to remove the Checkout button from all products (easy method) or from only the selected products (advanced method).<br>
                                                    For the easy method, watch <a href="#">this quick video</a> or follow <a href="#">these simple steps</a>.</li>
                                                <li>After completing all of the steps above, go and import your first product for Amazon Associate Program.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="settings-advanced-pan" role="tabpanel" aria-labelledby="settings-advanced-tab">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="ad-aa-diagnostics-chk" v-model="diagnostics">
                                    <label class="custom-control-label" for="ad-aa-diagnostics-chk">Enable Diagnostic Messages</label>
                                </div>
                                <button class="btn btn-success" type="button" :disabled="!buttonsEnabled" @click.prevent="setDiagnostics()">Save Changes</button>
                            </div>
                            <div class="tab-pane fade" id="settings-reviews-pan" role="tabpanel" aria-labelledby="settings-advanced-tab">

                                <h3>{{page_contents.reviews_step_title ? page_contents.reviews_step_title : 'Steps to Display Reviews:'}}</h3>
                                 <div  class="setting-product-review">
                                    <p>1) Copy code: <b>{{review_code}}</b></p>
                                    <p>2) Go to Store > Theme > Edit Code</p>
                                    <p>3) Depending on your Theme, you need to find product.liquid </p>
                                    <p>4) Please insert or paste the code below to the section in the Product page where you want to display the Reviews (be carefull to paste code correctly) </p>
                                    <p>5) This code can be removed manually later in teh future if desired.</p>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <modal ref="modal" :is_show_ok="is_ok"></modal>
<!--         get confirmation to change plan-->
        <div class="modal fade" id="confirm-modal" tabindex="-1" role="dialog" aria-labelledby="ad-modal-title" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirm-modal-title">Change Plan?</h5>
                        <button type="button" class="close" @click="close()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>Are you sure you want to change your current plan?</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="changePlan()">Ok</button>
                        <button type="button" class="btn btn-secondary" @click="close()">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            propPlan: Number,
            propAmazonAssociateBtn: String,
            propDiagnostics: Boolean,
            page_contents:Object
        },
        data() {
            return {
                plan: this.propPlan,
                AmazonAssociateBtn: this.propAmazonAssociateBtn,
                AmazonAssociateLocale: 'US',
                AmazonAssociateId: '',
                diagnostics: this.propDiagnostics,
                associates: [],
                buttonsEnabled: true,
                is_ok: false,
                new_plan: '',
                is_disable_free_plan: 0,
                review_code: '<div class="dropshipping-product-review" data-id="{{ product.id }}"></div>'
            };
        },
        computed: {
        },
        methods: {
            close() {
                $('#confirm-modal').modal('hide');
            },
            currentPlanClass(planID) {
                return this.plan == planID ? 'current' : '';
            },
            setPlan(planID) {
                this.new_plan = planID;
                $('#confirm-modal').modal('show');

            },
            changePlan(){
                $('#confirm-modal').modal('hide');
                this.buttonsEnabled = false;
                // Display progress
                this.$refs.modal.open('Please wait...', 'Updating subscription plan', false, '', 100);

                axios.post('/settings/set-plan', { 'curr_plan_id': this.plan, 'new_plan_id': this.new_plan }).then((res) => {

                    // Process errors, if any
                    if (res.data.errors && res.data.errors.length > 0)
                    {
                        let errorMsg = "<p class=\"mb-0\">Could not switch to new plan:</p><ul>\n";

                        for (let i = 0; i < res.data.errors.length; i++) {
                            errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                        }

                        errorMsg += "</ul>\n";

                        this.$refs.modal.open('Info', errorMsg, true);

                        console.log('Errors:\n' + res.data.errors.join("\n"));
                    }

                    // Success
                    else if (res.data.success)  {
                        // Use timeout to allow modal close after fade in
                        window.location.href = "/billing/" + this.new_plan;
                        setTimeout(() => { this.$refs.modal.close(); this.plan = res.data.plan_id; }, 1000);
                    }

                    // Unknown server error (catch all)
                    else {
                        this.$refs.modal.open('Error', 'Could not switch to new plan due to server error. Please try again or contact support for help.', true);
                        console.log('Could not switch to new plan due to server error. Please try again or contact support for help.');
                        return false;
                    }

                    this.buttonsEnabled = true;
                });

                return true;
            },
            setAmazonAssociateBtn() {
                this.buttonsEnabled = false;

                // Display progress
                this.$refs.modal.open('Please wait...', 'Updating Amazon Associate Button Label', false, '', 100);

                axios.post('/settings/set-amazon-associate-btn', { 'amazon_associate_btn': this.AmazonAssociateBtn }).then((res) => {

                    // Process errors, if any
                    if (res.data.errors && res.data.errors.length > 0)
                    {
                        let errorMsg = "<p class=\"mb-0\">Could not complete update:</p><ul>\n";

                        for (let i = 0; i < res.data.errors.length; i++) {
                            errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                        }

                        errorMsg += "</ul>\n";

                        this.$refs.modal.open('Error', errorMsg, true);

                        console.log('Errors:\n' + res.data.errors.join("\n"));
                    }

                    // Success
                    else if (res.data.success)  {
                        setTimeout(() => { this.$refs.modal.open('Success', 'Amazon Associate Button Label has been updated.', true); }, 1000);
                    }

                    // Unknown server error (catch all)
                    else {
                        this.$refs.modal.open('Error', 'Could not complete update due to server error. Please try again or contact support for help.', true);
                        console.log('Could not complete update due to server error. Please try again or contact support for help.');
                        return false;
                    }

                    this.buttonsEnabled = true;
                });

                return true;
            },
            getAmazonAssociates() {
                this.buttonsEnabled = false;

                axios.get('/settings/get-amazon-associates').then((res) => {

                    // Process errors, if any
                    if (res.data.errors && res.data.errors.length > 0)
                    {
                        console.log('Errors:\n' + res.data.errors.join("\n"));
                    }

                    // Success
                    else if (res.data.success)  {
                        this.associates = res.data.associates;
                        this.is_disable_free_plan = res.data.is_disable_free_plan;
                    }

                    // Unknown server error (catch all)
                    else {
                        console.log('Could not retrieve Amazon Associates list due to server error. Please try again or contact support for help.');
                        return false;
                    }

                    this.buttonsEnabled = true;
                });

                return true;
            },
            addAmazonAssociate() {
                this.buttonsEnabled = false;

                // Display progress
                this.$refs.modal.open('Please wait...', 'Adding Amazon Associate ID', false, '', 100);

                let params = {
                    'locale': this.AmazonAssociateLocale,
                    'associate_id': this.AmazonAssociateId
                };

                axios.post('/settings/add-amazon-associate', params).then((res) => {

                    // Process errors, if any
                    if (res.data.errors && res.data.errors.length > 0)
                    {
                        let errorMsg = "<p class=\"mb-0\">Could not add Amazon Associate ID:</p><ul>\n";

                        for (let i = 0; i < res.data.errors.length; i++) {
                            errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                        }

                        errorMsg += "</ul>\n";

                        this.$refs.modal.open('Error', errorMsg, true);

                        console.log('Errors:\n' + res.data.errors.join("\n"));
                    }

                    // Success
                    else if (res.data.success)  {
                        //setTimeout(() => { this.$refs.modal.open('Success', 'Amazon Associate ID has been added.', true); }, 1000);
                        setTimeout(() => { this.$refs.modal.close(); }, 1000);
                        this.AmazonAssociateLocale = 'US';
                        this.AmazonAssociateId = '';
                        this.getAmazonAssociates();
                    }

                    // Unknown server error (catch all)
                    else {
                        this.$refs.modal.open('Error', 'Could not add Amazon Associate ID due to server error. Please try again or contact support for help.', true);
                        return false;
                    }

                    this.buttonsEnabled = true;
                });

                return true;
            },
            deleteAmazonAssociate(id) {
                this.buttonsEnabled = false;

                // Display progress
                this.$refs.modal.open('Please wait...', 'Deleting Amazon Associate ID', false, '', 100);

                axios.post('/settings/delete-amazon-associate', { 'id': id }).then((res) => {

                    // Process errors, if any
                    if (res.data.errors && res.data.errors.length > 0)
                    {
                        let errorMsg = "<p class=\"mb-0\">Could not complete update:</p><ul>\n";

                        for (let i = 0; i < res.data.errors.length; i++) {
                            errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                        }

                        errorMsg += "</ul>\n";

                        this.$refs.modal.open('Error', errorMsg, true);

                        console.log('Errors:\n' + res.data.errors.join("\n"));
                    }

                    // Success
                    else if (res.data.success)  {
                        //setTimeout(() => { this.$refs.modal.open('Success', 'Amazon Associate information has been updated.', true); }, 1000);
                        setTimeout(() => { this.$refs.modal.close(); }, 1000);
                        this.getAmazonAssociates();
                    }

                    // Unknown server error (catch all)
                    else {
                        this.$refs.modal.open('Error', 'Could not complete update due to server error. Please try again or contact support for help.', true);
                        console.log('Could not complete update due to server error. Please try again or contact support for help.');
                        return false;
                    }

                    this.buttonsEnabled = true;
                });

                return true;
            },
            setDiagnostics() {
                this.buttonsEnabled = false;

                // Display progress
                this.$refs.modal.open('Please wait...', 'Updating Advanced Settings', false, '', 100);

                axios.post('/settings/set-advanced', { 'diagnostics': this.diagnostics }).then((res) => {

                    // Process errors, if any
                    if (res.data.errors && res.data.errors.length > 0)
                    {
                        let errorMsg = "<p class=\"mb-0\">Could not complete update:</p><ul>\n";

                        for (let i = 0; i < res.data.errors.length; i++) {
                            errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                        }

                        errorMsg += "</ul>\n";

                        this.$refs.modal.open('Error', errorMsg, true);

                        console.log('Errors:\n' + res.data.errors.join("\n"));
                    }

                    // Success
                    else if (res.data.success)  {
                        setTimeout(() => { this.$refs.modal.open('Success', 'Advanced Settings have been updated.', true); }, 1000);
                    }

                    // Unknown server error (catch all)
                    else {
                        this.$refs.modal.open('Error', 'Could not complete update due to server error. Please try again or contact support for help.', true);
                        console.log('Could not complete update due to server error. Please try again or contact support for help.');
                        return false;
                    }

                    this.buttonsEnabled = true;
                });

                return true;
            }
        },
        mounted() {

            console.log("settings page content",this.page_contents);
            this.getAmazonAssociates();
        }
    }
</script>
