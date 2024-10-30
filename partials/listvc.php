<?php
	// If this file is called directly, abort.
	if (!defined('WPINC')) {
		die;
	}

//Create an instance of our CBXPollLog class
    $cbxaccvclist = new CBXWPSACC_VC_Listing();


?>
    <div class="wrap">
        <h1 class="wp-heading-inline wp-heading-inline-vc-list"><?php esc_html_e('CBX Accounting: Vendors & Clients Manager', 'cbxwpsimpleaccountingvc'); ?></h1>
        <p><?php echo '<a class="button-primary" href="' . admin_url('admin.php?page=cbxwpsimpleaccountingvc&view=addedit') . '">' . esc_attr__('Add New', 'cbxwpsimpleaccountingvc') . '</a>'; ?></p>
        <?php

            $cbxaccvclist->prepare_items();

	        if(isset($_SESSION['cbxwpsimpleaccounting_vcs_bulkdelete'])){
		        $validations = $_SESSION['cbxwpsimpleaccounting_vcs_bulkdelete'];
		        if(is_array($validations) && sizeof($validations) > 0){
			        foreach ($validations as $validation){
				        $error_class = (isset($validation['error']) && intval($validation['error']) == 1) ? 'notice notice-error': 'notice notice-success';

				        if(isset($validation['msg'])){
					        echo '<div class="'.esc_attr($error_class).'"><p>'.$validation['msg'].'</p></div>';
				        }
			        }
		        }


		        unset($_SESSION['cbxwpsimpleaccounting_vcs_bulkdelete']);
	        }
        ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-1">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <div class="postbox">
                            <div class="inside">
                                <?php $cbxaccvclist->views(); ?>
                                <form id="cbxaccvc_listing" method="post" action="">
                                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                                    <?php $cbxaccvclist->search_box(esc_html__('Search Vendor/Client', 'cbxwpsimpleaccountingvc'), 'vcsearch'); ?>
                                    <?php $cbxaccvclist->display() ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    //include dirname(__FILE__) . '/sidebar.php';
                ?>
            </div>
			<div class="clear clearfix"></div>
        </div>
    </div>