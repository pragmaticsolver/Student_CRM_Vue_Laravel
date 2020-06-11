<template>
    <tr>
        <td>{{ record.name }}</td>
        <td>
            <a  v-if="permissions['manage-availability-timeslots'] == 1" class="btn btn-warning"  :href="editCalendarUrl">{{ trans('messages.manage-timeslots') }}</a>
            <a  v-if="permissions['view-availability-responses'] == 1"  class="btn btn-success" :href="viewResponsesUrl">{{ trans('messages.view-responses') }}</a>
            <b-button v-if="permissions['manage-availability-selection-calendars'] == 1" variant="primary" @click="$emit('edit')" :disabled="isDeleting">{{ trans('messages.edit') }}</b-button>
            <b-button v-if="permissions['manage-availability-selection-calendars'] == 1" variant="danger" @click.prevent="deleteRecrod" :disabled="isDeleting">{{ trans('messages.delete') }}
                <b-spinner v-if="isDeleting" small label="Spinning"></b-spinner>
            </b-button>
        </td>
    </tr>
</template>

<script>

export default {
    props: ['record', 'permissions'],
    data: function(){
        return {
            isDeleting: false,
            editCalendarUrl: route('edit_calendar.index', this.record.id),
            viewResponsesUrl: route('availability_selection_calendars.responses', this.record.id)
        }
    },
    methods: {
        deleteRecrod: function(){
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
                    vm.isDeleting = true;
                    axios.delete(route('availability_selection_calendars.delete', vm.record.id).url())
                        .then(res => {
                            vm.$eventBus.$emit('availabilitySelectionCalendarRecordDeleted', vm.record.id);
                        });
                }
            });
        }
    }
}
</script>
