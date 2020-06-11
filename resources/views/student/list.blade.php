@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <form action="" id="filter_form">
            <input type="hidden" name="sort_field" id="sort_field" value="" >
            <input type="hidden" name="sort_dir" id="sort_dir" value="" >
            <input type="hidden" name="role_id" id="role_id" value="" >
        </form>
        <form id="reconfirm_form" method="post">
            @csrf
        </form>
        <div class="col-12">
            @if(session()->get('success'))
                    <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            @include('partials.error')
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-6">
                    <h1 class="pull-left">{{ __('messages.studentlist') }}</h1>
                    <div class="pull-right">
                        <label for="" class="mb-0">{{ __('messages.role') }}</label>
                        <select name="role_id" id="role_filter" class="form-control mb-1">
                            <option value="all" selected >{{ __('messages.all') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $filter['role_id']  == $role->id ? 'selected' : ''}}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-6 mb-1">
                    <div class="pull-right">
                        @can('student-create')
                            <a class="btn btn-success" href="{{ url('/student/create') }}">{{ __('messages.addstudent') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <table class="table table-hover">
                <tbody>
                    @if(!$students->isEmpty())
                        <tr>
                            <th data-collumn_name="fullname" class="collumn_sort">{{ __('messages.name') }}</th>
                            <th data-collumn_name="status" class="collumn_sort">{{ __('messages.contractstatus') }}</th>
                            <th data-collumn_name="join_date" class="collumn_sort">{{ __('messages.startdate') }}</th>
                            <th data-collumn_name="roles.name" class="collumn_sort">{{ __('messages.role') }}</th>
                            <th  class="text-center">{{  __('messages.todo-status') }}</th>
                            <th data-collumn_name="email_verifications.is_email_verified" class="collumn_sort">{{ __('messages.verify-status') }}</th>
                            <th data-collumn_name="email_verifications.uses_parent_email" class="collumn_sort">{{ __('messages.inherits-parent-email') }}</th>
                            <th width="320px;">{{ __('messages.actions') }}</th>
                        </tr>
                        @foreach($students as $student)
                            <tr>
                                <td><a href="{{ url('/student/'.$student->id) }}" data-toggle="popover" data-placement="right" data-img="{{ $student->image ? $student->getImageUrl() : '' }}">{{$student->fullname}}</a></td>
                                @if($student->status == '0')
                                    <td>{{ __('messages.incontact') }}</td>
                                @elseif($student->status == '1')
                                    <td>{{ __('messages.active') }}</td>
                                @elseif($student->status == '2')
                                    <td>{{ __('messages.quit') }}</td>
                                @endif
                                <td>{{ $student->join_date }}</td>
                                <td class="role-col">{{ $student->role_name }}</td>
                                <td class="text-center">
                                    <span>{{ $student->done_todos_count." / ". $student->assigend_todos_count }}</span>
                                    @if($student->due_todos_count > 0)
                                    <br>
                                        <span class="text-danger">{{ $student->due_todos_count." due" }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->is_email_verified)
                                        {{__('messages.verified')}}
                                    @else
                                        <button type="button" class="btn btn-danger btn_reconfirm btn-sm mb-1" data-user_id="{{ $student->user_id }}">{{ __('messages.unverified') }}</button>
                                    
                                        @can('student-edit')
                                            <button type="button" class="btn btn-outline-danger btn_force_verify btn-sm mb-1" data-user_id="{{ $student->user_id }}">{{ __('messages.force-verify') }}</button>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($student->uses_parent_email)
                                    <i class="fa fa-check"></i>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-light btn_add_contact btn-sm mb-1"
                                        data-modal_title="{{$student->fullname}}"
                                        data-student_id="{{ $student->id }}"
                                        >
                                        {{ __('messages.addcontact') }}
                                    </button>
                                    @can('student-edit')
                                        <a class="btn btn-warning btn-sm mb-1" href="{{ route('student.edit', $student->id) }}">{{ __('messages.edit') }}</a>
                                        @if($student->role_name != \App\Role::ARCHIVED_STUDENT)
                                            <button class="btn btn-danger btn-sm mb-1 btn_archive_student" data-student_id="{{ $student->id }}">{{ __('messages.archive') }}</button>
                                        @endif
                                    @endcan
                                    @can('student-impersonate')
                                        <a class="btn btn-primary btn-sm mb-1" href="{{ route('student.start_impersonate', $student->user_id) }}">{{ __('messages.impersonate') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection


@push('modals')
    <div class="modal fade" id="add_contact_modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="padding:15px 15px;">
                    <h4><span class="fa fa-pencil"></span> <span class="modal-title"></span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" style="padding:40px 50px;">
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf
                        <div class="form-group">
                            <b>{{ __('messages.contacttype') }}:</b>
                            <label class="radio-inline"><input type="radio" name="type" value="denwa" checked=""> {{ __('messages.telephone') }}</label>
                            <label class="radio-inline"><input type="radio" name="type" value="line"> {{ __('messages.line') }}</label>
                            <label class="radio-inline"><input type="radio" name="type" value="direct"> {{ __('messages.direct') }}</label>
                            <label class="radio-inline"><input type="radio" name="type" value="mail"> {{ __('messages.email') }}</label>
                        </div>
                        <div class="form-group">
                            <textarea name="message" rows="10" placeholder="{{ __('messages.pleasewritecontentshere') }}" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}" required=""></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" value="" name="customer_id" id="customer_id">
                        </div>
                        <button type="submit" class="btn btn-success btn-block"><span class="fa fa-pencil"></span> {{ __('messages.record') }}</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-light pull-left" data-dismiss="modal" aria-label="Close"> Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        var _sort_field = "{{ @$filter['sort_field'] }}";
        var _sort_dir = "{{ @$filter['sort_dir'] }}";
        var reconfirm_url = "{{ url('student/reconfirm') }}"
        var force_verify_url = "{{ url('student/force-verify') }}"
    </script>
    <script src="{{ asset('public'.mix('js/page/student/list.js')) }}"></script>
@endpush
