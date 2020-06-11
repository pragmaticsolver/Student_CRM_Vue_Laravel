@extends('layouts.app')

@section('content')
    @php  $email = $student->getEmailAddress(); @endphp
    @if($student->status == 0)
        <div class="row">
            <div class="col-lg-12">
                <h2>{{$student->$student->fullName}}</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox collapsed">
                    <div class="ibox-title">
                        <h5>連絡追加フォーム</h5>
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
                                        <label class="col-lg-2 col-form-label">お名前</label>
                                        <div class="col-lg-10">
                                            <select class="form-control" name="customer_id" required="">
                                                <option value="{{$student->id}}">{{$student->lastname_kanji}} {{$student->firstname_kanji}}</option>
                                            </select>​
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">連絡方法</label>
                                        <div class="col-lg-10">
                                            <label class="radio-inline"><input type="radio" name="type" value="denwa" checked=""> 電話</label>
                                            <label class="radio-inline"><input type="radio" name="type" value="line"> ライン</label>
                                            <label class="radio-inline"><input type="radio" name="type" value="direct"> 直接</label>
                                            <label class="radio-inline"><input type="radio" name="type" value="mail"> メール</label>
                                            <label class="radio-inline"><input type="radio" name="type" value="gmail"> gmail</label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label">連絡内容</label>
                                        <div class="col-lg-10">
                                            <textarea name="message" rows="5" placeholder="連絡内容を書いてください" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}" required="">{{old('message')}}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-2"></div>
                                        <div class="col-lg-10">
                                            <button type="submit" class="btn btn-success btn-block" name="add"><span class="fa fa-pencil"></span> 記録</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4"><h2>問い合わせ：</h2></div>
            <div class="col-lg-8"><h2>{{$student->toiawase_date}}</h2></div>
        </div>
        <div class="row">
            <div class="col-lg-4"><h2>問い合わせ受けた人：</h2></div>
            <div class="col-lg-8"><h2>{{$student->toiawase_getter}}</h2></div>
        </div>
        <div class="row">
            <div class="col-lg-4"><h2>問い合わせ方法：</h2></div>
            <div class="col-lg-8"><h2>{{$student->toiawase_houhou}}</h2></div>
        </div>
        <div class="row">
            <div class="col-lg-4"><h2>紹介者：</h2></div>
            <div class="col-lg-8"><h2>{{$student->toiawase_referral}}</h2></div>
        </div>
        @if($email)
            <div class="row">
                <div class="col-lg-4"><h2>Eメール：</h2></div>
                <div class="col-lg-8"><h2>{{ $email }}</h2></div>
                <div class="col-lg-4"></div>
            </div>
        @endif
        @if(!empty($student->home_phone))
            <div class="row">
                <div class="col-lg-4"><h2>固定電話：</h2></div>
                <div class="col-lg-8"><h2>{{$student->home_phone}}</h2></div>
                <div class="col-lg-4"></div>
            </div>
        @endif
        @if(!empty($student->mobile_phone))
            <div class="row">
                <div class="col-lg-4"><h2>携帯電話：</h2></div>
                <div class="col-lg-8"><h2>{{$student->mobile_phone}}</h2></div>
                <div class="col-lg-4"></div>
            </div>
        @endif
        @if(!empty($student->toiawase_memo))
            <div class="row">
                <div class="col-lg-4"><h2>問い合わせメモ：</h2></div>
                <div class="col-lg-8"><h2>{{$student->toiawase_memo}}</h2></div>
                <div class="col-lg-4"></div>
            </div>
        @endif
        @can('student-edit')
        <div class="row">
            <div class="col-lg-12">
                <a href="{{ url('/student/'.$student->id.'/edit') }}" class="btn btn-lg btn-success">情報編集</a>
            </div>
        </div>
        @endcan
        @if(!$yoteis->isEmpty())
            <div class="row mt-3">
                <div class="col-lg-12">
                    <h1>レベルチェック情報</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4"><h2>レベルチェック日付：</h2></div>
                <div class="col-lg-8"><h2>{{$yoteis[0]->date}}</h2></div>
                <div class="col-lg-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-4"><h2>レベルチェック時間：</h2></div>
                <div class="col-lg-8"><h2>{{$yoteis[0]->start_time}} {{$yoteis[0]->end_time}}</h2></div>
                <div class="col-lg-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-4"><h2>レベルチェック担当先生：</h2></div>
                <div class="col-lg-8"><h2>{{$yoteis[0]->name}}</h2></div>
                <div class="col-lg-4"></div>
            </div>
            @if($yoteis[0]->status == 0)
                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#levelcheckfinished">レベルチェック終了</button>
                    </div>
                </div>
            @endif
        @else
            <div class="row mt-3">
                <div class="col-lg-12">
                    <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#levelcheck">レベルチェック追加</button>
                </div>
            </div>
        @endif

        @if(!$yoyakus->isEmpty())
            @foreach($yoyakus as $yoyaku)
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <h1>体験情報</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4"><h2>体験日付：</h2></div>
                    <div class="col-lg-8"><h2>{{$yoyaku->start_time}}-{{$yoyaku->end_time}}</h2></div>
                    <div class="col-lg-4"></div>
                </div>
                <div class="row">
                    <div class="col-lg-4"><h2>体験クラス：</h2></div>
                    <div class="col-lg-8"><h2>{{$yoyaku->title}}</h2></div>
                    <div class="col-lg-4"></div>
                </div>
                <div class="row">
                    <div class="col-lg-4"><h2>体験担当先生：</h2></div>
                    <div class="col-lg-8"><h2>{{$yoyaku->nickname}}</h2></div>
                    <div class="col-lg-4"></div>
                </div>
            @endforeach
        @endif
    @else
        <div class="row">
            <div class="col-lg-12">
                @if(session()->get('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div><br/>
                @endif
                @if(session()->get('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div><br/>
                @endif
                <div class="border-bottom">
                    <div class="pull-left" style="max-width:50%">
                        <div class="align-middle d-inline-block">
                            <h2>{{ $student->fullName }}</h2>
                        </div>
                        @can('view-student-tags')
                            @php $enable_edit = \Auth::user()->hasPermissionTo('edit-student-tags') ? 'true' : 'false'; @endphp
                            <div id="vue-app" class="align-middle d-inline-block">
                                <app-student-tags
                                    :student_id="{{ $student->id }}"
                                    :student_tags="{{ json_encode($student->getTags()) }}"
                                    :enable_edit="{{ $enable_edit }}"
                                ></app-student-tags>
                            </div>
                        @endcan
                    </div>
                    <div class="pull-right">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                @if($use_points[0]->value == 'true')
                                    <a href="{{ url('/accounting/add/'.$student->id) }}" class="dropdown-item">{{ __('messages.addpayment')}}</a>
                                @endif
                                @if($use_monthly_payments[0]->value == 'true')
                                    <a href="{{ url('/accounting/monthly-payments/add/'.$student->id) }}" class="dropdown-item">{{ __('messages.add-payment')}}</a>
                                @endif
                                <a class="dropdown-item" data-toggle="modal" data-target="#add_contact_modal"
                                    data-modal_title="{{ $student->fullname}}"
                                    data-student_id="{{ $student->id }}" href="javascript:void(0);"
                                    > {{ __('messages.addcontact') }}
                                </a>
                                @can('student-edit')
                                    <a href="{{ url('/student/'.$student->id.'/edit') }}" class="dropdown-item">{{ __('messages.edit')}}</a>
                                    @if(!$student->isArchived())
                                        <a class="dropdown-item btn_archive_student" href="javascript:void(0);" data-student_id="{{ $student->id }}">{{ __('messages.archive') }}</a>
                                    @endif
                                @endcan
                                @can('student-delete')
                                    <form class="delete mb-0" method="POST" action="{{ route('student.destroy', $student->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <a class="dropdown-item" href="javascript:void(0);">{{ __('messages.delete')}}</a>
                                    </form>
                                @endcan
                                @can('student-impersonate')
                                    <a class="dropdown-item" href="{{ route('student.start_impersonate', $student->user_id) }}">{{ __('messages.impersonate') }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
                $nav = Request::query('nav');
                $student_todo_Count = $student->todo_alert_count($date);
            ?>
            <div class="col-lg-12 sticky_tabs_container">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link {{(!isset($nav)) || $nav == 'home' ? 'active' : ''}}" data-toggle="tab" href="#home">{{ __('messages.personalinformation')}}</a></li>
                    @if(!$contacts->isEmpty())
                        <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'renraku') ? 'active' : ''}}" data-toggle="tab" href="#renraku">{{ __('messages.contact')}}</a></li>
                    @endif
                    @can('student-edit')
                        <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'payment-settings') ? 'active' : ''}}" data-toggle="tab" href="#payment-settings">{{ __('messages.payment-settings') }}</a></li>
                    @endcan
                    
                    @if($use_points[0]->value == 'true')
                        <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'payment') ? 'active' : ''}}" data-toggle="tab" href="#payment">{{ __('messages.payment')}}</a></li>
                    @endif
                    @if($use_monthly_payments[0]->value == 'true')
                        @if(!$monthly_payments->isEmpty())
                            <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'monthlypayment') ? 'active' : ''}}" data-toggle="tab" href="#monthlypayment">{{ __('messages.monthlypayment')}}</a></li>
                        @endif
                        @if(!$oneoff_payments->isEmpty())
                            <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'otherpayments') ? 'active' : ''}}" data-toggle="tab" href="#otherpayments">{{ __('messages.other-payments')}}</a></li>
                        @endif
                    @endif
                    @if($events)
                        <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'eventpayment') ? 'active' : ''}}" data-toggle="tab" href="#eventpayment">{{ __('messages.eventpayment')}}</a></li>
                    @endif
                    @if($book_students->count() > 0)
                        <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'bookcheckout') ? 'active' : ''}}" data-toggle="tab" href="#bookcheckout">{{ __('messages.book-checkout-history')}}</a></li>
                    @endif
                    @if($student_tests->count() > 0)
                        <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'onlinetestresult') ? 'active' : ''}}" data-toggle="tab" href="#onlinetestresult">{{ __('messages.onlinetestresults')}}</a></li>
                    @endif
                    @if($paper_tests->count() > 0)
                        <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'papertestresult') ? 'active' : ''}}" data-toggle="tab" href="#papertestresult">{{ __('messages.papertestresults')}}</a></li>
                    @endif
                    @if($assessment_users->count() > 0)
                        <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'assessmentresult') ? 'active' : ''}}" data-toggle="tab" href="#assessmentresult">{{ __('messages.assessmentresults')}}</a></li>
                    @endif
                    @if($todoAccessList->count() > 0)
                    <li class="nav-item">
                        <a class="nav-link {{(isset($nav) && $nav == 'todo') ? 'active' : ''}}" data-toggle="tab" href="#todo">
                            {{ __('messages.todos')}} <span class="badge badge-danger tab_student_todo_count" style="font-size:12px;margin-left: 2px;{{ $student_todo_Count == 0 ? 'display:none;' : ''}}">{{ $student_todo_Count }}</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'classusage') ? 'active' : ''}}" data-toggle="tab" href="#classusage">{{ __('messages.class-usage')}}</a></li>
                </ul>
            </div>
            <div class="col-lg-12 tab-content-txt">
                <div class="tab-content">
                    <div id="home" class="tab-pane fade {{ (!isset($nav)) || $nav == 'home' ? 'active show' : ''}}">
                        <h3>{{ __('messages.personalinformation')}}</h3>
                        <div class="row">
                            <div class="table-responsive col-md-8">
                                <table class="table table-hover">
                                    <tr>
                                        <td>{{ __('messages.role')}}:</td>
                                        <td>
                                            {{ isset($student->user->getRoleNames()[0]) ? $student->user->getRoleNames()[0] : ''}}
                                        </td>
                                    </tr>
                                    @if($student->image)
                                    <tr>
                                        <td>{{ __('messages.profile-picture')}}:</td>
                                        <td>
                                                <img src="{{ $student->getImageUrl() }}" style="max-width:300px;" class="img-responsive">
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>{{ __('messages.lastnameromaji')}}:</td>
                                        <td>{{$student->lastname}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('messages.firstnameromaji')}}:</td>
                                        <td>{{$student->firstname}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('messages.lastnamekanji')}}:</td>
                                        <td>{{$student->lastname_kanji}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('messages.firstnamekanji')}}:</td>
                                        <td>{{$student->firstname_kanji}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('messages.lastnamekatakana')}}:</td>
                                        <td>{{$student->lastname_furigana}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('messages.firstnamekatakana')}}:</td>
                                        <td>{{$student->firstname_furigana}}</td>
                                    </tr>
                                    @if(isset($teacher->nickname))
                                    <tr>
                                        <td>{{ __('messages.advisor')}}:</td>
                                        <td>
                                                {{$teacher->nickname}}
                                        </td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->join_date))
                                    <tr>
                                        <td>{{ __('messages.joindate')}}:</td>
                                        <td>{{$student->join_date}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->birthday))
                                    <tr>
                                        <td>{{ __('messages.birthday')}}:</td>
                                        <td>{{$student->birthday}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->address))
                                    <tr>
                                        <td>{{ __('messages.address')}}:</td>
                                        <td>{{$student->address}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->home_phone))
                                    <tr>
                                        <td>{{ __('messages.homephone')}}:</td>
                                        <td>{{$student->home_phone}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->mobile_phone))
                                    <tr>
                                        <td>{{ __('messages.cellphone')}}:</td>
                                        <td>{{$student->mobile_phone}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($email))
                                    <tr>
                                        <td>{{ __('messages.email')}}:</td>
                                        <td>
                                                @if($student->willUseParentEmail())
                                                    <p>{{ __('messages.parent-email') }} <em>{{ $student->getEmailAddress() }}</em> {{ __('messages.will-be-used-for-all-communications') }}</p>
                                                @endif
                                                <form action="{{url('student/reconfirm/'.$student->user_id)}}" method="post" class="mb-0">
                                                    <button type="button" class="btn btn-info btn" data-toggle="modal" data-target="#mail">{{$email}}</button>
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn">{{__('messages.resend')}}</button>
                                                    <button type="button" class="btn btn-primary btn" onclick="copyToClipboard('{{ $email }}', this)">{{ __('messages.copy-to-clipboard') }}</button>
                                                </form>
                                        </td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->levels))
                                    <tr>
                                        <td>{{ __('messages.levels') }}:</td>
                                        <td>{{ implode(", ",explode(",",$student->levels)) }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->toiawase_referral))
                                    <tr>
                                        <td>{{ __('messages.referrer')}}:</td>
                                        <td>{{ $student->toiawase_referral }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->toiawase_houhou))
                                    <tr>
                                        <td>{{ __('messages.firstcontacttype')}}:</td>
                                        <td>
                                            @php
                                                switch ($student->toiawase_houhou) {
                                                    case 'Eメール':
                                                        $contact_type = __('messages.email');
                                                        break;

                                                    case '電話':
                                                        $contact_type = __('messages.telephone');
                                                        break;

                                                    case '直接':
                                                        $contact_type = __('messages.direct');
                                                        break;

                                                    case 'LINE':
                                                        $contact_type = __('messages.line');
                                                        break;

                                                    default:
                                                        $contact_type = "";
                                                        break;
                                                }
                                            @endphp
                                            {{ $contact_type }}
                                        </td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->toiawase_getter))
                                    <tr>
                                        <td>{{ __('messages.firstcontactgetter')}}:</td>
                                        <td>{{$student->toiawase_getter}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->toiawase_date))
                                    <tr>
                                        <td>{{ __('messages.firstcontactdate')}}:</td>
                                        <td>{{ $student->toiawase_date}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->toiawase_memo))
                                    <tr>
                                        <td>{{ __('messages.memo')}}:</td>
                                        <td>{!! nl2br($student->toiawase_memo) !!}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->rfid_token))
                                    <tr>
                                        <td>{{ __('messages.rfidtoken')}}:</td>
                                        <td>{{ $student->rfid_token }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->office_name))
                                    <tr>
                                        <td>{{ __('messages.office-name')}}:</td>
                                        <td>{{$student->office_name}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->office_address))
                                    <tr>
                                        <td>{{ __('messages.office-address')}}:</td>
                                        <td>{{$student->office_address}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->office_phone))
                                    <tr>
                                        <td>{{ __('messages.office-phone')}}:</td>
                                        <td>{{$student->office_phone}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->school_name))
                                    <tr>
                                        <td>{{ __('messages.school-name')}}:</td>
                                        <td>{{$student->school_name}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->school_address))
                                    <tr>
                                        <td>{{ __('messages.school-address')}}:</td>
                                        <td>{{$student->school_address}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->school_phone))
                                    <tr>
                                        <td>{{ __('messages.school-phone')}}:</td>
                                        <td>{{$student->school_phone}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->guardian1_name))
                                    <tr>
                                        <td>{{ __('messages.guardian1-name')}}:</td>
                                        <td>{{$student->guardian1_name}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->guardian1_address))
                                    <tr>
                                        <td>{{ __('messages.guardian1-address')}}:</td>
                                        <td>{{$student->guardian1_address}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->guardian1_phone))
                                    <tr>
                                        <td>{{ __('messages.guardian1-phone')}}:</td>
                                        <td>{{$student->guardian1_phone}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->guardian2_name))
                                    <tr>
                                        <td>{{ __('messages.guardian2-name')}}:</td>
                                        <td>{{$student->guardian2_name}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->guardian2_address))
                                    <tr>
                                        <td>{{ __('messages.guardian2-address')}}:</td>
                                        <td>{{$student->guardian2_address}}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($student->guardian2_phone))
                                    <tr>
                                        <td>{{ __('messages.guardian2-phone')}}:</td>
                                        <td>{{$student->guardian2_phone}}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        
                            <div class="textarea-box col-md-4">
                                <form method="post" action="{{ route('student.comment',['id' => $student->id]) }}" class="ajax" >
                                    <input type="hidden" value="0" id="notes_changed">
                                    <textarea class="form-control student-notes" id="text" rows="4" cols="50" name="comment" required>{!! ($student->comment) !!}</textarea>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if(!$contacts->isEmpty())
                    <div id="renraku" class="tab-pane fade {{(isset($nav) && $nav == 'renraku') ? 'active show' : ''}}">
                        <h3>{{ __('messages.contact')}}</h3>
                        <table class="table table-hover">
                            <tr>
                                <th>{{ __('messages.contacttype')}}</th>
                                <th>{{ __('messages.date')}}</th>
                                <th>{{ __('messages.staff')}}</th>
                                <th>{{ __('messages.memo')}}</th>
                            </tr>
                                @foreach($contacts as $contact)
                                    <tr>
                                        <td>{{ $contact->type }}</td>
                                        <td>{{ $contact->getLocalDate()  }}</td>
                                        <td>{{ isset($contact->createdBy->name) ? $contact->createdBy->name : '' }}</td>
                                        <td>{{ $contact->message }}</td>
                                    </tr>
                                @endforeach
                        </table>
                    </div>
                    @endif
                    @can('student-edit')
                        <div id="payment-settings" class="tab-pane fade {{(isset($nav) && $nav == 'payment-settings') ? 'active show' : ''}}">
                            <h3>{{ __('messages.payment-settings') }}</h3>
                            <form method="POST" id="monthlyPaymentForm" action="{{ route('student.payement-settings.save', $student->id) }}">
                                @csrf
                                <div class="form-group row form-section">
                                    <label class="col-lg-2 col-form-label">{{ __('messages.price') }}:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="price" id="price" class="form-control" value="{{ $paymentSettings->price }}" {{ $errors->has('price') ? ' is-invalid' : '' }} />
                                    </div>
                                </div>
                                <div class="form-group row form-section">
                                    <label class="col-lg-2 col-form-label">{{ __('messages.number-of-lessons') }}:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="no_of_lessons" id="no_of_lessons" class="form-control" value="{{ $paymentSettings->no_of_lessons }}" {{ $errors->has('no_of_lessons') ? ' is-invalid' : '' }} />
                                    </div>
                                </div>
                                <div id="payment-method-section" class="form-group row form-section">
                                    <label class="col-lg-2 col-form-label">{{ __('messages.paymentmethod') }}:</label>
                                    <div class="col-lg-10">
                                        <select name="payment_method" id="payment_method" class="form-control" {{ $errors->has('payment_method') ? ' is-invalid' : '' }}>
                                            @if($payment_methods)
                                                <option value="">{{ __('messages.choose-payment-method') }}</option>
                                                @foreach($payment_methods as $payment_method)
                                                    <option value="{{ strtolower($payment_method) }}" {{ old('payment_method', $paymentSettings->payment_method) == strtolower($payment_method) ? 'selected' : '' }}>{{ $payment_method }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label"></label>
                                    <div class="col-lg-10">
                                        <input name="add" type="submit" value="{{ __('messages.save') }}" class="form-control btn-success">
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endcan

                    <div id="payment" class="tab-pane fade {{(isset($nav) && $nav == 'payment') ? 'active show' : ''}}">
                        <h3>{{ __('messages.paymenthistory')}}</h3>
                        <table class='table table-hover'>
                            @if(!$payments->isEmpty())
                                @foreach($payments as $payment)
                                    <tr>
                                        <th>{{ __('messages.paymentdate')}}</th>
                                        <th>{{ __('messages.paymentamount')}}</th>
                                        <th>{{ __('messages.numberofpoints')}}</th>
                                        <th>{{ __('messages.expirationdate')}}</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td>{{$payment->date}}</td>
                                        <td>{{$payment->price}}</td>
                                        <td>{{$payment->points}}</td>
                                        <td>{{$payment->expiration_date}}</td>
                                        <td>
                                            @can('payment-delete')
                                            <form class="delete" method="POST" action="{{ route('payment.destroy', [$payment->id, $student->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" type="submit">{{ __('messages.delete')}}</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                    @if(!empty($attendance_payments[$payment->id]))
                                        <tr>
                                            <td>{{ __('messages.attendancedate')}}</td>
                                            <td>{{ __('messages.numberofpoints')}}</td>
                                            <td>{{ __('messages.remainingpoints')}}</td>
                                        </tr>
                                        @foreach($attendance_payments[$payment->id] as $attendance)
                                            @if($attendance->points != 0)
                                                <tr class="<?php if($attendance->cancel_policy_id != NULL) echo 'badge-danger'; ?>">
                                                    <td>{{$attendance->date}}</td>
                                                    <td>{{$attendance->points}}</td>
                                                    <td>{{$attendance->remaining_points}}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if(!empty($expiration_points[$payment->id]) && $expiration_points[$payment->id] != 0 && $payment->expiration_date < $date)
                                        <tr>
                                            <td>{{$payment->expiration_date}} <strong class="text-danger">-{{$expiration_points[$payment->id]}} points due to expiration</strong></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            @if(!empty($attendance_payments[9999]))
                                <tr>
                                    <td><h3><strong>{{ __('messages.paymentpending')}}</strong></h3></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.attendancedate')}}</td>
                                    <td>{{ __('messages.numberofpoints')}}</td>
                                    <td>{{ __('messages.remainingpoints')}}</td>
                                </tr>
                                @foreach($attendance_payments[9999] as $attendance)
                                    @if($attendance->points != 0)
                                        <tr class="<?php if($attendance->cancel_policy_id != NULL) echo 'badge-danger'; ?>">
                                            <td>{{$attendance->date}}</td>
                                            <td>{{$attendance->points}}</td>
                                            <td>{{$attendance->remaining_points}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </table>
                    </div>
                    @if(!$monthly_payments->isEmpty())
                    <div id="monthlypayment" class="tab-pane fade {{(isset($nav) && $nav == 'monthlypayment') ? 'active show' : ''}}">
                        <h3>{{ __('messages.paymenthistory')}}</h3>
                        <table class='table table-hover'>
                                <?php
                                    $total_furikae = 0;
                                ?>
                                <tr>
                                    <th>{{ __('messages.paymentperiod')}}</th>
                                    <th>{{ __('messages.paymentamount')}}</th>
                                    <th>{{ __('messages.paymentnumberlesson')}}</th>
                                    <th>{{ __('messages.attended')}}</th>
                                    <th>{{ __('messages.furikae')}}</th>
                                    <th>{{ __('messages.paymentmemo')}}</th>
                                    <th>{{ __('messages.payment-method') }}</th>
                                    <th>{{ __('messages.payment-status') }}</th>
                                    <th>{{ __('messages.payment-received-at') }}</th>
                                    <th>{{ __('messages.created-at') }}</th>
                                    <th>{{ __('messages.updated-at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                                @foreach($monthly_payments as $payment)
                                    <?php
                                        $furikae = $payment->number_of_lessons - $payment->attended;
                                        $total_furikae += $furikae;
                                    ?>
                                    <tr class="{{ $payment->rest_month ? 'rest-month-row' : '' }}">
                                        <td>{{$payment->period}}</td>
                                        <td>{{$payment->price}}</td>
                                        <td>{{$payment->number_of_lessons}}</td>
                                        <td>{{$payment->attended}}</td>
                                        <td>{{$furikae}}</td>
                                        <td>{{$payment->memo}}</td>
                                        <td>{{ $payment->display_payment_method }}</td>
                                        <td>{{ $payment->display_status }}</td>
                                        <td>{{ $payment->localPaymentRecievedAt() }}</td>
                                        <td>{{ $payment->localCreatedAt() }}</td>
                                        <td>{{ $payment->localUpdatedAt() }}</td>
                                        <td>
                                            @if($payment->stripe_invoice_url)
                                                <a href="{{ $payment->stripe_invoice_url }}" class="btn btn-sm btn-info my-1" target="_blank">{{ __('messages.view-stripe-invoice') }}</a>
                                                <button type="button" class="btn btn-sm btn-primary btn my-1" onclick="copyToClipboard('{{ $payment->stripe_invoice_url }}', this)">{{ __('messages.copy-stripe-invoice-url') }}</button>
                                            @endif
                                            @if($payment->canStripeInvoiceBeSent())
                                                <form class="delete mb-0" method="POST" action="{{ route('payment.send.stripe.invoice') }}">
                                                    @csrf
                                                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                                    <button class="btn btn-sm btn-primary  my-1" type="submit">{{ __('messages.send-stripe-invoice') }}</button>
                                                </form>
                                            @endif
                                            @can('payment-mark-as-paid')
                                                @if($payment->canBemarkedAsPaidManually())
                                                    <button class="btn btn-sm btn-primary btn_mark_as_paid my-1" data-id="{{ $payment->id }}">{{__('messages.mark-as-paid') }}</button>
                                                @endif
                                            @endcan
                                            @can('payment-edit')
                                                @if($payment->canBeEdited())
                                                    <a href="{{ route('payment.monthly.edit', $payment->id ) }}" class="btn btn-sm btn-warning my-1">{{ __('messages.edit') }}</a>
                                                @endif
                                            @endcan
                                            @can('payment-delete')
                                                <form class="delete mb-0" method="POST" action="{{ route('monthly.payment.destroy', [$payment->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger  my-1" type="submit">{{ __('messages.delete')}}</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4"></td>
                                    <td>{{ __('messages.totalfurikae')}}: {{$total_furikae}}</td>
                                    <td colspan="8"></td>
                                </tr>
                        </table>
                    </div>
                    @endif
                    @if(!$oneoff_payments->isEmpty())
                    <div id="otherpayments" class="tab-pane fade {{(isset($nav) && $nav == 'otherpayments') ? 'active show' : ''}}">
                        <h3>{{ __('messages.paymenthistory')}}</h3>
                        <table class='table table-hover'>
                                <?php
                                    $total_furikae = 0;
                                ?>
                                <tr>
                                    <th>{{ __('messages.paymentamount')}}</th>
                                    <th>{{ __('messages.payment-category')}}</th>
                                    <th>{{ __('messages.paymentmemo')}}</th>
                                    <th>{{ __('messages.payment-method') }}</th>
                                    <th>{{ __('messages.payment-status') }}</th>
                                    <th>{{ __('messages.payment-received-at') }}</th>
                                    <th>{{ __('messages.created-at') }}</th>
                                    <th>{{ __('messages.updated-at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                                @foreach($oneoff_payments as $payment)
                                    <tr>
                                        <td>{{ $payment->price }}</td>
                                        <td>{{ $payment->payment_category }}</td>
                                        <td>{{ $payment->memo }}</td>
                                        <td>{{ $payment->display_payment_method }}</td>
                                        <td>{{ $payment->display_status }}</td>
                                        <td>{{ $payment->localPaymentRecievedAt() }}</td>
                                        <td>{{ $payment->localCreatedAt() }}</td>
                                        <td>{{ $payment->localUpdatedAt() }}</td>
                                        <td>
                                            @if($payment->stripe_invoice_url)
                                                <a href="{{ $payment->stripe_invoice_url }}" class="btn btn-sm btn-info my-1" target="_blank">{{ __('messages.view-stripe-invoice') }}</a>
                                                <button type="button" class="btn btn-sm btn-primary btn my-1" onclick="copyToClipboard('{{ $payment->stripe_invoice_url }}', this)">{{ __('messages.copy-stripe-invoice-url') }}</button>
                                            @endif
                                            @if($payment->canStripeInvoiceBeSent())
                                                <form class="delete mb-0" method="POST" action="{{ route('payment.send.stripe.invoice') }}">
                                                    @csrf
                                                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                                    <button class="btn btn-sm btn-primary  my-1" type="submit">{{ __('messages.send-stripe-invoice') }}</button>
                                                </form>
                                            @endif
                                            @can('payment-mark-as-paid')
                                                @if($payment->canBemarkedAsPaidManually())
                                                    <button class="btn btn-sm btn-primary btn_mark_as_paid my-1" data-id="{{ $payment->id }}">{{__('messages.mark-as-paid') }}</button>
                                                @endif
                                            @endcan
                                            @can('payment-edit')
                                                @if($payment->canBeEdited())
                                                    <a href="{{ route('payment.monthly.edit', $payment->id ) }}" class="btn btn-sm btn-warning my-1">{{  __('messages.edit') }}</a>
                                                @endif
                                            @endcan
                                            @can('payment-delete')
                                                <form class="delete mb-0" method="POST" action="{{ route('monthly.payment.destroy', [$payment->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger  my-1" type="submit">{{ __('messages.delete')}}</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                        </table>
                    </div>
                    @endif
                    @if($events)
                    <div id="eventpayment" class="tab-pane fade {{(isset($nav) && $nav == 'eventpayment') ? 'active show' : ''}}">
                        <h3>{{ __('messages.eventpayment')}}</h3>
                        <table class='table table-hover'>
                            <tr>
                                <th>{{ __('messages.eventname')}}</th>
                                <th>{{ __('messages.paymentamount')}}</th>
                                <th>{{ __('messages.date')}}</th>
                            </tr>
                                @foreach($events as $event)
                                    <tr>
                                        <td>{{ $event['event']->title }}</td>
                                        <td>{{ $event['event']->cost }}</td>
                                        <td>{{ $event['schedule']->date }}</td>
                                    </tr>
                                @endforeach
                        </table>
                    </div>
                    @endif
                    @if($book_students->count() > 0)
                    <div id="bookcheckout" class="tab-pane fade {{(isset($nav) && $nav == 'bookcheckout') ? 'active show' : ''}}">
                        <h3>{{ __('messages.book-checkout-history')}}</h3>
                        <table class='table table-hover table-bordered table-striped'>
                            <thead>
                                <tr>
                                    @foreach(App\BookStudents::get_history_columns('student') as $column)
                                        <th>{{ $column }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($book_students as $book_student)
                                    <tr>
                                        @foreach($book_student->get_history_column_values('student') as $column_value)
                                            <td>{{ $column_value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    @if($student_tests->count() > 0)
                    <div id="onlinetestresult" class="tab-pane fade {{(isset($nav) && $nav == 'onlinetestresult') ? 'active show' : ''}} table-responsive">
                        <h3>{{ __('messages.onlinetestresults')}}</h3>
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.test') }}</th>
                                    <th>{{ __('messages.class') }}</th>
                                    <th>{{ __('messages.classdate') }}</th>
                                    <th>{{ __('messages.complete') }}</th>
                                    <th>{{ __('messages.score') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student_tests as $student_test)
                                    @php
                                        $test = $student_test->test;
                                        $schedule = $student_test->schedule;
                                    @endphp
                                    <tr>
                                        <td><a href="{{ route('test.show', $test->id) }}">{{ $test->name }}</a></td>
                                        <td><a href="{{ route('schedule.show', $schedule->id) }}">{{ $schedule->class->title }}</a></td>
                                        <td><a href="{{ route('schedule.show', $schedule->id) }}">{{ $schedule->get_date() }}</a></td>
                                        <td>{!! $student_test->is_complete() ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>' !!}</td>
                                        <td>
                                            @if($student_test->is_complete())
                                                {{ $student_test->score.'/'.$student_test->total_score }}
                                            @endif
                                        </td>
                                        <td>
                                            <form class="delete" method="POST" action="{{ route('student-test.destroy', $student_test->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    @if($paper_tests->count() > 0)
                    <div id="papertestresult" class="tab-pane fade {{(isset($nav) && $nav == 'papertestresult') ? 'active show' : ''}} table-responsive">
                        <h3>{{ __('messages.papertestresults')}}</h3>
                            <table class="table table-hover table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>{{ __('messages.test') }}</th>
                                    <th>{{ __('messages.class') }}</th>
                                    <th>{{ __('messages.classdate') }}</th>
                                    <th>{{ __('messages.score') }}</th>
                                    <th>{{ __('messages.testdate') }}</th>
                                    <th>{{ __('messages.commenten') }}</th>
                                    <th>{{ __('messages.commentja') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($paper_tests as $paper_test)
                                    @php($schedule = $paper_test->schedule)
                                    <tr>
                                        <td>{{ $paper_test->paper_test->name }}</a></td>
                                        <td><a href="{{ route('schedule.show', $schedule->id) }}">{{ $schedule->class->title }}</a></td>
                                        <td><a href="{{ route('schedule.show', $schedule->id) }}">{{ $schedule->get_date() }}</a></td>
                                        <td>{{ $paper_test->get_score() }}</td>
                                        <td>{{ $paper_test->date }}</td>
                                        <td><pre>{!! $paper_test->comment_en !!}</pre></td>
                                        <td><pre>{!! $paper_test->comment_ja !!}</pre></td>
                                        <td>
                                            <form class="delete" method="POST" action="{{ route('student.paper_test.destroy', ['schedule_id' => $schedule->id, 'student_paper_test_id' => $paper_test->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                    @endif
                    
                    @if($assessment_users->count() > 0)
                    <div id="assessmentresult" class="tab-pane fade {{(isset($nav) && $nav == 'assessmentresult') ? 'active show' : ''}} table-responsive">
                        <h3>{{ __('messages.assessment')}}</h3>
                            <table class="table table-bordered table-hover ">
                                <thead>
                                <tr>
                                    <th>{{ __('messages.assessment') }}</th>
                                    <th>{{ __('messages.complete') }}</th>
                                    <th>{{ __('messages.type') }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($assessment_users as $assessment_user)
                                        <tr>
                                            <td><a href="{{ route('assessment.show', $assessment_user->assessment->id) }}">{{ $assessment_user->assessment->name }}</a></td>
                                            <td>{!! $assessment_user->is_complete() ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>' !!}</td>
                                            <td>{{ $assessment_user->assessment->type }}</td>
                                            <td>
                                                @if($assessment_user->is_complete())
                                                    <a class="btn btn-success" href="{{ route('assessment_user.show', $assessment_user->id ) }}">
                                                        {{ __('messages.seedetails') }}
                                                    </a>
                                            @endif
                                            <td>
                                                <form class="delete" method="POST" action="{{ route('assessment_user.destroy', $assessment_user->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                    @endif
                    @if($todoAccessList->count() > 0)
                    <div id="todo" class="tab-pane fade {{(isset($nav) && $nav == 'todo') ? 'active show' : ''}} table-responsive">
                        @include('todo.list-todo')
                    </div>
                    @endif
                    <div id="classusage" class="tab-pane fade {{(isset($nav) && $nav == 'classusage') ? 'active show' : ''}} table-responsive">
                        @include('student.class_usage_tab')
                    </div>
                </div>

            </div>
        </div>
    @endif
@endsection

@push('modals')
    @if(!$yoteis->isEmpty())
        <!-- Modal -->
        <div id="levelcheckfinished" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">レベルチェック終了</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" method="POST" action="{{ route('yotei.update') }}">
                            @csrf
                            @if(empty($student->lastname_kanji))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">苗字（漢字）:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="lastname_kanji" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="lastname_kanji" value="{{$student->lastname_kanji}}">
                            @endif

                            @if(empty($student->firstname_kanji))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">名前（漢字）:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="firstname_kanji" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="firstname_kanji" value="{{$student->firstname_kanji}}">
                            @endif

                            @if(empty($student->lastname_furigana))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">苗字（フリガナ）:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="lastname_furigana" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="lastname_furigana" value="{{$student->lastname_furigana}}">
                            @endif

                            @if(empty($student->firstname_furigana))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">名前（フリガナ）:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="firstname_furigana" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="firstname_furigana" value="{{$student->firstname_furigana}}">
                            @endif

                            @if(empty($student->lastname))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">苗字（ローマ字）:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="lastname" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="lastname" value="{{$student->lastname}}">
                            @endif

                            @if(empty($student->firstname))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">名前（ローマ字）:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="firstname" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="firstname" value="{{$student->firstname}}">
                            @endif

                            @if(!$email)
                                <div class="form-group">
                                    <label class="control-label col-sm-2">Eメール:</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" name="email" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="email" value="{{$student->email}}">
                            @endif

                            @if(empty($student->home_phone))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">固定電話:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="home_phone" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="home_phone" value="{{$student->home_phone}}">
                            @endif

                            @if(empty($student->mobile_phone))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">携帯電話:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="mobile_phone" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="mobile_phone" value="{{$student->mobile_phone}}">
                            @endif

                            @if(empty($student->toiawase_referral))
                                <div class="form-group">
                                    <label class="control-label col-sm-2">紹介者:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="toiawase_referral" required="">
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="toiawase_referral" value="{{$student->toiawase_referral}}">
                            @endif
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <input type="hidden" name="guest" value="{{$student->id}}">
                                    <input type="hidden" name="yotei_id" value="{{$yoteis[0]->id}}">
                                    <button type="submit" class="btn btn-light">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal -->
    <div id="levelcheck" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">レベルチェック追加</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="POST" action="{{ route('yotei.store') }}">
                        @csrf
                        <div class="form-group">
                            <label class="control-label col-sm-2">日付:</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" name="date" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">開始時間:</label>
                            <div class="col-sm-10">
                                <input type="time" class="form-control" name="start_time" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">終了時間:</label>
                            <div class="col-sm-10">
                                <input type="time" class="form-control" name="end_time" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">担当先生:</label>
                                <div class="col-sm-10">
                                    <select name="teacher_id" class="form-control" required="">
                                        <option value="">担当先生</option>
                                        @if(!$teachers->isEmpty())
                                            @foreach($teachers as $teacher)
                                                <option value="{{$teacher->id}}" <?php if($teacher->id == $student->teacher_id) echo 'selected'; ?>>{{$teacher->nickname}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="hidden" name="guest" value="{{$student->id}}">
                                <button type="submit" class="btn btn-light">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="mail" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{ __('messages.sendmail')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('mail.send', $student->id) }}">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-2">{{ __('messages.subject')}}</label>
                            <div class="col-lg-10">
                                <input type="subject" class="form-control" name="subject" required="">
                                <input type="hidden" value="{{ $email }}" name="email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2">{{ __('messages.message')}}</label>
                            <div class="col-lg-10">
                                <textarea name="message" class="form-control" rows="5" required></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2"></label>
                            <div class="col-lg-10">
                                <input type="submit" name="submit" value="Submit" class="btn btn-info form-control">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_contact_modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="padding:15px 15px;">
                    <h4><span class="fa fa-pencil"></span> <span class="modal-title">{{ __('messages.addcontact') }}</span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" style="padding:40px 50px;">
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">{{ __('messages.name')}}</label>
                            <div class="col-lg-10">
                                <select class="form-control" name="customer_id" required="">
                                    <option value="{{$student->id}}">{{$student->fullName}}</option>
                                </select>​
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">{{ __('messages.contacttype')}}</label>
                            <div class="col-lg-10">
                                <label class="radio-inline"><input type="radio" name="type" value="denwa" checked=""> {{ __('messages.telephone')}}</label>
                                <label class="radio-inline"><input type="radio" name="type" value="line"> {{ __('messages.line')}}</label>
                                <label class="radio-inline"><input type="radio" name="type" value="direct"> {{ __('messages.direct')}}</label>
                                <label class="radio-inline"><input type="radio" name="type" value="mail"> {{ __('messages.email')}}</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">{{ __('messages.contents')}}</label>
                            <div class="col-lg-10">
                                <textarea name="message" rows="5" placeholder="{{ __('messages.pleasewritecontentshere') }}" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}" required="">{{old('message')}}</textarea>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-success" name="add"><span class="fa fa-pencil"></span> {{ __('messages.record')}}</button>

                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"> Cancel</button>
                </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mark_as_paid_modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="padding:15px 15px;">
                    <h4>{{ __('messages.mark-payment-as-paid') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('payment.paid') }}">
                        @csrf
                        <input type="hidden" name="id" value="">
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">{{ __('messages.payment-received-date')}}:</label>
                            <div class="col-lg-8">
                                <input name="date" type="date" class="form-control required" value="{{ \Carbon\carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">{{ __('messages.payment-received-time') }}:</label>
                            <div class="col-lg-8">
                                <input name="time" type="time" class="form-control required" value="{{ \Carbon\carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('H:i') }}">
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">{{ __('messages.submit') }}</button>
                    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close">{{ __('messages.cancel') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form id="archive_student_form" method="post">
        @csrf
    </form>
@endpush

@push('scripts')
<script src="{{ asset('public'.mix('js/page/student/details.js')) }}"></script>
@endpush
