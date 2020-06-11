@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>{{ __('messages.email-settings') }}</h1>
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
            @include('partials.error')
            <form method="POST" action="{{ route('email-settings.update') }}" autocomplete="off" enctype="multipart/form-data">
                @csrf

                <h4>{{ __('messages.smtp-settings') }}</h4>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.host') }}: </label>
                    </div>
                    <div class="col-10">
                        <input class="form-control" type="text" id="smtp_host" name="smtp_host" value="{{ @$smtp_settings['host'] }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.port') }}: </label>
                    </div>
                    <div class="col-10">
                        <input class="form-control" type="text" id="smtp_port" name="smtp_port" value="{{ @$smtp_settings['port'] }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.username') }}: </label>
                    </div>
                    <div class="col-10">
                        <input class="form-control" type="text" id="smtp_username" name="smtp_username" value="{{ @$smtp_settings['username'] }}" aria-describedby="smtp_username_block">
                        <small id="smtp_username_block" class="form-text text-muted">
                        {{ __('messages.leave-it-blank-if-you-have-configured-your-smtp-server-to-be-authenticated-by-ip-address-for.eg.-in-case-of-gsuite-smtp-relay-service.') }}
                        </small>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.password') }}: </label>
                    </div>
                    <div class="col-10">
                        <input class="form-control" type="text" id="smtp_password" name="smtp_password" value="{{ @$smtp_settings['password'] }}" aria-describedby="smtp_password_block">
                        <small id="smtp_password_block" class="form-text text-muted">
                            {{ __('messages.leave-it-blank-if-you-have-configured-your-smtp-server-to-be-authenticated-by-ip-address-for.eg.-in-case-of-gsuite-smtp-relay-service.') }}
                        </small>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.from-address') }}: </label>
                    </div>
                    <div class="col-10">
                        <input class="form-control" type="text" id="smtp_from_address" name="smtp_from_address" value="{{ @$smtp_settings['from_address'] }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.from-name') }}: </label>
                    </div>
                    <div class="col-10">
                        <input class="form-control" type="text"  id="smtp_from_name" name="smtp_from_name" value="{{ @$smtp_settings['from_name'] }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                    </div>
                    <div class="col-10">
                        <div class="alert alert-danger" role="alert">
                            {{ __('messages.please-verify-your-smtp-configuration-is-working-by-sending-test-email-before-saving-this-form') }}
                        </div>
                        <button type="button" class="btn btn-primary" id="test-mail-btn">{{ __('messages.test-mail') }}</button>
                    </div>
                </div>
                <hr>

                <h4>{{ __('messages.email-layout-settings') }}</h4>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.header-footer-color') }}</label>
                    </div>
                    <div class="col-10">
                        <div id="email_header_footer_color_picker" data-default="{{ $email_header_footer_color }}"></div>
                        <input type="hidden" value="{{ $email_header_footer_color }}" name="email_header_footer_color">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.header-image') }}</label>
                    </div>
                    <div class="col-10">
                        <div class="input-group mb-3">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputGroupFile02" name="email_header_image" data-default_placeholder="{{ __('messages.choose-image') }}">
                                <label class="custom-file-label" for="inputGroupFile02" aria-describedby="inputGroupFileAddon02"></label>
                            </div>
                        </div>
                        <input type="hidden" id="remove_email_header_image" name="remove_email_header_image" value="0">
                        @if($email_header_image)
                            <div id="email_header_image_preview_section">
                                <div>
                                <img src="{{ \Storage::disk('public')->url($email_header_image) }}" alt="{{ __('messages.header-image') }}" style="max-width:100px;">
                                </div>
                                <div>
                                    <button type="button" id="remove_eh_image_btn" class="btn btn-sm btn-primary mt-1" >{{ __('messages.remove-image') }}</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.header-text-size-px') }}</label>
                    </div>
                    <div class="col-10">
                        <input class="form-control" type="text" name="email_header_text_size" value="{{ $email_header_text_size }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.body-text-size-px') }}</label>
                    </div>
                    <div class="col-10">
                        <input class="form-control" type="text" name="email_body_text_size" value="{{ $email_body_text_size }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.header-text-en') }}</label>
                    </div>
                    <div class="col-10">
                        <textarea class="form-control" name="email_header_text_en" rows="3" required>{{ $email_header_text_en  }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.header-text-ja') }}</label>
                    </div>
                    <div class="col-10">
                        <textarea class="form-control" name="email_header_text_ja" rows="3" required>{{ $email_header_text_ja  }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.footer-text-en') }}</label>
                    </div>
                    <div class="col-10">
                        <textarea class="form-control" name="email_footer_text_en" rows="5" required>{{ $email_footer_text_en  }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-2">
                        <label>{{ __('messages.footer-text-ja') }}</label>
                    </div>
                    <div class="col-10">
                        <textarea class="form-control" name="email_footer_text_ja" rows="5" required>{{ $email_footer_text_ja  }}</textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col">
                        <input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('modals')
    <!-- send mail modal -->
    <div id="test-smtp-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{ __('messages.test-mail') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="test_smtp_form" onsubmit="return false;">
                        @csrf

                        <div class="form-group row">
                            <label class="col-lg-3">{{ __('messages.host') }}: </label>
                            <div class="col-lg-9">
                                <span id="label_smtp_host"></span>
                                <input type="hidden" name="test_smtp_host" id="test_smtp_host">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3">{{ __('messages.port') }}: </label>
                            <div class="col-lg-9">
                                <span id="label_smtp_port"></span>
                                <input type="hidden" name="test_smtp_port" id="test_smtp_port">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3">{{ __('messages.username') }}: </label>
                            <div class="col-lg-9">
                                <span id="label_smtp_username"></span>
                                <input type="hidden" name="test_smtp_username" id="test_smtp_username">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3">{{ __('messages.password') }}: </label>
                            <div class="col-lg-9">
                                <span id="label_smtp_password"></span>
                                <input type="hidden" name="test_smtp_password" id="test_smtp_password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3">{{ __('messages.from-address') }}: </label>
                            <div class="col-lg-9">
                                <span id="label_smtp_from_address"></span>
                                <input type="hidden" name="test_smtp_from_address" id="test_smtp_from_address">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3">{{ __('messages.from-name') }}: </label>
                            <div class="col-lg-9">
                                <span id="label_smtp_from_name"></span>
                                <input type="hidden" name="test_smtp_from_name" id="test_smtp_from_name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3">{{ __('messages.email')}}: </label>
                            <div class="col-lg-9">
                                <input type="email" value="{{ \Auth::user()->email }}" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3">{{ __('messages.subject')}}: </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="subject" value="{{ __('messages.smtp-config-test-email') }}" required >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3">{{ __('messages.message')}}: </label>
                            <div class="col-lg-9">
                                <textarea name="message" class="form-control" rows="5" required>{{ __('messages.smtp-config-test-email') }}</textarea>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                        <button type="submit" id="submit_test_smtp" class="btn btn-primary">
                            {{ __('messages.test') }}
                            <span>
                                <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                            </span>
                        </button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script src="{{ asset('public'.mix('js/page/setting/email_templates.js')) }}"></script>
@endpush
