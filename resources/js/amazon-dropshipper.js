const host = process.env.MIX_APP_URL;
const apiEndPoint = host + '/api';

if(!window.jQuery)
{
    var script = document.createElement('script');
    script.type = "text/javascript";
    // script.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js";
    script.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js";
    document.getElementsByTagName('head')[0].appendChild(script);

    // <script type="text/javascript" src=""></script>
}
window.Vue = require('vue');
import axios from "axios";

const app = new Vue({
        template: '<div></div>',
        data: {
            shopifyDomain: '',
        },
        methods: {
            init() {
                let url = window.location.href;

                if (url.includes("/")) {
                    let params = this.getParams('amazon-dropshipper');
                    this.shopifyDomain = params['shop'];
                    let handle = url.split('/').pop();
                    if (url.includes("/products/")) {
                        this.checkAffiliate(handle);
                    }
                }
            },
            checkAffiliate(handle) {
                let base = this;
                let reviewClass = document.getElementsByClassName('dropshipping-product-review');
                let is_review_class = (reviewClass.length) ? true : false;
                let aPIEndPoint = `${apiEndPoint}/check-affiliate?shop=${base.shopifyDomain}&handle=` + handle + `&is_review_class=` + false;
                axios.get(aPIEndPoint)
                    .then(res => {
                        let data = res.data.data;
                        if(data.is_product){


                            if(data.show_data){
                                let anode = document.createElement("a");
                                if( data.style == 0 ){
                                    anode.innerHTML = data.show_data;
                                }else if( data.style === "1" || data.style === "2" ){
                                    let imgd = document.createElement('img');
                                    imgd.src = host + data.show_data;
                                    imgd.style = "width:150px;";
                                    anode.appendChild(imgd);
                                }
                                anode.href = data.redirect;
                                anode.target = '_blank';
                                let cart = document.getElementsByName('add');
                                let frms = document.getElementsByTagName('form');

                                if( cart.length >= 1 ){

                                    console.log("1");

                                    if(data.style == 0){
                                        let cartClass = cart[0].className;
                                        anode.className = cartClass;
                                        cart[0].replaceWith(anode);
                                        // cart[0].replaceChild(anode, cart[0].childNodes[1]);

                                    }else{
                                        let parent = cart[0].parentNode;
                                        cart[0].replaceWith(anode);
                                        //  parent.replaceChild(anode, parent.childNodes[1]);

                                    }
                                    document.getElementsByClassName('shopify-payment-button')[0].style.visibility = 'hidden';


                                }else{

                                    console.log("0");


                                    document.getElementsByClassName('shopify-payment-button')[0].style.visibility = 'hidden';
                                    for (var i = 0; i < frms.length; i++) {
                                        let ac = 'https://' + base.shopifyDomain + '/cart/add';
                                        if(frms[i].action === ac){
                                            let btns = $(frms[i]).find('button');
                                            for (var i = 0; i < btns.length; i++) {
                                                if( btns[i].type === 'submit' ){
                                                    if(data.style === 0){
                                                        let btnClass = btns[i].className;
                                                        anode.className = btnClass;
                                                        // btns[i].replaceWith(anode);
                                                        btns[i].replaceChild(anode, btns[i].childNodes[1]);
                                                    }else{
                                                        $( btns[i] ).replaceWith(anode );
                                                    }
                                                }
                                                else{
                                                    btns[i].style.visibility = 'hidden';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // display product review
                        if(is_review_class) {
                            base.addBootstrap();
                            $('.dropshipping-product-review').html(res.data.review);
                        }
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },
            addBootstrap(){
                let jqueryV = $().jquery;
                let v = jqueryV.charAt(0);
                if( v >= 3 ){
                    var jscript = document.createElement('script');
                    jscript.type = "text/javascript";
                    jscript.src = "https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js";
                    document.getElementsByTagName('head')[0].appendChild(jscript);

                }

                var bootstrap_enabled = (typeof $().modal == 'function');
                if( !bootstrap_enabled ){
                    // add bootstrap.min.css
                    var style = document.createElement('link');
                    style.type = "text/css";
                    style.rel = "stylesheet";
                    style.href = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css";
                    document.getElementsByTagName('head')[0].prepend(style);

                    // add bootstrap.min.js
                    var script = document.createElement('script');
                    script.type = "text/javascript";
                    script.src = "http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js";
                    document.getElementsByTagName('head')[0].appendChild(script);
                }
                // <script type="text/javascript" src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
                // <link type="text/css" rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
            },
            getParams(script_name) {
                // Find all script tags
                var scripts = document.getElementsByTagName("script");
                // Look through them trying to find ourselves
                for (var i = 0; i < scripts.length; i++) {
                    if (scripts[i].src.indexOf("/" + script_name) > -1) {
                        // Get an array of key=value strings of params
                        var pa = scripts[i].src.split("?").pop().split("&");
                        // Split each key=value into array, the construct js object
                        var p = {};
                        for (var j = 0; j < pa.length; j++) {
                            var kv = pa[j].split("=");
                            p[kv[0]] = kv[1];
                        }
                        return p;
                    }
                }

                // No scripts match

                return {};
            }
        },
        created() {
            this.init();
        }
    })
;

Window.crawlapps_order_delivery = {
    init: function () {
        app.$mount();
    },
};

window.onload = Window.crawlapps_order_delivery.init();
