<div>
    @foreach($todoAccessList as $todoAccess)
        @include('todo.todo-section')
    @endforeach
</div>

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', function() {

        $(document).on('click','.todo_task_cb',function(){
            element = $(this);
            is_done = element.prop('checked') ? 1 : 0;
            if(!is_done)
            {
                Swal.fire({
                    text: "{{ __('messages.are-u-sure-undone-tasks') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('messages.yes-mark-as-incomplete') }}",
                    cancelButtonText: "{{ __('messages.no') }}"
                }).then(function(result){
                    if(result.value)
                    {
                        update_task_status(element, is_done);
                    }
                });
            }
            else
            {
                update_task_status(element, is_done);
            }
            return false;
        });

        $(document).on('change','.due_date',function(){
            $(this).closest('.row').find('.update_due_date_section').show();
        });

        $(document).on('input propertychange paste','.notes_field',function(){
            save_notes_section = $(this).closest('.notes_section').find('.save_notes_section');
            if($(this).attr('data-old_value') != $(this).val())
            {
                save_notes_section.show();
            }
            else
            {
                save_notes_section.hide();
            }
        });

        $(document).on('click','.update_due_date_btn',function(){
            btn_element = $(this);
            date_element = $(this).closest('.row').find('.due_date');
            update_due_date_section = $(this).closest('.row').find('.update_due_date_section');

            data = {
                todo_access_id: date_element.data('todo_task_id'),
                due_date: date_element.val(),
                _token: '{{ csrf_token() }}',
            };

            $.ajax({
                url: "{{ route('todo.update_duedate') }}",
                type : 'POST',
                data: data,
                success: function(response){
                    if(response.status != 1)
                    {
                        error = response.message || "{{ __('messages.something-went-wrong')}}",
                        Swal.fire({
                            text: error,
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        });
                    }
                    else
                    {
                        update_due_date_section.hide();
                        refresh_todo(btn_element.closest('.todo-section'));
                    }
                },
                error: function(e){
                    Swal.fire({
                        text: "{{ __('messages.something-went-wrong')}}",
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
            });
        });

        $(document).on('click','.save_notes_button',function(){
            element = $(this);
            update_task_note(element);
        });

        $(document).on('click','.toggle_details',function(){
            display_details = $(this).closest('.todo-section').attr('display_details');
            display_details = display_details == 1 ? 0 : 1;
            $(this).closest('.todo-section').attr('display_details',display_details);
            if(display_details)
            {
                $(this).closest('.todo-section').find('.details_section').show();
            }
            else
            {
                $(this).closest('.todo-section').find('.details_section').hide();
            }
        });

        function update_task_status(element, is_done)
        {
            data = {
                todo_task_id: element.data('todo_task_id'),
                todo_access_id:  element.data('todo_access_id'),
                _token: '{{ csrf_token() }}',
                is_done: is_done
            };

            $.ajax({
                url: "{{ route('todo.update_task_status') }}",
                type : 'POST',
                data: data,
                success: function(response){
                    if(response.status != 1)
                    {
                        message = response.message || "{{ __('messages.something-went-wrong')}}"
                        Swal.fire({
                            text: message,
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        });
                    }
                    else
                    {
                        element.prop('checked', is_done);
                        refresh_todo(element.closest('.todo-section'));
                    }
                },
                error: function(e){
                    Swal.fire({
                        text: "{{ __('messages.something-went-wrong')}}",
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
            });
        }

        function update_task_note(element)
        {
            save_notes_section = element.closest('.notes_section').find('.save_notes_section');
            notes_field = element.closest('.notes_section').find('.notes_field');
            note_text = notes_field.val();

            data = {
                todo_task_id: notes_field.data('todo_task_id'),
                todo_access_id:  notes_field.data('todo_access_id'),
                _token: '{{ csrf_token() }}',
                note_text: note_text
            };

            $.ajax({
                url: "{{ route('todo.update_task_note') }}",
                type : 'POST',
                data: data,
                success: function(response){
                    if(response.status != 1)
                    {
                        message = response.message || "{{ __('messages.something-went-wrong')}}"
                        Swal.fire({
                            text: message,
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        });
                    }
                    else
                    {
                        notes_field.attr('data-old_value',note_text);
                        save_notes_section.hide();
                        refresh_todo(element.closest('.todo-section'));
                    }
                },
                error: function(e){
                    Swal.fire({
                        text: "{{ __('messages.something-went-wrong')}}",
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
            });
        }

        function refresh_todo(element)
        {
            data = {
                todo_access_id: element.data('todo_access_id'),
                display_details: element.attr('display_details'),
                loaded_from_page: element.attr('loaded_from_page'),
            };

            $.ajax({
                url: "{{ route('todo.details') }}",
                type : 'GET',
                data: data,
                success: function(response){
                    if(response.status != 1)
                    {
                        message = response.message || "{{ __('messages.something-went-wrong')}}"
                        Swal.fire({
                            text: message,
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        });
                    }
                    else
                    {
                        element.after(response.html);
                        element.remove();
                        if(response.my_todo_alert_count > 0)
                        {
                            $('.nav_my_todo_count').text(response.my_todo_alert_count).show();
                        }
                        else
                        {
                            $('.nav_my_todo_count').hide();
                        }

                        if(response.all_student_todo_alert_count > 0)
                        {
                            $('.nav_all_student_todo_count').text(response.all_student_todo_alert_count).show();
                        }
                        else
                        {
                            $('.nav_all_student_todo_count').hide();
                        }

                        if(response.student_todo_alert_count > 0)
                        {
                            $('.tab_student_todo_count').text(response.student_todo_alert_count).show();
                        }
                        else
                        {
                            $('.tab_student_todo_count').hide();
                        }

                        // for custom functions to run on any pages that includes this page
                        // e.g. used to refresh the tooltip after dom content updated on home page.
                        if(typeof onTodoHTMlUpdated == "function"){
                            onTodoHTMlUpdated();
                        }
                    }
                },
                error: function(e){
                    Swal.fire({
                        text: "{{ __('messages.something-went-wrong')}}",
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
            });
        }
    });
</script>
@endpush
