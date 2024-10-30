<?php
    /**
     * Provide a dashboard view for the plugin
     *
     * This file is used to markup the public-facing aspects of the plugin.
     *
     * @link       https://codeboxr.com
     * @since      1.0.7
     *
     * @package    Cbxwpsimpleaccounting
     * @subpackage Cbxwpsimpleaccounting/admin/partials
     */

    if (!defined('WPINC')) {
        die;
    }
?>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <div id="cbxaccountingloading" style="display:none"></div>
    <h1 class="wp-heading-inline wp-heading-inline-vc-view"><?php esc_html_e('CBX Accounting: Vendors & Clients','cbxwpsimpleaccountingvc'); ?></h1>
	<p>
		<?php echo '<a class="button button-primary button-large" href="' . admin_url('admin.php?page=cbxwpsimpleaccountingvc') . '">' . esc_attr__('Back to Lists', 'cbxwpsimpleaccountingvc') . '</a>'; ?> <?php echo '<a class="button" href="' . admin_url('admin.php?page=cbxwpsimpleaccountingvc&view=addedit') . '">' . esc_attr__('Add New', 'cbxwpsimpleaccountingvc') . '</a>'; ?>
	</p>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-1">
            <!-- main content -->
            <div id="post-body-content">
                <div id="cbxaccounting_accmanager" class="meta-box-sortables ui-sortable">

                    <?php
                        global $wpdb;
                        if (isset($_GET['id'])) {
                            $vc_table = $wpdb->prefix . "cbaccounting_vc_manager";

                            $vc_id = absint($_GET['id']);

                            $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $vc_table WHERE id=%d", $vc_id), 'ARRAY_A');
                            $contactinfo = maybe_unserialize($data['contactinfo_office']);
                            $contactinfo_phone = maybe_unserialize($contactinfo['phone']);
                            $addressinfo = maybe_unserialize($data['address']);
                        }
                    ?>
                    <div class="postbox">
                        <h3>
                            <?php esc_html_e('Vendor Details', 'cbxwpsimpleaccountingvc'); ?>
                        </h3>
                        <div class="inside">

                            <?php
                                if ($data == null) {
                                    echo '<div class="notice notice-error inline"><p>' . esc_html__('No Vendor or Client Found', 'cbxwpsimpleaccountingvc') . '</p></div>';
                                }
                            ?>
                            <?php
                                do_action('cbxwpsimpleaccountingvc_view_before', $vc_id, $data);
                            ?>

                            <?php if ($data != null): ?>
                                <table class="form-table">
                                    <?php $type = isset($data['type']) ? $data['type'] : ''; ?>
                                    <tr valign="top">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Type', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th"><?php echo esc_html(CBXWPSimpleaccountingVCHelper::vcType($type)); ?></td>
                                    </tr>

                                    <?php $name = isset($data['name']) ? $data['name'] : ''; ?>
                                    <tr valign="top">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Name', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th"><?php echo $name; ?></td>
                                    </tr>

                                    <?php $description = isset($data['description']) ? $data['description'] : ''; ?>
                                    <tr valign="top">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Description', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php echo $description; ?>
                                        </td>
                                    </tr>

                                    <?php $organization = isset($data['organization']) ? $data['organization'] : ''; ?>
                                    <tr valign="top">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Organization', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php echo $organization; ?>
                                        </td>
                                    </tr>
                                    <tr valign="top">
                                        <th class="row-title" scope="row" colspan="2">
                                            <p><?php esc_html_e('Address', 'cbxwpsimpleaccountingvc'); ?></p>
                                        </th>
                                    </tr>

                                    <?php $address_line1 = isset($addressinfo['line1']) ? $addressinfo['line1'] : ''; ?>
                                    <tr valign="top" class="cbxacc_bankdetails">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Address Line 1', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php echo $address_line1; ?>
                                        </td>
                                    </tr>

                                    <?php $address_line2 = isset($addressinfo['line2']) ? $addressinfo['line2'] : ''; ?>
                                    <tr valign="top" class="cbxacc_bankdetails">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Address Line 2', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php echo $address_line2; ?>
                                        </td>
                                    </tr>

                                    <?php $address_city = isset($addressinfo['city']) ? $addressinfo['city'] : ''; ?>
                                    <tr valign="top" class="cbxacc_bankdetails">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('City', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php echo $address_city; ?>
                                        </td>
                                    </tr>

                                    <?php $address_state = isset($addressinfo['state']) ? $addressinfo['state'] : ''; ?>
                                    <tr valign="top" class="cbxacc_bankdetails">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('State/Province/Region', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php echo $address_state; ?>
                                        </td>
                                    </tr>

                                    <?php  $address_postal = isset($addressinfo['postal']) ? $addressinfo['postal'] :''; ?>
                                    <tr valign="top" class="cbxacc_bankdetails">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Zip/Postal Code', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php echo $address_postal; ?>
                                        </td>
                                    </tr>

                                    <tr valign="top">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Country', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php
                                                if (isset($addressinfo['country']) && $addressinfo['country'] != '') {
                                                    $countries = CBXWPSimpleaccountingVCHelper::getAllCountries();
                                                    if (array_key_exists($addressinfo['country'], $countries)) {
                                                        echo $countries[$addressinfo['country']];
                                                    }
                                                }
                                            ?>
                                        </td>
                                    </tr>

                                    <tr valign="top">
                                        <th class="row-title" scope="row" colspan="2">
                                            <p><?php esc_html_e('Official Contact Information', 'cbxwpsimpleaccountingvc'); ?></p>
                                        </th>
                                    </tr>

                                    <?php  $contact_name = isset($contactinfo['name']) ? $contactinfo['name'] : ''; ?>
                                    <tr valign="top">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Name', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php echo $contact_name; ?>
                                        </td>
                                    </tr>

                                    <?php $contact_designation = isset($contactinfo['designation']) ?  $contactinfo['designation'] : ''; ?>
                                    <tr valign="top">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <?php esc_html_e('Designation', 'cbxwpsimpleaccountingvc'); ?>
                                        </th>
                                        <td class="lowpadding_th">
                                            <?php echo $contact_designation; ?>
                                        </td>
                                    </tr>
	                                <?php $contact_email = isset($contactinfo['email']) ? $contactinfo['email'] : ''; ?>
									<tr valign="top">
										<th class="row-title lowpadding_th" scope="row">
			                                <?php esc_html_e('Email', 'cbxwpsimpleaccountingvc'); ?>
										</th>
										<td class="lowpadding_th">
			                                <?php echo $contact_email; ?>
										</td>
									</tr>
                                    <tr valign="top">
                                        <th class="row-title lowpadding_th" scope="row">
                                            <label class="cbacnt-acc cbacnt-label"><?php _e('Phone', 'cbxwpsimpleaccountingvc'); ?></label>
                                        </th>
                                        <td class="lowpadding_th">
                                            <div id="cbacnt-vc-contactphone_wrap">
                                                <?php if (is_array($contactinfo_phone) && sizeof($contactinfo_phone) > 0) { ?>
                                                    <div class="cbacnt-vc-contactphone">
                                                        <?php foreach ($contactinfo_phone as $counter => $value) {
                                                            echo $contactinfo_phone[$counter]['phonetype'] . ': ' . $contactinfo_phone[$counter]['phoneval'] . '<br>';
                                                        }
                                                        ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            <?php endif; ?>
                            <?php
                                do_action('cbxwpsimpleaccountingvc_view_after', $vc_id, $data);
                            ?>
                            <p>
                                <?php
                                    if (current_user_can('log_cbxaccounting') && defined('CBXWPSIMPLEACCOUNTINGLOG_PLUGIN_VERSION')) {

                                        $admin_url = admin_url('admin.php?page=cbxwpsimpleaccountinglog&cbxfilter_action=Filter&cbxlogexpinc_vcid=' . $vc_id);

                                        $admin_nonce_url = wp_nonce_url($admin_url, 'bulk-cbxaccountinglogs');
                                        echo '<a class="button button-secondary" href="' . $admin_nonce_url . '" target="_blank">' . esc_html__('View All Log', 'cbxwpsimpleaccountingvc') . '</a>';
                                    }
                                ?>
                                <a class="button button-primary" target="_blank" href="<?php echo admin_url('admin.php?page=cbxwpsimpleaccountingvc&view=addedit&id=' . $vc_id); ?>"><?php esc_html_e('Edit', 'cbxwpsimpleaccountingvc'); ?></a>
                            </p>

                        </div> <!-- .inside -->
                    </div> <!-- .postbox -->

                </div> <!-- .meta-box-sortables .ui-sortable -->
            </div> <!-- post-body-content -->
            <?php   //include('sidebar.php');   ?>
        </div> <!-- #post-body .metabox-holder .columns-2 -->
		<div class="clear clearfix"></div>
    </div> <!-- #poststuff -->
</div> <!-- .wrap -->