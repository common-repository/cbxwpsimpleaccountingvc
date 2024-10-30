<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    cbxwpsimpleaccountingvc
 * @subpackage cbxwpsimpleaccountingvc/admin/partials
 */
?>
<?php

//ready made templates

echo '<!-- mustache template -->
<script id="new_phone_template" type="x-tmpl-mustache">
	 <div class="cbacnt-vc-contactphone" data-boxincrement="{{increment}}">
	    <div class="row">
            <div class="form-group col-sm-9 error_container">
                <input name="contactinfo_office[phone][{{increment}}][phoneval]" type="text" class="cbacnt-acc regular-text form-control" value=""/>
            </div>
            <div class="form-group col-sm-2">
                 <select name="contactinfo_office[phone][{{increment}}][phonetype]" class="form-control">
                     <option value="work">Work</option>
                     <option value="home">Home</option>
                     <option value="other">Other</option>
                 </select>		   
            </div>
            <div class="form-group col-sm-1">
                <a href="#" title="'.esc_html__('Delete Phone', 'cbxwpsimpleaccountingvc').'"  class="dashicons dashicons-post-trash trash-new-phone"></a>
            </div>
	    </div>
	 </div>
</script>';


