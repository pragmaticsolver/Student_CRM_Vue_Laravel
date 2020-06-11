@extends('layouts.app')

@section('custom-css')

	<style>
		.tooltip-inner {
		background-color: #00acd6 !important;
		color: #fff;
		}

		.tooltip.top .tooltip-arrow {
		border-top-color: #00acd6;
		}

		.tooltip.right .tooltip-arrow {
		border-right-color: #00acd6;
		}

		.tooltip.bottom .tooltip-arrow {
		border-bottom-color: #00acd6;
		}

		.tooltip.left .tooltip-arrow {
		border-left-color: #00acd6;
		}
	</style>

@endsection


@section('content')

    <div class="row justify-content-center">
        <div class="col-12">
        	@include('partials.error')
            @include('partials.success')
	        <form method="POST" action="{{ route('roles.update',$role->id) }}">
			
	            @method('PATCH')
	        	@csrf
	          	<h1>{{ __('messages.editrole') }} {{$role->name}}</h1>
	          	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
	            	<div class="col-lg-10">
	              		<input name="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ empty(old('name'))?$role->name:old('name') }}" required="">
	            	</div>
				 </div>
				 <div class="form-group row">
					<label class="col-lg-2 col-form-label">{{ __('messages.defaultlanguage') }}</label>
					<div class="col-lg-10">
						<select name="default_lang" class="form-control">
							<option value="">Select Language</option>
							<option value="en" <?php if($role->default_lang == 'en') echo 'selected'; ?>>English</option>
							<option value="ja" <?php if($role->default_lang == 'ja') echo 'selected'; ?>>Japanese</option>
						</select>
					</div>
			  	</div>
	         	<div class="form-group row">
	            	<label class="col-lg-2 col-form-label">{{ __('messages.permissions') }}</label>
	            	<div class="col-lg-10">

						<?php 
							$categoryArr=array();
						?>

						@foreach ($categories as $category)
							<button class="btn btn-block btn-light" id="btn_{{$category->name}}" type="button" data-toggle="collapse" data-target="#branding_container_{{$category->name}}" aria-expanded="false" aria-controls="branding_container">
								{{ucfirst($category->name)}}
								
							</button>
							<?php array_push($categoryArr, ($category->name));?>
						
							<div class="collapse row" style="margin-left:0px; padding:10px;" id="branding_container_{{$category->name}}">
		
							</div>
						@endforeach
			

	            	</div>
	         	</div>
				<div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.can-login') }}</label>
                    <div class="col-lg-10">
						<input type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="info"  data-height="20" class="form-control" name="can_login" @if($role->can_login === 1) checked @endif>
                    </div>
				</div>
				<div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.login-redirect-path') }}</label>
                    <div class="col-lg-10">
                        <input name="login_redirect_path" type="text" class="form-control{{ $errors->has('login_redirect_path') ? ' is-invalid' : '' }}" value="{{ $role->login_redirect_path }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.is-student') }}</label>
                    <div class="col-lg-10">
						<input type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="info"  data-height="20" class="form-control" name="is_student" @if($role->is_student === 1) checked @endif>
                    </div>
				</div>
				<div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.sendlogindetails') }}</label>
                    <div class="col-lg-10">
						
						<input type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="info"  data-height="20" class="form-control" name="send_login_details" @if($role->send_login_details === 1) checked @endif>
                    </div>
                </div>
	          	<div class="form-group row">
		            <label class="col-lg-2 col-form-label"></label>
		            <div class="col-lg-10">
						<input name="submit" type="submit" value="{{ __('messages.editrole') }}" class="form-control btn-success">
		            </div>
		        </div>
	        </form>
      	</div>
    </div>

	<style>
		.toggle-group label {
			padding-top: 0px;
			line-height: 20px!important;
		}
	</style>	

@endsection

@section('custom-js')

	<script>
		let roleperArr = [];
		let categoryArr = [];
		<?php foreach($categoryArr as $category) {?>
			categoryArr.push('<?php echo $category; ?>');
		<?php }?>
		<?php foreach($roleper as $role) {?>
			roleperArr.push('<?php echo $role; ?>');
		<?php }?>
		

		$(document).ready(function() {

			+function ($) {
				'use strict';

				var Toggle = function (element, options) {
					this.$element = $(element)
					this.options = $.extend({}, this.defaults(), options)
					this.render()
				}

				Toggle.VERSION = '2.2.0'

				Toggle.DEFAULTS = {
					on: 'On',
					off: 'Off',
					onstyle: 'primary',
					offstyle: 'default',
					size: 'normal',
					style: '',
					width: null,
					height: null
				}

				Toggle.prototype.defaults = function () {
					return {
						on: this.$element.attr('data-on') || Toggle.DEFAULTS.on,
						off: this.$element.attr('data-off') || Toggle.DEFAULTS.off,
						onstyle: this.$element.attr('data-onstyle') || Toggle.DEFAULTS.onstyle,
						offstyle: this.$element.attr('data-offstyle') || Toggle.DEFAULTS.offstyle,
						size: this.$element.attr('data-size') || Toggle.DEFAULTS.size,
						style: this.$element.attr('data-style') || Toggle.DEFAULTS.style,
						width: this.$element.attr('data-width') || Toggle.DEFAULTS.width,
						height: this.$element.attr('data-height') || Toggle.DEFAULTS.height
					}
				}

				Toggle.prototype.render = function () {
					this._onstyle = 'btn-' + this.options.onstyle
					this._offstyle = 'btn-' + this.options.offstyle
					var size = this.options.size === 'large' ? 'btn-lg'
						: this.options.size === 'small' ? 'btn-sm'
							: this.options.size === 'mini' ? 'btn-xs'
								: ''
					var $toggleOn = $('<label class="btn">').html(this.options.on)
						.addClass(this._onstyle + ' ' + size)
					var $toggleOff = $('<label class="btn">').html(this.options.off)
						.addClass(this._offstyle + ' ' + size + ' active')
					var $toggleHandle = $('<span class="toggle-handle btn btn-default">')
						.addClass(size)
					var $toggleGroup = $('<div class="toggle-group">')
						.append($toggleOn, $toggleOff, $toggleHandle)
					var $toggle = $('<div class="toggle btn" data-toggle="toggle">')
						.addClass(this.$element.prop('checked') ? this._onstyle : this._offstyle + ' off')
						.addClass(size).addClass(this.options.style)

					this.$element.wrap($toggle)
					$.extend(this, {
						$toggle: this.$element.parent(),
						$toggleOn: $toggleOn,
						$toggleOff: $toggleOff,
						$toggleGroup: $toggleGroup
					})
					this.$toggle.append($toggleGroup)

					var width = this.options.width || Math.max($toggleOn.outerWidth(), $toggleOff.outerWidth()) + ($toggleHandle.outerWidth() / 2)
					var height = this.options.height || Math.max($toggleOn.outerHeight(), $toggleOff.outerHeight())
					$toggleOn.addClass('toggle-on')
					$toggleOff.addClass('toggle-off')
					this.$toggle.css({ width: width, height: height })
					if (this.options.height) {
						$toggleOn.css('line-height', $toggleOn.height() + 'px')
						$toggleOff.css('line-height', $toggleOff.height() + 'px')
					}
					this.update(true)
					this.trigger(true)
				}

				Toggle.prototype.toggle = function () {
					if (this.$element.prop('checked')) this.off()
					else this.on()
				}

				Toggle.prototype.on = function (silent) {
					if (this.$element.prop('disabled')) return false
					this.$toggle.removeClass(this._offstyle + ' off').addClass(this._onstyle)
					this.$element.prop('checked', true)
					if (!silent) this.trigger()
				}

				Toggle.prototype.off = function (silent) {
					if (this.$element.prop('disabled')) return false
					this.$toggle.removeClass(this._onstyle).addClass(this._offstyle + ' off')
					this.$element.prop('checked', false)
					if (!silent) this.trigger()
				}

				Toggle.prototype.enable = function () {
					this.$toggle.removeAttr('disabled')
					this.$element.prop('disabled', false)
				}

				Toggle.prototype.disable = function () {
					this.$toggle.attr('disabled', 'disabled')
					this.$element.prop('disabled', true)
				}

				Toggle.prototype.update = function (silent) {
					if (this.$element.prop('disabled')) this.disable()
					else this.enable()
					if (this.$element.prop('checked')) this.on(silent)
					else this.off(silent)
				}

				Toggle.prototype.trigger = function (silent) {
					this.$element.off('change.bs.toggle')
					if (!silent) this.$element.change()
					this.$element.on('change.bs.toggle', $.proxy(function () {
						this.update()
					}, this))
				}

				Toggle.prototype.destroy = function () {
					this.$element.off('change.bs.toggle')
					this.$toggleGroup.remove()
					this.$element.removeData('bs.toggle')
					this.$element.unwrap()
				}

				function Plugin(option) {
					return this.each(function () {
						var $this = $(this)
						var data = $this.data('bs.toggle')
						var options = typeof option == 'object' && option

						if (!data) $this.data('bs.toggle', (data = new Toggle(this, options)))
						if (typeof option == 'string' && data[option]) data[option]()
					})
				}

				var old = $.fn.bootstrapToggle

				$.fn.bootstrapToggle = Plugin
				$.fn.bootstrapToggle.Constructor = Toggle

				$.fn.toggle.noConflict = function () {
					$.fn.bootstrapToggle = old
					return this
				}

				$(document).on('click.bs.toggle', 'div[data-toggle^=toggle]', function (e) {
					var $checkbox = $(this).find('input[type=checkbox]')
					$checkbox.bootstrapToggle('toggle')
					e.preventDefault()
				})
				$('input[type=checkbox][data-toggle^=toggle]').bootstrapToggle();

			}(jQuery);

		});

		categoryArr.forEach(category => {

			$("#btn_" + category).click(function() {
		
				$.ajax({
					method: "POST",
					url: '{{url('roles/categories')}}',
					data: { category: category , _token: '{{csrf_token()}}' }
					
				})
				.done(function( response ) {
					$("#branding_container_" + category).empty();
					$.each(response,function(index, item) {

						if(roleperArr.includes(String(item.id))) {

							$("#branding_container_" + category).append(
								'<div class="col-md-3" style="padding:4px;">' +
									'<div class="checkbox  form-horizontal">' +

										'<input class="bootstrapToggle" type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="info"  data-width="' + 60 + '" data-height="' + 25 + '" name="permissions[]"' + 
											'value="' + item.id + '" id="checkbox'+ item.id + '"' + 'checked' + '>' + '</input>' +
										'&nbsp;' + '&nbsp;' + '<label for="checkbox' + item.id + '" id="checkbox' + item.id + '" data-toggle="tooltip" title="' + item.permissionTooltip + '">' +
											item.name +
										'</label></div></div>');
						} else {

							$("#branding_container_" + category).append(
								'<div class="col-md-3" style="padding:4px;">' +
									'<div class="checkbox  form-horizontal">' +
										'<input type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="info"  data-width="60" data-height="25" name="permissions[]"' + 
											'value="' + item.id + '" id="checkbox'+ item.id + '"' + '>' + '</input>' +
											'&nbsp;' + '&nbsp;' + '<label for="checkbox' + item.id + '" data-toggle="tooltip" title="' + item.permissionTooltip + '">' +
											item.name +
										'</label></div></div>');
						}

						$('#checkbox' + item.id).bootstrapToggle();
						$('[data-toggle="tooltip"]').tooltip();

						$( ".label_enable" ).tooltip({
							content: function() { return "testtesttest"; },
						});


					});
			
				});
				
			});

		})

	</script>
@endsection
