import AssignAssessment from '../../components/assessment/AssignAssessment.vue';

window.addEventListener('DOMContentLoaded', function() {
    const vm = new Vue({
        el: '#vue-app',
        data: {
            assign_assessment_id: null,
        },
        components: {
            'app-assign-assessment' : AssignAssessment
        },
        methods: {
            assignmentAssigned(){
                this.showMessage('success', trans('messages.assessment-assigned-successfully'));
                this.assign_assessment_id = null;
            }
        }
    });

    $('.assign_assessment_btn').click(function(element){
        vm.assign_assessment_id = $(this).data('id');
    });
});
