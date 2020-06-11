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
            @include('partials.error')
            <h1>{{ __('messages.line-settings') }}</h1>

            <div class="accordion my-2" id="accordionLineMessagingSettings">
                <div class="card">
                    <div class="card-header" id="heading_accordion_line_messaging_settings">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse_line_messaging_settings" aria-expanded="false" aria-controls="collapse_line_messaging_settings">
                            {{ __('messages.line-messaging-api-settings') }}
                            </button>
                        </h2>
                    </div>
                    <div id="collapse_line_messaging_settings" class="collapse" aria-labelledby="heading_accordion_line_messaging_settings" data-parent="#accordionLineMessagingSettings">
                        <div class="card-body">
                            <form class="form-submits-via-ajax" action="{{ route('line-messaging-settings.save') }}">
                                <p>To Use line messaging functionality, line channel has to be created, please follow the following instructions to create a line channel</p>
                                <ol>
                                    <li>click this link <a href="https://developers.line.biz/console/channel/new?type=messaging-api" target="_blank">https://developers.line.biz/console/channel/new?type=messaging-api</a> to start creating channel</li>
                                    <li>In the create channel form on that page, <b>select the same provider</b> you have used while setting up line login functionality with this app, or select <b>create new provider</b> if this is your very first channel for this app</li>
                                    <li>Enter Provider name (if you have selected create new provider in previous step), Channel Name, Channel Description, select category and subcategory. The values you enter here will be displayed to user when they add your line account as their friend.</li>
                                    <li>Fill the remaining fields and create channel, once channel is created, you will be redirected to the channel details page</li>
                                    <li>on channel details page, click <b>Messaging API</b> tab and set this url <b>{{ route('api.line.webhook') }}</b> in <b>Webhook URL</b> field and turn on <b>Use webhook</b> option.</li>
                                    <li>Do not try to verify webhook setting by clicking <b>verify</b> button on line settings page yet, it will fail untill the following form is submited, instead try it once the following form is saved</li>
                                    <li>Now you are done setting up channel on your line account, enter the details of your line channel in below form</li>
                                </ol>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Use line messaging:</label>
                                    <div class="col-lg-10">
                                        <input type="checkbox" class="form-control" name="use_line_messaging_api" value="1" style="width: 18px;height: 21px;margin-top: 7px;" {{ old('use_line_messaging_api',$use_line_messaging_api) == 1 ? 'checked' : '' }} >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Channel Id:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="line_channel_id" id="line_channel_id" class="form-control required {{ $errors->has('line_channel_id') ? ' is-invalid' : '' }}" value="{{ old('line_channel_id',$line_channel_id) }}" autocomplete="off">
                                        <p>You will find this value form <b>Basic settings</b> tab on line channel details page</p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Channel Secret:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="line_channel_secret" id="line_channel_secret" class="form-control required {{ $errors->has('line_channel_secret') ? ' is-invalid' : '' }}" value="{{ old('line_channel_secret',$line_channel_secret) }}" autocomplete="off">
                                        <p>You will find this value form <b>Basic settings</b> tab on line channel details page</p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Assertion Private Key:</label>
                                    <div class="col-lg-10">
                                        <textarea name="line_assertion_private_key" id="line_assertion_private_key" rows="5"  class="form-control {{ $errors->has('line_assertion_private_key') ? ' is-invalid' : '' }}" autocomplete="off">{{ old('line_assertion_private_key',$line_assertion_private_key) }}</textarea>
                                        <p>On <b>Basic settings</b> tab on line channel details page, click <b>issue</b> button near <b>Assertion Signing Key</b>
                                            <br>Private key will be displayed in modal, copy the contents of modal and paste it in this field</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Add Friend Button HTML:</label>
                                    <div class="col-lg-10">
                                        <textarea name="line_add_friend_button_html" id="line_add_friend_button_html" rows="2"  class="form-control {{ $errors->has('line_add_friend_button_html') ? ' is-invalid' : '' }}" autocomplete="off">{{ old('line_add_friend_button_html',$line_add_friend_button_html) }}</textarea>
                                        <p>To get the value for this field: <br>
                                            Go to <a href="https://manager.line.biz/" target="_blank">https://manager.line.biz/</a> <br>
                                            Select Channel you just created with above steps<br>
                                            Click <b>Gain friends</b> option from side menu <br>
                                            Copy the text of <b>Button</b> field under <b>Add a button to your website</b> section on that page, and paste it in this field
                                        </p>
                                    </div>
                                </div>
                                <hr>
                                <p>Text to send to start linking user account process (when user follows the channel):</p>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Message Text: </label>
                                    <div class="col-lg-10">
                                        <textarea 
                                            name="line_account_link_meesage_text" 
                                            rows="3" 
                                            class="form-control form-control char-sensitive-field {{ $errors->has('line_account_link_meesage_text') ? ' is-invalid' : '' }}"
                                            data-max-length="160"
                                            autocomplete="off"
                                            >{{ old('line_account_link_meesage_text',$line_account_link_meesage_text) }}</textarea>
                                            <div class="float-right char-length-indicator"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Account Link button text: </label>
                                    <div class="col-lg-10">
                                        <input type="text" 
                                            name="line_account_link_meesage_button_text"
                                            class="form-control char-sensitive-field {{ $errors->has('line_account_link_meesage_button_text') ? ' is-invalid' : '' }}"
                                            data-max-length="20"
                                            autocomplete="off"
                                            value="{{ old('line_account_link_meesage_button_text',$line_account_link_meesage_button_text) }}"
                                        >
                                        <div class="float-right char-length-indicator"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <p>Text to send when user account is linked:</p>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Message Text (English): </label>
                                    <div class="col-lg-10">
                                        <textarea 
                                            name="line_account_linked_message_text_en"
                                            rows="3" 
                                            class="form-control char-sensitive-field {{ $errors->has('line_account_linked_message_text_en') ? ' is-invalid' : '' }}"
                                            data-max-length="2000"
                                            autocomplete="off"
                                        >{{ old('line_account_linked_message_text_en',$line_account_linked_message_text_en) }}</textarea>
                                        <div class="float-right char-length-indicator"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Message Text (Japanese): </label>
                                    <div class="col-lg-10">
                                        <textarea
                                            name="line_account_linked_message_text_ja"
                                            rows="3"  class="form-control char-sensitive-field {{ $errors->has('line_account_linked_message_text_ja') ? ' is-invalid' : '' }}"
                                            data-max-length="2000"
                                            autocomplete="off"
                                        >{{ old('line_account_linked_message_text_ja',$line_account_linked_message_text_ja) }}</textarea>
                                        <div class="float-right char-length-indicator"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group row">
                                    <div class="col">
                                        <input name="edit" type="submit" value="{{ __('messages.save') }}" class="btn btn-success form-control">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion my-2" id="accordionLoginWithLine">
                <div class="card">
                    <div class="card-header" id="heading_accordion_login_with_line">
                        <h2 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse_login_with_line" aria-expanded="false" aria-controls="collapse_login_with_line">
                            {{ __('messages.login-with-line-settings') }}
                            </button>
                        </h2>
                    </div>
                    <div id="collapse_login_with_line" class="collapse" aria-labelledby="heading_accordion_login_with_line" data-parent="#accordionLoginWithLine">
                        <div class="card-body">
                            <form class="form-submits-via-ajax" action="{{ route('line-login-settings.save') }}">
                                <p>To Use Login with line functionality, line channel has to be created, please follow the following instructions to create a line channel</p>
                                <ol>
                                    <li>click this link <a href="https://developers.line.biz/console/channel/new?type=line-login" target="_blank">https://developers.line.biz/console/channel/new?type=line-login</a> to start creating channel</li>
                                    <li>In the create channel form on that page, <b>select the same provider</b> you have used while setting up messaging functionality with this app, or select <b>create new provider</b> if this is your very first channel for this app</li>
                                    <li>Enter Provider name (if you have selected create new provider in previous step), Channel Name and Channel Description. The values you enter here will be displayed to user when they first time login with their line account</li>
                                    <li>In App types field select <b>Web app</b></li>
                                    <li>Fill the remaining fields and create channel, once channel is created, you will be redirected to the channel details page</li>
                                    <li>on channel details page, click <b>Line Login</b> tab and set this url <b>{{ route('login.line.callback') }}</b> in <b>Callback URL</b> field</li>
                                    <li>Publish the channel by clicking the <b>Developing</b> button near channel name</li>
                                    <li>Now you are done setting up channel on your line account, enter the details of your line channel in below form</li>
                                </ol>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Use login with line:</label>
                                    <div class="col-lg-10">
                                        <input type="checkbox" class="form-control" name="use_login_with_line" value="1" style="width: 18px;height: 21px;margin-top: 7px;" {{ old('use_login_with_line',$use_login_with_line) == 1 ? 'checked' : '' }} >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Channel Id:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="line_login_channel_id" id="line_login_channel_id" class="form-control required {{ $errors->has('line_login_channel_id') ? ' is-invalid' : '' }}" value="{{ old('line_login_channel_id',$line_login_channel_id) }}" autocomplete="off">
                                        <p>You will find this value form <b>Basic settings</b> tab on line channel details page</p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">Channel Secret:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="line_login_channel_secret" id="line_login_channel_secret" class="form-control required {{ $errors->has('line_login_channel_secret') ? ' is-invalid' : '' }}" value="{{ old('line_login_channel_secret',$line_login_channel_secret) }}" autocomplete="off">
                                        <p>You will find this value form <b>Basic settings</b> tab on line channel details page</p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col">
                                        <input name="edit" type="submit" value="{{ __('messages.save') }}" class="btn btn-success form-control">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
@endsection

@push('scripts')
    <script src="{{ asset('public'.mix('js/page/setting/line-settings.js')) }}"></script>
@endpush