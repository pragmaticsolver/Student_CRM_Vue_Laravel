@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            <div class="clearfix mt-2 mb-2">
                <h1 class="float-left">{{ __('messages.class-category-list') }}</h1>
            </div>

            <table class="table table-hover data-table order-column">
                <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.number-of-class') }}</th>
                    <th>{{ __('messages.number-of-event') }}</th>
                    <th>{{ __('messages.visibility-roles') }}</th>
                    <th>{{ __('messages.edit') }}</th>
                    <th>{{ __('messages.delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($categories->count() > 0)
                    @foreach($categories as $category)
                        <tr>
                            <td><a href="{{ route('class-category.show', $category->id) }}">{{$category->name}}</a></td>
                            <td>{{ $category->get_classes()->count() }}</td>
                            <td>{{ $category->get_events()->count() }}</td>
                            <td>{{ $category->get_user_roles_label() }}</td>
                            <td><a href="{{ route('class-category.edit', $category->id) }}" class="btn btn-success">{{ __('messages.edit') }}</a><a></a></td>
                            <td>
                                <form class="delete" method="POST" action="{{ route('class-category.destroy', $category->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{ __('messages.empty-class-category') }}
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
