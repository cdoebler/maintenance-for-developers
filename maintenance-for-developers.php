<?php
/*
Plugin Name: Maintenance For Developers
Plugin URI: https://www.christian-doebler.net/
Description: Displays a custom maintenance page.
Version: 1.0
Author: Christian Doebler
Author URI: https://www.christian-doebler.net/
License: MIT
*/

namespace Doebzen;

/**
 * Class Maintenance_For_Developers
 *
 * @author Christian Doebler <mail@christian-doebler.net>
 */
class Maintenance_For_Developers {
	public function __construct() {
		add_action( 'do_feed_atom', [ $this, 'show_feed' ], 0 );
		add_action( 'do_feed_rdf', [ $this, 'show_feed' ], 0 );
		add_action( 'do_feed_rss', [ $this, 'show_feed' ], 0 );
		add_action( 'do_feed_rss2', [ $this, 'show_feed' ], 0 );
		add_action( 'get_header', [ $this, 'show_template' ], 0 );
	}

	/**
	 * Sets maintenance headers.
	 *
	 * @return void
	 */
	private function set_headers(): void {
		nocache_headers();
		$http_protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
		header( $http_protocol . ' 503 Service unavailable' );
	}

	/**
	 * Shows maintenance content for feed.
	 *
	 * @return void
	 */
	public function show_feed(): void {
		if ( ! is_user_logged_in() ) {
			$this->set_headers();
			header( 'Content-Type: text/xml' );
			echo '<?xml version="1.0" encoding="UTF-8" ?><status>Service unavailable.</status>';
			exit;
		}
	}

	/**
	 * Shows custom template, default template or just a message.
	 *
	 * @return void
	 */
	public function show_template(): void {
		if ( ! is_user_logged_in() ) {
			$this->set_headers();

			// Check for custom template and display if available
			$template_file = get_stylesheet_directory() . '/maintenance-for-developers/index.php';
			if ( file_exists( $template_file ) ) {
				$template_dir_url = get_stylesheet_directory_uri() . '/maintenance-for-developers';
				include( $template_file );
				exit;
			}

			// Check for default template and display if available
			$template_file = plugin_dir_path( __FILE__ ) . 'template/index.php';
			if ( file_exists( $template_file ) ) {
				$template_dir_url = plugin_dir_url( __FILE__ ) . 'template';
				include( $template_file );
				exit;
			}

			// Display pretty naked maintenance page
			wp_die( '<h1>Under Maintenance</h1><br />Website under maintenance. Please check back later.' );
		}
	}
}

// Init plugin
$maintenance_for_developers = new Maintenance_For_Developers();
