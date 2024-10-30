<?php
    /**
     * Provide a dashboard view for the plugin
     *
     * This file is used to markup the admin-facing aspects of the plugin.
     *
     * @link       https://codeboxr.com
     * @since      1.0.7
     *
     * @package    cbxwpsimpleaccountingvc
     * @subpackage cbxwpsimpleaccountingvc/admin/partials
     */

    if (!defined('WPINC')) {
        die;
    }

    global $wpdb;
    $counter = 1;
    $data = array();
	$vc_id = 0;

    if (isset($_GET['id']) && intval($_GET['id']) > 0) {
        $vc_id = absint($_GET['id']);

        $vc_table = $wpdb->prefix . "cbaccounting_vc_manager";

        $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $vc_table WHERE id=%d", $vc_id), 'ARRAY_A');

        $contactinfo = maybe_unserialize($data['contactinfo_office']);

		if(is_serialized($contactinfo['phone'])){
			$contactinfo_phone = maybe_unserialize($contactinfo['phone']); //backword compatibility
		}
		else{
			$contactinfo_phone = $contactinfo['phone'];
		}


        $addressinfo = maybe_unserialize($data['address']);


    }
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <div id="cbxaccountingloading" style="display:none"></div>
    <h1 class="wp-heading-inline wp-heading-inline-vc-edit">
        <?php esc_html_e('CBX Accounting: Add/Edit Vendors & Clients', 'cbxwpsimpleaccountingvc'); ?>
		<p>
			<?php echo '<a class="button button-primary button-large" href="' . admin_url('admin.php?page=cbxwpsimpleaccountingvc') . '">' . esc_html__('Back to Listing', 'cbxwpsimpleaccountingvc') . '</a>'; ?> <?php echo '<a class="button" href="' . admin_url('admin.php?page=cbxwpsimpleaccountingvc&view=addedit') . '">' . esc_attr__('Add New', 'cbxwpsimpleaccountingvc') . '</a>'; ?>
		</p>
    </h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-1">

            <!-- main content -->
            <div id="post-body-content">
                <div id="cbxaccounting_accmanager" class="meta-box-sortables ui-sortable">

                    <div class="postbox">
                        <h3><span><?php esc_html_e('Add/Edit Account', 'cbxwpsimpleaccountingvc'); ?></span></h3>
                        <div class="inside">
                            <div id="cbxwpsimpleaccounting">
                                <form id="cbacnt-vc-account-form" class="form-horizontal" action="" method="post">

                                    <div class="cbacnt-msg-box below-h2 hidden"><p class="cbacnt-msg-text"></p></div>

                                    <input name="id" id="vc_id" type="hidden" value="<?php echo $vc_id; ?>"/>
                                    <?php wp_nonce_field('add_new_acc', 'new_acc_verifier'); ?>

                                    <div class="form-group">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="type"><?php esc_html_e('Type', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <label for="cbacnt-acc-type-client" class="radio-inline">
                                                <input type="radio" name="type" class="cbacnt-acc-type"
                                                       id="cbacnt-acc-type-client" required
                                                       value="client" <?php if (!isset($data['type']) || $data['type'] === 'client') echo 'checked'; ?>
                                                /><?php esc_html_e('Client', 'cbxwpsimpleaccountingvc'); ?>
                                            </label>
                                            <label for="cbacnt-acc-type-vendor" class="radio-inline">
                                                <input type="radio" name="type" class="cbacnt-acc-type"
                                                       id="cbacnt-acc-type-vendor" required
                                                       value="vendor" <?php if (isset($data['type']) && $data['type'] === 'vendor') echo 'checked'; ?>
                                                /><?php esc_html_e('Vendor', 'cbxwpsimpleaccountingvc'); ?>
                                            </label>
                                        </div>
                                    </div>

                                    <?php $name = isset($data['name']) ? esc_attr($data['name']) : ''; ?>
                                    <div class="form-group">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="name"><?php esc_html_e('Name', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <input name="name" id="name" type="text" value="<?php echo $name; ?>"
                                                   class="cbacnt-acc regular-text form-control" data-rule-required="true"  data-rule-minlength="5" data-rule-maxlength="200" autocomplete="off" autofocus />
                                        </div>
                                    </div>

                                    <?php $description = isset($data['description']) ? esc_html($data['description']) : ''; ?>
                                    <div class="form-group">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="description"><?php esc_html_e('Description', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <textarea name="description" id="description" cols="50" rows="6"
                                                      class="cbacnt-acc regular-text form-control"><?php echo $description; ?></textarea>
                                        </div>
                                    </div>

                                    <?php $organization = isset($data['organization']) ? esc_attr($data['organization']) : ''; ?>
                                    <div class="form-group">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="organization"><?php esc_html_e('Organization', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <input name="organization" id="organization" type="text"
                                                   class="cbacnt-acc regular-text form-control"
                                                   value="<?php echo $organization; ?>"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-1">
                                            <h3><?php esc_html_e('Address Information', 'cbxwpsimpleaccountingvc'); ?></h3>
                                        </div>
                                    </div>

                                    <?php $address_line1 = isset($addressinfo['line1']) ? $addressinfo['line1'] : ''; ?>
                                    <div class="form-group cbxacc_bankdetails">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="address_line1"><?php esc_html_e('Address Line 1', 'cbxwpsimpleaccountingvc'); ?></label>

                                        <div class="col-sm-10 error_container">
                                            <input name="address[line1]" id="address_line1" type="text"
                                                   value="<?php echo $address_line1; ?>"
                                                   class="cbacnt-acc-acc-no regular-text form-control"/>
                                        </div>
                                    </div>

                                    <?php $address_line2 = isset($addressinfo['line2']) ? $addressinfo['line2'] : ''; ?>
                                    <div class="form-group cbxacc_bankdetails">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="address_line2"><?php esc_html_e('Address Line 2', 'cbxwpsimpleaccountingvc'); ?></label>

                                        <div class="col-sm-10 error_container">
                                            <input name="address[line2]" id="address_line2" type="text"
                                                   value="<?php echo $address_line2; ?>"
                                                   class="cbacnt-acc-acc-no regular-text form-control"/>
                                        </div>
                                    </div>

                                    <?php $address_city = isset($addressinfo['city']) ? $addressinfo['city'] : ''; ?>
                                    <div class="form-group cbxacc_bankdetails">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="address_city"><?php esc_html_e('City', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <input name="address[city]" id="address_city" type="text"
                                                   value="<?php echo $address_city; ?>"
                                                   class="cbacnt-acc-acc-name regular-text form-control"/>
                                        </div>
                                    </div>

                                    <?php $address_state = isset($addressinfo['state']) ? $addressinfo['state'] : ''; ?>
                                    <div class="form-group cbxacc_bankdetails">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="address_state"><?php esc_html_e('State/Province/Region', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <input name="address[state]" id="address_state" type="text"
                                                   value="<?php echo $address_state; ?>"
                                                   class="cbacnt-acc-bank-name regular-text form-control"/>
                                        </div>
                                    </div>

                                    <?php $address_postal = isset($addressinfo['postal']) ? $addressinfo['postal'] : ''; ?>
                                    <div class="form-group cbxacc_bankdetails">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="address_postal"><?php esc_html_e('Zip/Postal Code', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <input name="address[postal]" id="address_postal" type="text"
                                                   value="<?php echo $address_postal; ?>"
                                                   class="cbacnt-acc-branch-name regular-text form-control"/>
                                        </div>
                                    </div>

                                    <?php $address_country = isset($addressinfo['country']) ? $addressinfo['country'] : ''; ?>
                                    <div class="form-group">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="address_country"><?php esc_html_e('Country', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <select name="address[country]" id="address_country"
                                                    class="selecttwo-select form-control">
                                                <option value="none">
                                                    <?php esc_html_e('Select Country', 'cbxwpsimpleaccountingvc'); ?>
                                                </option>
                                                <?php
                                                    foreach (CBXWPSimpleaccountingVCHelper::getAllCountries() as $country_code => $country_name) { ?>
                                                        <option value="<?php echo $country_code; ?>" <?php if ($country_code == $address_country) echo 'selected="selected"'; ?>>
                                                            <?php echo $country_name; ?>
                                                        </option>
                                                    <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-1">
                                            <h3><?php esc_html_e('Official Contact Information', 'cbxwpsimpleaccountingvc'); ?></h3>
                                        </div>
                                    </div>

                                    <?php $contact_name = isset($contactinfo['name']) ? $contactinfo['name'] : ''; ?>
                                    <div class="form-group">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="contactinfo_office_name"><?php esc_html_e('Name', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <input name="contactinfo_office[name]" id="contactinfo_office_name"
                                                   type="text" class="cbacnt-acc regular-text form-control"
                                                   value="<?php echo $contact_name; ?>"/>
                                        </div>
                                    </div>

                                    <?php $contact_designation = isset($contactinfo['designation']) ? $contactinfo['designation'] : ''; ?>
                                    <div class="form-group">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="contactinfo_office_designation"><?php esc_html_e('Designation', 'cbxwpsimpleaccountingvc'); ?></label>
                                        <div class="col-sm-10 error_container">
                                            <input name="contactinfo_office[designation]"
                                                   id="contactinfo_office_designation" type="text"
                                                   class="cbacnt-acc regular-text form-control"
                                                   value="<?php echo $contact_designation; ?>"/>
                                        </div>
                                    </div>

	                                <?php $contact_email = isset($contactinfo['email']) ? $contactinfo['email'] : ''; ?>
									<div class="form-group">
										<label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
											   for="contactinfo_office_email"><?php esc_html_e('Contact Email', 'cbxwpsimpleaccountingvc'); ?></label>
										<div class="col-sm-10 error_container">
											<input name="contactinfo_office[email]"
												   id="contactinfo_office_email" type="email"
												   class="cbacnt-acc regular-text form-control"
												   value="<?php echo $contact_email; ?>"/>
										</div>
									</div>

                                    <div class="">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"><?php esc_html_e('Phone', 'cbxwpsimpleaccountingvc'); ?></label>

                                        <div class="col-sm-10" id="cbacnt-vc-contactphone_wrap">
                                            <?php
                                                $contact_phoneval  = isset($contactinfo_phone[0]['phoneval']) ? $contactinfo_phone[0]['phoneval'] : '';
                                                $contact_phonetype = isset($contactinfo_phone[0]['phonetype']) ? $contactinfo_phone[0]['phonetype'] : '';
                                            ?>
                                            <div class="cbacnt-vc-contactphone">
                                                <div class="row">
                                                    <div class="form-group col-sm-9 error_container">
                                                        <input name="contactinfo_office[phone][0][phoneval]" type="text"
                                                               class="cbacnt-acc regular-text form-control"
                                                               value="<?php echo $contact_phoneval; ?>"/>
                                                    </div>
                                                    <div class="form-group col-sm-2">
                                                        <select name="contactinfo_office[phone][0][phonetype]" class="form-control">
                                                            <?php
                                                                foreach (CBXWPSimpleaccountingVCHelper::getCommunicationWay() as $way_type => $way_value) { ?>
                                                                    <option value="<?php echo $way_type; ?>" <?php if ($way_type == $contact_phonetype) echo 'selected="selected"'; ?> >
                                                                        <?php echo $way_value; ?>
                                                                    </option>
                                                                <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="cbx_new_phone_wrapper">
                                                <?php if (isset($contactinfo_phone) && sizeof($contactinfo_phone) > 0) {
                                                    $counter = count($contactinfo_phone);
                                                    unset($contactinfo_phone[0]);
                                                    ?>
                                                    <?php foreach ($contactinfo_phone as $phone_index => $phoneval_type) { ?>
                                                        <div class="cbacnt-vc-contactphone">
                                                            <div class="row">
                                                                <div class="form-group col-sm-9 error_container">
                                                                    <input name="contactinfo_office[phone][<?php echo $phone_index; ?>][phoneval]"
                                                                           type="text"
                                                                           class="cbacnt-acc regular-text form-control"
                                                                           value="<?php echo $phoneval_type['phoneval']; ?>"/>
                                                                </div>
                                                                <?php $contact_phonetype = isset($contactinfo_phone[$phone_index]['phonetype']) ? $contactinfo_phone[$phone_index]['phonetype'] : ''; ?>
                                                                <div class="form-group col-sm-2">
                                                                    <select name="contactinfo_office[phone][<?php echo $phone_index; ?>][phonetype]"  class="form-control">
                                                                        <?php
                                                                            foreach (CBXWPSimpleaccountingVCHelper::getCommunicationWay() as $way_type => $way_value) { ?>
                                                                                <option value="<?php echo $way_type; ?>" <?php if ($way_type == $contact_phonetype) echo 'selected="selected"'; ?> >
                                                                                    <?php echo $way_value; ?>
                                                                                </option>
                                                                            <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-sm-1">
                                                                    <a href="#" title="<?php esc_html_e('Delete Phone', 'cbxwpsimpleaccountingvc') ?>" class="dashicons dashicons-post-trash trash-new-phone"></a>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <a href="#" class="btn btn-info col-sm-offset-2" role="button" data-counter="<?php echo $counter; ?>"
                                              id="cbacnt-vc-contactphone_new" title="<?php esc_html_e('Add new Phone', 'cbxwpsimpleaccountingvc') ?>"><?php esc_html_e('Add New', 'cbxwpsimpleaccountingvc'); ?></a>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-1">
                                            <h3><?php esc_html_e('Connected User', 'cbxwpsimpleaccountingvc'); ?></h3>
                                        </div>
                                    </div>
                                    <?php
                                        $user_id = isset($data['user_id']) ? $data['user_id'] : "";
                                        if ($user_id == 0) $user_id = '';
                                    ?>
                                    <div class="form-group">
                                        <label class="cbacnt-acc cbacnt-label col-sm-2 control-label"
                                               for="user_id"><?php esc_html_e('User ID', 'cbxwpsimpleaccountingvc'); ?></label>

                                        <div class="col-sm-10">
                                            <input name="user_id" id="user_id" type="text"
                                                   value="<?php echo $user_id; ?>"
                                                   class="cbacnt-acc regular-text form-control"/>
                                            <?php
                                                if (intval($user_id) > 0):
                                                    echo '<p id="user_id_information"><a href= ' . get_edit_user_link($user_id) . ' target = "_blank">' . stripslashes(get_user_by('id', $user_id)->display_name) . '</a></p>';
                                                endif;
                                            ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10">
                                            <input id="cbxvc-new-acc" class="btn btn-default btn-primary" type="submit"
                                                   name="cbxvc-new-acc"
                                                   data-add-value="<?php esc_html_e('Add New', 'cbxwpsimpleaccountingvc'); ?>"
                                                   data-update-value="<?php esc_html_e('Update', 'cbxwpsimpleaccountingvc'); ?>"
                                                   value="<?php
                                                       if ($vc_id > 0) {
                                                           esc_html_e('Update', 'cbxwpsimpleaccountingvc');
                                                       } else {
                                                           esc_html_e('Add New', 'cbxwpsimpleaccountingvc');
                                                       }
                                                   ?>"/>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- .inside -->
                    </div> <!-- .postbox -->
                </div> <!-- .meta-box-sortables .ui-sortable -->
            </div> <!-- post-body-content -->
            <?php
                //include('sidebar.php');
            ?>
        </div>
		<div class="clear"></div>
    </div>
</div>