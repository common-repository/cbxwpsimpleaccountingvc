<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>
<div id="postbox-container-1" class="postbox-container">
    <div class="meta-box-sortables">
        <div class="postbox">
            <h3><?php esc_html_e( 'Help & Supports', 'cbxwpsimpleaccounting' ) ?></h3>
            <div class="inside">
                <p>Support: <a href="https://codeboxr.com/contact-us" target="_blank"><span class="dashicons dashicons-external"></span> Contact Us</a></p>
                <p><span class="dashicons dashicons-email"></span> <a href="mailto:info@codeboxr.com">info@codeboxr.com</a></p>
                <p><span class="dashicons dashicons-info"></span> <a href="https://codeboxr.com/product/cbx-accounting/" target="_blank">Plugin Information</a></p>
                <p><span class="dashicons dashicons-editor-help"></span> <a href="https://codeboxr.com/documentation-for-cbx-accounting/" target="_blank">Plugin Documentation</a></p>
                <p><span class="dashicons dashicons-star-half"></span> <a href="https://wordpress.org/support/plugin/cbxwpsimpleaccounting/reviews/#new-post" target="_blank">Review This Plugin</a></p>
            </div>
        </div>
		<div class="postbox">
			<h3><?php esc_html_e( 'Free & Pro Addons', 'cbxwpsimpleaccounting' ) ?></h3>
			<div class="inside">
				<ul>
					<li><a target="_blank" href="https://codeboxr.com/product/vendor-and-client-addon-for-cbx-accounting/?utm_source=cbxwpsimpleaccounting&utm_medium=website&utm_campaign=cbxwpsimpleaccounting&utm_term=cbxwpsimpleaccounting">Vendors and Client addon for CBX Accounting</a></li>
					<li><a target="_blank" href="https://codeboxr.com/product/statement-addon-for-cbx-accounting/?utm_source=cbxwpsimpleaccounting&utm_medium=website&utm_campaign=cbxwpsimpleaccounting&utm_term=cbxwpsimpleaccounting">Statement addon for CBX Accounting</a></li>
					<li><a target="_blank" href="https://codeboxr.com/product/woocommerce-addon-for-cbx-accounting/?utm_source=cbxwpsimpleaccounting&utm_medium=website&utm_campaign=cbxwpsimpleaccounting&utm_term=cbxwpsimpleaccounting">Woocommerce Addon for CBX Accounting</a></li>
					<li><a target="_blank" href="https://codeboxr.com/product/frontend-addon-for-cbx-accounting/?utm_source=cbxwpsimpleaccounting&utm_medium=website&utm_campaign=cbxwpsimpleaccounting&utm_term=cbxwpsimpleaccounting">Frontend Addon for CBX Accounting</a></li>
				</ul>
			</div>
		</div>
		<div class="postbox">
			<h3><?php esc_html_e( 'Other WordPress Plugins', 'cbxwpsimpleaccounting' ); ?></h3>
			<div class="inside">
			    <?php

				    include_once( ABSPATH . WPINC . '/feed.php' );
				    if ( function_exists( 'fetch_feed' ) ) {
					    //$feed = fetch_feed( 'https://codeboxr.com/feed?post_type=product' );
					    $feed = fetch_feed( 'https://codeboxr.com/product_cat/wordpress/feed/' );
					    if ( ! is_wp_error( $feed ) ) : $feed->init();
						    $feed->set_output_encoding( 'UTF-8' ); // this is the encoding parameter, and can be left unchanged in almost every case
						    $feed->handle_content_type(); // this double-checks the encoding type
						    $feed->set_cache_duration( 21600 ); // 21,600 seconds is six hours
						    $limit = $feed->get_item_quantity( 20 ); // fetches the 18 most recent RSS feed stories
						    $items = $feed->get_items( 0, $limit ); // this sets the limit and array for parsing the feed

						    $blocks = array_slice( $items, 0, 20 );

						    echo '<ul>';

						    foreach ( $blocks as $block ) {
							    $url = $block->get_permalink();

							    echo '<li style="clear:both;  margin-bottom:5px;"><a target="_blank" href="' . $url . '">';
							    //echo '<img style="float: left; display: inline; width:70px; height:70px; margin-right:10px;" src="https://codeboxr.com/wp-content/uploads/productshots/'.$id.'-profile.png" alt="wpboxrplugins" />';
							    echo '<strong>' . $block->get_title() . '</strong></a></li>';
						    }//end foreach

						    echo '</ul>';


					    endif;
				    }
			    ?>
			</div>
		</div>
		<div class="postbox">
			<h3><?php esc_html_e( 'Codeboxr News Updates', 'cbxwpsimpleaccounting' ) ?></h3>
			<div class="inside">
			    <?php

				    include_once( ABSPATH . WPINC . '/feed.php' );
				    if ( function_exists( 'fetch_feed' ) ) {
					    //$feed = fetch_feed( 'https://codeboxr.com/feed' );
					    $feed = fetch_feed( 'https://codeboxr.com/feed?post_type=post' );
					    // $feed = fetch_feed('http://feeds.feedburner.com/codeboxr'); // this is the external website's RSS feed URL
					    if ( ! is_wp_error( $feed ) ) : $feed->init();
						    $feed->set_output_encoding( 'UTF-8' ); // this is the encoding parameter, and can be left unchanged in almost every case
						    $feed->handle_content_type(); // this double-checks the encoding type
						    $feed->set_cache_duration( 21600 ); // 21,600 seconds is six hours
						    $limit = $feed->get_item_quantity( 10 ); // fetches the 10 most recent RSS feed stories
						    $items = $feed->get_items( 0, $limit ); // this sets the limit and array for parsing the feed

						    $blocks = array_slice( $items, 0, 10 ); // Items zero through six will be displayed here
						    echo '<ul>';
						    foreach ( $blocks as $block ) {
							    $url = $block->get_permalink();
							    echo '<li><a target="_blank" href="' . $url . '">';
							    echo '<strong>' . $block->get_title() . '</strong></a></li>';
						    }//end foreach
						    echo '</ul>';


					    endif;
				    }
			    ?>
			</div>
		</div>
    </div>
</div>