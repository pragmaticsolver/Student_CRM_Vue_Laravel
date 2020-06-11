<template>
    <div class="row">
        <div class="col-12">
            <p class="mt-1 mb-0"><em>{{ trans('messages.statistics') }}</em></p>
        </div>
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="stats-card card bg-success text-white h-100">
                <div class="card-body bg-success">
                    <h6 class="text-uppercase">{{ trans('messages.reservations') }}</h6>
                    <h1 class="display-4">{{ reservations }}</h1>
                    <i class="fa fa-spinner fa-spin card-spinner" v-show="isLoading"></i>
                    <div class="percentage_block h4">
                        <i class="fa" v-bind:class="[ reservations_percentage >= 0 ? 'fa-chevron-circle-up' : 'fa-chevron-circle-down']"></i> {{ Math.abs(reservations_percentage) }}%
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="stats-card card bg-info text-white h-100">
                <div class="card-body bg-info">
                    <h6 class="text-uppercase">{{ trans('messages.students-per-class') }}</h6>
                    <h1 class="display-4">{{ students_per_class }}</h1>
                    <i class="fa fa-spinner fa-spin card-spinner" v-show="isLoading"></i>
                    <div class="percentage_block h4">
                        <i class="fa" v-bind:class="[ students_per_class_percentage >= 0 ? 'fa-chevron-circle-up' : 'fa-chevron-circle-down']"></i> {{ Math.abs(students_per_class_percentage) }}%
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="stats-card card bg-danger text-white h-100">
                <div class="card-body bg-danger">
                    <h6 class="text-uppercase">{{ trans('messages.cancels') }}</h6>
                    <h1 class="display-4">{{ cancels }}</h1>
                    <i class="fa fa-spinner fa-spin card-spinner" v-show="isLoading"></i>
                    <div class="percentage_block h4">
                        <i class="fa" v-bind:class="[ cancels_percentage >= 0 ? 'fa-chevron-circle-up' : 'fa-chevron-circle-down']"></i> {{ Math.abs(cancels_percentage) }}%
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 py-2">
            <div class="stats-card card bg-warning text-black h-100">
                <div class="card-body bg-warning">
                    <a v-bind:href="statsPageUrl" style="text-decoration:none;color:#00050a;">
                        <h6 class="text-uppercase">{{ trans('messages.view-full-stats') }}</h6>
                        <h1 class="display-4"><i class="fa fa-arrow-right"></i></h1>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import 'axios';

export default {
    props: ['date', 'selected_teachers'],
    data : function(){
        return {
            reservations: 0,
            reservations_percentage: 0,
            students_per_class: 0,
            students_per_class_percentage: 0,
            cancels: 0,
            cancels_percentage: 0,
            isLoading: 0,
            statsPageUrl: route('stats')
        };
    },
    computed: {
        filter_data: function(){
            return {
                date: this.date,
                selected_teachers: this.selected_teachers
            }
        }
    },
    watch: {
        filter_data: function(){
            this.fetchData();
        }
    },
    methods: {
        fetchData: function(){
            this.isLoading = 1;
            axios.get(route('daily_stats').url(), {
                params: this.filter_data
            })
            .then(res => {
                this.reservations = res.data.reservations;
                this.reservations_percentage = res.data.reservations_percentage;
                this.students_per_class = res.data.students_per_class;
                this.students_per_class_percentage = res.data.students_per_class_percentage;
                this.cancels = res.data.cancels;
                this.cancels_percentage = res.data.cancels_percentage;
                this.isLoading = 0;
            });
        }
    },
    created: function(){
        this.fetchData();
    }
}
</script>

<style scoped>
    .percentage_block{
        position:absolute;right:10px;bottom:10px;
    }
    .stats-card {
        position:reltive;
    }
    .card-spinner {
        position:absolute;right:10px;top:10px;
    }
</style>
