(function ($) {
	'use strict';
	var serializeObject = function ($form, wp_action_name) {
		var o       = {};
		o['action'] = wp_action_name;
		var a       = $form.serializeArray();
		$.each(a, function () {
			if (o[this.name]) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};

	jQuery(document).ready(function ($) {

		var $validate_messages = cbxvc_admin_l10n.validation_messages.jquery_validate_messages;

		$.extend($.validator.messages, {
			required   : $validate_messages.required,
			remote     : $validate_messages.remote,
			email      : $validate_messages.email,
			url        : $validate_messages.url,
			date       : $validate_messages.date,
			dateISO    : $validate_messages.dateISO,
			number     : $validate_messages.number,
			digits     : $validate_messages.digits,
			creditcard : $validate_messages.creditcard,
			equalTo    : $validate_messages.equalTo,
			extension  : $validate_messages.extension,
			maxlength  : $.validator.format($validate_messages.maxlength),
			minlength  : $.validator.format($validate_messages.minlength),
			rangelength: $.validator.format($validate_messages.rangelength),
			range      : $.validator.format($validate_messages.range),
			max        : $.validator.format($validate_messages.max),
			min        : $.validator.format($validate_messages.min),
		});

		var $formvalidator = $('#cbacnt-vc-account-form').validate({
			errorPlacement: function (error, element) {
				error.appendTo(element.parents('.error_container'));
			},
			errorElement  : 'p',
			errorClass    : 'error cbxwpsimpleaccounting_fielderror'
		});

		//create new account
		$('#cbacnt-vc-account-form').on('submit', function (evnt) {
			evnt.preventDefault();
			var $form = $(this);

			if ($formvalidator.valid()) {
				$('#cbxaccountingloading').show();
				$form.find('#cbxvc-new-acc').prop("disabled", true);


				$.ajax({
					type    : 'post',
					dataType: 'json',
					url     : ajaxurl,
					data    : serializeObject($form, 'add_new_vc_manager_acc'),
					success : function (response) {
						//console.log(response);

						//clear all error and update field
						$('#cbxaccountingloading').hide();
						$form.find('.cbacnt-acc').removeClass('cbacnt-error');
						$form.find('#cbxvc-new-acc').prop("disabled", false);

						$form.find('#cbacnt-edit-manage-acc-cancel').attr('disabled', 'disabled');

						if (response.error) {
							$form.find('.cbacnt-msg-text').text(response.msg);
							$form.find('.cbacnt-msg-box').addClass('error').removeClass('updated hidden').show();
							$.each(response.field, function (index, value) {
								$form.find('label[for="' + value + '"]' + value).addClass('cbacnt-error');
							});
						} else {
							//$all_acc_list[response.form_value.id] = response.form_value;

							$form.find('.cbacnt-msg-text').html(response.msg);
							$form.find('.cbacnt-msg-box').addClass('updated').removeClass('error hidden').show();

							$form.find('#vc_id').val(response.form_value.id);

							$form.find('#cbxvc-new-acc').val($form.find('#cbxvc-new-acc').data('update-value'));
							var $user_id = parseInt(response.form_value.user_id);
							if($form.find('#user_id_information').length > 0){
								$form.find('#user_id_information').remove();
							}


							if($user_id > 0){
								$(response.form_value.user_id_information).insertAfter( $form.find('#user_id') );
							}

							//reset form is new item inserted
							/*if (response.form_value.status = 'new') {
								$form[0].reset();
							}*/
						}
					}
				});//end ajax calling for category
			}


		});//end category form submission

		var $new_phone_template = $('#new_phone_template').html();
		Mustache.parse($new_phone_template);   // optional, speeds up future uses

		// vc add new phone
		$('#cbacnt-vc-contactphone_new').on('click', function (e) {
			e.preventDefault();

			var $this    = $(this);
			var $counter = parseInt($this.attr('data-counter'));

			var rendered = Mustache.render($new_phone_template, {increment: $counter, incrementplus: ($counter + 1)});
			$("#cbx_new_phone_wrapper").append(rendered);

			$counter++;
			$this.attr('data-counter', $counter);
		});

		// vc remove phone
		$("#cbx_new_phone_wrapper").on('click', 'a.trash-new-phone', function (e) {
			e.preventDefault();

			var $this = $(this);

			Ply.dialog({
				"confirm-step": {
					ui        : "confirm",
					data      : {
						text  : cbxvc_admin_l10n.deleteconfirm,
						ok    : cbxvc_admin_l10n.deleteconfirmok, // button text
						cancel: cbxvc_admin_l10n.deleteconfirmcancel
					},
					backEffect: "3d-flip[-180,180]"
				}
			}).always(function (ui) {
				if (ui.state) {
					// Ok
					$this.parents('.cbacnt-vc-contactphone').fadeOut("slow", function () {
						$(this).remove();
					});
				} else {
					// Cancel
					// ui.by � 'overlay', 'x', 'esc'
				}
			});
		});

	});

})(jQuery);