<template>
    <li class="c-header-nav-item dropdown d-md-down-none mx-2" v-if="alerts.length"><a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="far fa-bell fa-lg"></i><span class="badge badge-pill badge-danger" v-if="newAlertsNum">{{ newAlertsNum }}</span></a>
        <div class="dropdown-menu dropdown-menu-lg pt-0">
            <div class="dropdown-header bg-light"><strong>You have {{ newAlertsNum }} new notifications</strong></div><a v-for="a in alerts"
                :class="'dropdown-item ' + getAlertClass()" href="/help/announcements" v-html="a.heading" @click="setLastAlertID()"></a>
        </div>
    </li>
</template>

<script>
    import VueCookies from 'vue-cookies';

    export default {
        props: { },
        data() {
            return {
                alerts: [],
                lastReadID: 0,
                maxAlertID: 0,
                newAlertsNum: 0
            };
        },
        computed: { },
        methods: {
            getAlerts() {

                // Get last read alert id
                this.lastReadID = Number(VueCookies.get("ad_last_read_alert"));
                console.log("Last read ID: " + this.lastReadID);

                axios
                    .get('/js/alerts.json')
                    .then((res) => {
                        this.alerts = res.data.reverse();
                        for (let i = 0; i < this.alerts.length; i++) {

                            let alertIDNum = Number(this.alerts[i].id);

                            if (alertIDNum > this.lastReadID) {
                                this.newAlertsNum++;
                            }

                            if (alertIDNum > this.maxAlertID) {
                                this.maxAlertID = alertIDNum;
                            }
                        }
                        console.log("New alerts: " + this.newAlertsNum);
                        console.log("Max alert id: " + this.maxAlertID);
                    });
            },
            setLastAlertID() {
                console.log("Dropdown click");
                if (this.newAlertsNum > 0)
                {
                    VueCookies.set("ad_last_read_alert", "" + this.maxAlertID, Infinity);  // never expire
                }
            },
            getAlertClass(id) {
                return id > this.lastReadID ? 'text-danger' : '';
            }
        },
        mounted() {
            console.log('Component mounted.');
            this.getAlerts();
        }
    }
</script>
