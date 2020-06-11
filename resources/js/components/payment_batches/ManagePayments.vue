<template>
    <div class="row"> 
        <div class="col-sm-12">
             <div class="row">
                    <div class="col-sm-12 px-0">
                        <h2>{{ __('messages.manage-monthly-payments-of') }}
                            <div class="col-sm-3 d-inline-block"><input name="period" type="month" class="form-control " v-model="month_year" required :disabled="isLoading"></div>
                            <b-button 
                                :disabled="isLoading"
                                @click.prevent="fetchData()"
                                variant="light" 
                                type="submit"
                                class="btn my-1 mr-1 float-right">
                                {{ __('messages.refresh') }}
                            </b-button>
                        </h2>
                    </div>
                </div>
            <template v-if="!isLoading">
                <div class="row">
                    <h4 class="float-left col-sm-6 px-0">{{ __('messages.generated-payment-records') }}</h4>
                    <div class="col-sm-6 px-0">
                        <b-button 
                            v-if="generated_payments.length > 0"
                            @click.prevent="sendMutlipleStripeInvoicesSelection()"
                            variant="primary" 
                            type="submit"
                            class="btn my-1 float-right">
                            {{ __('messages.send-stripe-invoices') }}
                        </b-button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                
                <div class="row">
                    <table class='table table-striped' v-if="generated_payments.length > 0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.student') }}</th>
                                <th>{{ trans('messages.paymentprice')}}</th>
                                <th>{{ trans('messages.paymentnumberlesson')}}</th>
                                <th>{{ trans('messages.paymentmemo')}}</th>
                                <th>{{ trans('messages.payment-method') }}</th>
                                <th>{{ trans('messages.payment-status') }}</th>
                                <th>{{ trans('messages.payment-received-at') }}</th>
                                <th>{{ trans('messages.created-at') }}</th>
                                <th>{{ trans('messages.updated-at') }}</th>
                                <th>{{ trans('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="payment in generated_payments"
                                :key="payment.id"
                                :class="{ 'rest-month-row' : payment.rest_month }"
                                >
                                <td>
                                    <a :href="studentProfileUrl(payment.student.id)" target="_blank">{{ payment.student.fullname }}</a>
                                </td>
                                <td>{{ payment.price }}</td>
                                <td>{{ payment.number_of_lessons }}</td>
                                <td>{{ payment.memo }}</td>
                                <td>{{ payment.display_payment_method }}</td>
                                <td>{{ payment.display_status }}</td>
                                <td>{{ payment.payment_recieved_at }}</td>
                                <td>{{ payment.created_at }}</td>
                                <td>{{ payment.updated_at }}</td>
                                <td>
                                    <template v-if="payment.stripe_invoice_url">
                                        <a :href="payment.stripe_invoice_url" class="btn btn-sm btn-info" target="_blank">{{ trans('messages.view-stripe-invoice') }}</a>
                                        <button type="button"
                                            class="btn btn-sm btn-primary btn my-1" 
                                            @click="copyToClipboard(payment.stripe_invoice_url, $event)">{{ trans('messages.copy-stripe-invoice-url') }}</button>
                                    </template>
                                
                                    <b-button 
                                        v-if="payment.action_btns.send_stripe_invoice"
                                        @click.prevent="sendStripeInvoice(payment.id)"
                                        variant="primary" 
                                        type="submit"
                                        class="btn btn-sm my-1"
                                        :disabled="(sending_invoices_for_payments.includes(payment.id))">
                                        {{ __('messages.send-stripe-invoice') }} <b-spinner small v-if="sending_invoices_for_payments.includes(payment.id)" label="Spinning"></b-spinner>
                                    </b-button>

                                    <button
                                        v-if="payment.action_btns.mark_as_paid"
                                        @click.prevent="markPaymentAsPaid(payment.id)"
                                        class="btn btn-sm btn-primary btn_mark_as_paid my-1"
                                        type="button"
                                    >{{ trans('messages.mark-as-paid') }}</button>

                                    <button
                                        v-if="payment.action_btns.edit_payment"
                                        @click.prevent="editPayment(payment)"
                                        class="btn btn-sm btn-warning btn_mark_as_paid my-1"
                                        type="button"
                                    >{{ trans('messages.edit') }}</button>

                                    <button
                                        v-if="payment.action_btns.delete_payment"
                                        @click.prevent="deletePayment(payment.id)"
                                        class="btn btn-sm btn-danger my-1" 
                                        type="button"
                                        :disabled="deleting_payments.includes(payment.id)"
                                    >{{ trans('messages.delete')}}
                                    <b-spinner small v-if="deleting_payments.includes(payment.id)" label="Spinning"></b-spinner>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else class="col-12 text-center">{{ __('messages.no-records-found') }}</div>
                </div>
                
                <div class="row">
                    <div class="float-left col-sm-6 px-0">
                        <h4>{{ __('messages.generate-payment-records') }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form @submit.prevent="generatePaymentRecords">
                            <div class="form-group">
                                <label for="">{{ trans('messages.select-students') }}:</label> <br>
                                
                                <div class="col-12 p-0">
                                    <input type="text" 
                                        :placeholder="trans('messages.search')" 
                                        class="d-inline-block form-control col-sm-3 align-middle" 
                                        v-model="studentSearch" 
                                        v-on:keydown.enter.prevent>
                                    <button type="button" class="d-inline-block btn btn-primary mx-1 align-middle" @click.prevent="selectAllStudents">{{ trans('messages.select-all') }}</button>
                                    <button type="button" class="d-inline-block btn btn-primary mx-1 align-middle" @click.prevent="clearStudentsSelection">{{ trans('messages.clear-selection') }}</button>
                                </div>

                                {{ selected_student_ids.length }} {{ trans('messages.selected') }} out of {{ students.length }}
                                <div class="row mt-2" style="max-height: 300px;overflow-y: auto;overflow-x: hidden;">
                                    <div class="col-sm-4" v-for="student of filteredStudents" :key="student.id">
                                        <label>
                                            <input type="checkbox" name="dates[]" v-model="selected_student_ids" :value="student.id" class="cancel_multiple_checkbox" style="width:25px;padding-right:0px;" :disabled="student.payment_record_generated">
                                            {{ student.fullname }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="20%">{{ __('messages.student') }}</th>
                                        <th width="5%">{{ __('messages.is-rest-month') }}</th>
                                        <th width="15%">{{ __('messages.price') }}</th>
                                        <th width="15%">{{ __('messages.number-of-lessons') }}</th>
                                        <th width="20%">{{ __('messages.memo') }}</th>
                                        <th width="15%">{{ __('messages.payment-method') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    <tr v-for="(student) in selectedStudents" :key="student.id">
                                        <td>
                                            <a :href="studentProfileUrl(student.id)" target="_blank">{{ student.fullname }}</a>
                                        </td>
                                        <td>
                                            <input type="checkbox" v-model="student.rest_month">
                                        </td>
                                        <td>
                                            <input
                                                v-if="!student.rest_month"
                                                type="text"
                                                class="form-control"
                                                v-model="student.payment_settings.price"
                                                required>
                                        </td>
                                        <td>
                                            <input
                                                v-if="!student.rest_month"
                                                type="text"
                                                class="form-control"
                                                v-model="student.payment_settings.no_of_lessons"
                                                required>
                                        </td>
                                        <td>
                                            <textarea 
                                                v-if="!student.rest_month"
                                                class="form-control"
                                                v-model="student.memo"
                                                ></textarea>
                                        </td>
                                        <td>
                                            <select 
                                                v-if="!student.rest_month" 
                                                v-model="student.payment_settings.payment_method" 
                                                class="form-control"
                                                required>
                                                    <option 
                                                        v-for="payment_method in payment_methods" 
                                                        :key="payment_method"
                                                        :value="payment_method.toLowerCase()" 
                                                    >{{ payment_method }}</option>
                                            </select>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col-sm-12">
                                <b-button variant="primary" type="submit"  class="btn-primary float-right"  :disabled="(!enableSubmit) || isGenerating">
                                    {{ __('messages.generate-payment-records') }} <b-spinner small v-if="isGenerating" label="Spinning"></b-spinner>
                                </b-button>
                            </div>
                        </form>
                    </div>
                </div>
        </template>
            <div class="row" v-else>
                <b-spinner label="Spinning" class="preloader"></b-spinner>
            </div>
        </div>

        <b-modal ref="markPaymentPaidModal" :title="__('messages.mark-payment-as-paid')" no-fade>
            <div slot="modal-footer">
                <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="isUpdatingPayment">{{ trans('messages.submit') }}
                    <b-spinner v-if="isUpdatingPayment" small label="Spinning"></b-spinner>
                </b-button>
                <b-button variant="secondary" @click="hideModal('markPaymentPaidModal')">{{  trans('messages.cancel') }}</b-button>
            </div>
            <form ref="my-form" @submit.prevent="markPaymentAsPaidSubmit">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">{{ trans('messages.payment-received-date')}}:</label>
                            <div class="col-lg-8">
                                <input name="date" type="date" class="form-control required" 
                                    v-model="payment_recieved.date"
                                >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">{{ trans('messages.payment-received-time') }}:</label>
                            <div class="col-lg-8">
                                <input name="time" type="time" class="form-control required" 
                                    v-model="payment_recieved.time"
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <button ref="dummy_submit" style="display:none;"></button>
            </form>
        </b-modal>

        <b-modal ref="sendStripeInvoiceModal" :title="__('messages.send-stripe-invoices')" no-fade>
            <div slot="modal-footer">
                <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="selected_payment_ids_for_sending_invoice.length == 0 || sending_mutliple_stripe_invoices">{{ trans('messages.submit') }}
                    <b-spinner v-if="sending_mutliple_stripe_invoices" small label="Spinning"></b-spinner>
                </b-button>
                <b-button variant="secondary" @click="hideModal('sendStripeInvoiceModal')">{{  trans('messages.cancel') }}</b-button>
            </div>
            <form ref="my-form" @submit.prevent="sendMultipleStripeInvoiceSubmit">
                <div class="row">
                   <table class='table table-striped' v-if="stripeInvoiceSendablePayments.length > 0">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" v-model="check_all_send_stripe_invoices" />
                                </th>
                                <th>{{ __('messages.student') }}</th>
                                <th>{{ trans('messages.paymentamount')}}</th>
                                <th>{{ trans('messages.payment-status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="payment in stripeInvoiceSendablePayments"
                                :key="payment.id"
                                :class="{ 'rest-month-row' : payment.rest_month }"
                                >
                                <td v-if="payment.action_btns.send_stripe_invoice">
                                    <input type="checkbox" :value="payment.id" v-model="selected_payment_ids_for_sending_invoice" />
                                </td>
                                <td>
                                    <a :href="studentProfileUrl(payment.student.id)" target="_blank">{{ payment.student.fullname }}</a>
                                </td>
                                <td>{{ payment.price }}</td>
                                <td>{{ payment.status }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else class="col-12 text-center">
                        {{ __('messages.there-are-no-payment-records-for-which-stripe-invoices-can-be-sent') }}
                    </p>
                </div>
                <button ref="dummy_submit" style="display:none;"></button>
            </form>
        </b-modal>

        <b-modal ref="editPyamentModal" :title="__('messages.edit-payment')" no-fade>
            <div slot="modal-footer">
                <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="isUpdatingPayment">{{ trans('messages.submit') }}
                    <b-spinner v-if="isUpdatingPayment" small label="Spinning"></b-spinner>
                </b-button>
                <b-button variant="secondary" @click="hideModal('editPyamentModal')">{{  trans('messages.cancel') }}</b-button>
            </div>
            <form ref="my-form" @submit.prevent="editPaymentSubmit">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-warning" v-if="edit_payment.show_stripe_warning">
                            <span class="fa fa-exclamation-triangle"></span>
                            {{ __('messages.details-you-update-here-will-not-be-reflected-in-stripe-invoice-if-stripe-invoice-is-already-generated-and-sent-to-customer') }}
                        </div>
                        <template v-if="edit_payment.rest_month">
                            <div class="form-group row form-section">
                                <label class="col-lg-2 col-form-label">{{ trans('messages.paymentperiod') }}:</label>
                                <div class="col-lg-10">
                                    <input name="period" type="month" class="form-control required" v-model="edit_payment.period" >
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="form-group row form-section">
                                <label class="col-lg-2 col-form-label">{{ trans('messages.paymentprice') }}:</label>
                                <div class="col-lg-10">
                                    <input name="price" type="number" class="form-control required" v-model="edit_payment.price">
                                </div>
                            </div>
                            <div class="form-group row form-section">
                                <label class="col-lg-2 col-form-label">{{ trans('messages.paymentperiod') }}:</label>
                                <div class="col-lg-10">
                                    <input name="period" type="month" class="form-control required" v-model="edit_payment.period" >
                                </div>
                            </div>
                            <div class="form-group row form-section">
                                <label class="col-lg-2 col-form-label">{{ trans('messages.paymentnumberlesson') }}:</label>
                                <div class="col-lg-10">
                                    <input name="number_of_lessons" type="number" class="form-control required" v-model="edit_payment.number_of_lessons">
                                </div>
                            </div>
                            <div class="form-group row form-section">
                                <label class="col-lg-2 col-form-label">{{ trans('messages.paymentmemo') }}:</label>
                                <div class="col-lg-10">
                                    <textarea name="memo" class="form-control" v-model="edit_payment.memo"></textarea>
                                </div>
                            </div>
                            <div class="form-group row form-section">
                                <label class="col-lg-2 col-form-label">{{ trans('messages.paymentmethod') }}:</label>
                                <div class="col-lg-10">
                                    <select name="payment_method" id="payment_method" class="form-control" required v-model="edit_payment.payment_method">
                                        <option 
                                            v-for="payment_method in payment_methods" 
                                            :key="payment_method"
                                            :value="payment_method.toLowerCase()" 
                                        >{{ payment_method }}</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <button ref="dummy_submit" style="display:none;"></button>
            </form>
        </b-modal>
    </div>
</template>

<script>
    import axios from 'axios';
    import _ from 'lodash';

    export default {
        props: ['initial_month_year'],
        data: function(){
            return {
                month_year: this.initial_month_year,           
                payment_methods: [],
                students: [],
                isLoading: false,
                isGenerating: false,
                isUpdatingPayment: false,
                selected_student_ids: [],
                studentSearch: '',
                generated_payments: [],
                date: '',
                time: '',
                payment_recieved: {
                    id: null,
                    date: null,
                    time: null
                },
                form_errors: [],
                sending_invoices_for_payments: [],
                sending_mutliple_stripe_invoices: false,
                selected_payment_ids_for_sending_invoice: [],
                check_all_send_stripe_invoices: false,
                edit_payment: {},
                deleting_payments: [],
                selectedStudents: [],
            }
        },
        created: function(){
            this.fetchData();
        },
        watch: {
            check_all_send_stripe_invoices: function(){
                if(this.check_all_send_stripe_invoices)
                {
                    let temp = [];
                    this.stripeInvoiceSendablePayments.forEach(function(payment){
                        temp.push(payment.id);
                    });

                    this.selected_payment_ids_for_sending_invoice = temp;
                }
                else
                {
                    this.selected_payment_ids_for_sending_invoice = [];
                }
            },
            selected_student_ids: function(){

                let existingRecords = _.cloneDeep(this.selectedStudents);

                let allSelectedStudents = _.cloneDeep(this.students.filter((student) => {
                    return this.selected_student_ids.includes(student.id) ? true : false;
                }));

                let finalStudents = allSelectedStudents.map(function(obj){
                    return existingRecords.find(o => o.id === obj.id) || obj;
                });

                this.selectedStudents = _.cloneDeep(finalStudents);
            },
            month_year: function() {
                this.fetchData();
            }
        },
        computed: {
            filteredStudents: function(){
                const searchRegex = RegExp(this.studentSearch,'ig');
                const students = this.students.filter((student) => {
                    return searchRegex.test(student.fullname);
                });

                const final_students = [];
                students.forEach((student) => {
                    const index = this.generated_payments.findIndex((payment) => {
                        return payment.student.id == student.id
                    })

                    // Remove from selection
                    if (index >= 0) {
                        this.selected_student_ids = this.selected_student_ids.filter((student_id) => student_id != student.id)
                    }

                    student.payment_record_generated = index >= 0 ? true : false;
                    final_students.push(student)
                })

                return final_students;
            },
            enableSubmit: function(){
                return this.selected_student_ids.length > 0 ? true : false;
            },
            stripeInvoiceSendablePayments: function(){
                return this.generated_payments.filter(payment => payment.action_btns.send_stripe_invoice);
            }
        },
        methods: {
            fetchData: function(){
                let vm = this;
                this.isLoading = true;
                axios.get(route('manage.monthly.payments.data', this.month_year).url())
                    .then(res => {
                        this.isLoading = false;
                        let data = res.data;
                        this.students = data.students;
                        this.payment_methods = data.payment_methods;
                        this.date = data.date;
                        this.time = data.time;
                        this.generated_payments = data.generated_payments;
                    })
                    .catch(error => {
                        vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                        throw error;
                    });
            },
            generatePaymentRecords(){
                this.isGenerating = true;
                let vm = this;

                let form_data = {};
                let payments = [];
                this.selectedStudents.forEach(function(student){
                    let temp = {}
                    temp.customer_id = student.id;
                    temp.rest_month = student.rest_month ? 1 : 0;
                    if(!temp.rest_month)
                    {
                        temp.price = student.payment_settings.price;
                        temp.no_of_lessons = student.payment_settings.no_of_lessons;
                        temp.memo = student.memo ? student.memo : "",
                        temp.payment_method = student.payment_settings.payment_method 
                    }
                    payments.push(temp);
                });

                let data = {
                    month_year: this.month_year,
                    payments: payments
                };
                axios.post(route('manage.monthly.payments.generate.records').url(), data)
                    .then(res => {
                        let data = res.data;

                        if (data.status == 1) {
                            vm.showMessage('success',data.message);
                            vm.fetchData();
                            this.clearStudentsSelection();
                            this.studentSearch = '';
                            this.isGenerating = false;
                        } else {
                            vm.showError(data.message || trans('messages.something-went-wrong'));
                            this.isGenerating = false;
                        } 
                    })
                    .catch(error => {
                        vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                        this.isGenerating = false;
                    });
            },
            studentProfileUrl(id){
                return route('student.show', id).url();
            },
            selectAllStudents() {
                let temp = [];
                this.students.forEach(function(student){
                    temp.push(student.id);
                });
                this.selected_student_ids =  temp;  
            },
            clearStudentsSelection() {
                this.selected_student_ids = [];   
            },
            deletePayment(id) {
                let vm = this;
                this.$swal.fire({
                    title: trans('messages.are-you-sure'),
                    text: trans('messages.you-wont-be-able-to-revert-this'),
                    confirmButtonText: trans('messages.yes-i-sure'),
                    cancelButtonText: trans('messages.cancel'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then(function (result) {
                    if (result.value) {
                        vm.deleting_payments.push(id);
                        axios.delete(route('monthly.payment.destroy', id).url())
                            .then(res => {
                                let data = res.data;
                                vm.showMessage('success',data.message);
                                vm.paymentDeleted(id);
                                vm.deleting_payments = vm.deleting_payments.filter(item_id => item_id !== id);
                            })
                            .catch(error => {
                                vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                                vm.deleting_payments = vm.deleting_payments.filter(item_id => item_id !== id);
                                throw error;
                            });
                    }
                });
            },
            sendStripeInvoice(payment_id) {
                let vm = this;
                this.$swal.fire({
                    title: trans('messages.are-you-sure'),
                    text: trans('messages.you-wont-be-able-to-revert-this'),
                    confirmButtonText: trans('messages.yes-i-sure'),
                    cancelButtonText: trans('messages.cancel'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then(function (result) {
                    if (result.value) {
                        vm.sending_invoices_for_payments.push(payment_id);
                        let data = {
                            payment_id: payment_id
                        };
                        axios.post(route('payment.send.stripe.invoice').url(), data)
                            .then(res => {
                                let data = res.data;
                                vm.showMessage('success',data.message);
                                vm.paymentUpdated(data.payment);
                                vm.sending_invoices_for_payments = vm.sending_invoices_for_payments.filter(item_id => item_id !== payment_id);
                            })
                            .catch(error => {
                                vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                                vm.sending_invoices_for_payments = vm.sending_invoices_for_payments.filter(item_id => item_id !== payment_id);
                                throw error;
                            });
                    }
                });
            },
            sendMutlipleStripeInvoicesSelection(){
                this.sending_mutliple_stripe_invoices = false;
                this.selected_payment_ids_for_sending_invoice = [];
                this.check_all_send_stripe_invoices = false;
                this.showModal('sendStripeInvoiceModal');
            },
            sendMultipleStripeInvoiceSubmit(){
                let vm = this;
                this.$swal.fire({
                    title: trans('messages.are-you-sure'),
                    text: trans('messages.you-wont-be-able-to-revert-this'),
                    confirmButtonText: trans('messages.yes-i-sure'),
                    cancelButtonText: trans('messages.cancel'),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then(function (result) {
                    if (result.value) {
                        vm.sending_mutliple_stripe_invoices = true;
                        let data = {
                            payment_ids: vm.selected_payment_ids_for_sending_invoice
                        };
                        axios.post(route('payment.send.multiple.stripe.invoice').url(), data)
                            .then(res => {
                                let data = res.data;
                                vm.showMessage('success',data.message);
                                data.payments.forEach(function(payment){
                                    vm.paymentUpdated(payment);
                                });
                                vm.sending_mutliple_stripe_invoices = false;
                                vm.selected_payment_ids_for_sending_invoice = [];
                                vm.check_all_send_stripe_invoices = false;
                                vm.hideModal('sendStripeInvoiceModal');
                            })
                            .catch(error => {
                                vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                                vm.sending_mutliple_stripe_invoices = false;

                                vm.selected_payment_ids_for_sending_invoice = [];
                                vm.check_all_send_stripe_invoices = false;
                                vm.hideModal('sendStripeInvoiceModal');
                                throw error;
                            });
                    }
                });
            },
            paymentDeleted(id){
                var index = this.generated_payments.findIndex(rec => rec.id == id);
                this.generated_payments.splice(index, 1);
            },
            markPaymentAsPaid(id){
                this.payment_recieved.id = id;
                this.payment_recieved.date = this.date;
                this.payment_recieved.time = this.time;
                this.showModal('markPaymentPaidModal');
            },
            showModal(ref) {
                this.$refs[ref].show()
            },
            hideModal(ref) {
                this.$refs[ref].hide()
            },
            markPaymentAsPaidSubmit(){
                let vm = this;
                this.isUpdatingPayment = true;
                axios.post(route('payment.paid').url(), this.payment_recieved)
                    .then(res => {
                        let data = res.data;
                        this.showMessage('success',data.message);
                        this.paymentUpdated(data.payment);
                        vm.hideModal('markPaymentPaidModal');
                        this.isUpdatingPayment = false;
                    }).catch(error => {
                        if(error.response.status == 422)
                        {
                            vm.form_errors = error.response.data.errors;
                            vm.isUpdatingPayment = false;
                        }
                        else
                        {
                            vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                            vm.hideModal('markPaymentPaidModal');
                            this.isUpdatingPayment = false;
                            throw error;
                        }
                    });
            },
            paymentUpdated(payment){
                var index = this.generated_payments.findIndex(rec => payment.id == rec.id);
                this.generated_payments.splice(index, 1, payment);
            },
            editPayment(payment){
                this.edit_payment = {
                    ...payment,
                    show_stripe_warning : payment.payment_method == 'stripe' ? true : false
                };
                this.showModal('editPyamentModal');
            },
            editPaymentSubmit(){
                let vm = this;
                this.isUpdatingPayment = true;
                axios.post(route('payment.monthly.update', this.edit_payment.id).url(), this.edit_payment)
                    .then(res => {
                        let data = res.data;
                        this.showMessage('success',data.message);
                        this.paymentUpdated(data.payment);
                        vm.hideModal('editPyamentModal');
                        this.isUpdatingPayment = false;
                    }).catch(error => {
                        if(error.response.status == 422)
                        {
                            vm.form_errors = error.response.data.errors;
                            vm.isUpdatingPayment = false;
                        }
                        else
                        {
                            vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                            vm.hideModal('editPyamentModal');
                            this.isUpdatingPayment = false;
                            throw error;
                        }
                    });
            },
        }
    }
</script>

<style scoped>
    .preloader {
        margin: auto;
    }
</style>