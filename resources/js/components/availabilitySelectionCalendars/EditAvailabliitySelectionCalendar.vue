<template>
    <b-modal ref="my-modal" :title="modal_title" @hidden="$emit('modalClose')" no-fade>
        <div slot="modal-footer">
            <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="isLoading">{{ trans('messages.save') }}
                <b-spinner v-if="isLoading" small label="Spinning"></b-spinner>
            </b-button>
            <b-button variant="secondary" @click="hideModal">{{  trans('messages.cancel') }}</b-button>
        </div>
        <form ref="my-form" @submit.prevent="saveRecord">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>{{ trans('messages.name') }}:</label>
                        <input
                            type="text"
                            class="form-control col-sm-12"
                            :class="{ 'is-invalid' :  errors.name }"
                            v-model="name" required>
                        <div v-if="errors.name" class="invalid-feedback">
                            <template v-for="error_message in errors.name" >{{ error_message }}</template>
                        </div>
                    </div>
                </div>
            </div>
            <button ref="dummy_submit" style="display:none;"></button>
        </form>
    </b-modal>
</template>

<script>
import axios from 'axios';

export default {
    props: ['record'],
    data : function(){
        return {
            id: this.record.id,
            name: this.record.name,
            isLoading: false,
            errors: []
        };
    },
    computed: {
        modal_title: function(){
            let title = this.id ? trans('messages.edit') : trans('messages.add');
            title += " " + trans('messages.availability-selection-calendar');
            return title;
        }
    },
    methods: {
        showModal() {
            this.$refs['my-modal'].show()
        },
        hideModal() {
            this.$refs['my-modal'].hide()
        },
        saveRecord: function(){
            let vm = this;
            this.isLoading = true;
            let data = {
                id: this.id,
                name: this.name
            };
            axios.post(route('availability_selection_calendars.save').url(), data)
            .then(res => {
                let data = res.data;
                if(this.id)
                {
                    vm.$eventBus.$emit('availabilitySelectionCalendarRecordUpdated', data.record);
                }
                else
                {
                    vm.$eventBus.$emit('availabilitySelectionCalendarRecordCreated', data.record);
                }
                vm.hideModal();
            }).catch(error => {
                if(error.response.status == 422)
                {
                    vm.errors = error.response.data.errors;
                    vm.isLoading = false;
                }
                else
                {
                    throw error;
                }
            });
        }
    },
    mounted(){
        this.showModal();
    }
}
</script>
