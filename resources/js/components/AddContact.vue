<template>
    <b-modal ref="my-modal" :title="__('messages.addcontact')" @hidden="$emit('modal-close')" no-fade>
        <div slot="modal-footer">
             <b-button variant="secondary" @click="hideModal">{{  trans('messages.cancel') }}</b-button>
             <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="isSaving">
                {{ trans('messages.save') }}
                <b-spinner v-if="isSaving" small label="Spinning"></b-spinner>
            </b-button>
        </div>
        <form @submit.prevent="saveContact">
           <div class="row" v-if="isLoading">
               <div class="m-auto"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>
            </div>
            <template v-if="!isLoading">
                <div class="form-group">
                    <label>{{ trans('messages.name') }}:</label>
                    <select class="form-control" v-model="customer_id" required :placeholder="__('messages.selectstudent')">
                        <option value="" selected >{{ __('messages.selectstudent') }}</option>
                        <option v-for="student of students" :key="student.id" :value="student.id">{{ student.fullname }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('messages.contacttype')}}:</label>
                    <div>
                        <label class="radio-inline"><input type="radio" name="type" value="denwa" required v-model="type"> {{ __('messages.telephone')}}</label>
                        <label class="radio-inline"><input type="radio" name="type" value="line" required v-model="type"> {{ __('messages.line')}}</label>
                        <label class="radio-inline"><input type="radio" name="type" value="direct" required v-model="type"> {{ __('messages.direct')}}</label>
                        <label class="radio-inline"><input type="radio" name="type" value="mail" required v-model="type"> {{ __('messages.email')}}</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ __('messages.contents')}}:</label>
                    <textarea name="message" rows="5" :placeholder="__('messages.pleasewritecontentshere')" class="form-control" required v-model="message"></textarea>
                </div>
                <button ref="dummy_submit" style="display:none;"></button>
            </template>
        </form>
    </b-modal>
</template>

<script>
export default {
    data: function(){
        return {
            isLoading: false,
            isSaving: false,
            students: [],
            customer_id: '',
            type: 'denwa',
            message: ''
        }
    },
    methods: {
         showModal() {
            this.$refs['my-modal'].show()
        },
        hideModal() {
            this.$refs['my-modal'].hide()
        },
        fetchData: function(){
            this.isLoading = true;
            axios.get(route('contact.form.data').url())
                .then(res => {
                    this.isLoading = false;
                    let data = res.data;
                    this.students = data.students;
                });
        },
        saveContact: function(){
            let vm = this;
            this.isSaving = true;
            let data = {
                customer_id: this.customer_id,
                type: this.type,
                message: this.message
            };
            axios.post(route('contact.store').url(), data)
            .then(res => {
                let data = res.data;
                if(data.status == 1)
                {
                    vm.$emit('contact-created',data.message);
                    vm.hideModal();
                }
            });
        }
    },
    mounted: function(){
        this.showModal();
        this.fetchData();
    }
}
</script>