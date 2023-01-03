
<!--<product-services></product-services>-->

<template>
    <div>
        <div id="services_section">
        <div class="row">

            <div class="col-md-12">
                <div class="row  justify-content-md-center">
                <div v-for="(service, index) in services" :key="index" class="col-md-3">
                    <div class="service-block">
                    <a class="service-text-link" :href="service.link" :target="service.link=='#' ? '_self' : '_blank'" @click="productServicesModal(service.name,service.content)">
                    <img :src="`${service.image_url}`" alt="Go with Amazon/Walmart
      dropship or Amazon Affiliates" class="img-fluid">
                <p class="service-text" v-html="service.name"></p>
                    </a>
                    </div>
                </div>
            </div>
            </div>
        </div>

      <!--    START:: product_services_modal-->

        <div class="modal fade" id="product_services_modal"  role="dialog" aria-labelledby="product_services_modal_title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="product_services_modal_title">Service Title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="product_services_modal_body">
                        Service Content
                    </div>
                </div>
            </div>
        </div>

        <!--    END:: product_services_modal-->
        </div>

    </div>
</template>

<script>

    export default {
        name: "ProductServices",
        components: {

        },
        props: ['page_contents'],
        data(){
            return{
                service_name:'',
                service_content:'',
                services : [
                    {
                        image_url: this.page_contents.import_service1_image,
                        link: this.page_contents.import_service1_link,
                        name: this.page_contents.import_service1_text,
                        content: "Go with Amazon/Walmart </br>" +
                            "                    dropship or Amazon Affiliates"
                    },
                    {
                       image_url: this.page_contents.import_service2_image,
                        link: this.page_contents.import_service2_link,
                       name:this.page_contents.import_service2_text,
                        content:"Set automatic price update </br>" +
                            "                    for Amazon imported products"
                    },
                    {
                        image_url:this.page_contents.import_service3_image,
                        link: this.page_contents.import_service3_link,
                            name:this.page_contents.import_service3_text,
                        content:"Import Amazon </br>" +
                            "                    product reviews"
                    },
                    {
                        image_url: this.page_contents.import_service4_image,
                        link: this.page_contents.import_service4_link,
                            name: this.page_contents.import_service4_text,
                        content:"Import with one click and </br>" +
                        "                    semi-automate orders (soon)"
                    }
                ]
            }
        },
        mounted() {
            console.log('Product Services Component mounted.')
        },
        methods: {
            productServicesModal(name,content) {

                console.log("Product servies model open",content);
                this.service_name =name;
                this.service_content = content;
               // $('#product_services_modal').modal('show');
            },

        }
    }
</script>

<style scoped>

</style>
