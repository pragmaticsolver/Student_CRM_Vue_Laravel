<template>
    <div class="row">
        <div class="col-12 edit-calendar-component" v-if="!isLoading">
            <fullcalendar ref="fullcalendar" v-if="!isLoading"
                :allDaySlot="false"
                themeSystem="bootstrap"
                :plugins="calendarConfig.plugins"
                defaultView="timeGridWeek"
                :eventTimeFormat="{ hour: '2-digit', minute: '2-digit', meridiem: false, hour12: false }"
                contentHeight="auto"
                :header="null"
                
                :timeZone="calendarConfig.timeZone"
                :hiddenDays="calendarConfig.hiddenDays"
                :firstDay="calendarConfig.firstDay"
                :minTime="calendarConfig.minTime"
                :maxTime="calendarConfig.maxTime"
                :locale="calendarConfig.locale"
                :events="events"

                :columnHeaderText="columnHeaderTextCallback"
                :eventRender="onEventRender"
                @eventClick="onEventClick"
            ></fullcalendar>
            <input type="hidden" :name="timeslots_field_name" v-for="id in selected_timeslot_ids" :key="id" :value="id">
        </div>
        <div class="col-12" v-if="isLoading">
            <b-spinner label="Spinning"></b-spinner>
        </div>
    </div>
</template>

<script>

import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import bootstrapPlugin from '@fullcalendar/bootstrap';
import jaLocale from '@fullcalendar/core/locales/ja';

export default {
    props: ['assessment_question_id','timeslots_field_name','selected_timeslots','disabled'],
    components: {
        'fullcalendar' : FullCalendar
    },
    data: function(){
        return {
            name: '',
            isLoading: true,
            raw_events: [],
            selected_timeslot_ids: [],
            calendarConfig: {
                plugins: [ dayGridPlugin, timeGridPlugin, bootstrapPlugin ],
                locales: [ jaLocale ],
                timeZone: null,
                hiddenDays: [],
                firstDay: null,
                minTime: null,
                maxTime: null,
                locale: null,
            },
        }
    },
    computed: {
        events: function(){
            let vm = this;
            let formatedEvents = [];
            this.raw_events.forEach(function(event){
                let temp = event;
                temp.is_selected = vm.selected_timeslot_ids.includes(event.id) ? true : false;
                formatedEvents.push(temp);
            });
            return formatedEvents;
        }
    },
    methods:{
        fetchData: function(){
            this.isLoading = true;
            axios.get(route('timeslotpicker.data',[this.assessment_question_id]).url())
                .then(res => {
                    this.isLoading = false;
                    let data = res.data;
                    this.calendarConfig.timeZone = data.calendarSettings.timeZone;
                    this.calendarConfig.hiddenDays = data.calendarSettings.hiddenDays;
                    this.calendarConfig.firstDay = data.calendarSettings.firstDay;
                    this.calendarConfig.minTime = data.calendarSettings.minTime;
                    this.calendarConfig.maxTime = data.calendarSettings.maxTime;
                    this.calendarConfig.locale = data.calendarSettings.locale;
                    this.raw_events = data.events;
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
        onEventRender: function(info){
            var element = $(info.el);
            let className = info.event.extendedProps.is_selected ? 'timeslot-selected' : 'timeslot-unselected';
            element.addClass(className);
            if(!this.disabled)
            {
                element.addClass('timeslot-clickable');    
            }
        },
        onEventClick: function(info){
            if(!this.disabled)
            {
                var eventId = info.event.id;
                var index = this.selected_timeslot_ids.findIndex(id => id == eventId);
                if(index >= 0)
                {
                    this.selected_timeslot_ids.splice(index, 1);
                }
                else
                {
                    this.selected_timeslot_ids.push(parseInt(eventId));
                }
            }
        }
    },
    created: function(){
        this.fetchData();
        if(this.selected_timeslots)
        {
            let vm = this;
            let temp = this.selected_timeslots.split(",");
            temp.forEach(function(selecteTimeslotId){
                vm.selected_timeslot_ids.push(parseInt(selecteTimeslotId));
            });
        }
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

    ::v-deep .timeslot-unselected{
        background-color: #e2efec;
        color:#1ab394 !important;
    }

    ::v-deep .timeslot-clickable{
        cursor: pointer;
    }
</style>