@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>{{ __('messages.planlist') }}</h1>
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}  
                </div><br/>
            @endif
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <th>{{ __('messages.plannumber') }}</th>
                        <th>{{ __('messages.paymentprice') }}</th>
                        <th>{{ __('messages.teachersalary') }}</th>
                        <th>{{ __('messages.pointvalue') }}</th>
                    </tr>
                    @if(!$plans->isEmpty())
                        @foreach($plans as $plan)
                            <tr>
                                <td>{{$plan->id}}</td>
                                <td>{{$plan->cost}}</td>
                                <td>{{$plan->cost_to_teacher}}</td>
                                <td>{{$plan->points}}</td>
                                @can('payment-plan-delete')
                                <td>
                                    <form class="delete" method="POST" action="{{ route('plan.destroy', $plan->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                    </form>
                                </td>
                                @endcan
		                    </tr>
                		@endforeach
                	@endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
