<template>
    <div class="col-12 border border-info my-2 p-1">
        <div>
            <p class="float-left">{{ __('messages.zoom-meeting-operations') }}</p>
            <span v-if="zoomMeeting.id && !isLoading" class="float-right">
                <button class="btn btn-primary btn-sm" @click="syncMeetingData" v-b-tooltip.hover.right :title="__('messages.sync-meeting-details-with-zoom-server')">
                    <i class="fa fa-refresh" aria-hidden="true"></i>
                </button>
            </span>
            <div class="clearfix"></div>
        </div>
        <div v-if="!isLoading">
            <template v-if="!zoomMeeting.id">
                <button class="btn btn-info btn-sm mb-1" @click="createZoomMeeting">{{ __('messages.create-zoom-meeting') }}</button>    
            </template>

            <template v-if="zoomMeeting.id">
                <p><b>{{ __('messages.meeting-id') }}:</b> {{ zoomMeeting.display_meeting_id }}</p>
                <p><b>{{ __('messages.password') }}:</b> {{ zoomMeeting.password }}</p>
                <button class="btn btn-primary btn-sm mb-1"
                    @click="copyToClipboard(zoomMeeting.start_url, $event)"
                    >{{ __('messages.copy-start-meeting-url') }}</button>

                <button class="btn btn-primary btn-sm mb-1"
                    @click="copyToClipboard(zoomMeeting.join_url, $event)"
                    >{{ __('messages.copy-join-meeting-url') }}</button>

                <button class="btn btn-danger btn-sm mb-1"
                    @click="deleteZoomMeeting"
                    >{{ __('messages.delete-zoom-meeting') }}</button>

                <button class="btn btn-warning btn-sm mb-1" 
                    @click="sendMeetingReminder('students')"
                    >{{ __('messages.send-meeting-reminder-to-students') }}</button>

                 <button class="btn btn-warning btn-sm mb-1" 
                    @click="sendMeetingReminder('teacher')"
                    >{{ __('messages.send-meeting-reminder-to-teacher') }}</button>
            </template>
        </div>
        <div class="row" v-if="isLoading">
            <b-spinner label="Spinning" class="preloader"></b-spinner>
        </div>
    </div>
</template>

<script>
export default {
    props: ['schedule_id', 'date', 'zoom_meeting'],
    data: function(){
        return {
            isLoading: false,
            zoomMeeting: {}
        }
    },
    created: function(){
        this.zoomMeeting = this.zoom_meeting;
    },
    methods: {
        createZoomMeeting: function(){
            this.isLoading = true;
            var form_data = {
                schedule_id: this.schedule_id,
                date: this.date
            }
            var vm = this;
            axios.post(route('zoom.create.meeting').url(), form_data)
                .then(res => {
                    let data = res.data;
                    if (data.status == 1) {
                        vm.showMessage("success", data.message);
                        vm.zoomMeeting = data.zoom_meeting;
                        this.isLoading = false;
                    } else {
                        vm.showError(data.message || trans('messages.something-went-wrong'));
                        this.isLoading = false;
                    }
                })
                .catch(error => {
                    vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                    this.isLoading = false;
                });
        },
        deleteZoomMeeting: function() {
            var vm = this;

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
                    vm.isLoading = true;
                    axios.delete(route('zoom.delete.meeting', vm.zoomMeeting.id).url())
                        .then(res => {
                            let data = res.data;
                            if (data.status == 1) {
                                vm.showMessage("success", data.message);
                                vm.zoomMeeting = {};
                            } else {
                               vm.showError(data.message || trans('messages.something-went-wrong'));  
                            }
                           vm.isLoading = false;
                        })
                        .catch(error => {
                            vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                        });
                }
            });
        },
        sendMeetingReminder: function (to) {
            var vm = this;
            var form_data = {
                schedule_id: this.schedule_id,
                date: this.date
            }
            axios.post(route('zoom.send-meeting-reminder',to).url(), form_data)
                .then(res => {
                    let data = res.data;
                    vm.showMessage("success", data.message);
                    this.isLoading = false;
                })
                .catch(error => {
                    vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                });
        },
        syncMeetingData: function(){
            this.isLoading = true;
            var vm = this;
            axios.post(route('zoom.meeting.sync', vm.zoomMeeting.id).url())
                .then(res => {
                    let data = res.data;
                    if (data.status == 1) {
                        vm.showMessage("success", data.message);
                        vm.zoomMeeting = data.zoom_meeting;
                        this.isLoading = false;
                    } else {
                        vm.showError(data.message || trans('messages.something-went-wrong'));
                        this.isLoading = false;
                    }
                })
                .catch(error => {
                    vm.showError(error.response.data.message || trans('messages.something-went-wrong'));
                    this.isLoading = false;
                });
        },
    }
}
</script>


<style scoped>
    .preloader {
        margin: auto;
    }
</style>