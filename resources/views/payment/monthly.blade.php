@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <h1>{{ __('messages.monthlyaccounting') }}</h1>
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}  
                </div><br/>
            @endif
        </div>
        @if(!$payments->isEmpty())
            <div class="col-lg-12">
                <h2>今月の売り上げ</h2>
                <table class="table table-hover">
                    <tr>
                        <td>{{ __('messages.date') }}</td>
                        <td>{{ __('messages.name') }}</td>
                        <td>{{ __('messages.pointvalue') }}</td>
                        <td>{{ __('messages.sales') }}</td>
                    </tr>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{$payment->date}}</td>
                            <td>{{$payment->lastname_kanji}} {{$payment->firstname_kanji}}</td>
                            <td>{{$payment->points}}</td>
                            <td>{{$payment->price}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
        @if(!$attendances->isEmpty())
            <div class="col-lg-12">
                <h2>{{ __('messages.teacherpointcalculations') }}</h2>
                <table class="table table-hover">
                    <?php
                        $old_nickname = '';
                        $cost_total = 0;
                    ?>
                    @foreach($attendances as $attendance)
                        @if($attendance->nickname != $old_nickname)
                            <tr><td><h3>{{$attendance->nickname}} {{ __('messages.salarycalculations') }}</h3></td></tr>
                            <tr>
                                <td>{{ __('messages.date') }}</td>
                                <td>{{ __('messages.name') }}</td>
                                <td>{{ __('messages.pointvalue') }}</td>
                                <td>{{ __('messages.sales') }}</td>
                                <td>{{ __('messages.teachersalary') }}</td>
                                <td>{{ __('messages.total') }}</td>
                            </tr>
                            <?php
                                $old_nickname = $attendance->nickname;
                                $cost_to_teacher_total = 0;
                            ?>
                        @endif
                        @if($attendance->points != 0)
                            <?php
                                $cost_to_teacher_total += $attendance->cost_to_teacher; 
                            ?>
                            <tr class="<?php if($attendance->cancel_policy_id != NULL) echo 'badge-danger'; ?>">
                                <td>{{$attendance->date}}</td>
                                <td>{{$attendance->lastname_kanji}} {{$attendance->firstname_kanji}}</td>
                                <td>{{$attendance->points}}</td>
                                <td>{{$attendance->cost}}</td>
                                <td>{{$attendance->cost_to_teacher}}</td>
                                <td>{{$cost_to_teacher_total}}</td>
                            </tr>
                            <?php
                                $cost_total += $attendance->cost; 
                            ?>
                        @endif
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{ __('messages.total') }}</td>
                        <td>{{$cost_total}}</td>
                    </tr>
                </table>
            </div>
         @endif
    </div>
@endsection
