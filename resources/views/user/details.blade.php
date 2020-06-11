@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
		<div class="col-lg-12">
            <h2>{{ __('messages.user')}}: {{$user->name}}</h2>
            <div class="col-lg-12">
                <table class="table table-striped">
                    <tr>
                        <th>{{ __('messages.id')}}</th>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.name')}}</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.username')}}</th>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.email')}}</th>
                        <td>{{ $user->getEmailAddress() }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.roles')}}</th>
                        <td>{{$user->roles()->pluck('name')->implode(',')}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
