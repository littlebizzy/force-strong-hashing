<?php
/*
Plugin Name: Force Strong Hashing
Plugin URI: https://www.littlebizzy.com
Description: Forces all user passwords generated by WordPress to be hashed using Bcrypt, the most secure and popular PHP hashing algorithm currently available.
Version: 1.0.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
Text Domain: force-strong-hashing
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages
*/

/**
 * Define main plugin class
 */
class LBizzy_Force_Strong_Hashing {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Error message
	 *
	 * @var null
	 */
	public $system_error_message = null;

	/**
	 * Initalize plugin actions
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'init', array( $this, 'lang' ), 0 );

		if ( ! function_exists( 'password_hash' ) ) {

			$this->system_error_message = esc_html__( 'Your current system configuration does support password hashing with password_hash() function. Please upgrade your PHP version to PHP 5.5 or later, or disable the "Force Strong Hashing" plugin.', 'force-strong-hashing' );

			add_action( 'admin_notices', array( $this, 'system_error' ) );
			return false;
		}

		if ( $this->is_rewritten() ) {

			$this->system_error_message = esc_html__( 'Another plugin has already overridden the password hashing mechanism. The "Force Strong Hashing" plugin will not work.', 'force-strong-hashing' );

			add_action( 'admin_notices', array( $this, 'system_error' ) );
			return false;
		}

		require plugin_dir_path( __FILE__ ) . 'includes/functions.php';

	}

	/**
	 * Chek if required functions was already rewritten
	 * @return boolean [description]
	 */
	public function is_rewritten() {

		$func_list = array(
			'wp_check_password',
			'wp_hash_password',
			'wp_set_password',
		);

		$rewritten = false;

		foreach ( $func_list as $function ) {
			if ( function_exists( $function ) ) {
				$rewritten = true;
			}
		}

		return $rewritten;
	}

	/**
	 * Load text domain
	 *
	 * @return void
	 */
	public function lang() {
		load_plugin_textdomain( 'force-strong-hashing', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Show plugin system notices
	 *
	 * @return void
	 */
	public function system_error() {
		printf( '<div class="notice notice-error"><p>%s</p></div>', $this->system_error_message );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

/**
 * Returns instance of LB_Force_Strong_Hashing class
 *
 * @return object
 */
function lbizzy_force_strong_hashing() {
	return LBizzy_Force_Strong_Hashing::get_instance();
}

lbizzy_force_strong_hashing()->init();
