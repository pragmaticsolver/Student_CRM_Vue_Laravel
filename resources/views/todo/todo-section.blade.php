@php
    $_todo = $todoAccess->todo;
    $_todo_task_status = $todoAccess->todoTaskStatusByTaskId();
    $_todo_task_note = $todoAccess->todoTaskNoteByTaskId();
    $display_details = isset($display_details) && $display_details == 1 ? 1 : 0;
    $loaded_from_page = isset($loaded_from_page) ? $loaded_from_page : '';
@endphp
<div class="col py-3 todo-section" data-todo_access_id="{{ $todoAccess->id }}" display_details="{{ $display_details }}" loaded_from_page="{{ $loaded_from_page }}">
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-12">
                    <div class="pull-left">
                        <h2>{{ $_todo->title }}</h2>
                        @if($loaded_from_page == 'home')
                            @if($todoAccess->student)
                                <a href="{{ url('/student/'.$todoAccess->student->id) }}" data-toggle="popover" data-placement="right" data-img="{{ $todoAccess->student->image ? $todoAccess->student->getImageUrl() : '' }}">{{ $todoAccess->student->getFullNameAttribute() }}</a>
                            @endif
                        @endif
                    </div>
                    <div class="pull-right">
                        <input class="btn btn-sm toggle_details" type="button" name="" id="" value="{{ __('messages.toggle-details') }}">
                    </div>
                </div>
            </div>
            @foreach($_todo->todoTasks as $todoTask)
                <div class="row mt-1">
                    @php
                        $is_done = 0;
                        $status_line = '';
                        $note_text = '';
                        $note_status_line = '';
                        if(isset($_todo_task_status[$todoTask->id]))
                        {
                            $todoTaskStatus = $_todo_task_status[$todoTask->id];
                            if($todoTaskStatus->status == 1)
                            {
                                $is_done = 1;
                            }
                            $updated_at = $todoTaskStatus->getLocalUpdatedAt()->format('Y/m/d H:i');
                            $updated_by = @$todoTaskStatus->updatedByUser->name;
                            $status_line = ( $is_done ?  __('messages.marked-as-complete-by') : __('messages.marked-as-incomplete-by') ).' '.$updated_by.' at '.$updated_at;
                        }
                        if(isset($_todo_task_note[$todoTask->id]))
                        {
                            $todoTaskNote = $_todo_task_note[$todoTask->id];
                            $note_text = $todoTaskNote->note_text;
                            $updated_at = $todoTaskNote->getLocalUpdatedAt()->format('Y/m/d H:i');
                            $updated_by = @$todoTaskNote->updatedByUser->name;
                            $note_status_line =  __('messages.saved-by').' '.$updated_by.' at '.$updated_at;
                        }
                    @endphp
                    <div class="col-sm-1 pr-0">
                        <input type="checkbox"
                        data-todo_task_id="{{ $todoTask->id }}"
                        data-todo_access_id="{{ $todoAccess->id }}"
                        {{ $is_done  ? 'checked' : '' }}
                        class="todo_task_cb form-control my-1" style="width:25px;padding-right:0px;">
                    </div>
                    <div class="col-sm-11 pl-0">
                        <input type="text" class="form-control ml-0 my-1" value="{{ $todoTask->task }}" disabled>
                    </div>
                </div>
                <div class="row details_section" style="{{ $display_details == 0 ? 'display:none;' : '' }}">
                    @if($status_line)
                        <div class="col-sm-12 pl-0 text-right">
                            <label style="font-style: italic;">{{ $status_line }}</label>
                        </div>
                    @endif
                    <div class="col-sm-12 pl-0 text-right notes_section">
                        <label class="col-sm-11 pl-0 text-left" for="">{{ __('messages.notes') }}:</label>
                        <textarea name=""
                        class="form-control pull-right col-sm-11 notes_field"
                        rows="2"
                        data-todo_task_id="{{ $todoTask->id }}"
                        data-todo_access_id="{{ $todoAccess->id }}"
                        data-old_value = "{{ $note_text }}"
                        >{{ $note_text }}</textarea>
                        <div class="save_notes_section mt-1" style="display:none;">
                            <input
                                type="button"
                                value="{{ __('messages.save-notes') }}"
                                class="btn btn-primary my-1 save_notes_button">
                        </div>
                    </div>
                    @if($note_status_line)
                        <div class="col-sm-12 pl-0 text-right">
                            <label style="font-style: italic;">{{ $note_status_line }}</label>
                        </div>
                    @endif
                </div>
            @endforeach
            @if(count($_todo->todoFiles) > 0)
                <div class="row my-1">
                    <label class="col-sm-12" for="">{{ __('messages.attachments')}}:</label>
                    @foreach($_todo->todoFiles as $file)
                        <a class="col-sm-12" target="_blank" href="{{ \Storage::disk('public')->url($file->file_path) }}" style="margin:4px;">
                            <i class="fa fa-file"></i> {{ $file->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="col-sm-3">
            <p class="text-{{ $todoAccess->status['class'] }}">{{ $todoAccess->status['status'] }}</p>
            <div class="row my-1">
                    <div class="col-sm-12">
                        <label for="">{{ __('messages.assigned-on')}}:</label>
                        <input
                        type="text"
                        class="form-control"
                        value="{{ $todoAccess->getLocalCreatedAt()->format('Y/m/d') }}"
                        data-todo_task_id="{{ $todoAccess->id }}"
                        disabled>
                    </div>
                </div>
            <div class="row my-1">
                <div class="col-sm-12">
                    <label>{{ __('messages.duedate')}}:</label>
                    <input
                    type="date"
                    class="form-control due_date"
                    value="{{ $todoAccess->custom_due_date ? $todoAccess->custom_due_date : $todoAccess->due_date }}"
                    data-todo_task_id="{{ $todoAccess->id }}"
                    min="{{ $todoAccess->getLocalCreatedAt()->format('Y-m-d') }}"
                    required>

                    <div class="update_due_date_section mt-1" style="display:none;">
                        <input
                            type="button"
                            value="{{ __('messages.update-duedate') }}"
                            class="btn btn-primary my-1 update_due_date_btn">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
