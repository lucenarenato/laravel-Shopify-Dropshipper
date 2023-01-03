<template>
    <div class="bestseller-product-main">
        <div class="bestseller-head">
            <img :src="page_contents.bestseller_resources_logo" alt="Resources" class="img-fluid">
            <h4>Resources:</h4>
        </div>

        <!--       START:: Category-->
        <div class="bestseller-head">
            <a  style="color:#2d2f69" @click="open_category_search(is_show_category_search);"> <i class="fas fa-play fa-lg"></i>
                {{page_contents.bestseller_resources_tab1_text ? page_contents.bestseller_resources_tab1_text : 'Search Amazon Best Seller Products ( Only Amazon US )'}}
            </a>
        </div>


        <div class="row" v-if="is_show_category_search">
            <div class="col-md-6 offset-3">
<!--                <div class="dropdown show mt-2 row">-->
<!--                    <div class="col-md-4">-->
<!--                        <label for="country" class="a d-block text-right">Select Amazon Country: </label>-->
<!--                    </div>-->
<!--                    <div class="col-md-8">-->
<!--                        <input type="text" name="country" id="country" value="United States" disabled>-->
<!--                    </div>-->
<!--                </div>-->
<!--                -->
                <!--                        <option value="amazon.com">United States</option>-->
                <!--                        <option value="amazon.com.au">Australia</option>-->
                <!--                        <option value="amazon.com.br">Brazil</option>-->
                <!--                        <option value="amazon.ca">Canada</option>-->
                <!--                        <option value="amazon.cn">China</option>-->
                <!--                        <option value="amazon.fr">France</option>-->
                <!--                        <option value="amazon.de">Germany</option>-->
                <!--                        <option value="amazon.in">India</option>-->
                <!--                        <option value="amazon.it">Italy</option>-->
                <!--                        <option value="amazon.co.jp">Japan</option>-->
                <!--                        <option value="amazon.com.mx">Mexico</option>-->
                <!--                        <option value="amazon.es">Spain</option>-->
                <!--                        <option value="amazon.co.uk">United Kingdom</option>-->
                <!--           category text box-->

                    <div class="amazon-categories">
                        <!--                main category text box-->
                            <div class="row justify-content-md-center">
                                <div class="col-md-12">
                                    <label for="category" class="a d-block text-center category-label">Select Category: </label>

                                    <select name="category" class="amazon-category-dp srch-dropdown" id="category"
                                            v-model="selected_category">
                                        <option :value="category.url" v-for="(category, index) in categories">{{ category.name }}</option>
                                    </select>
                                </div>

                                    <div class="row justify-content-md-center">
                                        <div class="col-md-12">
                                            <a :href="selected_category" target="_blank" class="btn btn-primary btn-lg btn-amazon" type="submit" id="ad-search-product-btn" v-html="page_contents.bestseller_tab1_search_btn_title ? page_contents.bestseller_tab1_search_btn_title : 'Search Best Seller <br> Products'"></a>
                                        </div>
                                    </div>
                                </div>
                    </div>
            </div>
        </div>

        <!--       START:: Category-->


        <!--       START:: Chrome Extenstion-->
        <div class="bestseller-head">
            <a   style="color:#2d2f69" @click="open_chrome_extenstion(is_show_chrome_extenstion);"><i class="fas fa-play fa-lg"></i>
                {{page_contents.bestseller_resources_tab2_text ? page_contents.bestseller_resources_tab2_text : 'Import with just one Click with our Chrome Extension'}}
            </a>
        </div>
        <div id="Card_second" class="card chrome_extenstion_section" v-if="is_show_chrome_extenstion">
            <div class="row">
                <div class="col-md-12">
                        <p>
                        {{page_contents.bestseller_tab2_text ? page_contents.bestseller_tab2_text : '"Install our Chrome Extension (soon)"'}}
                       </p>
                </div>
            </div>
        </div>

        <!--       START:: Chrome Extenstion-->

    </div>

</template>

<script>
export default {
    name: "ProductSearch",
    props: ['categories','page_contents'],
    data() {
        return {
            selected_category: '',
            is_show_category_search: false,
            is_show_chrome_extenstion: false,
        }
    },
    methods:{
       open_category_search(toggle_status){

           console.log("toggle_status",toggle_status);
             if(toggle_status==false){
                 this.is_show_category_search = true;
             }
             else{
                 this.is_show_category_search = false;
             }

           this.is_show_chrome_extenstion = false;
        },
        open_chrome_extenstion(toggle_status){
           console.log("open_chrome_extenstion");

            if(toggle_status==false){
                this.is_show_chrome_extenstion = true;
            }
            else{
                this.is_show_chrome_extenstion = false;
            }

            this.is_show_category_search =false;

        },
    },
    updated() {
        this.selected_category = ( this.selected_category == '' ) ? this.categories[0].url : this.selected_category;
    }
}
</script>

<style scoped>

</style>
