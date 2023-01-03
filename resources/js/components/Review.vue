<template>
    <div class="product-reviews-main">
        <div class="product-review-data" v-for="(review, index) in reviews" v-if="review.title">
            <div class="review-profile">
                <img v-if="review.profile.id" :src="`https://www.amazon.com/avatar/default/amzn1.account.` + review.profile.id + `?square=true&max_width=460`" alt="Profile" class="review-profile-img"/>
                <img v-else src="/images/static/no-user.jpg" alt="Profile" class="review-profile-img"/>
                <p v-if="review.profile">{{ review.profile.name }}</p>
            </div>
            <div class="review-body">

                <p v-if="review.rating">
                    <span class="Stars" :style="`--rating:` + review.rating + `;`"></span>

                    <b>{{ review.title }}</b>
                </p>

                <span v-if="review.date" class="review-date">{{ review.date.raw }}</span>
                <span v-if="review.verified_purchase" class="review-varified"><b>Verified Purchase</b></span>
                <p v-if="review.body_html" v-html="review.body_html"></p>
                <p v-if="review.bodyHtml" v-html="review.bodyHtml"></p>
                <div class="review-images" v-if="review.images">
                    <a v-for="( review_img, index ) in review.images" :href="review_img.link" target="_blank"><img :src="review_img.link"></a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Review",
    data(){
       return{
           reviews: [],
       }
    },
    methods:{
        getReviews(id){
            console.log(id);
            axios.get('/get-reviews?id=' + id).then((res) => {
                let data = res.data.data;
                this.reviews = data.reviews;
            });
        }
    },
    mounted() {
    }
}
</script>

<style scoped>

</style>
