@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <h2>{{ __('messages.teacherlist') }}</h2>
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}  
                </div><br/>
            @endif
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.nickname') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.zoom-email') }}</th>
                        <th>{{ __('messages.address') }}</th>
                        <th>{{ __('messages.birthday') }}</th>
                        <th>{{ __('messages.profile') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                    @if(!$teachers->isEmpty())
                        @foreach($teachers as $teacher)
                            <tr>
                                <td><a href="{{ url('/teacher/'.$teacher->id) }}">{{$teacher->name}}({{$teacher->furigana}})</a></td>
                                <td>{{$teacher->nickname}}</td>
                                <td>{{ $teacher->user->email }}</td>
                                <td>{{ $teacher->user->zoom_email }}</td>
                                <td>{{$teacher->birthplace}}</td>
                                <td>{{$teacher->birthday}}</td>
                                <td>{{$teacher->profile}}</td>
                                <td>
                                    @can('teacher-edit')
                                        <a href="{{ url('/teacher/'.$teacher->id.'/edit') }}" class="btn btn-sm btn-warning mb-1">{{ __('messages.edit') }}</a>
                                        @if($teacher->status != 1)
                                            <button class="btn btn-danger btn-sm mb-1 btn_archive_teacher" data-teacher_id="{{ $teacher->id }}" data-teacher_name="{{ $teacher->nickname }}">{{ __('messages.archive') }}</button>
                                        @endif
                                    @endcan

                                    @can('teacher-delete')
                                        <form class="delete" method="POST" action="{{ route('teacher.destroy', $teacher->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger mb-1" type="submit">{{ __('messages.delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            @if(!$archivedTeachers->isEmpty())
                <h2>{{ __('messages.archived-teachers') }}</h2>
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.nickname') }}</th>
                            <th>{{ __('messages.email') }}</th>
                            <th>{{ __('messages.zoom-email') }}</th>
                            <th>{{ __('messages.address') }}</th>
                            <th>{{ __('messages.birthday') }}</th>
                            <th>{{ __('messages.profile') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                            @foreach($archivedTeachers as $teacher)
                                <tr>
                                    <td><a href="{{ url('/teacher/'.$teacher->id) }}">{{$teacher->name}}({{$teacher->furigana}})</a></td>
                                    <td>{{$teacher->nickname}}</td>
                                    <td>{{ $teacher->user->email }}</td>
                                    <td>{{ $teacher->user->zoom_email }}</td>
                                    <td>{{$teacher->birthplace}}</td>
                                    <td>{{$teacher->birthday}}</td>
                                    <td>{{$teacher->profile}}</td>
                                    <td>
                                        @can('teacher-edit')
                                            <a href="{{ url('/teacher/'.$teacher->id.'/edit') }}" class="btn btn-sm btn-warning mb-1">{{ __('messages.edit') }}</a>
                                        @endcan

                                        @can('teacher-delete')
                                            <form class="delete" method="POST" action="{{ route('teacher.destroy', $teacher->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger mb-1" type="submit">{{ __('messages.delete') }}</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal inmodal" id="DropEventModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.archive-teacher') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="archive_teacher_form" method="POST" action="" >
                        @csrf
                        
                        <input type="hidden" name="teacher_id" value="">
                        
                        <div class="form-group row">
                            <label class="col-lg-12 col-form-label">{{ __('messages.select-teacher-who-will-takeover-future-classes-of') }} <span id="current_teacher_name"></span></label>
                            <div class="col-lg-12">
                                <select name="take_over_teacher_id" class="form-control" required="">
                                    <option value="">{{ __('messages.selectteacher') }}</option>
                                    @if(!$teachers->isEmpty())
                                        @foreach($teachers as $teacher)
                                            <option value="{{$teacher->id}}">{{$teacher->nickname}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div id="schedule-date">
                            <div class="form-group row">
                                <label class="col-lg-12 col-form-label">{{ __('messages.takeover-classes-from-date') }}: </label>
                                <div class="col-lg-12">
                                    <input class="form-control" type="date" name="take_over_date" required >
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                        <input type="hidden" name="teacher_id" value="">
                        <button type="submit" class="btn btn-primary pull-left">{{ __('messages.archive') }}</button>
                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close">{{ __('messages.cancel')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush


@push('scripts')
    <script>
        var today_date = "{{ $today_date }}";
    </script>
    <script src="{{ asset('public'.mix('js/page/teacher/list.js')) }}"></script>
@endpush