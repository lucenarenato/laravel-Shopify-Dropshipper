<template>
    <div class="row">
        <div style="margin: 10px;" v-if="err_msg"><p style="color: red;" v-html="err_msg"></p></div>
        <div class="custom-control custom-switch mb-3 col-md-12" style="margin-left: 13px;">
            <input type="checkbox" id="ad-auto-update" role="checkbox" class="custom-control-input" :disabled="status_disabled" v-model="value.status" @change="checkDP()">
            <label for="ad-auto-update" class="custom-control-label">Auto-update product Price and Availability
            </label>
        </div>

<!--        <div class="custom-control custom-switch mb-3 col-md-12" style="margin-left: 13px;">-->
<!--            <input type="checkbox" id="ad-save-shopify" role="checkbox" class="custom-control-input" v-model="value.save_shopify">-->
<!--            <label for="ad-save-shopify" class="custom-control-label">Update price in shopify-->
<!--            </label>-->
<!--        </div>-->

        <div v-if="show_note" style="margin: 10px;">
        <p style="color: red;" v-html="note_msg"></p>
        </div>

        <div class="dropdown float-sm-right show col-md-12">
            <label for="frequency">Frequency: </label>
            <select name="frequency" class="auto-update-dp" id="frequency" v-model="value.frequency">
                <option v-if="type == 'manager'" value="0">Update now</option>
                <option value="24" disabled>Every 24 hours</option>
                <option value="48" disabled>Every 48 hours</option>
                <option value="72" disabled>Every 72 hours</option>
                <option value="1"  disabled>Once a week</option>
            </select>
        </div>
    </div>
</template>

<script>
export default {
    props: ['value', 'type', 'err_msg', 'note_msg', 'show_note','autoupdate_status_disabled'],
    data(){
        return {
            status_disabled: false,
        }
    },
    methods:{
        checkDP(){
           if(this.value.status && this.err_msg == ''){
               document.getElementById("frequency").disabled = false;
               // document.getElementById("ad-save-shopify").disabled = false;
           }else{
               this.value.frequency = 0; //24
               document.getElementById("frequency").disabled = true;
               // document.getElementById("ad-save-shopify").disabled = true;

               if( this.err_msg != '' ){
                   // document.getElementById("ad-auto-update").disabled = true;
               }
           }
        }
    },
    updated() {
        this.checkDP();
    },
    mounted() {
        this.checkDP();

        console.log("autoupdate_status_disabled",this.autoupdate_status_disabled);

        this.status_disabled = this.autoupdate_status_disabled==true ? true : false;
    }
}
</script>

<style scoped>

</style>
