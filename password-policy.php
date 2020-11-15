<?php
/*
Plugin Name: Password policy
Plugin URI: https://github.com/mehdiAberkane/wordpress-password-policy
Description: A plugin for enfance the password policy for each users.
Version: 1.0.0
Author: Mehdi Aberkane
Author URI: https://mehdi-aberkane.fr
Text Domain: password-policy
*/

define( 'PASSWORD_POLICY_VERSION', '1.0.0' );
define( 'PASSWORD_POLICY__MINIMUM_WP_VERSION', '4.0' );
define( 'PASSWORD_POLICY__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PASSWORD_POLICY_DELETE_LIMIT', 100000 );
define( 'PASSWORD_POLICY_VERSION__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

global $wpdb;
global $password_policy_config;
global $db;

if (is_admin()) {
	require_once( PASSWORD_POLICY_VERSION__PLUGIN_DIR . 'src/admin.php' );
}

require_once( PASSWORD_POLICY_VERSION__PLUGIN_DIR . 'src/class.db.php' );

$db = new PSSP_Db($wpdb);

$password_policy_config = $db->get_config();

/**
 * Function trigger when plugin is enabled
 * Generate default config in db
 * 
 */
function password_policy_plugin_activation() {
	global $db;

	$arr = ['option_name' => '_password_policy_config', 'option_value' => json_encode(array('weak-password' => 'true', 'number-characters' => 8
	, 'enable-special' => 'true', 'enable-number' => 'true', 
	'enable-uppercase' => 'true', 'regex-password-option' => false,
	'regex-password' => base64_encode('^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$'))), 'autoload' => false];

	$db->set_config($arr);
}

/**
 * Function trigger when plugin is disabled
 * He delete all config in db
 * 
 */
function password_policy_plugin_deactivation() {
	global $db;
	$db->drop_config();
}

register_activation_hook( __FILE__, 'password_policy_plugin_activation' );
register_deactivation_hook( __FILE__, 'password_policy_plugin_deactivation' );
require_once( PASSWORD_POLICY_VERSION__PLUGIN_DIR . 'src/check_password_policy.php' );

/**
 * For CSS in BackOffice
 */
function pssp_admin_css() {
	$admin_handle = 'admin_css';
	$admin_stylesheet = plugin_dir_url( __FILE__ ) . 'public/style.css';

	wp_enqueue_style($admin_handle, $admin_stylesheet);
}

add_action('admin_print_styles', 'pssp_admin_css', 11);

/**
 * Enqueue a script in the WordPress admin, excluding edit.php.
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function pssp_wpdocs_selectively_enqueue_admin_script($hook) {
	if ('user-edit.php' == $hook OR 'user-new.php' == $hook OR 'profile.php' == $hook OR 'wp-login.php?action=rp' == $hook) {
		wp_enqueue_script( 'password-policy-script', plugin_dir_url( __FILE__ ) . 'public/password-policy-script.js', array(), '1.0');
	}
}

/**
 * Disable the checkbox for weak password wordpress
 */
if ($password_policy_config['weak-password']) {
	add_action( 'admin_enqueue_scripts', 'pssp_wpdocs_selectively_enqueue_admin_script' );
}
