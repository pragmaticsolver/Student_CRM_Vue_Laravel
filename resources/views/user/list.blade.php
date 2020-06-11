@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <h2>{{ __('messages.manageusers')}} <a class="pull-right btn btn-success" href="{{route('users.create')}}">{{ __('messages.addnew')}}</a></h2>
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <th>{{ __('messages.id')}}</th>
                        <th>{{ __('messages.name')}}</th>
                        <th>{{ __('messages.username')}}</th>
                        <th>{{ __('messages.email')}}</th>
                        <th>{{ __('messages.role')}}</th>
                        <th>{{ __('messages.no-of-children-student')}}</th>
                        <th>{{ __('messages.actions')}}</th>
                    </tr>
                    @if($users)
                        @foreach($users as $user)
                            <tr>
                                <td><a href="{{ url('/users/'.$user->id) }}">{{$user->id}}</a></td>
                                <td><a href="{{ url('/users/'.$user->id) }}">{{$user->name}}</a></td>
                                <td><a href="{{ url('/users/'.$user->id) }}">{{$user->username}}</a></td>
                                <td>{{ $user->getEmailAddress() }}</td>
                                <td>{{ucfirst($user->roles()->pluck('name')->implode(', '))}}</td>
                                <td>
                                    @if($user->hasRole('parent'))
                                        {{ $user->children()->count()  }}
                                    @else
                                        NA
                                    @endif
                                </td>
                                <td><a href="{{ url('/users/'.$user->id.'/edit') }}" class="btn btn-warning">{{ __('messages.edit')}}</a>
                                    <form class="delete" method="POST" action="{{ route('users.destroy', $user->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">{{ __('messages.delete')}}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
