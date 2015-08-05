<?php

/*
Plugin Name: Flush Network Permalink
Plugin URI:  http://borzacchiello.it
Description: Add a button to the admin bar to flush permalink for all sites.
Version:     0.1
Author:      Giustino Borzacchiello
Author URI:  http://borzacchiello.it
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: fnp
*/

class Flush_Network_Permalink {
	/**
	 * Factory method
	 *
	 * @return static
	 */
	public static function init() {
		$obj = new static;
		if ( current_user_can( 'manage_network_options' ) ) {
			add_action( 'admin_bar_menu', array( $obj, 'add_admin_bar_button' ), 999 );
			add_action( 'init', array( $obj, 'flush_rules' ) );
		}
		return $obj;
	}

	/**
	 * @param $wp_admin_bar WP_Admin_Bar
	 */
	public function add_admin_bar_button( $wp_admin_bar ) {
		$args        = array( 'action' => 'flush-rules' );
		$self        = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$current_url = add_query_arg( $args, $self );
		$args        = array(
			'id'    => 'dk_flush_network_permalink',
			'title' => __( 'Flush network permalink', 'fnp' ),
			'href'  => $current_url,
		);
		$wp_admin_bar->add_node( $args );
	}

	/**
	 * Thanks to Jeremy Felt.
	 *
	 * Source: https://jeremyfelt.com/2015/07/17/flushing-rewrite-rules-in-wordpress-multisite-for-fun-and-profit/
	 */
	public function flush_rules() {

		if ( wp_is_large_network() ) {
			return;
		}
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'flush-rules' ) {
			$sites = wp_get_sites( array( 'network' => 1, 'limit' => 1000 ) );
			foreach ( $sites as $site ) {
				switch_to_blog( $site['blog_id'] );
				delete_option( 'rewrite_rules' );
				restore_current_blog();
			}
		}
	}
}

Flush_Network_Permalink::init();


