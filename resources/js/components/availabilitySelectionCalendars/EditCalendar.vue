<template>
    <div class="edit-calendar-component">
        <h2>{{ trans('messages.manage-timeslots') }}: {{ name }}</h2>        
        <fullcalendar ref="fullcalendar" v-if="!isLoading"
            :selectable="true"
            :selectMirror="true"
            :eventDurationEditable="false"
            :dragScroll="false"
            :allDaySlot="false"

            :plugins="calendarConfig.plugins"
            :locales="calendarConfig.locales"
            :themeSystem="calendarConfig.themeSystem"
            :timeZone="calendarConfig.timeZone"
            :hiddenDays="calendarConfig.hiddenDays"
            :firstDay="calendarConfig.firstDay"
            :minTime="calendarConfig.minTime"
            :maxTime="calendarConfig.maxTime"
            :locale="calendarConfig.locale"
            :events="calEvents"

            :defaultView="calendarConfig.defaultView"
            :eventTimeFormat="calendarConfig.eventTimeFormat"
            :contentHeight="calendarConfig.contentHeight"
            :header="calendarConfig.header"

            :columnHeaderText="columnHeaderTextCallback"
            @select="onCalenderSelect($event)"
            :eventRender="onEventRender"
        ></fullcalendar>
        <div class="row" v-else>
            <b-spinner class="m-auto" label="Spinning"></b-spinner>
        </div>

        <div class="alert alert-primary mt-2" role="alert">
            <em class="mt-2">{{ trans('messages.notes') }}:</em>
            <p>
                {{ trans('messages.to-create-timeslot-click-anywhere-on-calendar-and-drag-mouse') }}  <br>
                {{ trans('messages.to-delete-timeslot-double-click-on-it') }}
            </p>
        </div>

        <!-- Create Timeslot -->
        <app-create-timeslot 
            v-if="create_timeslot"
            @modalClose="create_timeslot = null"
            :form_data="create_timeslot"
            @timeslotSaved="timeslotSaved"
        ></app-create-timeslot>
    </div>
</template>

<script>

import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrapPlugin from '@fullcalendar/bootstrap';
import jaLocale from '@fullcalendar/core/locales/ja';

import CreateTimeslot from './CreateTimeslot.vue';

export default {
    props: ['cal_id'],
    components: {
        'fullcalendar' : FullCalendar,
        'app-create-timeslot': CreateTimeslot
    },
    data: function(){
        return {
            name: '',
            isLoading: true,
            events: [],
            create_timeslot: null,
            calendarConfig: {
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin,
                    bootstrapPlugin
                ],
                locales: [
                    jaLocale
                ],
                themeSystem: 'bootstrap',
                timeZone: null,
                defaultView: 'timeGridWeek',
                hiddenDays: [],
                firstDay: null,
                minTime: null,
                maxTime: null,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false,
                    hour12: false
                },
                contentHeight: 'auto',
                header : null,
                locale: null
            },
        }
    },
    computed: {
        calEvents: function(){
            let temp = [...this.events];

            if(this.create_timeslot){
                let temp_event = {
                    daysOfWeek: [
                        this.create_timeslot.day_of_week
                    ],
                    startTime: this.create_timeslot.from,
                    endTime: this.create_timeslot.to,
                    creating: true
                }
                temp.push(temp_event);
            }
            return temp;
        },
    },
    methods:{
        fetchData: function(){
            this.isLoading = true;
            axios.get(route('edit_calendar.data', this.cal_id).url())
                .then(res => {
                    this.isLoading = false;
                    let data = res.data;
                    this.name = data.name;
                    this.calendarConfig.timeZone = data.calendarSettings.timeZone;
                    this.calendarConfig.hiddenDays = data.calendarSettings.hiddenDays;
                    this.calendarConfig.firstDay = data.calendarSettings.firstDay;
                    this.calendarConfig.minTime = data.calendarSettings.minTime;
                    this.calendarConfig.maxTime = data.calendarSettings.maxTime;
                    this.calendarConfig.locale = data.calendarSettings.locale;
                    this.events = data.events;
                });
        },
        columnHeaderTextCallback: function(date){
            var days = [trans('messages.sunday'), 
                        trans('messages.monday'),
                        trans('messages.tuesday'),
                        trans('messages.wednesday'),
                        trans('messages.thursday'),
                        trans('messages.friday'),
                        trans('messages.saturday')];
            var dayName = days[date.getDay()];
            return dayName;
        },
        onCalenderSelect: function(info){
            let calendarApi = this.$refs.fullcalendar.getApi();

            let from_time_splits = info.startStr.split("T")[1].split(":");
            let to_time_splits = info.endStr.split("T")[1].split(":");
            this.create_timeslot = {
                calender_id: this.cal_id,
                day_of_week: info.start.getUTCDay(),
                from: from_time_splits[0] + ':' + from_time_splits[1],
                to: to_time_splits[0] + ':' + to_time_splits[1],
            }

            calendarApi.unselect();
        },
        timeslotSaved: function(newEvent){
            this.events.push(newEvent);
            this.create_timeslot = null;
            this.showMessage('success', trans('messages.timeslot-created-successfully'));
        },
        onEventRender: function(info){
            let vm = this;
            if(info.event.extendedProps.deleting)
            {
                $(info.el).addClass('deleting');
            }
            else if(info.event.extendedProps.creating || info.isMirror)
            {
                $(info.el).addClass('creating');
            }
            else
            {
                info.el.addEventListener('dblclick', function(){
                    vm.deleteTimeSlot(info.event.id);
                });
            }
        },
        deleteTimeSlot(id){
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
                    var index = vm.events.findIndex(event => event.id == id);
                    let temp_event = {
                        ...vm.events[index]
                    };
                    temp_event.deleting = true;
                    temp_event.title = trans('messages.deleting');
                    vm.events.splice(index, 1, temp_event);

                    axios.delete(route('edit_calender.timeslot.delete', id).url())
                        .then(res => {
                            let data = res.data;
                            vm.events.splice(index, 1);
                            vm.showMessage('success', trans('messages.timeslot-deleted-successfully'));
                        });
                }
            });   
        }
    },
    created: function(){
        this.fetchData();
    }
}
</script>

<style lang='scss' scoped>
    @import '~@fullcalendar/core/main.css';
    @import '~@fullcalendar/daygrid/main.css';
    @import '~@fullcalendar/timegrid/main.css';
    @import '~@fullcalendar/bootstrap/main.css';

    ::v-deep .fc-today{
        background-color:inherit !important;
    }

    ::v-deep .fc-event.creating{
        background-color: #6c757d !important;
        border-color: #6c757d !important;
    }

    ::v-deep .fc-event.deleting{
        background-color: #d86969 !important;
        border-color: #d86969 !important;
    }
</style>