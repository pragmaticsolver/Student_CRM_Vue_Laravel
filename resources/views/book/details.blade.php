@extends('layouts.app')

@section('content')
    <div class="justify-content-center">
            <h1>{{ __('messages.bookdetails') }}</h1>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.bookname') }}</th>
                    <th>{{ __('messages.quantity') }}</th>
                    <th>{{ __('messages.authorname') }}</th>
                    <th>{{ __('messages.level') }}</th>
                    <th>{{ __('messages.thumbnail') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.barcode') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $book->name }}</td>
                    <td>{{ $book->quantity }}</td>
                    <td>{{ $book->author_name }}</td>
                    <td>{{ $book->level }}</td>
                    <td>@if($book->thumbnail) {!! $book->the_image() !!} @endif</td>
                    <td>{{ $book->date }}</td>
                    <td>{{ $book->barcode }}</td>
                </tr>
            </tbody>
        </table>

        <h3>{{ __('messages.book-checkout-history')}}</h3>
        <table class='table table-hover'>
            <tr>
                @foreach(App\BookStudents::get_history_columns('book') as $column)
                    <td>{{ $column }}</td>
                @endforeach
            </tr>
            @if($book->book_students->count() > 0)
                @foreach($book->book_students as $book_student)
                    <tr>
                        @foreach($book_student->get_history_column_values('book') as $column_value)
                            <td>{{ $column_value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
@endsection
