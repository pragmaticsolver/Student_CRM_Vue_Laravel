@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            <form method="POST" action="{{ route('class-category.update', $category->id) }}">
                @csrf
                @method('PATCH')
                <h1>{{ __('messages.edit-class-category') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
                    <div class="col-lg-10">
                        <input name="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ $category->name }}" required="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.visibility-roles') }}</label>
                    <div class="col-lg-10">
                        @if($roles->count() > 0)
                            @foreach($roles as $role)
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input{{ $errors->has('visible_user_roles') ? ' is-invalid' : '' }}" type="checkbox"
                                        value="{{ $role->name }}" name="visible_user_roles[]"
                                        @if(in_array($role->name, json_decode($category->visible_user_roles))) checked @endif
                                    >
                                    <label class="form-check-label">{{ $role->name }}</label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="add" type="submit" value="{{ __('messages.edit') }}" class="form-control btn-success">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
