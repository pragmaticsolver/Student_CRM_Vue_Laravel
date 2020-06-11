<template>
    <div class="col-12">
        <div class="row">
            <h1>{{ trans('messages.tag-settings') }}</h1>
        </div>

        <div class="row" v-if="isLoading">
            <b-spinner label="Spinning" class="preloader m-auto"></b-spinner>
        </div>

        <div class="row">
            <form ref="my-form" @submit.prevent="saveSettings" v-if="!isLoading">
                <div class="form-group">
                    <label>{{ trans('messages.new-student-tag-attachment-duration-days') }}:</label>
                    <input
                        type="text"
                        class="form-control"
                        v-model="settings.new_student_tag_attachment_duration_days"
                        :class="{ 'is-invalid' :  errors.new_student_tag_attachment_duration_days }"
                        required
                    >
                    <div v-if="errors.new_student_tag_attachment_duration_days" class="invalid-feedback">
                        <template v-for="error_message in errors.new_student_tag_attachment_duration_days" >{{ error_message }}</template>
                    </div>
                </div>
                <b-button type="submit" variant="primary" :disabled="isSaving">
                    {{ trans('messages.save') }}
                    <b-spinner v-if="isSaving" small label="Spinning"></b-spinner>
                </b-button>
            </form>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {
    data : function(){
        return {
            isLoading: false,
            isSaving: false,
            settings: {},
            errors: []
        }
    },
    methods: {
        fetchSettings: function(){
            this.isLoading = true;
            axios.get(route('tags.get_settings').url())
                .then(res => {
                    this.isLoading = false;
                    let data = res.data;
                    this.settings = data;
                });
        },
        saveSettings: function(){
            let vm = this;
            vm.isSaving = true;
            axios.post(route('tags.save_settings').url(), vm.settings)
            .then(res => {
                vm.isSaving = false;
                vm.errors = [];
                vm.showMessage('success',trans('messages.tag-settings-saved-successfully'));
            }).catch(error => {
                vm.isSaving = false;
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
    created: function(){
        this.fetchSettings();
    }
}
</script>