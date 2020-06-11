<template>
    <b-modal ref="my-modal" :title="trans('messages.add-timeslot')" @hidden="$emit('modalClose')" no-fade>
        <div slot="modal-footer">
            <b-button variant="primary"  :disabled="isSaving" @click="$refs.dummy_submit.click()">{{  trans('messages.save') }}
                <b-spinner small v-if="isSaving" label="Spinning"></b-spinner>
            </b-button>
            <b-button variant="secondary" @click="hideModal">{{  trans('messages.cancel') }}</b-button>
        </div>
        <form action="" @submit.prevent="saveTimeSlot">
            <div class="form-group">
                <label for="">{{trans('messages.weekday')}}: </label> <br>
                <select class="form-control" v-model="form_data.day_of_week" >
                    <option  v-for="(weekday, i) of weekdays" :value="i" :key="i">{{ weekday }}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="">{{ trans('messages.starttime') }}:</label>
                <input type="time" class="form-control" required v-model="form_data.from">
            </div>
            <div class="form-group">
                <label for="">{{ trans('messages.endtime') }}:</label>
                <input type="time" class="form-control" required v-model="form_data.to">
            </div>
            <button type="submit" ref="dummy_submit" class="d-none"></button>
        </form>
    </b-modal>
</template>

<script>
import axios from 'axios';

export default {
    props: ['form_data'],
    data: function(){
        return {
            weekdays: [
                trans('messages.sunday'), 
                trans('messages.monday'),
                trans('messages.tuesday'),
                trans('messages.wednesday'),
                trans('messages.thursday'),
                trans('messages.friday'),
                trans('messages.saturday')
            ],
            isSaving: false,
        }
    },
    methods: {
        showModal() {
            this.$refs['my-modal'].show()
        },
        hideModal() {
            this.$refs['my-modal'].hide()
        },
        saveTimeSlot() {
            this.isSaving = true;
            axios.post(route('edit_calender.save_timeslot').url(), this.form_data)
                .then(res => {
                    let data = res.data;
                    this.$emit('timeslotSaved', data.event);
                });
        }
    },
    mounted(){
        this.showModal();
    }
}
</script>
