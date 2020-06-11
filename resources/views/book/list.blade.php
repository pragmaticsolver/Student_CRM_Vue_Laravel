@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            <div class="clearfix mt-2 mb-2">
                <h1 class="float-left">{{ __('messages.booklist') }}</h1>
                <div class="float-right mt-2">
                    <a href="{{ url('/book/checkin') }}" class="btn btn-primary">{{ __('messages.checkin') }}</a>
                    <a href="{{ url('/book/checkout') }}" class="btn btn-primary">{{ __('messages.checkout') }}</a>
                </div>
            </div>

            <table class="table table-hover data-table order-column">
                <thead>
                    <tr>
                        <th>{{ __('messages.bookname') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.authorname') }}</th>
                        <th>{{ __('messages.level') }}</th>
                        <th>{{ __('messages.thumbnail') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.barcode') }}</th>
                        <th>{{ __('messages.checkout-times') }}</th>
                        <th>{{ __('messages.edit') }}</th>
                        <th>{{ __('messages.delete') }}</th>
                    </tr>
                </thead>
                <tbody>
                @if(!$books->isEmpty())
                    @foreach($books as $book)
                        <tr>
                            <td><a href="{{ url('/book/'.$book->id) }}">{{$book->name}}</a></td>
                            <td>{{ $book->quantity }}</td>
                            <td>{{ $book->author_name }}</td>
                            <td>{{ $book->level }}</td>
                            <td>@if($book->thumbnail) {!! $book->the_image() !!} @endif</td>
                            <td>{{ $book->date }}</td>
                            <td>{{ $book->barcode }}</td>
                            <td>{{ $book->book_students->count() }}</td>
                            <td><a href="{{ url('/book/'.$book->id.'/edit') }}" class="btn btn-success">{{ __('messages.edit') }}</a><a></a></td>
                            <td>
                                <form class="delete" method="POST" action="{{ route('book.destroy', $book->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{ __('messages.emptybook') }}
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
