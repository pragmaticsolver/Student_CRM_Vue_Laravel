@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2" id="vue-app">
            <app-tag-list></app-tag-list>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('public'.mix('js/page/tags/index.js')) }}"></script>
@endpush
