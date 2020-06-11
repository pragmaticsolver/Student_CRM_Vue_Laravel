<template>
    <b-modal ref="my-modal" :title="trans('messages.assign-assessment')" @hidden="$emit('modalclose')" no-fade>
        <div slot="modal-footer">
            <b-button variant="primary"  :disabled="(!enableSubmit) || isSaving" @click="$refs.dummy_submit.click()">{{  trans('messages.submit') }}
                <b-spinner small v-if="isSaving" label="Spinning"></b-spinner>
            </b-button>
            <b-button variant="secondary" @click="hideModal">{{  trans('messages.cancel') }}</b-button>
        </div>
        <div class="row" v-if="isLoading">
            <b-spinner class="m-auto" label="Spinning"></b-spinner>
        </div>
        <form action="" @submit.prevent="assignSubmit" v-if="!isLoading">
            <div class="form-group">
                <label for="">{{ trans('messages.assessment') }}: {{ assessment_name }}</label>
            </div>
            <div class="form-group">
                <label for="">{{ trans('messages.select-students') }}:</label> <br>
                
                <div class="col-12 p-0">
                    <input type="text" :placeholder="trans('messages.search')" class="d-inline-block form-control col-sm-6 align-middle" v-model="studentSearch"  v-on:keyup.enter.prevent>
                    <button type="button" class="d-inline-block btn btn-primary mx-1 align-middle" @click.prevent="selectAllStudents">{{ trans('messages.select-all') }}</button>
                    <button type="button" class="d-inline-block btn btn-primary mx-1 align-middle" @click.prevent="clearStudentsSelection">{{ trans('messages.clear-selection') }}</button>
                    <div class="col-sm-12 p-0 mt-1">
                        <label class="align-middle">{{ trans('messages.select-by-level') }}:</label>
                        <select class="d-inline-block form-control col-sm-3 align-middle" v-model="selectedLevel">
                            <option v-for="level of levels" :key="level">{{ level }}</option>
                        </select>
                        <button type="button" class="btn btn-primary mx-1 align-middle" @click.prevent="selectAllByLevel">{{ trans('messages.apply') }}</button>
                    </div>
                </div>

                {{ selected_student_ids.length }} {{ trans('messages.selected') }}
                <div class="row mt-2" style="max-height: 300px;overflow-y: auto;overflow-x: hidden;">
                    <div class="col-sm-6" v-for="student of filteredStudents" :key="student.id">
                         <label>
                            <input type="checkbox" name="dates[]" v-model="selected_student_ids" :value="student.id" class="cancel_multiple_checkbox" style="width:25px;padding-right:0px;">
                            {{ student.fullname }}
                        </label>
                    </div>
                </div>
            </div>
            <button type="submit" :disabled="(!enableSubmit) || isSaving" ref="dummy_submit" class="d-none"></button>
        </form>
    </b-modal>
</template>

<script>
import axios from 'axios';

export default {
    props: ['assessment_id'],
    data: function(){
        return {
            isSaving: false,
            students: [],
            isLoading: true,
            selected_student_ids: [],
            assessment_name : {},
            studentSearch: '',
            selectedLevel: '',
            levels: []
        }
    },
    computed: {
        filteredStudents: function(){
            const searchRegex = RegExp(this.studentSearch,'ig');
            return this.students.filter((student) => {
                return searchRegex.test(student.fullname);
            });
        },
        enableSubmit: function(){
            return this.selected_student_ids.length > 0 ? true : false;
        }
    },
    methods: {
        showModal() {
            this.$refs['my-modal'].show()
        },
        hideModal() {
            this.$refs['my-modal'].hide()
        },
        assignSubmit() {
            this.isSaving = true;
            let data =  {
                selected_students: this.selected_student_ids,
                assessment_id: this.assessment_id
            };
            axios.post(route('assessment.assign').url(), data)
                .then(res => {
                    this.$emit('assignment-assigned');
                });
        },
        fetchData() {
            this.isLoading = true;
            axios.get(route('assessment.data', this.assessment_id).url())
                .then((res) => {
                    let data = res.data;
                    this.students = data.students;
                    this.assessment_name = data.assessment_name;
                    this.levels = data.levels;
                    this.isLoading = false;
                });
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
        selectAllByLevel(){
            let vm = this;
            let temp = [];
            this.students.forEach(function(student){
                if(student.levels.includes(vm.selectedLevel) && !vm.selected_student_ids.includes(student.id))
                {
                    temp.push(student.id);
                }
            });

            this.selected_student_ids = [...this.selected_student_ids, ...temp];
        }
    },
    mounted(){
        this.showModal();
    },
    created(){
        this.fetchData();
    }
}
</script>
