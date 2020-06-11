@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
        	@include('partials.success')
            @include('partials.error')
	        <form method="POST" action="{{ isset($todo->id) ? route('todo.update',$todo->id) : route('todo.store') }}" enctype="multipart/form-data">
	        	@csrf
				<h1>{{ isset($todo->id) ? __('messages.edit-todo') : __('messages.add-todo') }}</h1>
				<div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.title') }}</label>
					<div class="col-lg-6">
						<input name="title" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ @old('title',$todo->title) }}" required>
					</div>
				</div>
				<div class="form-group row todo_section">
                    <label class="col-lg-2 col-form-label">{{ __('messages.tasks') }}</label>
                    <div class="col-lg-6">
                        <div class="todo_tasks_container">

                        </div>
                    </div>
				</div>
				<div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.assignee-users') }}</label>
					<div class="col-lg-6">
						<select name="user_id[]" id="user_id" class="form-control" multiple="multiple">
							@foreach($users as $user)
								<option value="{{ $user->id }}"
									{{  in_array($user->id, $access_users) ? 'selected' : '' }}
									>{{ $user->name.' ('.$user->getEmailAddress().')' }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.assignee-students') }}</label>
					<div class="col-lg-6">
						<select name="student_id[]" id="student_id" class="form-control" multiple="multiple">
							@foreach($students as $student)
								<option value="{{ $student->id }}"
									{{  in_array($student->id, $access_students) ? 'selected' : '' }}
									>{{ $student->fullname }}</option>
							@endforeach
						</select>
					</div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.due-date') }}</label>
                    @php
                        $due_days = @old('due_days',$todo->due_days);
                        if(!$due_days)
                        {
                            $due_days = 0;
                        }
                        $due_date = \Carbon\Carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->addDays($due_days)->format('Y-m-d');
                    @endphp
					<div class="col-lg-6">
                        <input
                        type="date"
                        name="due_date"
                        class="form-control due_date"
                        value="{{ $due_date }}"
                        min="{{ $date }}"
                        required>
                        <label for="">{{ __('messages.due-in') }} <span id="display_due_days">{{ $due_days }}</span> {{ __('messages.days-after-assignment') }}</label>
                    </div>
                    <input type="hidden" name="due_days" id="due_days" value="{{ $due_days }}">
                </div>
                <div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.start-alert-before-days') }}</label>
					<div class="col-lg-6">
                        <input type="text" name="start_alert_before_days" value="{{ @old('start_alert_before_days',$todo->start_alert_before_days) }}" class="form-control">
					</div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.attachments') }}</label>
                    <div class="col-lg-6">
                        <div class="input-group mb-3">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputGroupFile02" name="todo_files[]" multiple="true" data-default_placeholder="{{ __('messages.choose-files') }}">
                                <label class="custom-file-label" for="inputGroupFile02" aria-describedby="inputGroupFileAddon02"></label>
                            </div>
                        </div>
                        @if(isset($todo->todoFiles))
                            <table>
                                @foreach($todo->todoFiles as $file)
                                    <tr class="file-row">
                                        <input type="hidden" name="old_todo_ids[]" value="{{ $file->id }}">
                                        <td style="width:87%">
                                            <a target="_blank" href="{{ \Storage::disk('public')->url($file->file_path) }}" style="margin:4px;">
                                                <i class="fa fa-file"></i> {{ $file->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <input type="button" value="Remove" class="btn btn-sm btn-danger ml-1" onclick="$(this).closest('.file-row').remove();">
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </div>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label"></label>
	            	<div class="col-lg-6">
						  <input name="add" type="submit"
						  value="{{ isset($todo->id) ? __('messages.edit') : __('messages.add-todo')  }}"
						  class="form-control btn-success">
	            	</div>
	          	</div>
	        </form>
      	</div>
    </div>
@endsection

@push('scripts')
<script>
	window.addEventListener('DOMContentLoaded', function() {
        (function($) {
            $('#user_id,#student_id').select2({ width: '100%'  });

			$.each(tasks, function(i, task){
				add_task(task)
			});
			if(tasks.length == 0)
			{
				add_task();
			}

			$('.add_task').click(function(){
				add_task();
            });

            $('.due_date').change(function(){
                const date1 = new Date();
                const date2 = new Date($(this).val());
                var diffTime = date2 - date1;
                if(diffTime < 0)
                {
                    diffTime = 0;
                }
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                $('#display_due_days').text(diffDays);
                $('#due_days').val(diffDays);
            });
        })(jQuery);
	});

	var tasks = <?php echo $tasks ?>;
	var task_counter = 0;
    function add_task(task)
    {
        task_id = task ? task.id : '';
        task_text = task ? task.task : '';
        var html = `
                <div class="row mb-1">
                    <input type="hidden" name="todo_task_id[]" value="${task_id}" >

                    <div class="col-sm-10 pr-0">
                        <input name="todo_task[]" type="text" value="${task_text}" class="form-control" required >
					</div>
                    <div class="col-sm-2">`;
		if(task_counter > 0)
		{
			html +=  `<input type="button" tabindex="-1" value="{{ __('messages.remove') }}" class="btn btn-danger btn-sm mt-1" onclick="remove_task(this)"> `;
		}
		else
		{
			html +=  `<input type="button" value="{{ __('messages.add') }}" class="btn btn-primary btn-sm mt-1 add_task">`;
		}
        html += `</div></div>`;
		$('.todo_tasks_container').append(html);
		task_counter++;
    }

    function remove_task(element)
    {
        $(element).closest('.row').remove();
    }
</script>
@endpush
