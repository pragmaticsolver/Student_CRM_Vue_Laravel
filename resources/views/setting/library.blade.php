@extends('layouts.app')

@section('content')
    <div class="row">
        <h1>{{ __('messages.librarysettings-page') }}</h1>
        <div class="col-12">
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div><br/>
            @endif
            <form method="POST" action="{{ route('library-settings.update') }}">
                @csrf
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.default-expectedcheckindays') }}</label>
                    <div class="col-lg-10">
                        <input type="number" name="expected_checkin_days" min=0 step=1 class="form-control" required="" value="{{ $expected_checkin_days }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.book-levels') }}</label>
                    <div class="col-lg-10">
                        <input type="text" name="book_levels" value="{{ $book_levels }}" class="level-selectize">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

