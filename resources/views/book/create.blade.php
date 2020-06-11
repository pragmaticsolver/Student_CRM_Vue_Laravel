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
            <form method="POST" action="{{ route('book.store') }}" enctype="multipart/form-data">
                @csrf
                <h1>{{ __('messages.addbook') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.bookname') }}</label>
                    <div class="col-lg-10">
                        <input name="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.quantity') }}</label>
                    <div class="col-lg-10">
                        <input name="quantity" type="number" min="0" class="form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}" value="{{ old('quantity') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.authorname') }}</label>
                    <div class="col-lg-10">
                        <input name="author_name" type="text" class="form-control{{ $errors->has('author_name') ? ' is-invalid' : '' }}" value="{{ old('author_name') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.level') }}</label>
                    <div class="col-lg-10">
                        <select name="level" class="form-control">
                            @if($book_levels)
                                @foreach($book_levels as $level)
                                    <option value="{{ $level }}">{{ $level }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.date') }}</label>
                    <div class="col-lg-10">
                        <input name="date" type="date" min="0" class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" value="{{ $today }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.barcode') }}</label>
                    <div class="col-lg-10">
                        <input name="barcode" type="text" class="form-control{{ $errors->has('barcode') ? ' is-invalid' : '' }}" value="{{ old('barcode') }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="cost" class="col-lg-2 col-form-label">{{ __('messages.thumbnail') }}</label>

                    <div class="col-lg-10 input-file-wrapper">
                        <div style="display: none" class="preview-section">
                            <img src="#" alt="thumbnail-image"/>
                        </div>
                        <div class="input-section">
                            <input type="file" class="insert-image {{ $errors->has('image') ? 'is-invalid' : '' }}" name="image" accept=".png,.jpg,.jpeg">
                            <small id="fileHelp" class="form-text text-muted">{{ __('messages.acceptfiletypes') }}</small>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="add" type="submit" value="{{ __('messages.add') }}" class="form-control btn-success">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
