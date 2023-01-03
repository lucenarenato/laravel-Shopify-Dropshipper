<template>

    <div>

    <div class="modal fade" id="ad-modal" tabindex="-1" role="dialog" aria-labelledby="ad-modal-title" aria-hidden="true" :data-backdrop="isDismissable()">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ad-modal-title" v-html="title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" v-if="dismissable">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-html="content"></div>

                    <div v-show="is_poll_script" id="poll_script" style="margin: 25px;padding-left: 25px;">
                        <a v-if="is_poll_script" class="OPP-powered-by" href="http://trailguide.net" style="text-decoration:none;"><div style="font: 9px arial; color: gray;">bike trail guide</div></a>
                    </div>

                    <div class="progress" v-if="showProgress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
                <div class="modal-footer d-flex" v-if="dismissable">
                    <span align="left" v-if="is_job_msg" style="color:blue;width:83%;">{{job_msg}}</span>
                    <a v-if="is_show_ok" type="button" class="btn btn-primary" href="/settings" v-html="okBtn"></a>
                    <button type="button" class="btn btn-secondary" v-html="dismissBtn" @click="showAppReview()"></button>
                </div>
            </div>
        </div>


<!--        &lt;!&ndash;        AppReviewModal&ndash;&gt;-->
<!--        <app-review-modal  ref="app_review"></app-review-modal>-->
    </div>
    </div>
</template>
<!--<script type="text/javascript" src="https://host1.easypolls.net/ext/scripts/emPoll.js?p=61e4f133e4b05e2a74f37122"></script>-->
<script>
    import AppReviewModal from "./AppReviewModal";
    import postscribe from 'postscribe';

    export default {
        props: ['is_show_ok', 'is_job_msg', 'job_msg', 'is_app_review','is_poll_script'],
        components: {
            AppReviewModal,postscribe
        },
        data() {
            return {
                title: '',
                content: '',
                dismissable: true,
                dismissBtn: 'Close',
                okBtn: 'Ok',
                showProgress: true,
                progress: 100,
            };
        },
        computed: {
        },
        methods: {
            showAppReview(){
                (this.is_app_review) ? this.$emit('update') : '';
                $('#ad-modal').modal('hide');
                this.is_poll_script = false;
            },
            open(mTitle, mContent, mDismissable = true, mDismissBtn = 'Close', mProgress = 0) {
                console.log("is_poll_script",this.is_poll_script);

                if(this.is_poll_script)  {
                    postscribe('#poll_script', `<script src="https://host1.easypolls.net/ext/scripts/emPoll.js?p=61e4f133e4b05e2a74f37122"><\/script>`);
                }

                this.title = mTitle;
                this.content = mContent;
                this.dismissable = mDismissable;
                this.dismissBtn = mDismissBtn;
                this.showProgress = mProgress > 0 ? true : false;
                this.progress = mProgress;
                $('#ad-modal').modal('show');
            },
            close() {
                $('#ad-modal').modal('hide');
                this.is_poll_script = false;
               // postscribe('#poll_script', ` `);
            },
            setProgress(val) {
                this.progress = val;
            },
            isDismissable() {
                return this.dismissable ? "dynamic" : "static";
            }
        },

        watch : {
            content: {
                handler: function() {
                    $('#ad-modal').modal('show');
                },
                immediate: true,
            },
        },
    }
</script>
