@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.commenttemplates') }}</h1>
            <table class="table table-hover data-table order-column">
        	@if($comment_templates->count() > 0)
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.contenten') }}</th>
                        <th>{{ __('messages.contentja') }}</th>
                        <th>{{ __('messages.edit') }}</th>
                        <th>{{ __('messages.delete') }}</th>
                    </tr>
                </thead>
                <tbody>
        		@foreach($comment_templates as $comment_template)
        			<tr>
                        <td>{{ $comment_template->name }}</td>
                        <td><pre>{!! $comment_template->content_en !!}</pre></td>
                        <td><pre>{!! $comment_template->content_ja !!}</pre></td>
                        <td><a href="{{ route('comment_template.edit', $comment_template->id) }}" class="btn btn-success">{{ __('messages.edit') }}</a></td>
                        <td>
                            <form class="delete" method="POST" action="{{ route('comment_template.destroy', $comment_template->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
        		@endforeach
                </tbody>
        	@endif
            </table>
        </div>
    </div>
@endsection
