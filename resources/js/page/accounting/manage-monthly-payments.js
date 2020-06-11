import ManagePayments from '../../components/payment_batches/ManagePayments.vue';

window.addEventListener('DOMContentLoaded', function() {
    const vm = new Vue({
        el: '#vue-app',
        components: {
            'app-manage-batch-payments' : ManagePayments
        }
    });
});
