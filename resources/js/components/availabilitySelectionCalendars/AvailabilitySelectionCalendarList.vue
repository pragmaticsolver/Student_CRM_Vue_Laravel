<template>
    <div class="row"> 
        <div class="col-sm-12">
            <div class="row">
                <div class="float-left col-sm-6 px-0">
                    <h2 class="float-left">{{ trans('messages.availability-selection-calendars') }}</h2>
                </div>
                <div class="float-right col-sm-6 px-0" v-if="permissions['manage-availability-selection-calendars'] == 1">
                    <button class="btn btn-primary ml-2 float-right" @click="edit_record = {}">{{ trans('messages.add') }}</button>
                </div>
            </div>
            <!-- Add / Edit -->
            <app-edit-availability-selection-calendar v-if="edit_record" :record="edit_record" @modalClose="edit_record = null"></app-edit-availability-selection-calendar>

            <!-- List -->
            <div class="row" v-if="!isLoading && records.length > 0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="15%">{{ trans('messages.name') }}</th>
                            <th>{{ trans('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <app-availability-selection-calendar-record :permissions="permissions" v-for="record in records" v-bind:key="record.id" :record="record" @edit="edit_record = record"></app-availability-selection-calendar-record>
                    </tbody>
                </table>
            </div>
            <div class="row" v-if="isLoading">
                <b-spinner label="Spinning" class="preloader"></b-spinner>
            </div>
            <div class="row" v-if="!isLoading && records.length == 0">
                <div class="col-sm-12">
                    <p class="text-center">{{ trans('messages.no-records-found') }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import AvailabilitySelectionCalendarRecord from './AvailabilitySelectionCalendarRecord.vue';
import EditAvailabliitySelectionCalendar from './EditAvailabliitySelectionCalendar.vue';
import axios from 'axios';

export default {
    props: ['permissions'],
    components: {
        'app-availability-selection-calendar-record': AvailabilitySelectionCalendarRecord,
        'app-edit-availability-selection-calendar': EditAvailabliitySelectionCalendar,
    },
    data: function(){
        return {
            records: [],
            edit_record: null,
            isLoading: false,
        }
    },
    created: function(){
        this.$eventBus.$on('availabilitySelectionCalendarRecordUpdated', (updatedRecord) => {
            this.showMessage('success',trans('messages.availability-selection-calendar-updated-successfully'));
            var index = this.records.findIndex(record => record.id == updatedRecord.id);
            this.records.splice(index, 1, updatedRecord);
        });
        this.$eventBus.$on('availabilitySelectionCalendarRecordCreated', (createdRecord) => {
            this.showMessage('success', trans('messages.availability-selection-calendar-created-successfully'));
            this.records.push(createdRecord);
        });
        this.$eventBus.$on('availabilitySelectionCalendarRecordDeleted', (recordId) => {
            this.showMessage('success', trans('messages.availability-selection-calendar-deleted-successfully'));
            var index = this.records.findIndex(record => record.id == recordId);
            this.records.splice(index, 1);
        });
        this.fetchRecords();
    },
    methods: {
        fetchRecords: function(){
            this.isLoading = true;
            axios.get(route('availability_selection_calendars.records').url())
                .then(res => {
                    this.isLoading = false;
                    let data = res.data;
                    this.records = data;
                });
        }
    }
}
</script>

<style scoped>
    .preloader {
        margin: auto;
    }
</style>
