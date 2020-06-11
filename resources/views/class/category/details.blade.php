@extends('layouts.app')

@section('content')
    <div class="row mt-2">
        <div class="col-lg-8">
            <h1>{{ __('messages.class-category-details') }}</h1>
        </div>
        <div class="col-lg-4 text-right">
            <a href="{{ route('class.create', ['category_id' => $category->id]) }}" class="btn btn-success">{{ __('messages.addclass') }}</a>
            <a href="{{ route('event.create', ['category_id' => $category->id]) }}" class="btn btn-success">{{ __('messages.addevent') }}</a>
            <a href="{{ route('class-category.edit', $category->id) }}" class="btn btn-warning">{{ __('messages.edit') }}</a>
            <form class="delete" method="POST" action="{{ route('class-category.destroy', $category->id) }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
            </form>
        </div>
        <table class="table table-hover">
            <thead>
                <th>{{ __('messages.category-name') }}</th>
                <th>{{ __('messages.number-of-class') }}</th>
                <th>{{ __('messages.number-of-event') }}</th>
                <th>{{ __('messages.visibility-roles') }}</th>
            </thead>
            <tbody>
            <td>{{ $category->name }}</td>
            <td>{{ $classes->count() }}</td>
            <td>{{ $events->count() }}</td>
            <td>{{ $category->get_user_roles_label() }}</td>
            </tbody>
        </table>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-6">
            @can('class-list')
                <h1>{{ __('messages.classlist') }}</h1>
                @if($classes->count() > 0)
                    <table class="table table-hover data-table">
                        <thead>
                        <tr>
                            <th>{{ __('messages.classname') }}</th>
                            @can('class-edit')
                                <th>{{ __('messages.edit') }}</th>
                            @endcan
                            @can('class-delete')
                                <th>{{ __('messages.delete') }}</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($classes as $class)
                            <tr>
                                <td><a href="{{ url('/class/'.$class->id) }}">{{$class->title}}</a></td>
                                @can('class-edit')
                                    <td><a href="{{ url('/class/'.$class->id.'/edit') }}" class="btn btn-success">{{ __('messages.edit') }}</a><a></a></td>
                                @endcan
                                @can('class-delete')
                                    <td>
                                        <form class="delete" method="POST" action="{{ route('class.destroy', $class->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            @endcan
        </div>
        <div class="col-lg-6">
            @can('event')
                <h1>{{ __('messages.eventlist') }}</h1>
                @if($events->count() > 0)
                    <table class="table table-hover data-table">
                        <thead>
                        <tr>
                            <th>{{ __('messages.eventname') }}</th>
                            <th>{{ __('messages.edit') }}</th>
                            <th>{{ __('messages.delete') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td><a href="{{ url('/event/'.$event->id) }}">{{$event->title}}</a></td>
                                <td><a href="{{ url('/event/'.$event->id.'/edit') }}" class="btn btn-success">{{ __('messages.edit') }}</a></td>
                                <td>
                                    <form class="delete" method="POST" action="{{ route('event.destroy', $event->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            @endcan
        </div>
    </div>
@endsection
