@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <h2>{{ __('messages.manageroles') }} <a class="pull-right btn btn-success" href="{{route('roles.create')}}">{{ __('messages.addnew') }}</a></h2>
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}  
                </div><br/>
            @endif
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <th>{{ __('messages.id') }}</th>
                        <th>{{ __('messages.rolename') }}</th>
                        <th>{{ __('messages.numberofusers') }}</th>
                        <th>{{ __('messages.defaultlanguage') }}</th>
                        <th>{{ __('messages.sendlogindetails') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                    @if($roles)
                        @foreach($roles as $role)
                            <tr>
                                <td>{{$role->id}}</td>
                                <td>{{ucfirst($role->name)}}</td>
                                <td>{{$rolecount[$role->id]}}</td>
                                <td>{{ $role->default_lang == 'en' ? 'English' : ($role->default_lang == 'ja' ? 'Japanese' : '') }}</td>
                                <td>{{ $role->send_login_details == 1 ? 'Yes' : 'No' }}</td>
                                <td>
                                    <a href="{{ url('/roles/'.$role->id.'/edit') }}" class="btn btn-warning">{{ __('messages.edit') }}</a>
                                    @if(!in_array($role->name, $undeleteable_roles))
                                    <form class="delete" method="POST" action="{{ route('roles.destroy', $role->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
