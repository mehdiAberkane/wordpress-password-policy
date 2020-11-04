<?php
/*
 * Display admin page in backoffice
*/
add_action('admin_menu', 'pssp_add_admin_page');

session_start();

include_once('class.token.php');
include_once('class.db.php');

/**
 * Hook admin page
 */
function pssp_add_admin_page() {
	add_menu_page( 'Password Policy', 'Password Policy', 'manage_options', 'password-policy', 'pssp_display_config_page' );

	add_submenu_page( 'password-policy', __('Listes des utilisateurs', 'password-policy'), __('Listes des utilisateurs', 'password-policy'),'manage_options', 'pssp_display_page_user', 'pssp_display_page_user');
}

/**
 * Return all roles
 */
function pssp_get_all_roles() {
	global $wp_roles;
	$list_roles = [];

	foreach($wp_roles->roles as $r) {
		array_push($list_roles, $r['name']);
	}

	return $list_roles;
}

/**
 * Check and sanitized the data
 */
function pssp_check_config($data) {

	$config_sanitized = [];

	if (!is_int(intval($_POST['number-characters'])))
		return false;

	$password_length = filter_var($_POST['number-characters'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
	
	$config_sanitized['number-characters'] = intval($password_length);
	
	if ($_POST['weak-password'] == 'true') {
		$config_sanitized['weak-password'] = true;
	} else {
		$config_sanitized['weak-password'] = NULL;
	}

	if ($_POST['regex-password-option'] == 'true') {
		$config_sanitized['regex-password-option'] = true;
	} else {
		$config_sanitized['regex-password-option'] = NULL;
	}

	if ($_POST['enable-uppercase'] == 'true') {
		$config_sanitized['enable-uppercase'] = true;
	} else {
		$config_sanitized['enable-uppercase'] = NULL;
	}

	if ($_POST['enable-number'] == 'true') {
		$config_sanitized['enable-number'] = true;
	} else {
		$config_sanitized['enable-number'] = NULL;
	}

	if ($_POST['enable-special'] == 'true') {
		$config_sanitized['enable-special'] = true;
	} else {
		$config_sanitized['enable-special'] = NULL;
	}

	if (is_string($_POST['regex-password'])) {
		$config_sanitized['regex-password'] = $_POST['regex-password'];
	} else {
		$config_sanitized['regex-password'] = base64_encode('^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$');
	}

	return $config_sanitized;
}

/**
 * Display config page for password policy
 */
function pssp_display_config_page() {
	global $wpdb;
	global $locale;

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_admin() && isset($_POST['securite_nonce'])) {

		if ($_POST['token'] === $_SESSION['token'] && wp_verify_nonce($_POST['securite_nonce'], 'securite-nonce')) {
			$config_sanitized = pssp_check_config($_POST);
			if ($config_sanitized) {
				$arr = ['option_name' => '_password_policy_config', 'option_value' => json_encode(array('weak-password' => $config_sanitized['weak-password'], 'number-characters' => $config_sanitized['number-characters']
				, 'enable-special' => $config_sanitized['enable-special'], 'enable-number' => $config_sanitized['enable-number'], 
				'enable-uppercase' => $config_sanitized['enable-uppercase'], 'regex-password-option' => $config_sanitized['regex-password-option'],
				'regex-password' => $config_sanitized['regex-password'])), 'autoload' => false];
				
				$db = new PSSP_Db($wpdb);
				$db->update_config($arr);

				?>
				<div class="notice notice-success is-dismissible">
					<p><?php _e( 'Your settings have been updated', 'password-policy' ); ?></p>
				</div>
				<?php
			} else {
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php _e( 'Error form invalide', 'password-policy' ); ?></p>
				</div>
				<?php
			}
		} else {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php _e( 'Error token invalide', 'password-policy' ); ?></p>
			</div>
			<?php
		}
	}

	
	$pw_config = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}options WHERE option_name = '_password_policy_config'", OBJECT );
	$val = $pw_config[0]->option_value;

	$data_config = json_decode($pw_config[0]->option_value, true);

	$data_config['number-characters'] = filter_var($data_config['number-characters'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));

	$token = New PSSP_Token();
	$_SESSION['token'] = $token->display_token();

	$url_doc = "https://blog.usejournal.com/regular-expressions-a-complete-beginners-tutorial-c7327b9fd8eb";

	switch($locale) {
		case "fr_FR":
			$url_doc = "https://www.lucaswillems.com/fr/articles/25/tutoriel-pour-maitriser-les-expressions-regulieres";
			break;
	}

	include('views/view_config.php');
}

/**
 * Check if user has role
 */
function pssp_has_role($roles, $user) {
	$check_role = array_intersect(array_map('strtolower', $user->roles), array_map('strtolower', $roles));

	if ($check_role) {
		return true;
	}

	return false;
}

/**
 * Send email for reset user password
 */
function pssp_retrieve_password_password_policy($user_data) {
	$errors 	= new WP_Error();

	// Redefining user_login ensures we return the right case in the email.
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key        = get_password_reset_key( $user_data );

	if ( is_multisite() ) {
		$site_name = get_network()->site_name;
	} else {
		/*
		 * The blogname option is escaped with esc_html on the way into the database
		 * in sanitize_option we want to reverse this for the plain text arena of emails.
		 */
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	$message = __( 'Your admin has requested a password reset for the following account:' ) . "\r\n\r\n";
	/* translators: %s: Site name. */
	$message .= sprintf( __( 'Site Name: %s' ), $site_name ) . "\r\n\r\n";
	/* translators: %s: User login. */
	$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
	$message .= __( 'To reset your password, visit the following address:' ) . "\r\n\r\n";
	$message .= network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n";

	/* translators: Password reset notification email subject. %s: Site title. */
	$title = sprintf( __( '[%s] Password Reset' ), $site_name );

	if ( $message && ! wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
		return false;
	}

	return true;
}

/**
 * GET: Display users page 
 * POST: Reset mdp user
 */
function pssp_display_page_user() {

	// get_users wp function
	$users = get_users();

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if ($_POST['token'] === $_SESSION['token']) {

			$role_name = [];
			$users_id = [];

			if (isset($_POST['role_name']) && array_walk($_POST['role_name'], 'sanitize_text_field'))
				$role_name = $_POST['role_name'];

			if (isset($_POST['users_id']) && array_walk($_POST['users_id'], 'intval')) 
				$users_id = $_POST['users_id'];

			$validor = false;
			foreach ( $users as $user ) {
				$has_role = false;
				$has_role = pssp_has_role($role_name, $user);

				if ($_POST['full-reset'] || in_array($user->id, $users_id) || $has_role) {
					$validor = pssp_retrieve_password_password_policy($user);
				}
			}

			if ($validor) {

				?>
				<div class="notice notice-success is-dismissible">
					<p><?php _e( 'Email have be send to users', 'password-policy' ); ?></p>
				</div>
				<?php

			} else {
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php _e( 'A error with wp_mail function has come', 'password-policy' ); ?></p>
				</div>
				<?php
			}

		} else {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php _e( 'Error token invalide', 'password-policy' ); ?></p>
			</div>
			<?php
		}
	}

	$token = New PSSP_Token();
	$_SESSION['token'] = $token->display_token();

	include('views/view_admin.php');
}
