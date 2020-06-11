export default class RowHelper
{
    static bindRemove()
    {
        $('.remove-row').click(function(e) {
            e.preventDefault();
            $(this).closest("tr").remove();
        });
    }

    static unbindRemove()
    {
        $('.remove-row').off('click');
    }

    static bindAddOption()
    {
        const RowHelper = this;

        $('#addRowOption').click(function(e) {
            e.preventDefault();

            const newRow = $('<tr>');
            let cols = "";

            cols += '<td width="80%"><input type="text" name="options[]" class="form-control" /></td>';
            cols += '<td><button class="btn btn-danger remove-row">Delete</button></td>';


            newRow.append(cols);
            $("table#assessmentQuestionOption").append(newRow);

            RowHelper.unbindRemove();
            RowHelper.bindRemove();
        });
    }
}
