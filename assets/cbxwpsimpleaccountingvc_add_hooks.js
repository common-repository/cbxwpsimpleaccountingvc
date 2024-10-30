//load data to fields after ajax submit of update or add
wp.cbxwpsajshooks.addAction( 'cbxwpsimpleaccounting_incexp_post_data', function($form, response) {
	$form.find('#cbacnt-exinc-vcid').val(response.form_value.vc_id).trigger("chosen:updated");;

} );

//ajax load for edit data
wp.cbxwpsajshooks.addAction( 'cbxwpsimpleaccounting_incexp_edit_data', function($form, response) {
	$form.find('#cbacnt-exinc-vcid').val(response.form_value.vc_id).trigger("chosen:updated");;

} );

//reset for new entry
wp.cbxwpsajshooks.addAction( 'cbxwpsimpleaccounting_incexp_new_reset', function($form) {

	$form.find('#cbacnt-exinc-vcid').val('').trigger("chosen:updated");

} );