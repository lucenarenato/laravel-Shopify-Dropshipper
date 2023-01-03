<template>
    <div>
        <div class="modal fade" id="upgrade-plan-modal" tabindex="-1" role="dialog" aria-labelledby="ad-modal-title"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered plan-modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="upgrade-plan-modal-title">Plan Upgrade Required</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div style="color:red;">You reached your maximum imports for the month. Please upgrade your plan
                            to continue importing.
                        </div>
                        <div>
                            <p class="my-product-cnt"><span class="my-product-cnt-fc">Current Plan: </span><span class="my-product-cnt-lc">{{current_plan_name}}</span></p>
                            <p class="my-product-cnt"><span class="my-product-cnt-fc">Product Imported this Month: </span><span class="my-product-cnt-lc">{{total_product}}</span></p>
                            <p class="my-product-cnt"><span class="my-product-cnt-fc">Affiliate Product Imported this Month: </span><span class="my-product-cnt-lc">{{total_affiliate_product}}</span></p>
                        </div>
<!--                        <div class="up-plan-details">-->
<!--                            <p>your current Plan is: <span class="tag">{{current_plan_name}}</span></p>-->
<!--                            <p>You have imported {{total_product}} products so far.</p>-->
<!--                        </div>-->
                        <div class="up-plan-des">
                            <div class="up-plan-container">
                                <div class="ad-plan-col up-plan-col" style="width:20%;color:#000000">
                                    <ul>
                                        <li class="header features up-td"
                                            style="color:#000000;padding:17%;font-weight:100;">FEATURES
                                        </li>
                                        <li class="features" style="padding:11%;">Products to import</li>
                                        <li class="features up-td">Amazon Associates Imports</li>
                                        <li class="features" style="padding: 11%;">Import Variants</li>
                                        <li class="features up-td" style="padding: 10%;">Update Product + Variants
                                            Price/Availability
                                        </li>
                                        <li class="features" style="padding: 8%;">Price</li>
                                        <li class="features" style="padding: 18%;"></li>
                                    </ul>
                                </div>
                                <div class="ad-plan-col up-plan-col">
                                    <ul>
                                        <li class="header" style="background-color: #6c6c6c">FREEMIUM</li>
                                        <li class="grey">10</li>
                                        <li class="grey up-td" style="padding: 19%;">10</li>
                                        <li class="grey">250*</li>
                                        <li class="grey up-td" style="padding: 26%;">--</li>
                                        <li class="footer" style="background-color: #6c6c6c;">Free</li>
                                        <li style="background-color: #ffffff;padding: 22%;"></li>
                                    </ul>
                                </div>
                                <div class="ad-plan-col up-plan-col">
                                    <ul>
                                        <li class="header" style="background-color: #337AB7">PRO</li>
                                        <li class="grey">15</li>
                                        <li class="grey up-td" style="padding: 19%;">Unlimited</li>
                                        <li class="grey">375</li>
                                        <li class="grey up-td" style="padding: 26%;">--</li>
                                        <li class="footer" style="background-color: #337AB7">$4.99/month</li>
                                        <li class="up-chose-plan">
                                            <p v-if="current_plan_id === 2">Current Plan</p>
                                            <button v-else class="btn" style="background-color: #337AB7;color:#ffffff"
                                                    :disabled="!buttonsEnabled" @click.prevent="setPlan(2)">Choose Plan
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="ad-plan-col up-plan-col">
                                    <ul>
                                        <li class="header" style="background-color: #976C30">GOLD</li>
                                        <li class="grey">100</li>
                                        <li class="grey up-td" style="padding: 19%;">Unlimited</li>
                                        <li class="grey">2500</li>
                                        <li class="grey up-td" style="padding: 26%;">Soon</li>
                                        <li class="footer" style="background-color: #976C30">$9.99 / month</li>
                                        <li class="up-chose-plan">
                                            <p v-if="current_plan_id === 3">Current Plan</p>
                                            <button v-else class="btn" style="background-color: #337AB7;color:#ffffff"
                                                    :disabled="!buttonsEnabled" @click.prevent="setPlan(3)">Choose Plan
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="ad-plan-col up-plan-col">
                                    <ul>
                                        <li class="header" style="background-color: #C84C48">ULTIMATE</li>
                                        <li class="grey">500</li>
                                        <li class="grey up-td" style="padding: 19%;">Unlimited</li>
                                        <li class="grey">12500</li>
                                        <li class="grey up-td" style="padding: 26%;">Soon</li>
                                        <li class="footer" style="background-color: #C84C48">$25.99 / month</li>
                                        <li class="up-chose-plan">
                                            <p v-if="current_plan_id === 4">Current Plan</p>
                                            <button v-else class="btn" style="background-color: #337AB7;color:#ffffff"
                                                    :disabled="!buttonsEnabled" @click.prevent="setPlan(4)">Choose Plan
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="ad-plan-col up-plan-col">
                                    <ul>
                                        <li class="header" style="background-color: #000000;padding: 15%;">ULTIMATE
                                            PLUS
                                        </li>
                                        <li class="grey">5000</li>
                                        <li class="grey up-td" style="padding: 19%;">Unlimited</li>
                                        <li class="grey">125000</li>
                                        <li class="grey up-td" style="padding: 26%;">Soon</li>
                                        <li class="footer" style="background-color: #000000">$99.99 / month</li>
                                        <li class="up-chose-plan">
                                            <p v-if="current_plan_id === 5">Current Plan</p>
                                            <button v-else class="btn" style="background-color: #337AB7;color:#ffffff"
                                                    :disabled="!buttonsEnabled" @click.prevent="setPlan(5)">Choose Plan
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <!--         get confirmation to change plan-->
        <div class="modal fade" id="confirm-modal" tabindex="-1" role="dialog" aria-labelledby="ad-modal-title"
             aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirm-modal-title">Change Plan?</h5>
                        <button type="button" class="close" @click="closeConfirm()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>Are you sure you want to change your current plan?</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="changePlan()">Ok</button>
                        <button type="button" class="btn btn-secondary" @click="closeConfirm()">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <modal ref="err_modal" :is_show_ok="is_ok"></modal>
<!--        error mesg modal -->
        <div class="modal fade" id="err-msg-modal" tabindex="-1" role="dialog" aria-labelledby="ad-modal-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="err-msg-modal-title">Could not switch to new plan:</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-html="errorMsg"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"> Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                total_product: '',
                total_affiliate_product: '',
                current_plan_name: '',
                current_plan_id: '',
                buttonsEnabled: true,
                new_plan: '',
                is_ok: false,
                errorMsg: '',
            };
        },
        computed: {},
        methods: {
            open(total_product, total_affiliate_product, current_plan_name, current_plan_id) {
                this.total_product = total_product;
                this.total_affiliate_product = total_affiliate_product;
                this.current_plan_name = current_plan_name;
                this.current_plan_id = current_plan_id;
                $('#upgrade-plan-modal').modal('show');
            },
            close() {
                $('#upgrade-plan-modal').modal('hide');
            },
            closeConfirm() {
                $('#confirm-modal').modal('hide');
                this.buttonsEnabled = true;
            },
            isDismissable() {
                return this.dismissable ? "dynamic" : "static";
            },
            setPlan(planID) {
                this.new_plan = planID;
                this.buttonsEnabled = false;
                $('#confirm-modal').modal('show');

            },
            changePlan() {
                // $('#upgrade-plan-modal').modal('hide');\
                let base = this;
                $('#confirm-modal').modal('hide');
                this.buttonsEnabled = false;
                // Display progress
                // this.$refs.err_modal.open('Please wait...', 'Updating subscription plan', false, '', 100);

                axios.post('/settings/set-plan', {
                    'curr_plan_id': this.current_plan_id,
                    'new_plan_id': this.new_plan
                }).then((res) => {
                    // Process errors, if any
                    if (res.data.errors && res.data.errors.length > 0) {
                        // let errorMsg = "<p class=\"mb-0\">Could not switch to new plan:</p><ul>\n";

                        let errorMsg = '';
                        for (let i = 0; i < res.data.errors.length; i++) {
                            errorMsg += "<li>" + res.data.errors[i] + "</li>\n";
                        }
                        errorMsg += "</ul>\n";
                        base.errorMsg = errorMsg;
                        // base.$refs.err_modal.close();
                        $('#err-msg-modal').modal('show');
                        // base.$refs.err_modal.open('Error', errorMsg, true);
                    }

                    // Success
                    else if (res.data.success) {
                        // Use timeout to allow modal close after fade in
                        window.location.href = "/billing/" + this.new_plan;
                        setTimeout(() => {
                            this.$refs.err_modal.close();
                            this.plan = res.data.plan_id;
                        }, 1000);
                    }

                    // Unknown server error (catch all)
                    else {
                        this.$refs.err_modal.open('Error', 'Could not switch to new plan due to server error. Please try again or contact support for help.', true);
                        console.log('Could not switch to new plan due to server error. Please try again or contact support for help.');
                        return false;
                    }

                    this.buttonsEnabled = true;
                });

                return true;
            },
        },
        mounted() {

        }
    }
</script>
