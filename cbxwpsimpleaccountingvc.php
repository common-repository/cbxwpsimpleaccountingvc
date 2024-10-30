<?php
	/**
	 * @link              https://codeboxr.com
	 * @since             1.0.0
	 * @package           cbxwpsimpleaccountingvc
	 *
	 * @wordpress-plugin
	 * Plugin Name:       CBX Accounting Vendors & Clients Addon
	 * Plugin URI:        https://codeboxr.com/product/cbx-accounting
	 * Description:       Vendors & Clients for CBX Accounting & Bookkeeping plugin
	 * Version:           1.1.0
	 * Author:            Codeboxr
	 * Author URI:        https://codeboxr.com
	 * License:           GPL-2.0+
	 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	 * Text Domain:       cbxwpsimpleaccountingvc
	 * Domain Path:       /languages
	 */
	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}


	defined( 'CBXWPSIMPLEACCOUNTINGVC_PLUGIN_NAME' ) or define( 'CBXWPSIMPLEACCOUNTINGVC_PLUGIN_NAME', 'cbxwpsimpleaccountingvc' );
	defined( 'CBXWPSIMPLEACCOUNTINGVC_PLUGIN_VERSION' ) or define( 'CBXWPSIMPLEACCOUNTINGVC_PLUGIN_VERSION', '1.1.0' );
	defined( 'CBXWPSIMPLEACCOUNTINGVC_BASE_NAME' ) or define( 'CBXWPSIMPLEACCOUNTINGVC_BASE_NAME', plugin_basename( __FILE__ ) );
	defined( 'CBXWPSIMPLEACCOUNTINGVC_ROOT_PATH' ) or define( 'CBXWPSIMPLEACCOUNTINGVC_ROOT_PATH', plugin_dir_path( __FILE__ ) );
	defined( 'CBXWPSIMPLEACCOUNTINGVC_ROOT_URL' ) or define( 'CBXWPSIMPLEACCOUNTINGVC_ROOT_URL', plugin_dir_url( __FILE__ ) );


	register_activation_hook( __FILE__, array( 'CBXWpsimpleaccountingVC', 'activation' ) );

	/**
	 * CBX Accounting Vendors & Clients.
	 *
	 * Defines the Functionality of the listed data.
	 *
	 * @package    Cbxwpsimpleaccountingvc
	 * @subpackage Cbxwpsimpleaccountingvc/admin
	 * @author     Codeboxr <info@codeboxr.com>
	 */
	class CBXWpsimpleaccountingVC {

		/**
		 * Initialize the plugin
		 */
		private $version;

		/**
		 * Initialize the class and set its properties.
		 *
		 */
		public function __construct() {
			$this->version = CBXWPSIMPLEACCOUNTINGVC_PLUGIN_VERSION;


			//load text domain
			load_plugin_textdomain( 'cbxwpsimpleaccountingvc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			add_action( 'upgrader_process_complete', array($this, 'plugin_upgrader_process_complete'), 10, 2 );
			add_action( 'admin_notices', array($this, 'plugin_activate_upgrade_notices') );

			if(version_compare(CBXWPSIMPLEACCOUNTING_PLUGIN_VERSION,'1.3.13', '<') ){
				set_transient( 'cbxwpsimpleaccountingvc_upgraded_notice', 1 );

				return;
			}

			//include helper file
			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-vc-helper.php' );

			//let's assing admin roles new permission on activate
			$role = get_role( 'administrator' );
			if ( ! $role->has_cap( 'vc_cbxaccounting' ) ) {
				$role->add_cap( 'vc_cbxaccounting' );
			}



			add_filter( 'cbxwpsimpleaccounting_table_list', array(
				$this,
				'add_vc_table_cbxwpsimpleaccounting_table_list'
			) );
			//reset vc data from global config
			add_action( 'cbxwpsimpleaccounting_plugin_reset', array(
				$this,
				'cbxwpsimpleaccountingvc_plugin_reset'
			), 10, 2 );


			//add submenu to left menu
			add_action( 'admin_menu', array( $this, 'cbxwpsimpleaccountingvc_menu' ) );

			//before the header load to check any request to execute or not!
			if ( isset( $_REQUEST['cbxwpsimpleaccountingvc_export'] ) && isset( $_REQUEST['format'] ) ) {
				add_action( 'admin_init', array( $this, 'cbxwpsimpleaccounting_vc_export' ) );
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

			//set screen option for result listing page
			add_filter( 'set-screen-option', array( $this, 'cbxvc_set_option_listing' ), 10, 3 );

			//new account manager
			add_action( 'wp_ajax_add_new_vc_manager_acc', array( $this, 'add_new_vc_manager_acc' ) );

			//add vc feature in income/expense entry form
			add_action( 'cbxwpsimpleaccounting_form_end', array( $this, 'add_vc_incform' ) );
			add_action( 'cbxwpsimpleaccounting_view_form_end', array( $this, 'view_vc_incform' ) );

			//add vc_id to data array for loading new incexp
			add_filter( 'cbxwpsimpleaccounting_incexp_single_data', array(
				$this,
				'cbxwpsimpleaccounting_incexp_single_data'
			), 10, 3 );
			add_filter( 'cbxwpsimpleaccounting_incexp_post_process', array(
				$this,
				'cbxwpsimpleaccounting_incexp_post_process'
			) );

			add_filter( 'cbxwpsimpleaccounting_incexp_edit_data', array(
				$this,
				'cbxwpsimpleaccounting_incexp_edit_data'
			), 10, 2 );
			add_filter( 'cbxwpsimpleaccounting_incexp_post_data', array(
				$this,
				'cbxwpsimpleaccounting_incexp_post_data'
			), 10, 2 );

			add_filter( 'cbxwpsimpleaccounting_incexp_post_coldataformat', array(
				$this,
				'cbxwpsimpleaccounting_incexp_post_coldataformat'
			) );


			//filter for log manager plugin
			//filter and hooks used in log manager plugin
			add_action( 'cbxwpsimpleaccountinglog_extra_filters', array(
				$this,
				'cbxwpsimpleaccountinglog_extra_filters'
			) ); //add extra filter fields in log manager before the lost listing
			add_filter( 'cbxwpsimpleaccountinglog_listing_columns', array(
				$this,
				'cbxwpsimpleaccountinglog_listing_columns'
			), 10, 2 ); //adds extra vendor column in log manager listing tbale
			add_filter( 'cbxwpsimpleaccountinglog_listing_column_default', array(
				$this,
				'cbxwpsimpleaccountinglog_listing_column_default'
			), 10, 4 ); //returns output for vendor column in log manage rlisting table.
			add_filter( 'cbxwpsimpleaccountinglog_listing_sortable_columns', array(
				$this,
				'cbxwpsimpleaccountinglog_listing_sortable_columns'
			), 10, 2 ); //add vendor as sortable column

			//sql related for log manager (listing and export both)
			add_filter( 'cbxwpsimpleaccountinglog_listing_sql_where', array(
				$this,
				'cbxwpsimpleaccountinglog_listing_sql_where'
			) ); //log manager listing table sql where clause condition for vendor
			add_filter( 'cbxwpsimpleaccountinglog_listing_sql_join', array(
				$this,
				'cbxwpsimpleaccountinglog_listing_sql_join'
			) ); //log manager listing table sql join clause condition for vendor
			add_filter( 'cbxwpsimpleaccountinglog_listing_sql_select_extra', array(
				$this,
				'cbxwpsimpleaccountinglog_listing_sql_select_extra'
			) ); //log manager listing table sql select extra clause condition for vendor



			add_filter( 'cbxwpsimpleaccountinglog_export_pdf_table_summary', array($this, 'log_export_pdf_table_summary') ); //adding vendor and client info before table in pdf export
            add_filter('cbxwpsimpleaccountinglog_log_export_cols', array($this, 'log_export_cols_vc'));
			add_filter('cbxwpsimpleaccountinglog_export_pdf_col_data', array($this, 'export_other_col_data_vc'), 10, 2);
			add_filter('cbxwpsimpleaccountinglog_export_other_col_data', array($this, 'export_other_col_data_vc'), 10, 2);

			//end filter for log manager plugin

			//filter for statement plugin
			add_action( 'cbxwpsimpleaccountingstatement_extra_filters', array(
				$this,
				'cbxwpsimpleaccountingstatement_extra_filters'
			) ); //add extra filter fields in statement before the lost listing
			add_action( 'cbxwpsimpleaccountingstatement_sql_where_income', array(
				$this,
				'cbxwpsimpleaccountingstatement_sql_where_income'
			) ); //extends where sql in statement plugin income
			add_action( 'cbxwpsimpleaccountingstatement_sql_where_expense', array(
				$this,
				'cbxwpsimpleaccountingstatement_sql_where_expense'
			) ); //extends where sql in statement plugin expense
			add_action( 'cbxwpsimpleaccountingstatement_sql_join_income', array(
				$this,
				'cbxwpsimpleaccountingstatement_sql_join_income'
			) ); //extends join sql in statement plugin income
			add_action( 'cbxwpsimpleaccountingstatement_sql_join_expense', array(
				$this,
				'cbxwpsimpleaccountingstatement_sql_join_expense'
			) ); //extends join sql in statement plugin expense
			//end filter for statement plugin

			//add vendor and clients dropdown in frontend addon statement filter
			add_action( 'cbxwpsimpleaccountingstatement_extra_filters_frontend', array(
				$this,
				'cbxwpsimpleaccountingstatement_extra_filters_frontend'
			) );


		}

		/**
		 * Reset plugin vendor and client(create table)
		 *
		 * @param $table_names
		 * @param $prefix
		 */
		public function cbxwpsimpleaccountingvc_plugin_reset( $table_names, $prefix ) {
			CBXWpsimpleaccountingVC::dbTableCreation();
		}


		/**
         * Add vendor and custom table names to table list
         *
		 * @param array $table_names
		 *
		 * @return array
		 */
		public function add_vc_table_cbxwpsimpleaccounting_table_list( $table_names = array() ) {
			global $wpdb;
			$table_names['Vendor and Client Manager Table'] = $wpdb->prefix . 'cbaccounting_vc_manager';;

			return $table_names;
		}//end method add_vc_table_cbxwpsimpleaccounting_table_list

		/**
		 * Statement plugin display income where sql extended for vc
		 *
		 * @param $where_icome
		 */
		public function cbxwpsimpleaccountingstatement_sql_where_income( $where_icome ) {
			global $wpdb;

			$cbxlogexpinc_vc_val = 0; //vendor and client

			if ( isset( $_POST['cbxaccountingstatement_verifier'] ) ) {
				$cbxlogexpinc_vc_val = isset( $_POST['cbxlogexpinc_vc'] ) ? absint( $_POST['cbxlogexpinc_vc'] ) : $cbxlogexpinc_vc_val;
			}

			if ( $cbxlogexpinc_vc_val > 0 ) {
				$where_icome .= $wpdb->prepare( " AND c.vc_id=%d", $cbxlogexpinc_vc_val );
			}

			return $where_icome;
		}//end method cbxwpsimpleaccountingstatement_sql_where_income

		/**
		 * Statement plugin display expense where sql extended for vc
		 *
		 * @param $where_expense
		 */
		public function cbxwpsimpleaccountingstatement_sql_where_expense( $where_expense ) {

			global $wpdb;


			$cbxlogexpinc_vc_val = 0; //vendor and client

			if ( isset( $_POST['cbxaccountingstatement_verifier'] ) ) {
				$cbxlogexpinc_vc_val = isset( $_POST['cbxlogexpinc_vc'] ) ? absint( $_POST['cbxlogexpinc_vc'] ) : $cbxlogexpinc_vc_val;
			}

			if ( $cbxlogexpinc_vc_val > 0 ) {
				$where_expense .= $wpdb->prepare( " AND c.vc_id=%d", $cbxlogexpinc_vc_val );
			}

			return $where_expense;
		}//end method cbxwpsimpleaccountingstatement_sql_where_expense


		/**
		 * Extended income sql join in statement plugin for vc
		 *
		 * @param $total_income_left_join
		 *
		 * @return mixed
		 */
		public function cbxwpsimpleaccountingstatement_sql_join_income( $total_income_left_join ) {
			global $wpdb;
			$cbaccounting_vc_table  = $wpdb->prefix . "cbaccounting_vc_manager";
			$total_income_left_join .= " LEFT JOIN $cbaccounting_vc_table vc ON vc.id = c.vc_id ";

			return $total_income_left_join;
		}//end method cbxwpsimpleaccountingstatement_sql_join_income

		/**
		 * Extended expense sql join in statement plugin for vc
		 *
		 * @param $total_expense_left_join
		 *
		 * @return mixed
		 */
		public function cbxwpsimpleaccountingstatement_sql_join_expense( $total_expense_left_join ) {
			global $wpdb;
			$cbaccounting_vc_table   = $wpdb->prefix . "cbaccounting_vc_manager";
			$total_expense_left_join .= " LEFT JOIN $cbaccounting_vc_table vc ON vc.id = c.vc_id ";

			return $total_expense_left_join;
		}//end method cbxwpsimpleaccountingstatement_sql_join_expense

		/**
		 * Adding vendor and client heading to excell, csv etc other type export
		 *
		 * @param        $objPHPExcel
		 * @param string $letter
		 *
		 * @return mixed
		 * @throws \PhpOffice\PhpSpreadsheet\Exception
		 */
		public function log_export_other_heading( $objPHPExcel, $letter = 'P' ) {
			$columnAlpha_index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letter);
			$columnAlpha = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnAlpha_index+1);


			$i = 1;
			$objPHPExcel->getActiveSheet()->setCellValue( $columnAlpha.$i, esc_html__( 'Vendor and Client', 'cbxwpsimpleaccountingvc' ) );

			return $objPHPExcel;
		}//end method log_export_other_heading

		/**
		 * Adding vendor and client col to excell, csv etc other type export
		 *
		 * @param        $objPHPExcel
		 * @param        $i
		 * @param        $data
		 * @param string $letter
		 *
		 * @return mixed
		 * @throws \PhpOffice\PhpSpreadsheet\Exception
		 */
		public function log_export_other_col( $objPHPExcel, $i, $data, $letter = 'P' ) {
			$columnAlpha_index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letter);
			$columnAlpha = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnAlpha_index+1);
			$objPHPExcel->getActiveSheet()->setCellValue( $columnAlpha . ( $i + 2 ), $data->vc_name );

			return $objPHPExcel;
		}//end method log_export_other_col

		/**
         * Adding vendor and client col to data for pdf, excell, csv etc other type export
         *
		 * @param object $data
		 *
		 * @return string
		 */
        public function export_other_col_data_vc($arr = array(), $data = null){
            if($data !== null){
	            $arr['v_c'] = wp_unslash($data->vc_name);
            }

		    return $arr;
        }//end export_other_col_data_vc



		/**
         * Add vc column to export col list
         *
		 * @param $table_headings
		 *
		 * @return mixed
		 */
		public function log_export_cols_vc( $table_headings ) {
			$table_headings['v_c'] = esc_html__( "V/C", 'cbxwpsimpleaccountingvc' );

			return $table_headings;
		}//end method log_export_cols_vc


		/**
         * PDF Export table summary -vc addition
         *
		 * @param $pdf
		 */
		public function log_export_pdf_table_summary( $pdf ) {
			global $wpdb;
			$cbaccounting_vc_table = $wpdb->prefix . "cbaccounting_vc_manager";

			$vc_id = ( isset( $_REQUEST['cbxlogexpinc_vc'] ) && intval( $_REQUEST['cbxlogexpinc_vc'] ) != 0 ) ? intval( $_REQUEST['cbxlogexpinc_vc'] ) : 0;

			if ( $vc_id > 0 ) {
				$vc_info  = $wpdb->get_row( $wpdb->prepare( 'Select name from ' . $cbaccounting_vc_table . ' where id = %d', $vc_id ), OBJECT );
				$vc_title = $vc_info->name;
			} else {
				$vc_title = esc_html__( 'All', 'cbxwpsimpleaccountingvc' );
			}

			$pdf->SetTextColor( 150, 150, 150 );
			$pdf->Cell( 0, 6, utf8_decode( esc_html__( 'Vendor/Client Name: ', 'cbxwpsimpleaccountingvc' ) . $vc_title ), 0, 2, 'L', false );

			//$cbx_export_html .= '<p>' . esc_html__( 'Vendor & Client Name: ' . $vc_title, 'cbxwpsimpleaccountingvc' ) . '</p>';

			//return $cbx_export_html;
			return $pdf;
		}//end method log_export_pdf_table_summary

		/**
		 * Extra sql select for log manager list table for vendor and client
		 *
		 * @param $select_extra
		 *
		 * @return string
		 */
		public function cbxwpsimpleaccountinglog_listing_sql_select_extra( $select_extra ) {
			$select_extra .= ', VC.name as vc_name, VC.user_id as vc_userid ';

			return $select_extra;
		}//end method cbxwpsimpleaccountinglog_listing_sql_select_extra

		/**
		 * Vendor table join for log table listing query
		 *
		 * @param $join
		 *
		 * @return string
		 */
		public function cbxwpsimpleaccountinglog_listing_sql_join( $join ) {
			global $wpdb;
			$cbaccounting_vc_table = $wpdb->prefix . "cbaccounting_vc_manager";
			$join                  .= " LEFT JOIN $cbaccounting_vc_table VC ON VC.id = c.vc_id ";

			return $join;
		}//end method cbxwpsimpleaccountinglog_listing_sql_join

		/**
		 * Condition on where for vendor on log export
		 *
		 * @param $where_sql
		 *
		 * @return mixed
		 */
		public function cbxwpsimpleaccountinglog_listing_sql_where( $where_sql ) {

			global $wpdb;
			$cbaccounting_vc_table = $wpdb->prefix . "cbaccounting_vc_manager";

			$vc_id = isset( $_REQUEST['cbxlogexpinc_vc'] ) ? absint( $_REQUEST['cbxlogexpinc_vc'] ) : 0; //account
			//filter by account no, where sql
			if ( intval( $vc_id ) > 0 ) {

				if ( $where_sql != '' ) {
					$where_sql .= ' AND ';
				}
				$where_sql .= $wpdb->prepare( "c.vc_id=%d ", $vc_id );
			}

			return $where_sql;
		}//end method cbxwpsimpleaccountinglog_listing_sql_where

		/**
		 * Log manager list table sortable for vendor
		 *
		 * @param $sortable_columns
		 * @param $list_table
		 *
		 * @return mixed
		 */
		public function cbxwpsimpleaccountinglog_listing_sortable_columns( $sortable_columns, $list_table ) {
			$sortable_columns['cbxexpincvendor'] = array( 'c.vc_id', false );

			return $sortable_columns;
		}//end method cbxwpsimpleaccountinglog_listing_sortable_columns

		/**
		 * Column value function hook for vendor in log manager list table
		 *
		 * @param $item
		 * @param $column_name
		 * @param $list_table
		 *
		 * @return string
		 */
		public function cbxwpsimpleaccountinglog_listing_column_default( $hooks_column, $item, $column_name, $list_table ) {

			if ( $column_name == 'cbxexpincvendor' ) {

				return $this->column_cbxexpincvendor( $hooks_column, $item, $list_table );
			}
		}//end method cbxwpsimpleaccountinglog_listing_column_default


		/**
		 * Callback for collumn cbxexpincvendor
		 *
		 * @param $hooks_column
		 * @param $item
		 * @param $list_table
		 */
		public function column_cbxexpincvendor( $hooks_column, $item, $list_table ) {

			$filterby_account_url = add_query_arg(
				array(
					'cbxlogexpinc_vc' => intval( $item['vc_id'] ),
				)
			);

			return ( intval( $item['vc_id'] ) == 0 ) ? esc_html__( 'N/A', 'cbxwpsimpleaccountingvc' ) : '<a href="' . $filterby_account_url . '">' . esc_html( $item['vc_name'] ) . '</a>';
		}//end method column_cbxexpincvendor

		/**
		 * Add Vendor column title to list table
		 *
		 * @param $columns
		 * @param $list_table
		 *
		 * @return array|bool
		 */
		public function cbxwpsimpleaccountinglog_listing_columns( $columns, $list_table ) {

			$columns = $this->array_insert_after( 'cbxexpincaccount', $columns, 'cbxexpincvendor', esc_html__( 'V&C', 'cbxwpsimpleaccountingvc' ) );

			return $columns;
		}//end method cbxwpsimpleaccountinglog_listing_columns


		/**
		 * Adds Vendor and clients Dropdown filters in  statement plugin filter area
		 */
		public function cbxwpsimpleaccountingstatement_extra_filters() {
			global $wpdb;

			$cbxstatementshowvc_val = 1;
			$cbxlogexpinc_vc_val    = 0;

			$cbaccounting_vc_table = $wpdb->prefix . "cbaccounting_vc_manager";
			$cbxlog_vc_list        = $wpdb->get_results( 'SELECT `id`, `name`, `type` FROM ' . $cbaccounting_vc_table . ' order by type, name asc', ARRAY_A ); //Selecting all vc

			if ( isset( $_POST['cbxwpsimpleaccountingstatement_filter'] ) ) {
				$cbxstatementshowvc_val = isset( $_REQUEST['cbxstatementshowvc'] ) ? absint( $_REQUEST['cbxstatementshowvc'] ) : 0; //break per vendors/clients
				$cbxlogexpinc_vc_val    = isset( $_POST['cbxlogexpinc_vc'] ) ? absint( $_POST['cbxlogexpinc_vc'] ) : 0;
			}

			?>
			<p>
				<!--<label for="cbxstatementshowvc">
					<input type="checkbox" <?php /*echo ( $cbxstatementshowvc_val == 1 ) ? 'checked' : ''; */ ?>
						   id="cbxstatementshowvc" name="cbxstatementshowvc" value="1" />
					<span><?php /*esc_html_e( 'Break Per Vendors/Clients: ', 'cbxwpsimpleaccountingvc' ); */ ?></span>
				</label>-->
				<select name="cbxlogexpinc_vc" id="cbxlogexpinc_vc">
					<optgroup label="<?php esc_html_e( 'Vendor and Clients', 'cbxwpsimpleaccountingvc' ); ?>">
						<option <?php echo ( $cbxlogexpinc_vc_val == 0 ) ? ' selected="selected" ' : ''; ?>
							value="0"><?php esc_html_e( 'Select Vendor/Client', 'cbxwpsimpleaccountingvc' ); ?></option>
					</optgroup>

					<optgroup label="<?php esc_html_e( 'Clients', 'cbxwpsimpleaccountingvc' ); ?>">
						<?php
							foreach ( $cbxlog_vc_list as $list ):

								if ( $list['type'] != 'client' ) {
									continue;
								}
								$selected = ( ( $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val == $list['id'] ) ? ' selected="selected" ' : '' );

								echo '<option   ' . $selected . ' value="' . $list['id'] . '">' . stripslashes( esc_attr( $list['name'] ) ) . '</option>';
								?>
							<?php endforeach; ?>
					</optgroup>
					<optgroup label="<?php esc_html_e( 'Vendors', 'cbxwpsimpleaccountingvc' ); ?>">
						<?php
							foreach ( $cbxlog_vc_list as $list ):

								if ( $list['type'] != 'vendor' ) {
									continue;
								}
								$selected = ( ( $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val == $list['id'] ) ? ' selected="selected" ' : '' );

								echo '<option   ' . $selected . ' value="' . $list['id'] . '">' . stripslashes( esc_attr( $list['name'] ) ) . '</option>';
								?>
							<?php endforeach; ?>
					</optgroup>
				</select>
			</p>
			<div class="clear"></div>
			<?php
		}

		/**
		 * Adds Vendor and clients Dropdown filters in  log manager plugin filter area
		 */
		public function cbxwpsimpleaccountinglog_extra_filters() {
			global $wpdb;
			$cbaccounting_vc_table = $wpdb->prefix . "cbaccounting_vc_manager";
			$cbxlog_vc_list        = $wpdb->get_results( 'SELECT `id`, `name`, `type` FROM ' . $cbaccounting_vc_table . ' order by type, name asc', ARRAY_A ); //Selecting all vc
			$cbxlogexpinc_vc_val   = isset( $_REQUEST['cbxlogexpinc_vc'] ) ? absint( $_REQUEST['cbxlogexpinc_vc'] ) : 0;
			?>

			<select name="cbxlogexpinc_vc" id="cbxlogexpinc_vc" class="form-control" style="width: auto; display:inline-block;">
				<optgroup label="<?php esc_html_e( 'Vendor and Clients', 'cbxwpsimpleaccountingvc' ); ?>">
					<option <?php echo ( $cbxlogexpinc_vc_val == 0 ) ? ' selected="selected" ' : ''; ?>
						value="0"><?php esc_html_e( 'Select Vendor/Client', 'cbxwpsimpleaccountingvc' ); ?></option>
				</optgroup>

				<optgroup label="<?php esc_html_e( 'Clients', 'cbxwpsimpleaccountingvc' ); ?>">
					<?php
						foreach ( $cbxlog_vc_list as $list ):

							if ( $list['type'] != 'client' ) {
								continue;
							}
							$selected = ( ( $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val == $list['id'] ) ? ' selected="selected" ' : '' );

							echo '<option   ' . $selected . ' value="' . $list['id'] . '">' . stripslashes( esc_attr( $list['name'] ) ) . '</option>';
							?>
						<?php endforeach; ?>
				</optgroup>
				<optgroup label="<?php esc_html_e( 'Vendors', 'cbxwpsimpleaccountingvc' ); ?>">
					<?php
						foreach ( $cbxlog_vc_list as $list ):

							if ( $list['type'] != 'vendor' ) {
								continue;
							}
							$selected = ( ( $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val == $list['id'] ) ? ' selected="selected" ' : '' );

							echo '<option   ' . $selected . ' value="' . $list['id'] . '">' . stripslashes( esc_attr( $list['name'] ) ) . '</option>';
							?>
						<?php endforeach; ?>
				</optgroup>
			</select>
			<?php
		}//end method cbxwpsimpleaccountinglog_extra_filters

		/**
		 * Filter col form and add new for vc_id
		 *
		 * @param $col_data_format
		 */
		public function cbxwpsimpleaccounting_incexp_post_coldataformat( $col_data_format ) {
			$col_data_format[] = '%d';
		}//end method cbxwpsimpleaccounting_incexp_post_coldataformat

		/**
		 * post data array for add/ edit incexp (post process col values before insert data)
		 *
		 * @param $single_incomeexpense
		 * @param $incexp
		 *
		 */
		public function cbxwpsimpleaccounting_incexp_post_process( $col_data ) {
			$col_data['vc_id'] = absint( $_POST['cbacnt-exinc-vcid'] );

			return $col_data;
		}//end method cbxwpsimpleaccounting_incexp_post_process


		/**
		 * render data array for add/edit incexp submission ajax
		 *
		 * @param $single_incomeexpense
		 * @param $incexp
		 *
		 */
		public function cbxwpsimpleaccounting_incexp_post_data( $cbacnt_validation, $col_data ) {
			$cbacnt_validation['form_value']['vc_id'] = $col_data['vc_id'];

			return $cbacnt_validation;
		}//end method cbxwpsimpleaccounting_incexp_post_data

		/**
		 * render data array for edit incexp
		 *
		 * @param $single_incomeexpense
		 * @param $incexp
		 *
		 */
		public function cbxwpsimpleaccounting_incexp_edit_data( $cbacnt_validation, $incexp ) {
			$cbacnt_validation['form_value']['vc_id'] = absint( $incexp['vc_id'] );

			return $cbacnt_validation;
		}//end method cbxwpsimpleaccounting_incexp_edit_data

		/**
		 * add vc_id to data array for loading new incexp
		 *
		 * @param $single_incomeexpense
		 * @param $incexp
		 * @param $id
		 */
		public function cbxwpsimpleaccounting_incexp_single_data( $single_incomeexpense, $incexp, $id ) {
			$single_incomeexpense['vc_id'] = absint( $incexp['vc_id'] );

			return $single_incomeexpense;
		}//end method cbxwpsimpleaccounting_incexp_single_data

		/**
		 * View vc list form field in income/expense form
		 *
		 * @param $data
		 */
		public function view_vc_incform( $single_incomeexpense ) {
			global $wpdb;


			$orderby    = 'id';
			$order      = 'desc';
			$vc_table   = $wpdb->prefix . "cbaccounting_vc_manager";
			$sql_select = "SELECT id, type, name FROM $vc_table";
			$where_sql  = '';

			if ( $where_sql == '' ) {
				$where_sql = '1';
			}

			$sortingOrder = " ORDER BY $orderby $order ";
			$all_vc       = $wpdb->get_results( "$sql_select  WHERE  $where_sql $sortingOrder  ", 'OBJECT' );


			$cbxlogexpinc_vc_val = isset( $single_incomeexpense['vc_id'] ) ? intval( $single_incomeexpense['vc_id'] ) : 0;


			echo '<tr valign="top">
					<th class="row-title" scope="row">
			            <label for="cbacnt-exinc-vcid">' . esc_html__( 'Vendor/Client', 'cbxwpsimpleaccountingvc' ) . '</label>
					</th>
					<td>';


			foreach ( $all_vc as $list ):
				$selected = ( ( $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val == $list->id ) ? true : false );
				if ( $selected ) {

					echo stripslashes( esc_attr( $list->name ) );
					break;
				}


			endforeach;


			echo '</td>
			</tr>';
		}

		/**
		 * Add vc list form field in income/expense form
		 *
		 * @param $data
		 */
		public function add_vc_incform( $single_incomeexpense ) {

			global $wpdb;


			$orderby    = 'id';
			$order      = 'desc';
			$vc_table   = $wpdb->prefix . "cbaccounting_vc_manager";
			$sql_select = "SELECT id, type, name FROM $vc_table";
			$where_sql  = '';

			if ( $where_sql == '' ) {
				$where_sql = '1';
			}

			$sortingOrder = " ORDER BY $orderby $order ";
			$all_vc       = $wpdb->get_results( "$sql_select  WHERE  $where_sql $sortingOrder  ", 'OBJECT' );


			$cbxlogexpinc_vc_val = isset( $single_incomeexpense['vc_id'] ) ? intval( $single_incomeexpense['vc_id'] ) : 0;


			echo '<div class="form-group">
                <label for="cbacnt-exinc-vcid" class="col-sm-2 control-label">' . esc_html__( 'Vendor/Client', 'cbxwpsimpleaccountingvc' ) . '</label>
             
                <div class="col-sm-10 error_container">';
			echo '<select name="cbacnt-exinc-vcid" id="cbacnt-exinc-vcid" class="selecttwo-select form-control">
                                        <option value="" >' . esc_html__( 'Select Vendor/Client', 'cbxwpsimpleaccountingvc' ) . '</option>';


			echo '<optgroup label="' . esc_html__( 'Clients', 'cbxwpsimpleaccountingvc' ) . '">';

			foreach ( $all_vc as $list ):

				if ( $list->type != 'client' ) {
					continue;
				}
				$selected = ( ( $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val == $list->id ) ? ' selected="selected" ' : '' );

				echo '<option   ' . $selected . ' value="' . $list->id . '">' . stripslashes( esc_attr( $list->name ) ) . '</option>';

			endforeach;
			echo '</optgroup>';

			echo '<optgroup label="' . esc_html__( 'Vendors', 'cbxwpsimpleaccountingvc' ) . '">';

			foreach ( $all_vc as $list ):

				if ( $list->type != 'vendor' ) {
					continue;
				}
				$selected = ( ( $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val == $list->id ) ? ' selected="selected" ' : '' );

				echo '<option   ' . $selected . ' value="' . $list->id . '">' . stripslashes( esc_attr( $list->name ) ) . '</option>';

			endforeach;
			echo '</optgroup>';


			echo '</select>';


			echo '</div>
			</div>';
		}//end method add_vc_incform


		/**
		 * Enqueue all css and js needed for this plugin
		 *
		 * @param $hook
		 */
		public function enqueue_styles_scripts( $hook ) {
			$current_page = isset( $_GET['page'] ) ? $_GET['page'] : '';

			$all_admin_pages = array(
				'cbxwpsimpleaccounting', //overview page
				'cbxwpsimpleaccounting_addexpinc', //add/edit income/expense page
				'cbxwpsimpleaccounting_cat', //category manager page
				'cbxwpsimpleaccounting_accmanager', // account manager page
				'cbxwpsimpleaccounting_addons', //addon page
				'cbxwpsimpleaccountinglog', //log page
				'cbxwpsimpleaccounting_settings', //setting page
				'cbxwpsimpleaccountingvc', //vendor and clients page
			);

			$validation_messages = array(
				'jquery_validate_messages' => array(
					'required'    => esc_html__( 'This field is required.', 'cbxwpsimpleaccountingvc' ),
					'remote'      => esc_html__( 'Please fix this field.', 'cbxwpsimpleaccountingvc' ),
					'email'       => esc_html__( 'Please enter a valid email address.', 'cbxwpsimpleaccountingvc' ),
					'url'         => esc_html__( 'Please enter a valid URL.', 'cbxwpsimpleaccountingvc' ),
					'date'        => esc_html__( 'Please enter a valid date.', 'cbxwpsimpleaccountingvc' ),
					'dateISO'     => esc_html__( 'Please enter a valid date ( ISO ).', 'cbxwpsimpleaccountingvc' ),
					'number'      => esc_html__( 'Please enter a valid number.', 'cbxwpsimpleaccountingvc' ),
					'digits'      => esc_html__( 'Please enter only digits.', 'cbxwpsimpleaccountingvc' ),
					'equalTo'     => esc_html__( 'Please enter the same value again.', 'cbxwpsimpleaccountingvc' ),
					'maxlength'   => esc_html__( 'Please enter no more than {0} characters.', 'cbxwpsimpleaccountingvc' ),
					'minlength'   => esc_html__( 'Please enter at least {0} characters.', 'cbxwpsimpleaccountingvc' ),
					'rangelength' => esc_html__( 'Please enter a value between {0} and {1} characters long.', 'cbxwpsimpleaccountingvc' ),
					'range'       => esc_html__( 'Please enter a value between {0} and {1}.', 'cbxwpsimpleaccountingvc' ),
					'max'         => esc_html__( 'Please enter a value less than or equal to {0}.', 'cbxwpsimpleaccountingvc' ),
					'min'         => esc_html__( 'Please enter a value greater than or equal to {0}.', 'cbxwpsimpleaccountingvc' ),
					'recaptcha'   => esc_html__( 'Please check the captcha.', 'cbxwpsimpleaccountingvc' ),
				),
				'validation_msg_required'  => esc_html__( 'This field is required.', 'cbxwpsimpleaccountingvc' ),
				'validation_msg_email'     => esc_html__( 'Please enter a valid email address.', 'cbxwpsimpleaccounting' ),
			);


			if ( $current_page == 'cbxwpsimpleaccountingvc' ) {
				wp_enqueue_style( 'cbxwpsimpleaccounting-branding');
				wp_register_style( 'cbxwpsimpleaccountingvc-ply', plugins_url( 'assets/css/ply.css', __FILE__ ), array(), $this->version );
				wp_register_style( 'cbxwpsimpleaccountingvc', plugin_dir_url( __FILE__ ) . 'assets/cbxwpsimpleaccountingvc.css', array(), $this->version );


				wp_register_script( 'jquery.validate.min', plugin_dir_url( __FILE__ ) . 'assets/jquery-validation/jquery.validate.min.js', array( 'jquery' ), $this->version, true );
				wp_register_script( 'jquery.validate-additional-methods.min', plugin_dir_url( __FILE__ ) . 'assets/jquery-validation/additional-methods.min.js', array(
					'jquery',
					'jquery.validate.min'
				), $this->version, true );
				wp_register_script( 'jquery.mustache', plugin_dir_url( __FILE__ ) . 'assets/jquery.mustache.js', array( 'jquery' ), $this->version, true );
				wp_register_script( 'mustache.min', plugin_dir_url( __FILE__ ) . 'assets/mustache.min.js', array(
					'jquery',
					'jquery.mustache',
				), $this->version, true );
				wp_register_script( 'ply.min', plugin_dir_url( __FILE__ ) . 'assets/ply.min.js', array( 'jquery' ), $this->version, true );
				wp_register_script( 'cbxwpsimpleaccountingvc', plugin_dir_url( __FILE__ ) . 'assets/cbxwpsimpleaccountingvc.js', array(
					'jquery',
					'jquery.mustache',
					'mustache.min',
					'ply.min',
					'jquery.validate.min',
					'jquery.validate-additional-methods.min'
				), $this->version, false );

				wp_localize_script(
					'cbxwpsimpleaccountingvc',
					'cbxvc_admin_l10n',
					array(
						'deleteconfirm'       => esc_html__( 'Are you sure to delete this item?', 'cbxwpsimpleaccountingvc' ),
						'deleteconfirmok'     => esc_html__( 'Sure', 'cbxwpsimpleaccountingvc' ),
						'deleteconfirmcancel' => esc_html__( 'Oh! No', 'cbxwpsimpleaccountingvc' ),
						'validation_messages' => $validation_messages
					)
				);

				wp_enqueue_style( 'cbxwpsimpleaccountingbs' );
				wp_enqueue_style( 'cbxwpsimpleaccountingvc-ply' );
				wp_enqueue_style( 'cbxwpsimpleaccountingvc' );

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery.mustache' );
				wp_enqueue_script( 'mustache.min' );
				wp_enqueue_script( 'ply.min' );
				wp_enqueue_script( 'jquery.validate.min' );
				wp_enqueue_script( 'jquery.validate-additional-methods.min' );
				wp_enqueue_script( 'cbxwpsimpleaccountingvc' );
			}


			if ( $current_page == 'cbxwpsimpleaccounting_addexpinc' ) {
				wp_register_script( 'cbxwpsimpleaccountingvc-add-hooks', plugin_dir_url( __FILE__ ) . 'assets/cbxwpsimpleaccountingvc_add_hooks.js', array(
					'jquery',
					'cbxwpsimpleaccountingeventjs'
				), $this->version, false );
				wp_enqueue_script( 'cbxwpsimpleaccountingvc-add-hooks' );
			}

		}//end method enqueue_styles_scripts

		/**
		 * Plugin activation check
		 */
		public static function activation() {
			if(!function_exists('is_plugin_active')){
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			//$plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
			//check_admin_referer("activate-plugin_{$plugin}");

			/* if (!current_user_can('activate_plugins')) {
				 return;
			 }*/


			if (in_array( 'cbxwpsimpleaccounting/cbxwpsimpleaccounting.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || defined('CBXWPSIMPLEACCOUNTING_PLUGIN_VERSION') ) {
				if(version_compare(CBXWPSIMPLEACCOUNTING_PLUGIN_VERSION,'1.3.13', '<') ){
					// Deactivate the plugin
					deactivate_plugins( __FILE__ );
					$error_message = sprintf( __( '<strong>CBX Accounting Vendors & Clients Addon</strong> plugin requires <a target="_blank" href="%s">%s</a> Version 1.3.13 or above', 'cbxwpsimpleaccountingvc' ), 'http://wordpress.org/extend/plugins/cbxwpsimpleaccounting/', 'CBX Accounting & Bookkeeping' );
					die( $error_message );
                }
			}
			else{
				// Deactivate the plugin
				deactivate_plugins( __FILE__ );
				// Throw an error in the wordpress admin console
				$error_message = sprintf( __( '<strong>CBX Accounting Vendors & Clients Addon</strong> plugin requires <a target="_blank" href="%s">%s</a>', 'cbxwpsimpleaccountingvc' ), 'http://wordpress.org/extend/plugins/cbxwpsimpleaccounting/', 'CBX Accounting & Bookkeeping' );
				die( $error_message );
            }


			//now create the vendors and clients table
			CBXWpsimpleaccountingVC::dbTableCreation();
		}//end method activation

		/**
		 * If we need to do something in upgrader process is completed for poll plugin
		 *
		 * @param $upgrader_object
		 * @param $options
		 */
		public function plugin_upgrader_process_complete( $upgrader_object, $options ) {
			if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
				foreach ( $options['plugins'] as $each_plugin ) {
					if ( $each_plugin == CBXWPSIMPLEACCOUNTINGVC_BASE_NAME ) {

						if(version_compare(CBXWPSIMPLEACCOUNTING_PLUGIN_VERSION,'1.3.13', '<') ){
							set_transient( 'cbxwpsimpleaccountingvc_upgraded_notice', 1 );
						}

						//now create the vendors and clients table
						CBXWpsimpleaccountingVC::dbTableCreation();
						break;
					}
				}
			}
		}//end method plugin_upgrader_process_complete

		/**
		 * Show a notice to anyone who has just installed the plugin for the first time
		 * This notice shouldn't display to anyone who has just updated this plugin
		 */
		public function plugin_activate_upgrade_notices() {
			// Check the transient to see if we've just activated the plugin
			if ( get_transient( 'cbxwpsimpleaccountingvc_upgraded_notice' ) ) {

				if(version_compare(CBXWPSIMPLEACCOUNTING_PLUGIN_VERSION,'1.3.13', '<') ){
					echo '<div style="border-left-color:#4834d4; margin-right: 15px;" class="notice notice-success is-dismissible">';
					if(version_compare(CBXWPSIMPLEACCOUNTING_PLUGIN_VERSION,'1.3.13', '<') ){
						echo '<p>' . sprintf( __( '<strong>CBX Accounting Vendors & Clients Addon</strong> plugin requires <a target="_blank" href="%s">%s</a> Version 1.3.13 or above', 'cbxwpsimpleaccountingvc' ), 'http://wordpress.org/extend/plugins/cbxwpsimpleaccounting/', 'CBX Accounting & Bookkeeping' ). '</p>';
					}
					echo '</div>';
                }


				// Delete the transient so we don't keep displaying the activation message
				delete_transient( 'cbxwpsimpleaccountingvc_upgraded_notice' );
			}
		}//end plugin_activate_upgrade_notices

		/**
		 * Create table for vc plugin
		 */
		public static function dbTableCreation() {
			global $wpdb;

			$charset_collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				if ( ! empty( $wpdb->charset ) ) {
					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
				}
				if ( ! empty( $wpdb->collate ) ) {
					$charset_collate .= " COLLATE $wpdb->collate";
				}
			}


			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$table_account = $wpdb->prefix . 'cbaccounting_vc_manager';

			$sql = "CREATE TABLE $table_account (
                          id int(11) unsigned NOT NULL AUTO_INCREMENT,
                          user_id bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'foreign key of user table. user associated with vc',
                          type varchar(6) NOT NULL COMMENT 'client or vendor',
                          name varchar(200) NOT NULL COMMENT 'client or vendor manager name',
                          description varchar(200) DEFAULT NULL COMMENT 'client or vendor description',
                          organization varchar(255) NOT  NULL DEFAULT '' COMMENT 'client or vendor organization',
                          contactinfo_office mediumtext NOT NULL DEFAULT '' COMMENT 'client or vendor  official contact info',
                          address text DEFAULT NULL COMMENT 'client or vendor address',
                          add_by bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'foreign key of user table. who add this category',
                          mod_by bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'foreign key of user table. who modified this category',
                          add_date datetime DEFAULT NULL COMMENT 'created date',
                          mod_date datetime DEFAULT NULL COMMENT 'modified date',
                          PRIMARY KEY (id)
                        ) $charset_collate; ";

			dbDelta( $sql );
		}//end method dbTableCreation


		/**
		 * @param array $links Default settings links
		 *
		 * @return array
		 */
		public function add_plugin_admin_page( $links ) {
			$new_links[] = '<a href="' . admin_url( 'admin.php?page=cbxwpsimpleaccounting_settings' ) . '">' . esc_html__( 'Settings', 'cbxwpsimpleaccountingvc' ) . '</a>';

			return array_merge( $new_links, $links );
		}//end method add_plugin_admin_page

		/**
		 * Add support link to plugin description in /wp-admin/plugins.php
		 *
		 * @param  array  $plugin_meta
		 * @param  string $plugin_file
		 *
		 * @return array
		 */
		public function support_link( $plugin_meta, $plugin_file ) {
			if ( plugin_basename( __FILE__ ) == $plugin_file ) {
				$plugin_meta[] = sprintf(
					'<a href="%s">%s</a>', 'https://codeboxr.com/product/cbx-accounting', esc_html__( 'Support', 'cbxwpsimpleaccountingvc' )
				);
			}

			return $plugin_meta;
		}//end method support_link

		/**
		 * Register the administration submenumenu for parent menu "CBX Accounting" for this plugin into the WordPress Dashboard.
		 *
		 */
		public function cbxwpsimpleaccountingvc_menu() {
			$vcpage_hook = add_submenu_page( 'cbxwpsimpleaccounting', esc_html__( 'CBX Accounting Vendors & Clients', 'cbxwpsimpleaccountingvc' ), esc_html__( 'Vendors & Clients', 'cbxwpsimpleaccountingvc' ), 'vc_cbxaccounting', 'cbxwpsimpleaccountingvc', array(
				$this,
				'cbxwpsimpleaccountingvc_display'
			) );

			//add screen option save option
			add_action( "load-$vcpage_hook", array( $this, 'cbxaccvc_add_option_listing' ) );
		}//end method cbxwpsimpleaccountingvc_menu


		/**
		 * Set options for user result log.
		 *
		 * @param $status
		 * @param $option
		 * @param $value
		 *
		 * @return mixed
		 */
		public function cbxvc_set_option_listing( $status, $option, $value ) {
			if ( 'cbxaccvc_results_per_page' == $option ) {
				return $value;
			}

			return $status;
		}//end method cbxvc_set_option_listing


		/**
		 * Add screen option for vc listing
		 */
		public static function cbxaccvc_add_option_listing() {
			$option = 'per_page';
			$args   = array(
				'label'   => esc_html__( 'Number of items per page:', 'cbxwpsimpleaccountingvc' ),
				'default' => 50,
				'option'  => 'cbxaccvc_results_per_page'
			);
			add_screen_option( $option, $args );
		}//end method cbxaccvc_add_option_listing


		/**
		 * Show VC page
		 *
		 */
		public function cbxwpsimpleaccountingvc_display() {
			if ( isset( $_GET['view'] ) && $_GET['view'] == 'addedit' ) {
				include( 'partials/addeditvc.php' );
				include( 'partials/mustache-template-vc.php' );
			} elseif ( isset( $_GET['view'] ) && $_GET['view'] == 'view' ) {
				include( 'partials/viewvc.php' );
			} else {
				if ( ! class_exists( 'CBXWPSACC_VC_Listing' ) ) {
					require_once( 'includes/class-vclisting.php' );
				}
				include( 'partials/listvc.php' );
			}
		}//end method cbxwpsimpleaccountingvc_display

		/**
		 * Add new vc manager
		 *
		 * return string
		 */
		public function add_new_vc_manager_acc() {
			global $wpdb;
			$form_validation            = true;
			$cbacnt_validation['error'] = false;
			$cbacnt_validation['field'] = array();

			$cbxacc_table_name = $wpdb->prefix . 'cbaccounting_vc_manager';

			//verify nonce field
			if ( wp_verify_nonce( $_POST['new_acc_verifier'], 'add_new_acc' ) ) {


				$vc_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

				$type         = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
				$name         = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
				$description  = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';
				$organization = isset( $_POST['organization'] ) ? sanitize_text_field( $_POST['organization'] ) : '';
				$user_id      = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;

				$address_input = array();

				$address = isset( $_POST['address'] ) ? $_POST['address'] : array();


				$address_input['line1']   = isset( $address['line1'] ) ? sanitize_text_field( $address['line1'] ) : '';
				$address_input['line2']   = isset( $address['line2'] ) ? sanitize_text_field( $address['line2'] ) : '';
				$address_input['city']    = isset( $address['city'] ) ? sanitize_text_field( $address['city'] ) : '';
				$address_input['state']   = isset( $address['state'] ) ? sanitize_text_field( $address['state'] ) : '';
				$address_input['postal']  = isset( $address['postal'] ) ? sanitize_text_field( $address['postal'] ) : '';
				$address_input['country'] = isset( $address['country'] ) ? sanitize_text_field( $address['country'] ) : '';


				$contactinfo_input = array();

				$contactinfo = isset( $_POST['contactinfo_office'] ) ? $_POST['contactinfo_office'] : array();

				$contactinfo_input['name']        = isset( $contactinfo['name'] ) ? sanitize_text_field( $contactinfo['name'] ) : '';
				$contactinfo_input['email']       = isset( $contactinfo['email'] ) ? sanitize_text_field( $contactinfo['email'] ) : '';
				$contactinfo_input['designation'] = isset( $contactinfo['designation'] ) ? sanitize_text_field( $contactinfo['designation'] ) : '';

				$phones_input = array();
				$phones       = isset( $contactinfo['phone'] ) ? $contactinfo['phone'] : array();
				if ( is_array( $phones ) && sizeof( $phones ) > 0 ) {
					foreach ( $phones as $index => $phone ) {
						$phone_val  = isset( $phone['phoneval'] ) ? sanitize_text_field( $phone['phoneval'] ) : '';
						$phone_type = isset( $phone['phonetype'] ) ? sanitize_text_field( $phone['phonetype'] ) : 'work';

						if ( $phone_val == '' ) {
							continue;
						} //we don't want to store empty information
						$phones_input[] = array( 'phoneval' => $phone_val, 'phonetype' => $phone_type );
					}
				}

				$contactinfo_input['phone'] = $phones_input;


				

				$col_data = array(
					'type'               => $type,
					'name'               => $name,
					'description'        => $description,
					'organization'       => $organization,
					'contactinfo_office' => maybe_serialize( $contactinfo_input ),
					'address'            => maybe_serialize( $address_input ),
					'user_id'            => $user_id,
				);


				$name_len = mb_strlen( $name );
				//$addressinfo = maybe_unserialize( $col_data['address'] );

				//$info = maybe_unserialize( $col_data['contactinfo_office'] );

				//$info_phone = maybe_unserialize( $info['phone'] );

				//check vc manager name length is not less than 5 or more than 200 char
				if ( $name_len < 5 || $name_len > 200 ) {
					$form_validation              = false;
					$cbacnt_validation['error']   = true;
					$cbacnt_validation['field'][] = 'name';
					$cbacnt_validation['msg']     = esc_html__( 'The name field character limit must be between 5 to 200.', 'cbxwpsimpleaccountingvc' );
				}

				//check form passes all validation rules
				if ( $form_validation ) {
					//edit mode
					if ( $vc_id > 0 ) {
						$col_data['mod_by']   = get_current_user_id();
						$col_data['mod_date'] = current_time( 'mysql' );

						// type, name, description, organization, contactinfo_office, address
						$col_data_format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' );

						$where = [
							'id' => $vc_id
						];

						$where_format = [ '%d' ];

						//matching update function return is false, then update failed.
						if ( $wpdb->update(
								$wpdb->prefix . 'cbaccounting_vc_manager', $col_data, $where,
								$col_data_format, $where_format
							) === false
						) {
							//update failed
							$cbacnt_validation['msg'] = esc_html__( 'Sorry! you don\'t have enough permission to update information.', 'cbxwpsimpleaccountingvc' );
						} else {
							//update successful
							$msg = esc_html__( 'Information updated.', 'cbxwpsimpleaccountingvc' );

							$edit_url = admin_url( 'admin.php?page=cbxwpsimpleaccountingvc&view=addedit&id=' . $vc_id );
							//$add_url  = admin_url( 'admin.php?page=cbxwpsimpleaccountingvc&view=addedit' );

							//$msg .= ' <a  data-accid="' . $vc_id . '"  href="' . $edit_url . '" class="button cbacnt-edit-cbxacc">';
							//$msg .= esc_html__( 'Edit', 'cbxwpsimpleaccountingvc' );
							//$msg .= '</a>';
							/*$msg .= ' <a  href="' . $add_url . '" class="button cbacnt-new-acc">';
							$msg .= esc_html__( 'Add new', 'cbxwpsimpleaccountingvc' );
							$msg .= '</a>';*/

							$cbacnt_validation['error']                 = false;
							$cbacnt_validation['msg']                   = $msg;
							$cbacnt_validation['form_value']['id']      = $vc_id;

							$cbacnt_validation['form_value']['user_id'] = $user_id;
							if($user_id > 0){
								$cbacnt_validation['form_value']['user_id_information'] = '<p id="user_id_information"><a href="'. get_edit_user_link($user_id).'" target = "_blank">' . stripslashes(get_user_by('id', $user_id)->display_name) . '</a></p>';
							}
							else $cbacnt_validation['form_value']['user_id_information'] = '';

							$cbacnt_validation['form_value']['edit_url'] = $edit_url;
							$cbacnt_validation['form_value']['status']  = 'updated';
							$cbacnt_validation['form_value']['name']    = stripslashes( esc_attr( ( $col_data['name'] ) ) );
							$cbacnt_validation['form_value']['type']    = $col_data['type'];
						}

					} else {
						//new

						$col_data['add_by']   = get_current_user_id();
						$col_data['add_date'] = current_time( 'mysql' );


						// type, name, description, organization, contactinfo_office, address
						$col_data_format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' );

						//insert new account
						if ( $wpdb->insert( $wpdb->prefix . 'cbaccounting_vc_manager', $col_data, $col_data_format ) ) {

							//new account inserted successfully
							$vc_id = $wpdb->insert_id;

							$edit_url = admin_url( 'admin.php?page=cbxwpsimpleaccountingvc&view=addedit&id=' . $vc_id );

							$msg = esc_html__( 'Account created successfully.', 'cbxwpsimpleaccountingvc' );
							//$msg .= ' <a  data-accid="' . $acc_id . '"  href="' . $edit_url . '" class="button cbacnt-edit-cbxacc">';
							//$msg .= esc_html__( 'Edit', 'cbxwpsimpleaccountingvc' );
							//$msg .= '</a>';

							$cbacnt_validation['error']                 = false;
							$cbacnt_validation['msg']                   = $msg;
							$cbacnt_validation['form_value']['id']      = $vc_id;
							$cbacnt_validation['form_value']['user_id'] = $user_id;
							if($user_id > 0){
								$cbacnt_validation['form_value']['user_id_information'] = '<p id="user_id_information"><a href=" ' . get_edit_user_link($user_id) . '" target = "_blank">' . stripslashes(get_user_by('id', $user_id)->display_name) . '</a></p>';
							}
							else $cbacnt_validation['form_value']['user_id_information'] = '';
							$cbacnt_validation['form_value']['edit_url'] = $edit_url;
							$cbacnt_validation['form_value']['status']  = 'new';
							$cbacnt_validation['form_value']['name']    = stripslashes( esc_attr( $col_data['name'] ) );
							$cbacnt_validation['form_value']['type']    = $col_data['type'];

						} else {
							$cbacnt_validation['error'] = true;
							$cbacnt_validation['msg']   = esc_html__( 'Sorry! Information insert failed', 'cbxwpsimpleaccountingvc' );
						}
					}

				}
			} else { //if wp_nonce not verified then entry here
				$cbacnt_validation['error']   = true;
				$cbacnt_validation['field'][] = 'wp_nonce';
				$cbacnt_validation['msg']     = esc_html__( 'Hacking attempt ?', 'cbxwpsimpleaccountingvc' );
			}

			echo json_encode( $cbacnt_validation );
			wp_die();
		}//end method add_new_vc_manager_acc

		/*
		 * Inserts a new key/value before the key in the array.
		 *
		 * @param $key
		 *   The key to insert before.
		 * @param $array
		 *   An array to insert in to.
		 * @param $new_key
		 *   The key to insert.
		 * @param $new_value
		 *   An value to insert.
		 *
		 * @return
		 *   The new array if the key exists, FALSE otherwise.
		 *
		 * @see array_insert_after()
		 */
		function array_insert_before( $key, array &$array, $new_key, $new_value ) {
			if ( array_key_exists( $key, $array ) ) {
				$new = array();
				foreach ( $array as $k => $value ) {
					if ( $k === $key ) {
						$new[ $new_key ] = $new_value;
					}
					$new[ $k ] = $value;
				}

				return $new;
			}

			return false;
		}//end method array_insert_before

		/*
		 * Inserts a new key/value after the key in the array.
		 *
		 * @param $key
		 *   The key to insert after.
		 * @param $array
		 *   An array to insert in to.
		 * @param $new_key
		 *   The key to insert.
		 * @param $new_value
		 *   An value to insert.
		 *
		 * @return
		 *   The new array if the key exists, FALSE otherwise.
		 *
		 * @see array_insert_before()
		 */
		function array_insert_after( $key, array &$array, $new_key, $new_value ) {
			if ( array_key_exists( $key, $array ) ) {
				$new = array();
				foreach ( $array as $k => $value ) {
					$new[ $k ] = $value;
					if ( $k === $key ) {
						$new[ $new_key ] = $new_value;
					}
				}

				return $new;
			}

			return false;
		}//end method array_insert_after


		/**
		 * accounts export
		 */
		public function cbxwpsimpleaccounting_vc_export() {

			if ( isset( $_REQUEST['cbxwpsimpleaccountingvc_export'] ) && isset( $_REQUEST['format'] ) && $_REQUEST['format'] !== null ) {
				$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
				$action = 'bulk-cbxaccvcs';

				if ( ! wp_verify_nonce( $nonce, $action ) ) {
					wp_die( 'Nope! Security check failed!' );
				}


				global $wpdb;
				$export_format = sanitize_text_field( $_REQUEST['format'] );

				$phpexcel_loaded = false;

				if ( defined( 'CBXPHPSPREADSHEET_PLUGIN_NAME' ) && file_exists( CBXPHPSPREADSHEET_ROOT_PATH . 'lib/vendor/autoload.php' ) ) {
					//Include PHPExcel
					require_once( CBXPHPSPREADSHEET_ROOT_PATH . 'lib/vendor/autoload.php' );
					$phpexcel_loaded = true;
				}

				if ( $phpexcel_loaded == false ) {
					echo esc_html__( 'Sorry PHPExcel library not loaded properly.', 'cbxwpsimpleaccountingvc' ) . sprintf( __( ' Back to <a href="%s">Vendor and Client Manager</a>.', 'cbxwpsimpleaccountingvc' ), admin_url( 'admin.php?page=cbxwpsimpleaccountingvc' ) );
					exit();
				}

				if ( $phpexcel_loaded ) {

					//error_reporting( 0 );

					$cbaccounting_vc_table = $wpdb->prefix . "cbaccounting_vc_manager";

					$order   = ( isset( $_REQUEST['order'] ) && $_REQUEST['order'] != '' ) ? $_REQUEST['order'] : 'desc';
					$orderby = ( isset( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] != '' ) ? $_REQUEST['orderby'] : 'id';

					$search = ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ) ? sanitize_text_field( $_REQUEST['s'] ) : '';

					$datas = CBXWpsimpleaccountingHelper::getVCData( $search, $orderby, $order, 20, 1 );

					$excell_cell_char = '';

					//$objPHPExcel = new PHPExcel();
					$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

					// Set document properties
					$objPHPExcel->getProperties()->setCreator( 'CBX Accounting Vendors & Clients' )
					            ->setLastModifiedBy( '' )
					            ->setTitle( 'Vendors & Clients Log' )
					            ->setSubject( '' )
					            ->setDescription( 'Vendors & Clients Log' )
					            ->setKeywords( 'restaurant, booking, vendor, clients' )
					            ->setCategory( '' );

					$objPHPExcel->setActiveSheetIndex( 0 );
					$objPHPExcel->getActiveSheet()->setCellValue( 'A1', esc_html__( 'Name', 'cbxwpsimpleaccountingvc' ) );
					$objPHPExcel->getActiveSheet()->setCellValue( 'B1', esc_html__( 'Type', 'cbxwpsimpleaccountingvc' ) );
					$objPHPExcel->getActiveSheet()->setCellValue( 'C1', esc_html__( 'Address', 'cbxwpsimpleaccountingvc' ) );
					$objPHPExcel->getActiveSheet()->setCellValue( 'D1', esc_html__( 'Contact Information', 'cbxwpsimpleaccountingvc' ) );
					$objPHPExcel->getActiveSheet()->setCellValue( 'E1', esc_html__( 'User Name', 'cbxwpsimpleaccountingvc' ) );
					$objPHPExcel->getActiveSheet()->setCellValue( 'F1', esc_html__( 'ID', 'cbxwpsimpleaccountingvc' ) );

					do_action( 'cbxwpsimpleaccounting_vc_export_other_heading', $objPHPExcel );

					$objPHPExcel->getActiveSheet()->getStyle( 'A1:Z1' )->getFont()->setBold( true );

					//$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn( 'A:K' )->setAutoSize( true );

					$nCols = apply_filters( 'cbxwpsimpleaccounting_vc_export_col_count', 6 ); //set the number of columns

					foreach ( range( 0, $nCols ) as $col ) {
						$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn( $col )->setAutoSize( true );
					}

					if ( $datas ) {


						foreach ( $datas as $i => $data ) {
							//for .xls,.xlsx and .csv
							$type = CBXWPSimpleaccountingVCHelper::vcType( $data['type'] );

							//address
							$addressinfo = unserialize( $data['address'] );

							$line1 = isset( $addressinfo['line1'] ) ? esc_html( $addressinfo['line1'] ) : '';
							$line2 = isset( $addressinfo['line2'] ) ? esc_html( $addressinfo['line2'] ) : '';

							$output_address = ( $line1 != '' ) ? $line1 : '';
							$output_address .= ( $line2 != '' ) ? ( ', ' . $line2 ) : '';

							//contact
							$contactinfo       = unserialize( $data['contactinfo_office'] );
							$contactinfo_phone = unserialize( $contactinfo['phone'] );

							$output_contact = '';

							$phones = array();
							foreach ( $contactinfo_phone as $counter => $value ) {
								$phones[] = $contactinfo_phone[ $counter ]['phoneval'] . '(' . CBXWPSimpleaccountingVCHelper::getCommunicationWayName( $contactinfo_phone[ $counter ]['phonetype'] ) . ')';
							}

							$output_contact .= $contactinfo['name'];
							$output_contact .= ( $contactinfo['designation'] != '' ) ? ( ' (' . $contactinfo['designation'] . '), ' ) : ', ';
							$output_contact .= ( sizeof( $phones ) > 0 ) ? ( implode( ', ', $phones ) ) : '';

							$user_name = '';
							if ( isset( $data['user_id'] ) && intval( $data['user_id'] ) > 0 ) {
								$user_name = stripslashes( get_user_by( 'id', $data['user_id'] )->display_name ) . '</a>';
							}

							$objPHPExcel->getActiveSheet()->setCellValue( 'A' . ( $i + 2 ), esc_html( $data['name'] ) );
							$objPHPExcel->getActiveSheet()->setCellValue( 'B' . ( $i + 2 ), $type );
							$objPHPExcel->getActiveSheet()->setCellValue( 'C' . ( $i + 2 ), $output_address );
							$objPHPExcel->getActiveSheet()->setCellValue( 'D' . ( $i + 2 ), $output_contact );
							$objPHPExcel->getActiveSheet()->setCellValue( 'E' . ( $i + 2 ), $user_name );
							$objPHPExcel->getActiveSheet()->setCellValue( 'F' . ( $i + 2 ), $data['id'] );

							do_action( 'cbxwpsimpleaccounting_vc_export_other_col', $objPHPExcel, $i, $data );

						}
					}

					//for .xls,.xlsx,.csv
					$objPHPExcel->setActiveSheetIndex( 0 );

					ob_clean();
					ob_start();

					$filename = 'cbxaccounting-vendor-clients';
					switch ( $export_format ) {
						/*case 'csv':
							// Redirect output to a client’s web browser (csv)
							$filename = $filename . '.csv';
							header( "Content-type: text/csv" );
							header( "Cache-Control: no-store, no-cache" );
							header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
							$objWriter = new PHPExcel_Writer_CSV( $objPHPExcel );
							$objWriter->setDelimiter( ',' );
							$objWriter->setEnclosure( '"' );
							$objWriter->setLineEnding( "\r\n" );
							$objWriter->setSheetIndex( 0 );
							$objWriter->save( 'php://output' );
							break;
						case 'xls':
							// Redirect output to a client’s web browser (Excel5)
							$filename = $filename . '.xls';
							header( 'Content-Type: application/vnd.ms-excel' );
							header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
							header( 'Cache-Control: max-age=0' );
							$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel5' );
							$objWriter->save( 'php://output' );
							break;
						case 'xlsx':
							// Redirect output to a client’s web browser (Excel2007)
							$filename = $filename . '.xlsx';
							header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
							header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
							header( 'Cache-Control: max-age=0' );
							$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );
							$objWriter->save( 'php://output' );
							break;*/
						case 'ods':
							// Redirect output to a client’s web browser (Excel5)
							$filename = $filename . '.ods';
							header( 'Content-Type: application/vnd.oasis.opendocument.spreadsheet' );
							header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
							header( 'Cache-Control: max-age=0' );
							// If you're serving to IE 9, then the following may be needed
							header( 'Cache-Control: max-age=1' );
							// If you're serving to IE over SSL, then the following may be needed
							header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
							header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
							header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
							header( 'Pragma: public' ); // HTTP/1.0
							$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $objPHPExcel, 'Ods' );
							$objWriter->save( 'php://output' );
							break;
						case 'xls':
							// Redirect output to a client’s web browser (Excel5)
							$filename = $filename . '.xls';
							header( 'Content-Type: application/vnd.ms-excel' );
							header( 'Content-Disposition: attachment;filename="' . $filename . '"' );
							header( 'Cache-Control: max-age=0' );
							// If you're serving to IE 9, then the following may be needed
							header( 'Cache-Control: max-age=1' );
							// If you're serving to IE over SSL, then the following may be needed
							header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
							header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
							header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
							header( 'Pragma: public' ); // HTTP/1.0
							$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $objPHPExcel, 'Xls' );
							$objWriter->save( 'php://output' );
							break;
						case 'xlsx':
							// Redirect output to a client’s web browser (Excel2007)
							$filename = $filename . '.xlsx';

							header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
							header( 'Content-Disposition: attachment;filename="' . $filename . '"' );

							header( 'Cache-Control: max-age=0' );
							// If you're serving to IE 9, then the following may be needed
							header( 'Cache-Control: max-age=1' );
							// If you're serving to IE over SSL, then the following may be needed
							header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
							header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
							header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
							header( 'Pragma: public' ); // HTTP/1.0

							//$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );
							//$objWriter->save( 'php://output' );
							$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $objPHPExcel, 'Xlsx' );
							$writer->save( 'php://output' );
							break;
					}
					exit;
				}
			}
		}//end method cbxwpsimpleaccounting_vc_export

		/**
		 * Display vendor/clients dropdown filter in frontend dashboard addon in statement display page
		 */
		public function cbxwpsimpleaccountingstatement_extra_filters_frontend() {
			global $wpdb;
			$cbxstatementshowvc_val = 1; //show vendors/clients
			$cbxstatementshowvc_val = isset( $_REQUEST['cbxstatementshowvc'] ) ? absint( $_REQUEST['cbxstatementshowvc'] ) : $cbxstatementshowvc_val; //show accounts

			$cbaccounting_vc_table = $wpdb->prefix . "cbaccounting_vc_manager";
			$cbxlog_vc_list        = $wpdb->get_results( 'SELECT `id`, `name`, `type` FROM ' . $cbaccounting_vc_table . ' order by type, name asc', ARRAY_A ); //Selecting all vc
			$cbxlogexpinc_vc_val   = isset( $_REQUEST['cbxlogexpinc_vc'] ) ? absint( $_REQUEST['cbxlogexpinc_vc'] ) : 0;
			?>
			<!--<div class="row">
				<div class="checkbox col-sm-12">
					<label>
						<input type="checkbox" <?php /*echo ( $cbxstatementshowvc_val == 1 ) ? 'checked' : ''; */ ?>
							   id="cbxstatementshowvc" name="cbxstatementshowvc" value="1" /> <?php /*esc_html_e( 'Break Per Vendors/Clients: ', 'cbxwpsimpleaccountingfrontend' ); */ ?>
					</label>
				</div>
			</div>-->
			<div class="row">
				<div class="form-group col-sm-12">
					<label for="cbxlogexpinc_account"><?php esc_html_e( 'Choose Vendor/Clients', 'cbxwpsimpleaccountingvc' ); ?></label>
					<select name="cbxlogexpinc_vc" id="cbxlogexpinc_vc" class="form-control">
						<optgroup label="<?php esc_html_e( 'Vendor and Clients', 'cbxwpsimpleaccountingvc' ); ?>">
							<option <?php echo ( $cbxlogexpinc_vc_val == 0 ) ? ' selected="selected" ' : ''; ?>
								value="0"><?php esc_html_e( 'Select Vendor/Client', 'cbxwpsimpleaccountingvc' ); ?></option>
						</optgroup>

						<optgroup label="<?php esc_html_e( 'Clients', 'cbxwpsimpleaccountingvc' ); ?>">
							<?php
								foreach ( $cbxlog_vc_list as $list ):

									if ( $list['type'] != 'client' ) {
										continue;
									}
									$selected = ( ( $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val == $list['id'] ) ? ' selected="selected" ' : '' );

									echo '<option   ' . $selected . ' value="' . $list['id'] . '">' . stripslashes( esc_attr( $list['name'] ) ) . '</option>';
									?>
								<?php endforeach; ?>
						</optgroup>
						<optgroup label="<?php esc_html_e( 'Vendors', 'cbxwpsimpleaccountingvc' ); ?>">
							<?php
								foreach ( $cbxlog_vc_list as $list ):

									if ( $list['type'] != 'vendor' ) {
										continue;
									}
									$selected = ( ( $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val > 0 && $cbxlogexpinc_vc_val == $list['id'] ) ? ' selected="selected" ' : '' );

									echo '<option   ' . $selected . ' value="' . $list['id'] . '">' . stripslashes( esc_attr( $list['name'] ) ) . '</option>';
									?>
								<?php endforeach; ?>
						</optgroup>
					</select>
				</div>
			</div>

			<?php

		}//end method cbxwpsimpleaccountingstatement_extra_filters_frontend
	}//end method CBXWpsimpleaccountingVC

	add_action( 'plugins_loaded', 'CBXWpsimpleaccountingVC_init' );
	function CBXWpsimpleaccountingVC_init() {
		if ( defined( 'CBXWPSIMPLEACCOUNTING_PLUGIN_VERSION' ) ) {
			new CBXWpsimpleaccountingVC();
		}
	}//end function CBXWpsimpleaccountingVC_init