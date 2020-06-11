<template>
    <b-modal ref="my-modal" :title="trans('messages.edit-schedule')" @hidden="$emit('modal-close')" no-fade>
        <div slot="modal-footer">
             <b-button variant="secondary" @click="hideModal">{{  trans('messages.cancel') }}</b-button>
             <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="isSaving || (!allow_submit)">
                {{ trans('messages.save') }}
                <b-spinner v-if="isSaving" small label="Spinning"></b-spinner>
            </b-button>
        </div>
        <form @submit.prevent="saveSchedule">
           <div class="row" v-if="isLoading">
               <div class="m-auto"><div class="fa fa-spinner fa-spin" style="font-size:100px"></div></div>
            </div>
            <template v-if="!isLoading">
                <div class="form-group">
                    <label>{{ trans('messages.class') }}:</label>
                    <select class="form-control" v-model="class_id" required>
                        <option v-for="singleClass of classes" :key="singleClass.id" :value="singleClass.id">{{ singleClass.title }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ trans('messages.teacher') }}:</label>
                    <select class="form-control" v-model="teacher_id" required>
                        <option v-for="singleTeacher of teachers" :key="singleTeacher.id" :value="singleTeacher.id">{{ singleTeacher.nickname }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ trans('messages.course') }}:</label>
                    <select class="form-control" v-model="course_id" >
                        <option :value="null">None</option>
                        <option v-for="singleCourse of courses" :key="singleCourse.id" :value="singleCourse.id">{{ singleCourse.title }}</option>
                    </select>
                </div>
                <b-form-group :label="trans('messages.update-mode') + ':'" v-if="update_modes.length" class="custom-radio-style">
                    <b-form-radio-group
                        id="radio-slots"
                        v-model="update_mode"
                        :options="update_modes"
                        required
                        stacked
                    ></b-form-radio-group>
                </b-form-group>
                <button ref="dummy_submit" style="display:none;"></button>

                <b-alert :show="warning_messages.length > 0" variant="danger">
                    <p class="alert-heading">
                        <span class="fa  fa-exclamation-triangle"></span> {{ trans('messages.following-things-will-happen-as-a-result-of-saving-this-form') }}:
                    </p>
                    <p class="mb-1" v-for="message in warning_messages" :key="message">{{ message }}</p>
                </b-alert>
            </template>
        </form>
    </b-modal>
</template>

<script>
export default {
    props: ['schedule_id','date'],
    data: function(){
        return {
            classes: [],
            courses: [],
            teachers: [],
            class_id: null,
            course_id: null,
            teacher_id: null,
            isLoading: false,
            isSaving: false,
            update_modes: [],
            update_mode: null,
            db_course_id: null,
            db_class_id: null,
            db_teacher_id: null,
        }
    },
    computed: {
        course_changed: function(){
            return this.db_course_id != this.course_id;
        },
        class_changed: function(){
            return this.db_class_id != this.class_id
        },
        teacher_changed: function(){
            return this.db_teacher_id != this.teacher_id
        },
        allow_submit: function(){
            return this.course_changed || this.class_changed || this.teacher_changed
        },
        warning_messages: function(){
            let messages = [];

            if(this.allow_submit)
            {
                if(this.update_mode == 'all')
                {
                    if(this.class_changed)
                    {
                        messages.push(trans('messages.class-will-be-changed-for-all-instance-of-current-schedule'));
                    }
                    if(this.teacher_changed)
                    {
                        messages.push(__('messages.teacher-will-be-changed-for-all-instance-of-current-schedule'));
                    }
                    if(this.course_changed)
                    {
                        messages.push(trans('messages.course-will-be-changed-for-all-instance-of-current-schedule'));
                        messages.push(trans('messages.course-progress-online-test-assessment-results-paper-test-result-data-will-be-lost'));
                    }
                }
                else if(this.update_mode == 'future')
                {
                    messages.push(trans('messages.schedule-will-be-splitted-into-two-parts'));
                    messages.push(trans('messages.class-off-days-reservations-and-attendance-data-will-be-copied-to-new-schedule-accordingly'));
                    if(this.class_changed)
                    {
                        messages.push(trans('messages.class-will-be-changed-for-all-instance-of-new-schedule-leaving-class-on-old-schedule-as-it-is'));
                    }
                    if(this.teacher_changed)
                    {
                        messages.push(__('messages.teacher-will-be-changed-for-all-instance-of-new-schedule-(leaving-teacher-on-old-schedule-as-it-is)'));
                    }
                    if(this.course_changed)
                    {
                        messages.push(trans('messages.course-will-be-changed-for-all-instance-of-new-schedule-leaving-course-course-progress-and-other-data-on-old-schedule-as-it-is'));
                    }
                }
            }
            return messages;
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
            axios.get(route('schedule.edit.data').url(), {
                    params: {
                        'schedule_id' : this.schedule_id,
                        'date': this.date
                    }
                })
                .then(res => {
                    this.isLoading = false;
                    let data = res.data;
                    this.classes = data.classes;
                    this.courses = data.courses;
                    this.teachers = data.teachers;
                    this.class_id = this.db_class_id = data.class_id;
                    this.course_id = this.db_course_id = data.course_id;
                    this.teacher_id = this.db_teacher_id = data.teacher_id;
                    this.update_modes = data.update_modes;
                    this.update_mode = data.default_update_mode;
                });
        },
        saveSchedule: function(){
            let vm = this;
            this.isSaving = true;
            let data = {
                schedule_id: this.schedule_id,
                class_id: this.class_id,
                course_id: this.course_id,
                teacher_id: this.teacher_id,
                update_mode: this.update_mode,
                date: this.date
            };
            axios.post(route('schedule.update').url(), data)
            .then(res => {
                let data = res.data;
                if(data.status == 1)
                {
                    vm.$emit('schedule-updated');
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

<style>
    .custom-radio-style .custom-control-label {
        padding-top: 2px !important;
    }
</style>