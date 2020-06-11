<template>
   <div>
       <highcharts :options="chartOptions" ></highcharts>
        <div class="text-center m-1">
            <div class="btn-group" role="group">
                <button class="btn" @click="timeline = 'year'" :class="{ 'btn-primary' : timeline == 'year'}">{{ trans('messages.year') }}</button>
                <button class="btn" @click="timeline = 'month'" :class="{ 'btn-primary' : timeline == 'month'}">{{ trans('messages.month') }}</button>
                <button class="btn" @click="timeline = 'week'" :class="{ 'btn-primary' : timeline == 'week'}">{{ trans('messages.week') }}</button>
                <button class="btn" @click="timeline = 'day'" :class="{ 'btn-primary' : timeline == 'day'}">{{ trans('messages.day') }}</button>
            </div>
        </div>
        <div v-show="timeline == 'day'" class="text-center m-1">
            <form class="form-inline justify-content-center">
                <div class="form-group m-2">
                    <label class="mr-1">{{ trans('messages.from') }}: </label>
                    <input type="date" class="form-control"  v-model="dayFilterFrom">
                </div>
                <div class="form-group m-2">
                    <label class="mr-1">{{ trans('messages.to') }}: </label>
                    <input type="date" class="form-control"  v-model="dayFilterTo">
                </div>
            </form>
        </div>
   </div>
</template>

<script>
import { Chart } from 'highcharts-vue';
import axios from 'axios';
var moment = require('moment-timezone');

export default {
    props: ['timezone'],
    components: {
        highcharts: Chart,
    },
    data() {
        let vm = this;
        return {
            rawChartObject : {},
            chartOptions : {
                chart: {
                    events: {
                        load() {
                            vm.rawChartObject = this;
                            vm.fetchChartData();
                        }
                    }
                },
                title: {
                    text: trans('messages.attendances')
                },
                yAxis: {
                    title: {
                        text: trans('messages.number-of-attendances')
                    }
                },
                xAxis: {
                    categories: []
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                series: [],
            },
            timeline: 'month',
            isLoading: false,
            dayFilterFrom: moment().tz(this.timezone).subtract(30, 'days').format('YYYY-MM-DD'),
            dayFilterTo: moment().tz(this.timezone).format('YYYY-MM-DD')
        }
    },
    computed: {
        requestData : function(){
            let temp = {};
            temp.timeline = this.timeline;
            if(this.timeline == 'day'){
                temp.dayFilterFrom = this.dayFilterFrom,
                temp.dayFilterTo = this.dayFilterTo
            }
            return temp;
        }
    },
    watch: {
        requestData: function(){
            this.fetchChartData();
        },
        isLoading: function(newVal){
            if(this.rawChartObject)
            {
                if(newVal)
                {
                    this.rawChartObject.showLoading();
                }
                else
                {
                    this.rawChartObject.hideLoading();

                    // to fix the issue sometimes parent width is not considered by highchart
                    this.rawChartObject.reflow(); // Call reflow to adjust chart width to parent container
                }
            }
        }
    },
    methods: {
        fetchChartData: function(){
            this.isLoading = true;
            axios.get(route('stats_data.attendances').url(), {
                params: this.requestData
            })
            .then(res => {
                let data = res.data;
                this.chartOptions.xAxis.categories = data.xaxis_categories;
                this.chartOptions.series = data.series;
                this.isLoading = false;
            });
        }
    }
}
</script>
