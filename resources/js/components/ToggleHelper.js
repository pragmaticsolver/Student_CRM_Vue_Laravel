export default class ToggleHelper {
    static unit(course, keepSelected = false)
    {
        const unitSelect = $('select[name="unit_id"]');
        const unitOptions = $('.option-unit');

        unitOptions.hide();

        if(!keepSelected) unitOptions.prop('selected', false);

        if(course) {
            unitSelect.parents('.form-group').show();
            unitOptions.filter(`[data-course="${course}"]`).show();
        } else {
            unitSelect.parents('.form-group').hide();
        }
    }

    static lesson(course, unit, keepSelected = false)
    {
        const lessonSelect = $('select[name="lesson_id"]');
        const lessonOptions = $('.option-lesson');

        lessonOptions.hide();

        if(!keepSelected) lessonOptions.prop('selected', false);

        if(course && unit) {
            lessonSelect.parents('.form-group').show();
            lessonOptions.filter(`[data-course="${course}"]`).filter(`[data-unit="${unit}"]`).show();
        } else {
            lessonSelect.parents('.form-group').hide();
        }
    }

    static question(test, keepSelected = false)
    {
        const questionSelect = $('select[name="question_id"]');
        const questionOptions = $('.option-question');

        questionOptions.hide();

        if(!keepSelected) questionOptions.prop('selected', false);

        if(test) {
            questionSelect.parents('.form-group').show();
            questionOptions.filter(`[data-test="${test}"]`).show();
        } else {
            questionSelect.parents('.form-group').hide();
        }
    }

    static fields(className, fieldVal)
    {
        $(className).hide();
        $(className+'-'+fieldVal).show();

        if(className == '.assessment-question-type')
        {
            var requiredFields = $(className+'-'+fieldVal+' .required');
            if(fieldVal == 'availability-selection-calender')
            {
                requiredFields.prop('required', true);
            }
            else
            {
                requiredFields.prop('required', false);
            }
        }
    }

    static paperTestPrefillTotalScore()
    {
        $('select[name="paper_test_id"]').change(function() {
            const paperTestId = $(this).val();
            const totalScoreInput = $('input[name="total_score"]');

            if(paperTestId) {
                $.ajax({
                    url: '/bce/paper-test/get-total-score',
                    data: {
                        paper_test_id: paperTestId
                    },
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(response) {
                        totalScoreInput.val(response.total_score);
                    }
                });
            }
        });
    }
}
