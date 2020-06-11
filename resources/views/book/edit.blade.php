@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div><br/>
            @endif
            <form method="POST" action="{{ route('book.update', $book->id) }}" enctype="multipart/form-data">
                @method('PATCH')
                @csrf
                <h1>{{ __('messages.editbook') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.bookname') }}</label>
                    <div class="col-lg-10">
                        <input name="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ empty(old('name')) ? $book->name : old('name') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.quantity') }}</label>
                    <div class="col-lg-10">
                        <input name="quantity" type="number" min="0" class="form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}" value="{{ empty(old('quantity')) ? $book->quantity : old('quantity') }}" required="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.authorname') }}</label>
                    <div class="col-lg-10">
                        <input name="author_name" type="text" class="form-control{{ $errors->has('author_name') ? ' is-invalid' : '' }}" value="{{ $book->author_name }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.level') }}</label>
                    <div class="col-lg-10">
                        <select name="level" class="form-control">
                            @if($book_levels)
                                @foreach($book_levels as $level)
                                    <option value="{{ $level }}"
                                            @if($level == $book->level) selected @endif>{{ $level }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.date') }}</label>
                    <div class="col-lg-10">
                        <input name="date" type="date" min="0" class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" value="{{ $book->date }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.barcode') }}</label>
                    <div class="col-lg-10">
                        <input name="barcode" type="text" class="form-control{{ $errors->has('barcode') ? ' is-invalid' : '' }}" value="{{ $book->barcode }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="cost" class="col-lg-2 col-form-label">{{ __('messages.thumbnail') }}</label>

                    <div class="col-lg-10 input-file-wrapper">
                        <div @if(!$book->thumbnail) style="display: none" @endif class="preview-section">
                            {!! $book->the_image() !!}
                        </div>
                        <div class="input-section">
                            <input type="file" class="insert-image {{ $errors->has('image') ? 'is-invalid' : '' }}" name="image" accept=".png,.jpg,.jpeg">
                            <small id="fileHelp" class="form-text text-muted">{{ __('messages.acceptfiletypes') }}</small>
                            <input type="hidden" name="update_image" value="false" class="file-update">
                        </div>
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
