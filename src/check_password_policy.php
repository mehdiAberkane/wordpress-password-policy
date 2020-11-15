<?php

/**
 * Check if the password is correct by the policy
 */
function pssp_check_password_policy($errors, $update, $user) {
	global $password_policy_config;

	$password = $user->user_pass;

	if ($password_policy_config['number-characters'] > strlen($password)) {
		$errors->add('check_password_policy',sprintf(__('The password need %s caracteres', $password_policy_config['number-characters']), $password_policy_config['number-characters']));
	}

	if ($password_policy_config['enable-special']) {
		if (!preg_match('/[\'^£$%&*()}{@#~!?><>,|=_+¬-]/', $password)) {
			$errors->add('check_password_policy',sprintf(__('The password need one special characters')));
		}
	}

	if ($password_policy_config['enable-number']) {
		preg_match_all('/[0-9]/', $password, $matche_number);

		if (count($matche_number[0]) == 0) {
			$errors->add('check_password_policy',sprintf(__('The password need one numeric characters')));
		}
	}

	if ($password_policy_config['enable-uppercase']) {
		preg_match_all('/[A-Z]/', $password, $matche_uppercase);

		if (count($matche_uppercase[0]) == 0) {
			$errors->add('check_password_policy',sprintf(__('The password need one uppercase characters')));
		}
	}

	if ($password_policy_config['regex-password-option']) {
        $regex = base64_decode($password_policy_config['regex-password']);
        $regex = '"' . $regex . '"';
        
		preg_match_all($regex, $password, $matche_regex);
		
		if (count($matche_regex[0]) == 0) {
			$errors->add('check_password_policy',sprintf(__('The password need to match to some logic')));
		}
	}
}

add_action('user_profile_update_errors', 'pssp_check_password_policy', 10, 3);

