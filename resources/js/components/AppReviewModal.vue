<template>
    <div class="modal fade" id="app-review-modal" tabindex="-1" role="dialog" aria-labelledby="ad-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ad-modal-title">Leave us your Review!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="app-review-body">
                        <p>Do you think we are doing a good job?</p>
                        <p>Then please click below and leave us your review in shopify.</p>
                        <div class="app-review-body-link">
                            <a class="app-review-link" @click="linkClicked()" target="_blank">
                                <a class="a" href="javascript:void(0)">CLICK HERE TO LEAVE A REVIEW</a>
                                <div class="app-review-images a">
                                    <img src="/images/icon.png" class="review-app-icon"/>
                                    <img src="/images/review/star.png" class="review-app-star"/>
                                </div>
                            </a>
                        </div>
                        <p>We will continue enhancing our app and creating some more cool stuff for you.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AppReviewModal",
    props: ['is_clicked'],
    methods: {
        open() {
            $('#app-review-modal').modal('show');
        },
        close() {
            $('#app-review-modal').modal('hide');
        },
        linkClicked(){
            if( !this.is_clicked ){
                axios.get('/link-clicked').then((res) => {
                    if (res.data.success) {
                        window.open('https://apps.shopify.com/amazone-dropshipper#modal-show=ReviewListingModal', '_blank');
                    }
                });
            }else{
                window.open('https://apps.shopify.com/amazone-dropshipper#modal-show=ReviewListingModal', '_blank');
            }
        }
    },
}
</script>

<style scoped>

</style>
