@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            <div class="ibox collapsed">
                <div class="ibox-title">
                    <h5>{{ __('messages.addcontact') }}</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                    </div>
                </div>
                <div class="ibox-content" style="display: none;">
                    <div class="row">
                        <div class="col-lg-12">
                            <form method="POST" action="{{ route('contact.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
                                    <div class="col-lg-10">
                                        <select class="form-control" name="customer_id" required="">
                                            <option value="">{{ __('messages.selectstudent') }}</option>
                                            @if(!$students->isEmpty())
                                                @foreach($students as $student)
                                                    <option value="{{$student->id}}" <?php if($student->id == old('customer_id')) echo 'selected'; ?>>{{$student->fullName}}</option>
                                                @endforeach
                                            @endif
                                        </select>​
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">{{ __('messages.contacttype') }}</label>
                                    <div class="col-lg-10">
                                        <label class="radio-inline"><input type="radio" name="type" value="denwa" checked=""> {{ __('messages.telephone') }}</label>
                                        <label class="radio-inline"><input type="radio" name="type" value="line"> {{ __('messages.line') }}</label>
                                        <label class="radio-inline"><input type="radio" name="type" value="direct"> {{ __('messages.direct') }}</label>
                                        <label class="radio-inline"><input type="radio" name="type" value="mail"> {{ __('messages.mail') }}</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">{{ __('messages.contents') }}</label>
                                    <div class="col-lg-10">
                                        <textarea name="message" rows="5" placeholder="{{ __('messages.pleasewritecontentshere') }}" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}" required="">{{old('message')}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-2"></div>
                                    <div class="col-lg-10">
                                        <button type="submit" class="btn btn-success btn-block" name="add"><span class="fa fa-pencil"></span> {{ __('messages.record') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <form method="GET" action="{{ route('contact.index') }}">
                <div class="input-group">
                    <input type="date" name="date" class="form-control" value="<?php if(isset($date)) echo $date; ?>" required>
                    <span class="input-group-btn">
                        <button class="btn btn-success" type="submit">{{ __('messages.datesearch') }}</button>
                    </span>
                </div>
            </form>
            <form method="GET" action="{{ route('contact.index')}}">
                <div class="input-group">
                    <input type="text" class="form-control" name="name" value="<?php if(isset($name)) echo $name; ?>" placeholder="{{ __('messages.namesearch') }}">
                    <span class="input-group-btn">
                        <button class="btn btn-success" type="submit">{{ __('messages.namesearch') }}</button>
                    </span>
                </div>
            </form>
            <table class="table table-hover">
                <tbody>
                	@if(!$contacts->isEmpty())
                        <tr>
                            <td>{{ __('messages.name') }}</td>
                            <td>{{ __('messages.contacttype') }}</td>
                            <td>{{ __('messages.date') }}</td>
                            <td>{{ __('messages.staff') }}</td>
                            <td>{{ __('messages.memo') }}</td>
                            <td>
                                {{ __('messages.actions') }}
                            </td>
                        </tr>
                		@foreach($contacts as $contact)
                			<tr>
		                        <td>
                                    <a href="{{ url('/student/'.$contact->student->id) }}">
                                        {{ $contact->student->fullName }}
                                    </a>
                                    <br>
                                </td>
		                        <td>{{ $contact->type }}</td>
		                        <td>{{ $contact->getLocalDate() }}</td>
		                        <td>{{ isset($contact->createdBy->name) ? $contact->createdBy->name : ''}}</td>
                                <td>{{ $contact->message }}</td>
                                <td>
                                    @can('contact-delete')
                                        <form class="delete" method="POST" action="{{ route('contact.destroy', $contact->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" class="submit btn btn-danger">{{ __('messages.delete') }}</a>
                                        </form>
                                    @endcan
                                </td>
		                    </tr>
                		@endforeach
                	@endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
