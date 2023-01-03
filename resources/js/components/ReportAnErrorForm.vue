
<!--<report-an-error-form></report-an-error-form>-->

<template>
    <div>
        <div class="text-right">
            <p class="import-search-report-text text-primary mb-0" @click.prevent="reportErrorFormModal('report')">
                {{page_contents.report_an_error_text ? page_contents.report_an_error_text : 'Report an error'}}
                </p>
            <p class="import-search-report-text text-primary mb-0" @click.prevent="reportErrorFormModal('suggestion')">
                {{page_contents.send_suggestion_text ? page_contents.send_suggestion_text : 'Send suggestion'}}
                </p>
        </div>

    <!--    Report an error model : start-->

        <div class="modal fade" id="report_form_modal"  role="dialog" aria-labelledby="report_form_modal_title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="report_form_modal_title">{{report_form_modal_title}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body card" id="report_form_modal_body">

                        <div class="row">
                        <div class="col-md-12">

                            <div v-if="errors" class="bg-danger text-red py-2 px-4 pr-0 rounded font-bold mb-4 shadow-lg">
                                <div v-for="(v, k) in errors" :key="k">
                                    <p v-for="error in v" :key="error" class="text-sm">
                                        {{ error }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="success_msg" class="bg-success text-green py-2 px-4 pr-0 rounded font-bold mb-4 shadow-lg">

                                    <p class="text-sm">
                                        {{ success_msg }}
                                    </p>

                            </div>
                            <div v-if="error_msg" class="bg-danger text-red py-2 px-4 pr-0 rounded font-bold mb-4 shadow-lg">

                                <p class="text-sm">
                                    {{ error_msg }}
                                </p>

                            </div>


                            <form class="shadow-sm" name="add-product-form" id="add-product-form" method="post">
                                <div class="card-header">
                                    {{report_form_modal_title}}
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="ad-product-title">Store Name</label>
                                            <input type="text" class="form-control" id="ad-product-title" v-model="form.store_name" readonly >
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="ad-product-title">Store Email</label>
                                            <input type="text" class="form-control" id="ad-product-title" v-model="form.store_email" readonly>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label v-if="report_type=='report'" for="ad-product-title">Describe Error</label>
                                            <label v-if="report_type=='suggestion'" for="ad-product-title">Describe Suggestion</label>
                                            <textarea v-model="form.description" class="form-control" placeholder="Describe here" required></textarea>
                                        </div>

                                        <input type="hidden" v-model="report_type">

                                    </div>
                                </div>
                         <div class="card-footer">
                            <button type="button" class="btn btn-success btn-lg mr-2" @click.prevent="sendMail()" :disabled="submit_btn_disabled"><i class="fas fa-envelope"></i> {{submit_btn_text}}</button>
                        </div>
                        </form>
                       </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    <!--    Report an error model : end-->



    </div>
</template>

<script>


    export default {
        name: "ReportAnError",
        components: {

        },
        props: ["shop_data","page_contents"],
        data(){
            return{

                form: {
                    store_name: '',
                    store_email: '',
                    description: '',
                    report_type: '',

                },
                errors: null,
                report_type : null,
                report_form_modal_title : '',
                success_msg : null,
                error_msg:null,
                submit_btn_text : 'Submit',
                submit_btn_disabled : false,
            }
        },
        mounted() {
             $(this.$refs.modal).on("hidden.bs.modal", this.sendMail);
        },
        methods: {
            reportErrorFormModal(form_type) {

                this.errors = null;
                this.form.description = null;
                this.success_msg = null;
                this.error_msg = null;
                console.log(form_type);
                this.report_type = form_type;

                let title = '';
                if(this.report_type=="suggestion"){
                    title = "Suggestion";
                }
                else{
                    title = "Report an Error";
                }
                this.report_form_modal_title = title;

                $('#report_form_modal').modal('show');
            },
            sendMail(){
                console.log("Send mail...");

                try{

                   let params = this.form;
                    params.report_type = this.report_type;
                   console.log("params",params);


                    this.submit_btn_text = "Sending mail..";
                    this.submit_btn_disabled = true;
                    axios.post('/send-mail-report-suggestion',params).then((res) => {
                        this.submit_btn_text = "Submit";
                        this.errors = null;

                        if (res.data.success) {

                            let title = '';
                            if(this.report_type=="suggestion"){
                                title = "Suggestion";
                            }
                            else{
                                title = "Report an Error";
                            }
                            this.success_msg = res.data.message;

                            setTimeout(() => {

                                $('#report_form_modal').modal('hide');
                                 }, 2000);
                            this.submit_btn_disabled = false;

                        }
                        else {
                            this.submit_btn_text = "Submit";
                            this.error_msg = 'Could not send mail due to server error. Please try again or contact support for help.';
                            this.submit_btn_disabled = false;
                            // console.log(res);
                            return false;
                        }

                       }).catch(error => {

                        if(error) {
                            console.log("error",error.response);
                            if (error.response.status === 422) {
                                console.log("status-422");
                                this.submit_btn_text = "Submit";
                                this.errors = error.response.data.errors || {};
                                this.submit_btn_disabled = false;
                            }
                        }
                        return false;
                        });

                    return true;

                }catch(error){
                    console.log("wrong", error);
                    return false;
                }


            }
        },
        updated(){
            this.form.store_name = this.shop_data.name;
            this.form.store_email = this.shop_data.email;
        }
    }
</script>

<style scoped>

</style>
