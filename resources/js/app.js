/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('modal', require('./components/Modal.vue').default);
Vue.component('plan-upgrade-modal', require('./components/PlanUpgradeModal.vue').default);
Vue.component('alerts-manager', require('./components/AlertsManager.vue').default);
Vue.component('product-importer', require('./components/ProductImporter.vue').default);
Vue.component('products-manager', require('./components/ProductsManager.vue').default);
Vue.component('orders-manager', require('./components/OrdersManager.vue').default);
Vue.component('settings-manager', require('./components/SettingsManager.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});

// Enable Bootstrap tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

// Paste from Clipboard
async function paste() {
    var pasteTarget = document.querySelector("#ad-product-url");
    pasteTarget.focus();
    const text = await navigator.clipboard.readText();
    pasteTarget.value = text;

    // Needed for the change to get tracked by Vue.js components
    pasteTarget.dispatchEvent(new Event('input'));
}

let pasteBtn = document.querySelector("#ad-paste-btn");

if (typeof pasteBtn !== 'undefined' && pasteBtn !== null) {
    pasteBtn.addEventListener("click", paste);
}
