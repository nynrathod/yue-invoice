<?php
/*
 * Plugin Name: YUE Invoice
 * Plugin URI: https://Yuezers.com
 * Version: 1.0
 * Author: Nayan Rathod
 * Author URI: https://Yuezers.com
 * License: GPL2
 *
 * @package YUE-Invoice
 * @copyright Copyright (c) 2022, Nayan Rathod
 * @license GPL2+
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
	die('-1');
}

if (!defined('YUE_PLUGIN_DIR')) {
	define('YUE_PLUGIN_DIR',plugins_url('', __FILE__));
}

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if (!class_exists('YUE_Invoice')) {

	class YUE_Invoice {

		protected static $yue_instance;


		// Adding column to order table
		function __construct() {
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'bbloomer_add_new_order_admin_list_column') );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'bbloomer_add_new_order_admin_list_column_content') );
		}
	
		// Invoice column Name		
		function bbloomer_add_new_order_admin_list_column( $columns ) {
			$columns['invoice_col'] = 'Invoice';
			return $columns;
		}
	
		// Column Name		
		function bbloomer_add_new_order_admin_list_column_content($col ) {
			global $post;
			if ( $col === 'invoice_col'  ) {
				$order = wc_get_order( $post->ID );
				$oid = $order->get_id();
				echo '<a href="#" class="ajax" id="myoid" my-oid="'.$order->get_id().'">Print Invoice</a>';

			}
		}

		// Performing Post Submitting		
		function yue_invoice() {
			$oids = $_POST['oid'];
			
			$order = wc_get_order( $oids );

			$html = '';

			$html .= '<p>Name: '.$order->get_billing_first_name().' '.$order->get_billing_last_name().'</p>';
			$html .= '<p>Address: '.$order->get_billing_address_1().' '.$order->get_billing_address_2().'</p>';
			$html .= '<p>City: '.$order->get_billing_city().'</p>';
			$html .= '<p>Country: '.$order->get_billing_country().'</p>';
			
			$html .= '<table>
							<thead>
								<th>Id</th>
								<th>Name</th>
								<th>Quantity</th>
								<th>Cost</th>

							</thead>
							<tbody>';
			$i=1;
				foreach ( $order->get_items() as $item_id => $item ) {
					$html .= '<tr>
							<td>'.$i.'</td>
							<td>'.$item->get_name().'</td>
							<td>'.$item->get_quantity().'</td>
							<td>'.$item->get_subtotal().'</td>
						  </tr>';
					$i++;
				}
			$html .= '</tbody>
						</table>';
				$html .= '<p>Order Total: '.$order->get_total().'</p>';
			echo $html;
			wp_die();
		}

	
		function init() {
			add_action( 'admin_enqueue_scripts', array($this, 'yue_load_admin'));
			add_action( 'wp_ajax_yue_invoice', array($this,'yue_invoice' ));
		}

		// Load Admin File		
		function yue_load_admin() {
			wp_enqueue_script( 'myjq', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js', array(), _S_VERSION, true );
			wp_enqueue_script( 'jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.1.135/jspdf.min.js', array(), _S_VERSION, true );
			wp_enqueue_script( 'fawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css');
			wp_enqueue_script( 'yue_front_script2', YUE_PLUGIN_DIR . '/includes/js/invoice.js', array(), _S_VERSION, true );
		}

		public static function yue_instance() {
			if (!isset(self::$yue_instance)) {
				self::$yue_instance = new self();
				self::$yue_instance->init();
			}
			return self::$yue_instance;
		}
	}

	add_action('plugins_loaded', array('YUE_Invoice', 'yue_instance'));

}