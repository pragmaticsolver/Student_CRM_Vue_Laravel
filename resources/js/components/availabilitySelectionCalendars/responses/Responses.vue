<template>
    <div>
        <h2>{{ trans('messages.availability-selection-responses') }}: {{ calendar_name }}</h2>
            
        
        <!-- Calendar View -->
        <div class="row edit-calendar-component" v-if="!isLoading" style="position:relative;">

             <div v-if="isLoadingFilter" class="text-center;" style="width: 100%;height: 100%;position: absolute;top:0;
            left: 0;z-index:10;">
                <b-spinner label="Spinning" style="position: absolute;left: 50%;top: 50%;
                text-align:center;"></b-spinner>
            </div>

            <div class="col-12">
                <fullcalendar ref="fullcalendar"
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
                    @eventClick="onEventClick"
                ></fullcalendar>
            </div>
        </div>
        
        <div class="row" v-if="!isLoading">
            <div class="col-12 mt-2">
                <label>{{ trans('messages.levels') }}: </label>
                <br>
                <button class="btn btn-secondary btn-sm" @click="selectAllLevelsFilter">{{ trans('messages.select-all') }}</button>
                <button class="btn btn-secondary btn-sm" @click="clearLevelsFilter">{{ trans('messages.clear-selection') }}</button>
                <br>
                <b-button 
                    v-for="class_level of level_filter_options" 
                    :key="class_level" 
                    :variant="applied_filters.levels.includes(class_level) ? 'primary' : 'outline-primary'" 
                    class="m-1" 
                    @click="toogleFilterLevel(class_level)"
                    >{{ class_level }}</b-button>
            </div>
        </div>

        <div class="row" v-if="!isLoading">
            <div class="col-12 mt-2">
                <label>{{ trans('messages.users') }}: </label>
                <br>
                <button class="btn btn-secondary btn-sm" @click="selectAllUsersFilter">{{ trans('messages.select-all') }}</button>
                <button class="btn btn-secondary btn-sm" @click="clearUsersFilter">{{ trans('messages.clear-selection') }}</button>
                <br>
                
                <b-button 
                    v-for="user of user_filter_options" 
                    :key="user.id" 

                    :variant="applied_filters.users.includes(user.id) ? 'primary' : 'outline-primary'" 
                    class="m-1"
                    @click="toogleFilterUser(user.id)"
                    >{{ user.name }}</b-button>
            </div>
        </div>

        <div class="row" v-if="isLoading">
            <b-spinner class="m-auto" label="Spinning"></b-spinner>
        </div>

        <!-- User list modal -->
        <app-user-list
            v-if="display_event"
            :users="display_event.extendedProps.users"
            @modalClose="display_event = null"
            ></app-user-list>
    </div>
</template>

<script>

import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import bootstrapPlugin from '@fullcalendar/bootstrap';
import jaLocale from '@fullcalendar/core/locales/ja';

import UserList from './UserList.vue';
import Select2 from 'v-select2-component';
 
export default {
    props: ['cal_id'],
    components: {
        'fullcalendar' : FullCalendar,
        'app-user-list': UserList,
        'select2' : Select2
    },
    data: function(){
        return {
            isLoading: true,
            isLoadingFilter: true,
            events: [],
            calendar_name: '',
            display_event: null,
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
            filter_options: {
                levels: [],
                users: [],
            },
            applied_filters: {
                levels: [],
                users: [],
            },
        }
    },
    watch: {
        applied_filters: {
            handler: function(){
                this.refreshData();
            },
            deep: true,
        }
    },
    computed: {
        level_filter_options: function(){
            return this.filter_options.levels;
        },
        user_filter_options: function(){
            let vm = this;
            return this.filter_options.users.filter(function(user){
                // if user has any one level of that of selected level filter then only show that user
                return vm.applied_filters.levels.some(v=> user.levels.indexOf(v) !== -1);
            });
        }
    },
    methods:{
        fetchData: function(){
            this.isLoading = true;
            axios.get(route('availability_selection_calendars.responses.initialdata',this.cal_id).url())
                .then(res => {
                    this.isLoading = false;
                    let data = res.data;
                    this.calendarConfig.timeZone = data.calendarSettings.timeZone;
                    this.calendarConfig.hiddenDays = data.calendarSettings.hiddenDays;
                    this.calendarConfig.firstDay = data.calendarSettings.firstDay;
                    this.calendarConfig.minTime = data.calendarSettings.minTime;
                    this.calendarConfig.maxTime = data.calendarSettings.maxTime;
                    this.calendarConfig.locale = data.calendarSettings.locale;
                    this.calendar_name = data.calendar_name;
                    this.filter_options = data.filter_options;
                    this.applied_filters = data.applied_filters;
                    this.refreshData();
                });
        },
        refreshData: function(){
            this.isLoadingFilter = true;
            axios.get(route('availability_selection_calendars.responses.data',this.cal_id).url(),{
                params: {
                    levels: this.applied_filters.levels,
                    user_ids: this.applied_filters.users
                }
            })
                .then(res => {
                    let data = res.data;
                    this.events = data.events;
                    this.isLoadingFilter = false;
                });
        },
        toogleFilterLevel(value){
            let index = this.applied_filters.levels.indexOf(value);
            if(index >= 0)
            {
                this.applied_filters.levels.splice(index, 1);
            }
            else
            {
                this.applied_filters.levels.push(value);
            }
        },
        toogleFilterUser(value){
            let index = this.applied_filters.users.indexOf(value);
            if(index >= 0)
            {
                this.applied_filters.users.splice(index, 1);
            }
            else
            {
                this.applied_filters.users.push(value);
            }
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
        onEventClick: function(info){
            this.display_event = info.event;
        },
        selectAllLevelsFilter: function(){
            let levels = [];
            this.level_filter_options.forEach(level => {
                levels.push(level)
            });
            this.applied_filters.levels = levels;
        },
        clearLevelsFilter: function(){
            this.applied_filters.levels = [];
        },
        selectAllUsersFilter: function(){
            let users = [];
            this.user_filter_options.forEach(user => {
                users.push(user.id)
            });
            this.applied_filters.users = users;
        },
        clearUsersFilter: function(){
            this.applied_filters.users = [];
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
</style>