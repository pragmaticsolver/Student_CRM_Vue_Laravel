@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1>{{ __('messages.classdetails') }}</h1>
        </div>
        <div class="col-lg-2">
            <a href="{{ url('/class/'.$class->id.'/edit') }}" class="btn btn-warning">{{ __('messages.edit') }}</a>
            <form class="delete" method="POST" action="{{ route('class.destroy', $class->id) }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
            </form>
        </div>
        <div class="col-lg-3">{{ __('messages.classname') }}</div>
        <div class="col-lg-3">{{ __('messages.dayofweek') }}</div>
        <div class="col-lg-3">{{ __('messages.classtime') }}</div>
        <div class="col-lg-3">{{ __('messages.classteacher') }}</div>

        <div class='col-lg-3'>{{$class->title}}</div>
        <div class='col-lg-3'></div>
        <div class='col-lg-3'></div>
        <div class='col-lg-3'></div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#memo">{{ __('messages.classmemo') }}</button>

            <!-- Modal -->
            <div id="memo" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>{{ __('messages.classmemo') }}</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="progress/memo.php" id="memo">
                                <div class="form-group row">
                                    <label class="col-lg-2">{{ __('messages.memo') }}</label>
                                    <div class="col-lg-10">
                                        <textarea name="memo" class="form-control" rows="7" required=""></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2">{{ __('messages.date') }}</label>
                                    <div class="col-lg-10">
                                        <input type="date" name="date" value="{{ $date }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2"></label>
                                    <div class="col-lg-10">
                                        <input type="hidden" value="{{$class->id}}" name="class_id">
                                        <input name="add" type="button" value="{{ __('messages.addmemo') }}" class="form-control btn-success">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
