@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12" id="vue-app">
            <h1>{{ __('messages.assessmentlist') }}</h1>
            <table class="table table-hover data-table order-column">
        	@if(!$assessments->isEmpty())
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.type') }}</th>
                        <th>{{ __('messages.description') }}</th>
                        <th>{{ __('messages.numberofquestions') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
        		@foreach($assessments as $assessment)
        			<tr>
                        <td><a href="{{ url('/assessment/details/'.$assessment->id) }}">{{ $assessment->name }}</a></td>
                        <td>{{ $assessment->type }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($assessment->description, $limit = 50, $end = '...')   }}</td>
                        <td>{{ $assessment->assessment_questions->count() }}</td>
                        <td>
                            <a href="{{ route('assessment.preview', $assessment->id) }}" class="btn btn-warning">{{ __('messages.preview-assessment') }}</a>
                            <a href="{{ url('/assessment-question/add?assessment_id='.$assessment->id) }}" class="btn btn-success m-1">{{ __('messages.addquestion') }}</a>
                            <a href="{{ url('/assessment/edit/'.$assessment->id) }}" class="btn btn-success m-1">{{ __('messages.edit') }}</a>
                            <button class="btn btn-primary assign_assessment_btn" data-id="{{ $assessment->id }}" >{{ __('messages.assign-assessment')  }}</button>
                            <a href="{{ route('assessment.responses',$assessment->id) }}" class="btn btn-primary assign_assessment_btn">{{ __('messages.view-responses') }}</a>
                            <form class="delete mb-0 m-1" method="POST" action="{{ route('assessment.destroy', $assessment->id) }}">
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
            <app-assign-assessment v-if="assign_assessment_id" 
                :assessment_id="assign_assessment_id"
                @modalclose="assign_assessment_id = null"
                @assignment-assigned="assignmentAssigned"></app-assign-assessment>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public'.mix('js/page/assessment/list.js')) }}"></script>
@endpush