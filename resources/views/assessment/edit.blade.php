@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
          <form method="POST" action="{{ route('assessment.update', $assessment->id) }}" enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <h1>{{ __('messages.editassessment') }}</h1>
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div><br/>
            @endif
            <div class="form-group row">
              <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
              <div class="col-lg-10">
                  <input name="name" type="text" value="{{ empty(old('name')) ? $assessment->name : old('name') }}" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" required="">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-lg-2 col-form-label">{{ __('messages.description') }}</label>
              <div class="col-lg-10">
                      <textarea name="description" rows="3" class="form-control" class="{{ $errors->has('description') ? ' is-invalid' : '' }}">{{ old('description',$assessment->description) }}</textarea>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-lg-2 col-form-label"></label>
              <div class="col-lg-10">
                  <input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
              </div>
            </div>
            </div>
          </form>
      </div>
    </div>
@endsection
