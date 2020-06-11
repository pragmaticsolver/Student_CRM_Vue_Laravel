<template>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="float-left col-sm-6 px-0">
                    <h2 class="float-left">{{ __('messages.payments') }}</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12 m-0 p-0">{{ __('messages.filter-by-payment-received-at') }}:</div>
                <br>
                <div class="col-sm-3 ml-0 pl-0">
                    {{ __('messages.from') }}:
                    <b-form-datepicker 
                        v-model="filter_from_date"
                        @input="fromDateSelected"
                        :date-format-options="{ year: 'numeric', month: 'long', day: 'numeric' }"
                    ></b-form-datepicker>
                </div>
                <div class="col-sm-3 ml-0 pl-0">
                    {{ __('messages.to') }}:
                    <b-form-datepicker 
                        v-model="filter_to_date"
                        @input="toDateSelected"
                        :min="filter_from_date"
                        :date-format-options="{ year: 'numeric', month: 'long', day: 'numeric' }"
                    ></b-form-datepicker>
                </div>
            </div>
            <div class="row">
                <h3>{{ __('messages.total-payments-received') }}: {{ payments_sum }}</h3>
            </div>

            <div class="row my-1" v-if="records.length > 0">
                <div class="col-md-6 mx-0 px-0 align-self-center">
                    {{ __('messages.displaying') }} {{displayingRecordsFrom }} {{ __('messages.to') }} {{ displayingRecordsTo }} {{ __('messages.of') }} {{ total_records }} {{ __('messages.records') }}
                </div>
                <div class="col-md-6 mx-0 px-0">
                    <b-pagination
                        @change="navigatePage"
                        v-model="filter_page"
                        :total-rows="total_records"
                        :per-page="per_page"
                        aria-controls="my-table"
                    ></b-pagination>
                </div>
            </div>
            <div class="row" v-if="!isLoading && records.length > 0">
                <table class='table table-striped'>
                    <thead>
                        <tr>
                            <th>{{ __('messages.student') }}</th>
                            <th>{{ __('messages.payment-for') }}</th>
                            <th>{{ __('messages.paymentamount')}}</th>
                            <th>{{ trans('messages.paymentmemo')}}</th>
                            <th>{{ trans('messages.payment-method') }}</th>
                            <th>{{ trans('messages.payment-status') }}</th>
                            <th>{{ trans('messages.payment-received-at') }}</th>
                            <th>{{ trans('messages.created-at') }}</th>
                            <th>{{ trans('messages.updated-at') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="payment in records"
                            :key="payment.id"
                            >
                            <td>
                                <a :href="studentProfileUrl(payment.student.id)" target="_blank">{{ payment.student.fullname }}</a>
                            </td>
                            <td>{{ payment.payment_for }}</td>
                            <td>{{ payment.price }}</td>
                            <td>{{ payment.memo }}</td>
                            <td>{{ payment.display_payment_method }}</td>
                            <td>{{ payment.display_status }}</td>
                            <td>{{ payment.payment_recieved_at }}</td>
                            <td>{{ payment.created_at }}</td>
                            <td>{{ payment.updated_at }}</td>
                            <td>
                                <template v-if="payment.stripe_invoice_url">
                                    <a :href="payment.stripe_invoice_url" class="btn btn-sm btn-info my-1" target="_blank">{{ __('messages.view-stripe-invoice') }}</a>
                                    <button type="button" class="btn btn-sm btn-primary btn my-1" 
                                    @click="copyToClipboard(payment.stripe_invoice_url, $event)" >{{ __('messages.copy-stripe-invoice-url') }}</button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row" v-if="isLoading">
                <div class="col-sm-12 text-center">
                    <b-spinner label="Spinning" class="preloader"></b-spinner>
                </div>
            </div>
            <div class="row" v-if="!isLoading && records.length == 0">
                <div class="col-sm-12">
                    <p class="text-center">{{ trans('messages.no-records-found') }}</p>
                </div>
            </div>

            <div class="row my-1" v-if="records.length > 0">
                <div class="col-md-6 mx-0 px-0 align-self-center">
                    {{ __('messages.displaying') }} {{displayingRecordsFrom }} {{ __('messages.to') }} {{ displayingRecordsTo }} {{ __('messages.of') }} {{ total_records }} {{ __('messages.records') }}
                </div>
                <div class="col-md-6 mx-0 px-0">
                    <b-pagination
                        @change="navigatePage"
                        v-model="filter_page"
                        :total-rows="total_records"
                        :per-page="per_page"
                        aria-controls="my-table"
                    ></b-pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: [ 'filter' ],
    data: function(){
        return {
            records: [],
            isLoading: false,
            total_records: 0,
            last_page: 0,
            per_page: 0,
            current_page: 0,
            payments_sum : 0,
            filter_page: 1,
            filter_from_date: null,
            filter_to_date: null
        }
    },
    created: function(){
        if(this.filter)
        {
            if(this.filter.page)
            {
                this.filter_page = this.filter.page;
            }
            if(this.filter.from_date)
            {
                this.filter_from_date = this.filter.from_date;
            }
            if(this.filter.to_date)
            {
                this.filter_to_date = this.filter.to_date;
            }
        }
        this.fetchRecords();
    },
    computed: {
        displayingRecordsFrom(){
            return (( this.current_page -1 ) *  this.per_page ) + 1;
        },
        displayingRecordsTo(){
            return this.displayingRecordsFrom - 1 + this.records.length;
        }
    },
    methods: {
        fetchRecords: function(){
            this.isLoading = true;
            let query = {
                page: this.filter_page,
                from_date: this.filter_from_date,
                to_date: this.filter_to_date
            };
            axios.get(route('accounting.payments.records',  query).url())
                .then(res => {
                    this.isLoading = false;
                    let data = res.data;
                    this.records = data.records;
                    this.total_records = data.total_records;
                    this.last_page = data.last_page;
                    this.per_page = data.per_page;
                    this.current_page = data.current_page;
                    this.payments_sum = data.payments_sum;
                    this.filter_page = this.current_page;
                })
                .catch(error => {
                    vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                    throw error;
                });
        },
        studentProfileUrl(id){
            return route('student.show', id).url();
        },
        navigatePage(page){
            if(this.filter_page != page)
            {
                this.filter_page = page;
                this.fetchRecords();
            }
        },
        fromDateSelected(date){
            if(this.filter_to_date < this.filter_from_date)
            {
                this.filter_to_date = this.filter_from_date;
            }
            this.filter_page = 1;
            this.fetchRecords();
        },
        toDateSelected(){
            this.filter_page = 1;
            this.fetchRecords();
        }
    }
}
</script>


<style scoped>
    ::v-deep .pagination.b-pagination{
        margin-bottom: 0px !important;
    }
</style>