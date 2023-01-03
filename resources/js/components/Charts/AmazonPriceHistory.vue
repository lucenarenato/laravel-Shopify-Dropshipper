<template>
    <div>
        <apex-chart  type="area" :options="options" :series="series"></apex-chart>
    </div>
</template>

<script>

    import ApexChart from 'vue-apexcharts';


    let yaxis_pre_labels = "";

    export default {
        name: "apexchart",
        components: {
            ApexChart
        },
        props: ['displayData', 'label','currency_symbol'],
        data(){
            return{
                options: {
                    chart: {
                        type: 'area',
                        width: "100%",
                        height: 380,
                    },
                    xaxis: {
                        categories: this.label,
                        labels: {
                            show: true,
                            showDuplicates: false,
                            style: {
                                colors: [],
                                fontSize: '10px',
                                fontFamily: 'Helvetica, Arial, sans-serif',
                                fontWeight: 400,
                                cssClass: 'apexcharts-xaxis-label',
                            },
                        },
                    },
                    yaxis: {
                        labels: {
                            /**
                             * Allows users to apply a custom formatter function to yaxis labels.
                             *
                             * @param { String } value - The generated value of the y-axis tick
                             * @param { index } index of the tick / currently executing iteration in yaxis labels array
                             */
                            formatter: function(val, index) {
                                return yaxis_pre_labels+" "+val.toFixed(2);
                            }
                        }
                    },
                    stroke: {
                        curve: 'stepline'
                    },
                    grid: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    responsive: [{
                        breakpoint: undefined,
                        options: {},
                    }],

                    colors:['rgb(255, 165, 0)', 'rgb(255, 165, 0)', 'rgb(255, 165, 0)'],
                    fill: {
                        colors: ['rgb(255, 165, 0)', 'rgb(255 165 0 / 41%)', 'rgb(255 165 0 / 41%)']
                    },
                    // dataLabels: {
                    //     style: {
                    //         colors: ['#000', '#000', '#000']
                    //     }
                    // },
                    //
                    // theme: {
                    //     monochrome: {
                    //         enabled: false,
                    //         color: '#88d',
                    //         shadeTo: 'light',
                    //         shadeIntensity: 0
                    //     }
                    // }
                },
                series: [
                    {
                        name: 'Amazon Price ',
                        data: this.displayData
                    }
                ],

            }
        },

        created() {
            yaxis_pre_labels = this.currency_symbol;
        }
    }
</script>

<style scoped>

</style>
