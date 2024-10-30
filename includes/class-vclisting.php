<?php
	// If this file is called directly, abort.
	if (!defined('WPINC')) {
		die;
	}

    if (!class_exists('WP_List_Table')) {
        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    }

    class CBXWPSACC_VC_Listing extends WP_List_TableCBXACCSVC
    {


        /** ************************************************************************
         * REQUIRED. Set up a constructor that references the parent constructor. We
         * use the parent reference to set some default configs.
         ***************************************************************************/
        function __construct()
        {
            global $status, $page;

            //Set parent defaults
            parent::__construct(array(
                                    'singular' => 'cbxaccvc',     //singular name of the listed records
                                    'plural'   => 'cbxaccvcs',    //plural name of the listed records
                                    'ajax'     => false      //does this table support ajax?
                                ));

        }


        /**
         * Callback for collumn 'id'
         *
         * @param array $item
         *
         * @return string
         */

        function column_id($item)
        {

            return $item['id'];
        }


        /**
         * Callback for collumn 'poll_title'
         *
         * @param array $item
         *
         * @return string
         */

        function column_name($item)
        {
            return '<a href="' . admin_url('admin.php?page=cbxwpsimpleaccountingvc&view=view&id=' . $item['id']) . '">' . $item['name'] . '</a>' . ' (<a target="_blank" href="' . admin_url('admin.php?page=cbxwpsimpleaccountingvc&view=addedit&id=' . $item['id']) . '">' . esc_html__('Edit', 'cbxwpsimpleaccountingvc') . '</a>)';
        }


        function column_type($item)
        {
            return CBXWPSimpleaccountingVCHelper::vcType($item['type']);
        }

        function column_user_id($item)
        {
            if (isset($item['user_id']) && intval($item['user_id']) > 0) {
                return '<a href= ' . get_edit_user_link($item['user_id']) . ' target = "_blank">' . $item['user_id'] . ' - ' . stripslashes(get_user_by('id', $item['user_id'])->display_name) . '</a>';
            }
            return '';
        }

        function column_created_date($item)
        {
            return date('Y-m-d', strtotime($item['created_date']));
        }

        function column_contact($item)
        {
            $contactinfo = maybe_unserialize($item['contactinfo_office']);


            $contactinfo_phone = maybe_unserialize($contactinfo['phone']);

            $output = '';

            $phones = array();
            foreach ($contactinfo_phone as $counter => $value) {
                $phones[] = $contactinfo_phone[$counter]['phoneval'] . '(' . CBXWPSimpleaccountingVCHelper::getCommunicationWayName($contactinfo_phone[$counter]['phonetype']) . ')';
            }

            $output .= '<p>' . $contactinfo['name'];
            $output .= ($contactinfo['designation'] != '') ? (' (' . $contactinfo['designation'] . ')') : '';
            $output .= '</p>';
            if(isset($contactinfo['email']) && $contactinfo['email'] != ''){
	            $output .= '<p>' . $contactinfo['email'].'</p>';
			}

            $output .= (sizeof($phones) > 0) ? ('<p>' . implode(', ', $phones) . '</p>') : '';

            return $output;
        }

        function column_address($item)
        {
	        $address = maybe_unserialize($item['address']);

            $line1 = isset($address['line1']) ? esc_html($address['line1']) : '';
            $line2 = isset($address['line2']) ? esc_html($address['line2']) : '';

	        $city    = isset( $address['city'] ) ? sanitize_text_field( $address['city'] ) : '';
	        $state   = isset( $address['state'] ) ? sanitize_text_field( $address['state'] ) : '';
	        $postal  = isset( $address['postal'] ) ? sanitize_text_field( $address['postal'] ) : '';
	        $country = isset( $address['country'] ) ? sanitize_text_field( $address['country'] ) : '';

	        $all_country = CBXWPSimpleaccountingVCHelper::getAllCountries();
	        if(isset($all_country[$country])) {
	        	$country = $all_country[$country];
	        }
	        else $country = ''; //none

            $output = ($line1 != '') ? ('<p>' . $line1 . '</p>') : '';
            $output .= ($line2 != '') ? ('<p>' . $line2 . '</p>') : '';

            $address_extra = array();
            if($city != '') $address_extra[] = $city;
            if($state != '') $address_extra[] = $state;
            if($postal != '') $address_extra[] = $postal;
            if($country != '') $address_extra[] = $country;
            if(sizeof($address_extra) > 0){
            	$output .= '<p>'.implode(',', $address_extra).'</p>';
			}


            return $output;
        }


        /** ************************************************************************
         * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
         * is given special treatment when columns are processed. It ALWAYS needs to
         * have it's own method.
         *
         * @see WP_List_Table::::single_row_columns()
         *
         * @param array $item A singular item (one full row's worth of data)
         *
         * @return string Text to be placed inside the column <td> (movie title only)
         **************************************************************************/
        function column_cb($item)
        {
            return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /*$1%s*/
                $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
                /*$2%s*/
                $item['id']                //The value of the checkbox should be the record's id
            );
        }

        /** ************************************************************************
         * Recommended. This method is called when the parent class can't find a method
         * specifically build for a given column. Generally, it's recommended to include
         * one method for each column you want to render, keeping your package class
         * neat and organized. For example, if the class needs to process a column
         * named 'title', it would first see if a method named $this->column_title()
         * exists - if it does, that method will be used. If it doesn't, this one will
         * be used. Generally, you should try to use custom column methods as much as
         * possible.
         *
         * Since we have defined a column_title() method later on, this method doesn't
         * need to concern itself with any column with a name of 'title'. Instead, it
         * needs to handle everything else.
         *
         * For more detailed insight into how columns are handled, take a look at
         * WP_List_Table::single_row_columns()
         *
         * @param array $item        A singular item (one full row's worth of data)
         * @param array $column_name The name/slug of the column to be processed
         *
         * @return string Text or HTML to be placed inside the column <td>
         **************************************************************************/
        function column_default($item, $column_name)
        {

            switch ($column_name) {
                case 'id':
                    return $item[$column_name];
                case 'type':
                    return $item[$column_name];
                case 'name':
                    return $item[$column_name];
                case 'address':
                    return $item[$column_name];
                case 'contact':
                    return $item[$column_name];
                case 'user_id':
                    return $item[$column_name];

                default:
                    return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }

        /**
         * Add extra markup in the toolbars before or after the list
         *
         * @param string $which , helps you decide if you add the markup after (bottom) or before (top) the list
         */
        function extra_tablenav($which)
        {
            if ($which == "top") {
	             if ( defined('CBXPHPSPREADSHEET_PLUGIN_NAME') && file_exists( CBXPHPSPREADSHEET_ROOT_PATH . 'lib/vendor/autoload.php' ) ) {
		             ?>
                     <div class="clear"></div>
                     <p style="margin-top: 20px;"><select name="format" class="cbxformatstatement">
                             <option id="formatXLS" value="xls"><?php esc_html_e( 'XLS', 'cbxwpsimpleaccountingvc' ); ?></option>
							 <option id="formatXLSX" value="xlsx"><?php esc_html_e( 'XLSX', 'cbxwpsimpleaccountingvc' ); ?></option>
							 <option id="formatODS" value="ods"><?php esc_html_e( 'ODS', 'cbxwpsimpleaccountingvc' ); ?></option>
						 </select>
                         <input type="submit" name="cbxwpsimpleaccountingvc_export" id="csvExport" class="button"
                                value="<?php esc_html_e( 'Export', 'cbxwpsimpleaccountingvc' ); ?>"/>

                     </p>
		             <?php
	             }
            }
        }



        function get_columns()
        {
            $columns = array(
                'cb'      => '<input type="checkbox" />', //Render a checkbox instead of text
                'name'    => esc_html__('Name', 'cbxwpsimpleaccountingvc'),
                'type'    => esc_html__('Type', 'cbxwpsimpleaccountingvc'),
                'address' => esc_html__('Address', 'cbxwpsimpleaccountingvc'),
                'contact' => esc_html__('Contact Information', 'cbxwpsimpleaccountingvc'),
                'user_id' => esc_html__('User ID', 'cbxwpsimpleaccountingvc'),
                'id'      => esc_html__('ID', 'cbxwpsimpleaccountingvc')
            );

            return $columns;
        }


        function get_sortable_columns()
        {
            $sortable_columns = array(
                'id'      => array('id', false),
                'type'    => array('type', false),     //true means it's already sorted
                'name'    => array('name', false),     //true means it's already sorted
                'user_id' => array('user_id', false),     //true means it's already sorted
            );

            return $sortable_columns;
        }


        function get_bulk_actions(){
            $bulk_actions = apply_filters('cbxwpsimpleaccountingvc_bulk_action', array(
                'delete' => esc_html__('Delete', 'cbxwpsimpleaccountingvc')
            ));

            return $bulk_actions;
        }


        function process_bulk_action()
        {
        	// security check!
	        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

		        $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
		        $action = 'bulk-' . $this->_args['plural'];

		        if ( ! wp_verify_nonce( $nonce, $action ) ){
			        wp_die( 'Nope! Security check failed!' );
		        }
	        }


            $current_action = $this->current_action();

            //Detect when a bulk action is being triggered...
            if ('delete' === $current_action) {

                if (!empty($_REQUEST['cbxaccvc'])) {
                    global $wpdb;

                    $results = $_REQUEST['cbxaccvc'];
	                //now delete the user log
	                $vc_table = $wpdb->prefix . "cbaccounting_vc_manager";

	                $delete_response = array();

                    foreach ($results as $id) {
                        $id = (int)$id;

	                    $isEmpty = CBXWPSimpleaccountingVCHelper::isVCEmpty($id);
	                    if($isEmpty == false) {
		                    $delete_response[] = array(
			                    'error' => 0,
			                    'msg'   => sprintf(__('Vendor/Client id: %d  can not be deleted as there are log entry associated this.', 'cbxwpsimpleaccountingvc'), $id)
		                    );

		                    continue;
	                    }

	                    do_action('cbxwpsimpleaccounting_vc_delete_before', $id);
                        //now delete
                        $sql = $wpdb->prepare("DELETE FROM $vc_table WHERE id=%d", $id);
                        $delete_status = $wpdb->query($sql);
                        if($delete_status !== false){
	                        do_action('cbxwpsimpleaccounting_vc_delete_after', $id);
						}
						else{
							//post process hook on delete failure
							do_action('cbxwpsimpleaccounting_vc_delete_failed', $id);
						}
                    }

	                $_SESSION['cbxwpsimpleaccounting_vcs_bulkdelete'] = $delete_response;
                }
            }
            return;

        }


        function prepare_items()
        {
	        $this->process_bulk_action();


            $user = get_current_user_id();
            $screen = get_current_screen();



            $current_page = $this->get_pagenum();

            $option_name = $screen->get_option('per_page', 'option'); //the core class name is WP_Screen


            $per_page = intval(get_user_meta($user, $option_name, true));


            if ($per_page == 0) {
                $per_page = intval($screen->get_option('per_page', 'default'));
            }



            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();



            $this->_column_headers = array($columns, $hidden, $sortable);


            $order = (isset($_REQUEST['order']) && $_REQUEST['order'] != '') ? $_REQUEST['order'] : 'desc';
            $orderby = (isset($_REQUEST['orderby']) && $_REQUEST['orderby'] != '') ? $_REQUEST['orderby'] : 'id';

            $search = (isset($_REQUEST['s']) && $_REQUEST['s'] != '') ? sanitize_text_field($_REQUEST['s']) : '';


            $data = CBXWpsimpleaccountingHelper::getVCData($search, $orderby, $order, $per_page, $current_page);

            $total_items = intval($this->getDataCount($search, $orderby, $order));


            $this->items = $data;



            $this->set_pagination_args(array(
                                           'total_items' => $total_items,                  //WE have to calculate the total number of items
                                           'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
                                           'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
                                       ));
        }

        /**
         * Get total data count
         *
         * @param int $perpage
         * @param int $page
         *
         * @return array|null|object
         */
        function getDataCount($search = '', $orderby = 'id', $order = 'desc') {

            global $wpdb;

            $vc_table = $wpdb->prefix . "cbaccounting_vc_manager";

            $sql_select = "SELECT COUNT(*) FROM $vc_table";

            $where_sql = '';


            if ($search != '') {
                if ($where_sql != '') $where_sql .= ' AND ';
                $where_sql .= $wpdb->prepare(" type LIKE '%%%s%%' OR name LIKE '%%%s%%' OR description LIKE '%%%s%%' OR organization LIKE '%%%s%%' ", $search, $search, $search, $search);
            }

            if ($where_sql == '') {
                $where_sql = '1';
            }


            $sortingOrder = " ORDER BY $orderby $order ";


            $count = $wpdb->get_var("$sql_select  WHERE  $where_sql $sortingOrder");

            return $count;
        }//end method getDataCount

	    /**
	     * Pagination
	     *
	     * @param string $which
	     */
	    protected function pagination( $which ) {

		    if ( empty( $this->_pagination_args ) ) {
			    return;
		    }

		    $total_items     = $this->_pagination_args['total_items'];
		    $total_pages     = $this->_pagination_args['total_pages'];
		    $infinite_scroll = false;
		    if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			    $infinite_scroll = $this->_pagination_args['infinite_scroll'];
		    }

		    if ( 'top' === $which && $total_pages > 1 ) {
			    $this->screen->render_screen_reader_content( 'heading_pagination' );
		    }

		    $output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		    $current              = $this->get_pagenum();
		    $removable_query_args = wp_removable_query_args();

		    $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

		    $current_url = remove_query_arg( $removable_query_args, $current_url );

		    $page_links = array();

		    $total_pages_before = '<span class="paging-input">';
		    $total_pages_after  = '</span></span>';

		    $disable_first = $disable_last = $disable_prev = $disable_next = false;

		    if ( $current == 1 ) {
			    $disable_first = true;
			    $disable_prev  = true;
		    }
		    if ( $current == 2 ) {
			    $disable_first = true;
		    }
		    if ( $current == $total_pages ) {
			    $disable_last = true;
			    $disable_next = true;
		    }
		    if ( $current == $total_pages - 1 ) {
			    $disable_last = true;
		    }

		    $pagination_params = array();

		    $search = isset( $_REQUEST['s'] ) ? esc_attr( wp_unslash( $_REQUEST['s'] ) ) : '';


		    if ( $search != '' ) {
			    $pagination_params['s'] = $search;
		    }




		    $pagination_params = apply_filters( 'cbxwpbookmark_pagination_log_params', $pagination_params );

		    if ( $disable_first ) {
			    $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
		    } else {
			    $page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				    esc_url( remove_query_arg( 'paged', $current_url ) ),
				    __( 'First page' ),
				    '&laquo;'
			    );
		    }

		    if ( $disable_prev ) {
			    $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
		    } else {
			    $pagination_params['paged'] = max( 1, $current - 1 );

			    $page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				    esc_url( add_query_arg( $pagination_params, $current_url ) ),
				    __( 'Previous page' ),
				    '&lsaquo;'
			    );
		    }

		    if ( 'bottom' === $which ) {
			    $html_current_page  = $current;
			    $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
		    } else {
			    $html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
				    '<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
				    $current,
				    strlen( $total_pages )
			    );
		    }
		    $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		    $page_links[]     = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

		    if ( $disable_next ) {
			    $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
		    } else {
			    $pagination_params['paged'] = min( $total_pages, $current + 1 );

			    $page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				    esc_url( add_query_arg( $pagination_params, $current_url ) ),
				    __( 'Next page' ),
				    '&rsaquo;'
			    );
		    }

		    if ( $disable_last ) {
			    $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
		    } else {
			    $pagination_params['paged'] = $total_pages;

			    $page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				    esc_url( add_query_arg( $pagination_params, $current_url ) ),
				    __( 'Last page' ),
				    '&raquo;'
			    );
		    }

		    $pagination_links_class = 'pagination-links';
		    if ( ! empty( $infinite_scroll ) ) {
			    $pagination_links_class = ' hide-if-js';
		    }
		    $output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		    if ( $total_pages ) {
			    $page_class = $total_pages < 2 ? ' one-page' : '';
		    } else {
			    //$page_class = ' no-pages';
			    $page_class = ' ';
		    }
		    $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		    echo $this->_pagination;
	    }//end

    }//end class CBXWPSACC_VC_Listing

    class WP_List_TableCBXACCSVC extends WP_List_Table
    {

	}//end class WP_List_TableCBXACCSVC