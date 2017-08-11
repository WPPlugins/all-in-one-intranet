<?php

/**
 * Plugin Name: All-In-One Intranet
 * Plugin URI: http://wp-glogin.com/all-in-one-intranet
 * Description: Instantly turn WordPress into a private corporate intranet 
 * Version: 1.3
 * Author: Dan Lester
 * Author URI: http://wp-glogin.com/
 * License: GPL3
 */

if (!class_exists('core_all_in_one_intranet')) {
	require_once( plugin_dir_path(__FILE__).'/core/core_all_in_one_intranet.php' );
}

class aioi_basic_all_in_one_intranet extends core_all_in_one_intranet {
	
	protected $PLUGIN_VERSION = '1.3';
	
	// Singleton
	private static $instance = null;
	
	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	
	// ADMIN
	
	protected function get_options_name() {
		return 'aioi_dsl';
	}
	
	// AUX
	
	protected function my_plugin_basename() {
		$basename = plugin_basename(__FILE__);
		if ('/'.$basename == __FILE__) { // Maybe due to symlink
			$basename = basename(dirname(__FILE__)).'/'.basename(__FILE__);
		}
		return $basename;
	}
	
	protected function my_plugin_url() {
		$basename = plugin_basename(__FILE__);
		if ('/'.$basename == __FILE__) { // Maybe due to symlink
			return plugins_url().'/'.basename(dirname(__FILE__)).'/';
		}
		// Normal case (non symlink)
		return plugin_dir_url( __FILE__ );
	}
	
}

// Global accessor function to singleton
function BasicAllInOneIntranet() {
	return aioi_basic_all_in_one_intranet::get_instance();
}

// Initialise at least once
BasicAllInOneIntranet();

?>