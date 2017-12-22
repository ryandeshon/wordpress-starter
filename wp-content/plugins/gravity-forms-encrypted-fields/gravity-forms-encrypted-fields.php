<?php
/**
 * @package   Gravity_Forms_Encrypted_Fields
 * @author    PluginOwl <info@pluginowl.com>
 * @link      https://codecanyon.net/user/pluginowl/portfolio
 * @Copyright: 2016 Neil Rowe
 *
 * @wordpress-plugin
 * Plugin Name: Gravity Forms Encrypted Fields
 * Plugin URI: https://codecanyon.net/item/gravity-forms-encrypted-fields/18564931
 * Description: Extends Gravity Forms with powerful encryption/decryption and data protection controls and tools.
 * Version: 3.7
 * Author: Plugin Owl
 * Author URI: https://codecanyon.net/user/pluginowl/portfolio
 * Copyright: 2016 Neil Rowe
 */
 
 
// Prevent direct access to this file.
if (!defined('ABSPATH')) {
    header('HTTP/1.0 403 Forbidden');
    echo '404 File Not Found';
    exit; // Exit if accessed directly
}
//redundancy with alt instruction
defined('ABSPATH') or die();

// Set variable for admin page url
define('GFEF_ADMIN', admin_url('options-general.php?page=gravity-forms-encrypted-fields'));

// Load salt
include_once("includes/salt.php");

// Load stylesheet -disabled for now due to injection possibility- keep styles simple and local
/*function wp_gfe_enqueue_scripts(){
	if (isset($_GET['page']) && $_GET['page'] == 'gravity-forms-encrypted-fields') {
		wp_register_style('gfestyle', plugin_dir_url( __FILE__ ) . 'includes/gfe.css');
		//wp_register_style( 'gfestyle', plugins_url( 'gravity-forms-encrypted-fields/includes/gfe.css' ) );
		wp_enqueue_style('gfestyle');
	}
}
add_action('admin_print_styles', 'wp_gfe_enqueue_scripts');*/


// Create the salt key
function gfef_create_salt($seed) {
	$salt 					= md5(wp_salt('nonce'));
	if ($seed != '' || $seed !=  null || $seed != false) {
		$salt 				= $seed;
		$file 				= file_get_contents("". plugin_dir_path(__FILE__) . "/includes/salt.php");
		$file 				= '<?php
		// This unique salt is generated from your wordpress security keys.
		// Prevent direct access to this file.
		defined( "ABSPATH" ) or die();
		
		function gfef_get_salt() {
			$salt = "' . $seed . '";
			if (strpos($salt, "%%SALT%%")) {
				$salt = false;
			} 
			return $salt;
		}';
		file_put_contents("". plugin_dir_path(__FILE__) . "/includes/salt.php", $file);
		delete_option('gfe_website_key');
		delete_site_option('gfe_website_key');
	} else {
		$file 				= file_get_contents("". plugin_dir_path(__FILE__) . "/includes/salt.php");
		$file 				= str_replace("[%%SALT%%]", $salt, $file);
		file_put_contents("" . plugin_dir_path(__FILE__) . "/includes/salt.php", $file);
	}
	return $salt;
}

// Echo the salt key
function gfef_echo_salt() {
	$salt 					= md5(wp_salt('nonce'));
	echo $salt;
}

// GET/CREATE KEY
function gfef_get_key($gfe_key, $operation) {
	$gfe_key_override 		= esc_html__(get_option('gfe_encryption_key_override'));
	if ($operation) {
		if ($operation === 'decrypt') {
			if ($gfe_key_override) {
				$gfe_key = md5($gfe_key_override);
			}
		}
	}
	$password 				= "!c64l?" . trim($gfe_key) . "h4s?09aq-p3x";
	$salt 					= gfef_get_salt() === false ? gfef_create_salt(false) : gfef_get_salt();
	$key 					= md5(hash('SHA256', $salt . $password, true));
	
	return $key;
}

// OPEN SSL Encrypt
function gfef_ssl_encrypt($text, $creatinguser, $key) {
    $ssl_cipher_name = "AES-256-CBC";
	$gfef_search_key = substr(hash('sha256', substr(hash('sha256', md5(substr($key, 12, 4) . $text)),24, 6) . substr(hash('sha256', md5(substr($key, 30, 10) . $text)),54, 6)), 33, 10);
    $key 			 = hash('sha256', $key);
	$iv_size		 = openssl_cipher_iv_length($ssl_cipher_name);  // iv for AES-256-CBC = 16 bytes
	$iv 			 = substr(hash('sha256', base64_encode(openssl_random_pseudo_bytes($iv_size))), 0, $iv_size);
    $text 			 = 'GFEncrypt: ' . $iv . $gfef_search_key . trim(base64_encode(openssl_encrypt($creatinguser . $text, $ssl_cipher_name, $key, 0, $iv)));
	
    return $text;
}

// OPEN SSL Decrypt
function gfef_ssl_decrypt($text, $key) {
    $ssl_cipher_name = "AES-256-CBC";
    $key 			 = hash('sha256', $key);
	$iv_size 		 = openssl_cipher_iv_length($ssl_cipher_name);  // iv for AES-256-CBC = 16 bytes
	$text_decode 	 = substr($text, 11);
    $iv 			 = substr($text_decode, 0, $iv_size);
	$text_decode 	 = substr($text_decode, $iv_size + 10);
    $text			 = trim(openssl_decrypt(base64_decode($text_decode), $ssl_cipher_name, $key, 0, $iv));

    return $text;
}

// Encrypt
function gfef_encrypt($text, $creatinguser, $key = null) {
	$use_mcrypt 			= apply_filters('gform_use_mcrypt', function_exists( 'mcrypt_encrypt'));
	$use_openssl			= function_exists('openssl_encrypt') && extension_loaded('openssl');
	$gfe_key 				= false;
	if (get_option('gfe_encryption_key')) {
		$gfe_key 				= md5(esc_html__(get_option('gfe_encryption_key')));
	}
	$gfe_bypass				= esc_html__(get_option('gfe_encryption_bypass'));
	$gfe_type			 	= get_option('gfe_encryption_method');
	$key 					= gfef_get_key($gfe_key, 'encrypt');
	$gfef_search_key 		= substr(hash('sha256', substr(hash('sha256', md5(substr($key, 12, 4) . $text)),24, 6) . substr(hash('sha256', md5(substr($key, 30, 10) . $text)),54, 6)), 33, 10);
	
	if ($creatinguser) {
		$creatinguser = 'GFEFU[[[' . $creatinguser . ']]]GFEFU';
	}
	
	if ($gfe_type == 2 && $use_mcrypt && $gfe_key && !$gfe_bypass) {
		$mcrypt_cipher_name = MCRYPT_RIJNDAEL_128;
		$iv_size            = mcrypt_get_iv_size($mcrypt_cipher_name, MCRYPT_MODE_CBC);
		//$iv 				= mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$iv					= substr(hash('sha256', base64_encode(mcrypt_create_iv($iv_size, MCRYPT_RAND))), 0, $iv_size);
		$encrypted_value 	= 'GFEncrypt: ' . $iv . $gfef_search_key . trim(base64_encode(mcrypt_encrypt($mcrypt_cipher_name, $key, $creatinguser . $text, MCRYPT_MODE_CBC, $iv)));
	} else if ($gfe_type == 1 && $gfe_key && $use_openssl && !$gfe_bypass) {
		$encrypted_value	= gfef_ssl_encrypt($text, $creatinguser, $key);
	} else {
		$encrypted_value 	= $text;
	}
	return $encrypted_value;
}

// Decrypt
function gfef_decrypt($text, $key = null) {
	$use_mcrypt 			= apply_filters('gform_use_mcrypt', function_exists( 'mcrypt_decrypt'));
	$use_openssl			= function_exists('openssl_encrypt') && extension_loaded('openssl');
	$gfe_type			 	= get_option('gfe_encryption_method');
	$gfe_key 				= false;
	if (get_option('gfe_encryption_key')) {
		$gfe_key 			= md5(esc_html__(get_option('gfe_encryption_key')));
	}
	$secure_key 			= substr($text, 0, 11);
	$key 					= gfef_get_key($gfe_key, 'decrypt');
	
	if ($secure_key === 'GFEncrypt: ') {
		$secure_key = 'locked';
	} else {
		$secure_key = 'unlocked';
	}
	
	if ($gfe_type == 2 && $use_mcrypt && $gfe_key && $secure_key === 'locked') {
		$mcrypt_cipher_name = MCRYPT_RIJNDAEL_128;
		$iv_size            = mcrypt_get_iv_size($mcrypt_cipher_name, MCRYPT_MODE_CBC);
		$text_decode 	 	= substr($text, 11);
		$iv 			 	= substr($text_decode, 0, $iv_size);
		$text_decode 	 	= base64_decode(substr($text_decode, $iv_size + 10));
		$decrypted_value 	= trim(mcrypt_decrypt($mcrypt_cipher_name, $key, $text_decode, MCRYPT_MODE_CBC, $iv));
	} else if ($gfe_type == 1 && $gfe_key && $use_openssl && $secure_key === 'locked') {
		$decrypted_value	= gfef_ssl_decrypt($text, $key);
	} else {
		$decrypted_value 	= $text;
	}
	return $decrypted_value;
}

/*GET RAW ENTRY FIELD VALUE*/
function gfef_get_raw_field_value($entry_id, $field_id) {
	global $wpdb;
	$gfef_raw_detail_table_name = GFFormsModel::get_lead_details_table_name();
	$gfef_raw_table_name        = GFFormsModel::get_lead_table_name();
	$gfef_raw_entries = $wpdb->get_results($wpdb->prepare("  SELECT l.*, field_number, value FROM $gfef_raw_table_name l INNER JOIN $gfef_raw_detail_table_name ld ON l.id = ld.lead_id WHERE l.id=%d", $entry_id));
	if (is_array($gfef_raw_entries) && count($gfef_raw_entries) > 0) {
		foreach($gfef_raw_entries as $gfef_raw_entry) {
			$field_raw_id	 = strval($gfef_raw_entry->field_number);
			$field_raw_value = $gfef_raw_entry->value;
			if ($field_id == $field_raw_id) {
				return $field_raw_value;
			}
		}
	} else {
		return false;
	}
}

/*GET USER OWNED FIELD VALUE FROM RAW OR DECRYPTED VALUE*/
function gfef_get_user_owned_value($value) {
	$gfe_current_user 			= wp_get_current_user();
	$gfe_current_username		= $gfe_current_user->user_login;
	$user_logged_in				= is_user_logged_in() ? TRUE : false;
	if ($user_logged_in) {
		$user_exctract			= gfef_decrypt($value);
		$owner_value			= explode(']]]GFEFU', $user_exctract);
		$user_owner		 		= $owner_value[0];
		$user_owner 			= explode('GFEFU[[[', $user_owner);
		$user_owner		 		= $user_owner[1];
		$owner_value			= $owner_value[1];

		if (md5($gfe_current_username . get_current_user_id()) === $user_owner) {
			$value				= $owner_value;
		} else {
			$value					= esc_html__(get_option('gfe_restricted'));
		}
	} else {
		$value					= esc_html__(get_option('gfe_restricted'));
	}
	return $value;
}

/*FIELD OUTPUT MASKING*/
function gfef_check_masks($form, $field, $data, $display){
	$mask_form 	= null;
	$mask_field = null;
	$mask_first = null;
	$mask_last 	= null;
	$masks 		= get_option('gfe_masking');
	$masks 		= explode(',', $masks);
	$raw_data 	= $data;
	
	foreach ($masks as $mask) {
		$mask 			= trim($mask);
		$mask_format 	= explode(':', $mask);
		if (count($mask_format) < 4 || count($mask_format) > 6){
			return false;
		} else {
			$mask_form 	= trim($mask_format[0]);
			$mask_field = trim($mask_format[1]);
			$mask_first = trim($mask_format[2]);
			$mask_last 	= trim($mask_format[3]);
			if ($form == $mask_form && $field == $mask_field) {
				$data_first = substr($data, 0, $mask_first);
				$data_last 	= substr($data, -$mask_last);
				if ($mask_last == '0') {
					$data_last 	= null;
				}
				$data = $data_first . '***' . $data_last;
				if (!in_array('M', $mask_format, TRUE) && $display == 'M') {
					return false;
				}
				if (in_array('F', $mask_format, TRUE)) {
					$data = $raw_data;
				} 
				return $data;
			}
		}
	}
}

/*GFORM FIELD DATA ENCRYPTION LOGIC*/
add_filter('gform_save_field_value', 'gfef_encrypt_field_value', 99999999, 4);
function gfef_encrypt_field_value($value, $lead, $field, $form) {
	if ($value || $value === 0 || $value === '0') {
		$decrypt 				= false;
		$gfe_current_user 		= wp_get_current_user();
		$encrypt_decrypt_user	= $gfe_current_user->user_login;
		$form_id				= rgar($form, 'id');
		
		if (is_admin() && $form_id == get_option('gfe_encrypt_decrypt_form') && $encrypt_decrypt_user == get_option('gfe_encrypt_decrypt_user') && isset($_GET['page']) && $_GET['page'] == 'gravity-forms-encrypted-fields'){
			$decrypt = true;
		}
		
		if ($field->encryptField == true && $field->hidevalueField == false && !get_option('gfe_encryption_bypass') && !$decrypt) {
				if ($field->gfedecryptownerField == true && is_user_logged_in() && (get_current_user_id() != 0)) {
					$gfe_created_user 			= null;
					$gfe_created_user_id        = null;
					if (!rgar($lead, 'created_by')) {
						$gfe_created_user_id	= get_current_user_id();
						if ($gfe_created_user_id != 0){
							$gfe_current_user 	= wp_get_current_user();
							$gfe_created_user	= $gfe_current_user->user_login;
						}
					} else {
						//$gfe_created_user_id	= $lead['created_by'];
						$gfe_created_user_id	= rgar($lead, 'created_by');
						$gfe_created_user		= get_user_by('id', $gfe_created_user_id);
						$gfe_created_user		= $gfe_created_user->user_login;
					}
					$gfe_crypt_user 			= md5($gfe_created_user . $gfe_created_user_id);
					return gfef_encrypt($value, $gfe_crypt_user);
				} else {
					return gfef_encrypt($value, false);
				}
		} else {
			return($value);
		}
	} else {
		return($value);
	}
}

/*GFORM FIELD DATA DECRYPTION LOGIC*/
add_filter('gform_get_input_value', 'gfef_decrypt_field', 10, 4);
function gfef_decrypt_field($value, $entry, $field, $input_id) {
	$db_value						= $value;
	if (get_option('gfe_show_encryption')) {
		$secure_key 				= substr($db_value, 0, 11);
		if ($secure_key === 'GFEncrypt: ') {
			return $db_value;
		} 
	}
	if ($value || $value === 0 || $value === '0') {
		$form_id					= $entry['form_id'];
		$field_id					= $field->id;
		$permmision_granted 		= 'no';
		$key						= null;
		$gfe_user_role				= false;
		$gfe_current_user 			= wp_get_current_user();
		$gfe_current_username		= $gfe_current_user->user_login;
		$gfe_current_user_roles		= $gfe_current_user->roles;
		$gfe_users_list 			= $field->decryptFieldUsers;
		$gfe_user_access_list 		= array_map('trim', explode(",", $gfe_users_list));
		$gfe_user_block_list 		= esc_html__(get_option('gfe_user_lockout_list'));
		$gfe_user_block_access_list = array_map('trim', explode(",", $gfe_user_block_list));
		$gfe_user_allow_list 		= esc_html__(get_option('gfe_user_lockout_override_list'));
		$gfe_user_allow_access_list = array_map('trim', explode(",", $gfe_user_allow_list));
		$gfe_user_limit_list 		= esc_html__(get_option('gfe_limit_user_view_permission_list'));
		$gfe_user_limit_access_list = array_map('trim', explode(",", $gfe_user_limit_list));
		$secure_key 				= substr($value, 0, 11);
		$user_owned 				= substr(gfef_decrypt($value), 0, 8);
		$user_access				= false;
		$owner_value				= null;
		$gfe_admin_only				= false;
		$user_logged_in				= is_user_logged_in() ? TRUE : false;
		$encrypt_decrypt			= false;
		
		if (is_admin() && get_option('gfe_encrypt_decrypt') && get_option('gfe_encrypt_decrypt_form') && $gfe_current_username == get_option('gfe_encrypt_decrypt_user') && isset($_GET['page']) && $_GET['page'] == 'gravity-forms-encrypted-fields') {
			$encrypt_decrypt		= true;
		}
		
		if ($secure_key === 'GFEncrypt: ') {
			$key = 'locked';
		} else {
			$key = 'unlocked';
		}
		
		if ($user_owned === 'GFEFU[[[') {
			$user_owned				= TRUE;
			if ($user_logged_in) {
				$user_exctract			= gfef_decrypt($value);
				$owner_value			= explode(']]]GFEFU', $user_exctract);
				$user_owner		 		= $owner_value[0];
				$user_owner 			= explode('GFEFU[[[', $user_owner);
				$user_owner		 		= $user_owner[1];
				$owner_value			= $owner_value[1];

				if (md5($gfe_current_username . get_current_user_id()) === $user_owner) {
					$user_access		= TRUE;
					
				}
			}
		} else if ($user_owned !== 'GFEFU[[[') {
			$user_owned				= false;
		}
		
		foreach ($gfe_user_access_list as $role) {
			if (in_array($role, $gfe_current_user_roles, TRUE)) {
				$gfe_user_role = true;
			}
		}
		
		foreach ($gfe_user_limit_access_list as $role) {
			if (in_array($role, $gfe_current_user_roles, TRUE)) {
				$gfe_user_role_limit_access_list = true;
			}
		}
		
		if (get_option('gfe_admin_only') && $user_logged_in && is_admin()) {
			$gfe_admin_only = TRUE;
		} else if (!get_option('gfe_admin_only') && $user_logged_in) {
			$gfe_admin_only = TRUE;
		}			
	
		//PERMISSIONS FILTERING 
		if (((((($gfe_users_list == '' || $gfe_users_list ==  null) || (in_array($gfe_current_username, $gfe_user_access_list, TRUE) || $gfe_user_role) && !in_array('lockdown', $gfe_user_access_list, TRUE)) && (($gfe_user_limit_list == '' || $gfe_user_limit_list == null) || in_array($gfe_current_username, $gfe_user_limit_access_list, TRUE) || $gfe_user_role_limit_access_list || ($gfe_users_list == '' || $gfe_users_list ==  null))) && !in_array($gfe_current_username, $gfe_user_block_access_list, TRUE) && !in_array('lockdown', $gfe_user_block_access_list, TRUE)) || in_array($gfe_current_username, $gfe_user_allow_access_list, TRUE)) && $user_logged_in && $gfe_admin_only && !$user_owned) {
			$permmision_granted = 'yes';
		} else if ($user_owned && $user_access && $gfe_admin_only) {
			$permmision_granted = 'yes';
			$value 				= $owner_value;
		} else {
			$permmision_granted = 'no';
		}
		if (/*$field->encryptField == true  &&*/ $permmision_granted == 'yes' && $key === 'locked') {
			if ($encrypt_decrypt && isset($_GET['page']) && $_GET['page'] == 'gravity-forms-encrypted-fields') {
				return($db_value);
			} else {
				return gfef_decrypt($value);
			}
		} else if (/*$field->encryptField == true &&*/ $permmision_granted == 'no' && $key === 'locked'){
			if ($encrypt_decrypt && isset($_GET['page']) && $_GET['page'] == 'gravity-forms-encrypted-fields') {
				return($db_value);
			}
			if (get_option('gfe_masking') && $_GET['page'] != 'gravity-forms-encrypted-fields' && !$user_owned) {
				$temp_value 		= gfef_decrypt($value);
				$masked_value 		= gfef_check_masks($form_id, $field_id, $temp_value, 'W');
				$field_user_owned 	= $field->gfedecryptownerField;
				if ($masked_value && !$field_user_owned) {
					return $masked_value;
				} else {
					return (esc_html__(get_option('gfe_restricted')));
				}
			} else {
				return (esc_html__(get_option('gfe_restricted')));
			}
		} else if (($field->hidevalueField == true || $field->encryptField == true) && $permmision_granted == 'no' && $key === 'unlocked'){
			if ($encrypt_decrypt && isset($_GET['page']) && $_GET['page'] == 'gravity-forms-encrypted-fields') {
				return($db_value);
			}
			if (get_option('gfe_masking') && $_GET['page'] != 'gravity-forms-encrypted-fields' && !$user_owned) {
				$temp_value 		= gfef_decrypt($value);
				$masked_value 		= gfef_check_masks($form_id, $field_id, $temp_value, 'W');
				$field_user_owned 	= $field->gfedecryptownerField;
				if ($masked_value && !$field_user_owned) {
					return $masked_value;
				} else {
					return (esc_html__(get_option('gfe_hidevalue')));
				}
			} else {
				return (esc_html__(get_option('gfe_hidevalue')));
			}
		} else {
			return($value);
		}
	} else {
		return($value);
	}
}

/*ENCRYPT/DECRYPT ADMIN OPTIONS*/
//admin decrypt
function gfef_decrypt_entries_process($entries, $fields, $form){
	echo('<p><b>DECRYPTION REPORT <img src="' . plugin_dir_url(__FILE__) . 'images/unlocked.png" height="12" width="12"> :</b><p><p><b>Form ID:</b> ' . get_option('gfe_encrypt_decrypt_form') . '</p><p><b>Processed User Owned Fields Status:</b> Decrypted and User Ownership Removed.</p><p>Below is a list of processed entries and the fields that were decrypted for that entry.<br/>Field IDs with decimal places represent multi part fields.<br/><b>Entry ID:</b> Field IDs</p>');
	if (!$fields && !get_option('gfe_encrypt_decrypt_form_entry_paging')) {
		echo('<p>No "FIELD IDs" were specified and the "MAX ENTRIES PER RUN" has been left blank and defaulted to 0 entries to process.<br/>When using the "Encrypt/Decrypt Form Entries" tool you must either specify ENTRY IDs to be processed or specify the "MAX ENTRIES PER RUN". </p>');
	}
	
	foreach($entries as $entry) {
		$value	  		= null;
		$entry_id 		= rgar($entry, 'id');
		$form_id		= rgar($entry, 'form_id');
		
		if ($form_id == $form){
			echo('<p><b>' . $entry_id . '</b>');
			echo(': ');

			foreach($entry as $key => $value) {
				$decyptable_field = false;
				if (substr($value, 0, 11) === 'GFEncrypt: ') {
					$value 			= gfef_decrypt($value);
					$user_owned 	= substr($value, 0, 8);
					if ($user_owned === 'GFEFU[[[') {
						$value	= explode(']]]GFEFU', $value);
						$value	= $value[1];
					}
					$decyptable_field = true;
				}

				if (!$fields){
					$entry[$key] = $value;
					if ($decyptable_field === true){
						echo($key . ', ');
					}
				} 
				if ($fields){
					if (strstr($key, '.')){
						$key_base 	 = explode('.', $key);
						$key_base 	 = $key_base[0];
						if (in_array($key_base, $fields)){
							$entry[$key] = $value;
							if ($decyptable_field === true){
								echo($key . ', ');
							}

							//GFAPI::update_entry_field($entry_id, $key_base, $value);
						}
					} else {
						if (in_array($key, $fields)){
							$entry[$key] = $value;
							if ($decyptable_field === true){
								echo($key . ', ');
							}
							//GFAPI::update_entry_field($entry_id, $key, $value);
						}
					}
				}
			}
			GFAPI::update_entry($entry);
			echo('</p>');
		}
	}
}

//admin encrypt
function gfef_encrypt_entries_process($entries, $fields, $form){
	$encryptable_fields	= array();
	$encryption_on		= array();
	$user_owned_fields	= array();
	echo('<p><b>ENCRYPTION REPORT <img src="' . plugin_dir_url(__FILE__) . 'images/locked.png" height="12" width="12"> :</b><p><p><b>Form ID:</b> ' . get_option('gfe_encrypt_decrypt_form') . '</p>');
	if (!$fields && !get_option('gfe_encrypt_decrypt_form_entry_paging')) {
		echo('<p>No "FIELD IDs" were specified and the "MAX ENTRIES PER RUN" has been left blank and defaulted to 0 entries to process.<br/>When using the "Encrypt/Decrypt Form Entries" tool you must either specify ENTRY IDs to be processed or specify the "MAX ENTRIES PER RUN". </p>');
	}
	
	foreach($entries as $entry) {
		$value	  			= null;
		$entry_id 			= rgar($entry, 'id');
		$form_id			= rgar($entry, 'form_id');
		$form_object		= GFAPI::get_form($form_id);
		$user_original		= rgar($entry, 'created_by');
		
		if ($form_id == $form){
			if (empty($encryptable_fields)){
				echo('<p><b>Encryptable Field IDs: </b> ');
				foreach ($form_object['fields'] as $field) {
				   if ($field->type === 'text' || $field->type === 'textarea' || $field->type === 'date' || $field->type === 'name' || $field->type === 'number' || $field->type === 'email' || $field->type === 'phone' || $field->type === 'website' || $field->type === 'address' || $field->type === 'select' || $field->type === 'radio' || $field->type === 'multiselect' || $field->type === 'checkbox') {
					   array_push($encryptable_fields, $field->id);
					   echo($field->id . ', ');
				   }
				}
			}
			if (empty($encryption_on)){
				echo('<p><b>Encryption is Turned ON for Field IDs: </b> ');
				foreach ($form_object['fields'] as $field) {
				   if ($field->encryptField == true) {
					   array_push($encryption_on, $field->id);
					   echo($field->id . ', ');
				   }
				}
			}
			if (empty($user_owned_fields)){
				echo('<p><b>Current User Owned Field IDs: </b>');
				
				foreach ($form_object['fields'] as $field) {
				   if ($field->gfedecryptownerField) {
					   array_push($user_owned_fields, $field->id);
					   echo($field->id . ', ');
				   }
				}
				echo('</p>');
				if (get_option('gfe_encrypt_decrypt_form_ubf')){
					echo('<p><b>Processed User Owned Fields Status:</b> Encrypted and User Owned</p>');
				} else {
					echo('<p><b>Processed User Owned Fields Status:</b> Encrypted Only. Not User Owned</p>');
				}
				
				echo('<p>Below is a list of processed entries and the fields that were encrypted for that entry.<br/>Field IDs with decimal places represent multi part fields.<br/><b>Entry ID:</b> Field IDs</p>');
				array_push($user_owned_fields, 'not empty ');
			}
			echo('<p><b>' . $entry_id . ': </b>');

			foreach($entry as $key => $value) {
				if (substr($value, 0, 11) !== 'GFEncrypt: ') {
					if (!$fields && !get_option('gfe_encrypt_decrypt_form_encrypt_all')){
						if (strstr($key, '.')){
							$key_base 	 = explode('.', $key);
							$key_base 	 = $key_base[0];
							if (in_array($key_base, $encryptable_fields) && in_array($key_base, $encryption_on) && ($value || $value === 0 || $value === '0')) {
								if (in_array($key_base, $user_owned_fields) && get_option('gfe_encrypt_decrypt_form_ubf')) {
									$gfe_created_user_id	= rgar($entry, 'created_by');
									$gfe_created_user		= get_user_by('id', $gfe_created_user_id);
									$gfe_created_user		= $gfe_created_user->user_login;
									$gfe_crypt_user 		= md5($gfe_created_user . $gfe_created_user_id);
									$value					= gfef_encrypt($value, $gfe_crypt_user);
									$entry[$key] 			= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								} else {
									$value 			= gfef_encrypt($value, false);
									$entry[$key] 	= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								}
								echo($key . ', ');
							}
						} else {
							if (in_array($key, $encryptable_fields) && in_array($key, $encryption_on) && ($value || $value === 0 || $value === '0')) {
								if (in_array($key, $user_owned_fields) && get_option('gfe_encrypt_decrypt_form_ubf')) {
									$gfe_created_user_id	= rgar($entry, 'created_by');
									$gfe_created_user		= get_user_by('id', $gfe_created_user_id);
									$gfe_created_user		= $gfe_created_user->user_login;
									$gfe_crypt_user 		= md5($gfe_created_user . $gfe_created_user_id);
									$value					= gfef_encrypt($value, $gfe_crypt_user);
									$entry[$key] 			= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								} else {
									$value 			= gfef_encrypt($value, false);
									$entry[$key]	= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								}
								echo($key . ', ');
								//GFAPI::update_entry_field($entry_id, $key, $value);
							}
						}
					} 
					else if ($fields && !get_option('gfe_encrypt_decrypt_form_encrypt_all')) {
						if (strstr($key, '.')){
							$key_base 	 = explode('.', $key);
							$key_base 	 = $key_base[0];
							if (in_array($key_base, $encryptable_fields) && in_array($key_base, $encryption_on) && in_array($key_base, $fields) && ($value || $value === 0 || $value === '0')) {
								if (in_array($key_base, $user_owned_fields) && get_option('gfe_encrypt_decrypt_form_ubf')) {
									$gfe_created_user_id	= rgar($entry, 'created_by');
									$gfe_created_user		= get_user_by('id', $gfe_created_user_id);
									$gfe_created_user		= $gfe_created_user->user_login;
									$gfe_crypt_user 		= md5($gfe_created_user . $gfe_created_user_id);
									$value					= gfef_encrypt($value, $gfe_crypt_user);
									$entry[$key] 			= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								} else {
									$value 			= gfef_encrypt($value, false);
									$entry[$key] 	= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								}
								echo($key . ', ');
							}
						} else {
							if (in_array($key, $encryptable_fields) && in_array($key, $encryption_on) && in_array($key, $fields) && ($value || $value === 0 || $value === '0')) {
								if (in_array($key, $user_owned_fields) && get_option('gfe_encrypt_decrypt_form_ubf')) {
									$gfe_created_user_id	= rgar($entry, 'created_by');
									$gfe_created_user		= get_user_by('id', $gfe_created_user_id);
									$gfe_created_user		= $gfe_created_user->user_login;
									$gfe_crypt_user 		= md5($gfe_created_user . $gfe_created_user_id);
									$value					= gfef_encrypt($value, $gfe_crypt_user);
									$entry[$key] 			= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								} else {
									$value 			= gfef_encrypt($value, false);
									$entry[$key]	= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								}
								echo($key . ', ');
								//GFAPI::update_entry_field($entry_id, $key, $value);
							}
						}
					}
					else if ($fields && get_option('gfe_encrypt_decrypt_form_encrypt_all')){
						if (strstr($key, '.')){
							$key_base 	 = explode('.', $key);
							$key_base 	 = $key_base[0];
							if (in_array($key_base, $encryptable_fields) && in_array($key_base, $fields) && ($value || $value === 0 || $value === '0')) {
								if (in_array($key_base, $user_owned_fields) && get_option('gfe_encrypt_decrypt_form_ubf')) {
									$gfe_created_user_id	= rgar($entry, 'created_by');
									$gfe_created_user		= get_user_by('id', $gfe_created_user_id);
									$gfe_created_user		= $gfe_created_user->user_login;
									$gfe_crypt_user 		= md5($gfe_created_user . $gfe_created_user_id);
									$value					= gfef_encrypt($value, $gfe_crypt_user);
									$entry[$key] 			= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								} else {
									$value 			= gfef_encrypt($value, false);
									$entry[$key] 	= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								}
								echo($key . ', ');
							}
						} else {
							if (in_array($key, $encryptable_fields) && in_array($key, $fields) && ($value || $value === 0 || $value === '0')) {
								if (in_array($key, $user_owned_fields) && get_option('gfe_encrypt_decrypt_form_ubf')) {
									$gfe_created_user_id	= rgar($entry, 'created_by');
									$gfe_created_user		= get_user_by('id', $gfe_created_user_id);
									$gfe_created_user		= $gfe_created_user->user_login;
									$gfe_crypt_user 		= md5($gfe_created_user . $gfe_created_user_id);
									$value					= gfef_encrypt($value, $gfe_crypt_user);
									$entry[$key] 			= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								} else {
									$value 			= gfef_encrypt($value, false);
									$entry[$key]	= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								}
								echo($key . ', ');
								//GFAPI::update_entry_field($entry_id, $key, $value);
							}
						}
					}
					else if (!$fields && get_option('gfe_encrypt_decrypt_form_encrypt_all')){
						if (strstr($key, '.')){
							$key_base 	 = explode('.', $key);
							$key_base 	 = $key_base[0];
							if (in_array($key_base, $encryptable_fields) && ($value || $value === 0 || $value === '0')) {
								if (in_array($key_base, $user_owned_fields) && get_option('gfe_encrypt_decrypt_form_ubf')) {
									$gfe_created_user_id	= rgar($entry, 'created_by');
									$gfe_created_user		= get_user_by('id', $gfe_created_user_id);
									$gfe_created_user		= $gfe_created_user->user_login;
									$gfe_crypt_user 		= md5($gfe_created_user . $gfe_created_user_id);
									$value					= gfef_encrypt($value, $gfe_crypt_user);
									$entry[$key] 			= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								} else {
									$value 			= gfef_encrypt($value, false);
									$entry[$key] 	= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								}
								echo($key . ', ');
							}
						} else {
							if (in_array($key, $encryptable_fields) && ($value || $value === 0 || $value === '0')) {
								if (in_array($key, $user_owned_fields) && get_option('gfe_encrypt_decrypt_form_ubf')) {
									$gfe_created_user_id	= rgar($entry, 'created_by');
									$gfe_created_user		= get_user_by('id', $gfe_created_user_id);
									$gfe_created_user		= $gfe_created_user->user_login;
									$gfe_crypt_user 		= md5($gfe_created_user . $gfe_created_user_id);
									$value					= gfef_encrypt($value, $gfe_crypt_user);
									$entry[$key] 			= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								} else {
									$value 			= gfef_encrypt($value, false);
									$entry[$key]	= $value;
									//GFAPI::update_entry_field($entry_id, $key_base, $value);
								}
								echo($key . ', ');
								//GFAPI::update_entry_field($entry_id, $key, $value);
							}
						}
					}
				}
			}
			GFAPI::update_entry($entry);
			echo('</p>');
		}
	}
}

//admin add or remove encryption processing
function gfef_add_remove_encryption($encrypt_decrypt, $encrypt_decrypt_form, $encrypt_decrypt_entries, $encrypt_decrypt_fields, $encrypt_decrypt_paging, $encrypt_decrypt_paging_offset) {
	$entries 		= null;
	$fields			= null;
	$form			= $encrypt_decrypt_form;
	?>
	<script type='text/javascript'>
		function gfef_remove_report() {
			var e = document.getElementById('add-remove-encryption-report');
			e.style.display = 'none';
		 }
	</script>
	<?php
	if ($encrypt_decrypt === 'decrypt' || $encrypt_decrypt === 'encrypt' && $encrypt_decrypt_form) {
		echo('<div id="add-remove-encryption-report" style="max-width: 790px; background-color: #ffffff; border-left: 4px solid #4ecd33; padding: 15px; margin: 10px 10px 10px 0px; position: relative; -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);"><a href="#" style="position: absolute; top: 10px; right: 15px" onClick="gfef_remove_report();">close</a>');
		if ($encrypt_decrypt_fields){
			$fields = explode(',', $encrypt_decrypt_fields);
		}

		if ($encrypt_decrypt === 'decrypt' && $encrypt_decrypt_entries){
			$entries = array();
			$encrypt_decrypt_entries = explode(',', $encrypt_decrypt_entries);
			foreach($encrypt_decrypt_entries as $specified_entry) {
				$entry = GFAPI::get_entry($specified_entry);
				array_push($entries, $entry);
			}
			gfef_decrypt_entries_process($entries, $fields, $form);
		}
		if ($encrypt_decrypt === 'encrypt' && $encrypt_decrypt_entries){
			$entries = array();
			$encrypt_decrypt_entries = explode(',', $encrypt_decrypt_entries);
			foreach($encrypt_decrypt_entries as $specified_entry) {
				$entry = GFAPI::get_entry($specified_entry);
				array_push($entries, $entry);
			}
			gfef_encrypt_entries_process($entries, $fields, $form);
		}
		
		if (!$encrypt_decrypt_paging){
			$encrypt_decrypt_paging = 0;
		}
		if ($encrypt_decrypt_paging > 200){
			$encrypt_decrypt_paging = 200;
		}
		if (!$encrypt_decrypt_paging_offset){
			$encrypt_decrypt_paging_offset = 0;
		}
		if ($encrypt_decrypt === 'decrypt' && !$encrypt_decrypt_entries){
			//$search_criteria['field_filters'][] = array('operator' => 'contains', 'value' => 'GFEncrypt: ');
			$entries = GFAPI::get_entries($encrypt_decrypt_form, $search_criteria = null, $sorting = null, $paging = array('offset' => $encrypt_decrypt_paging_offset, 'page_size' => $encrypt_decrypt_paging), $total_count = null);
			gfef_decrypt_entries_process($entries, $fields, $form);
		}
		if ($encrypt_decrypt === 'encrypt' && !$encrypt_decrypt_entries){
			$entries = GFAPI::get_entries($encrypt_decrypt_form, $search_criteria = null, $sorting = null, $paging = array('offset' => $encrypt_decrypt_paging_offset, 'page_size' => $encrypt_decrypt_paging), $total_count = null);
			gfef_encrypt_entries_process($entries, $fields, $form);
		}

		delete_option('gfe_encrypt_decrypt_user');
		delete_option('gfe_encrypt_decrypt');
		//delete_option('gfe_encrypt_decrypt_form');
		//delete_option('gfe_encrypt_decrypt_form_entries');
		//delete_option('gfe_encrypt_decrypt_form_fields');
		//delete_option('gfe_encrypt_decrypt_form_entry_paging');
		//delete_option('gfe_encrypt_decrypt_form_paging_offset');
		//delete_option('gfe_encrypt_decrypt_form_ubf');
		delete_site_option('gfe_encrypt_decrypt_user');
		delete_site_option('gfe_encrypt_decrypt');
		//delete_site_option('gfe_encrypt_decrypt_form');
		//delete_site_option('gfe_encrypt_decrypt_form_entries');
		//delete_site_option('gfe_encrypt_decrypt_form_fields');
		//delete_site_option('gfe_encrypt_decrypt_form_entry_paging');
		//delete_site_option('gfe_encrypt_decrypt_form_paging_offset');
		//delete_site_option('gfe_encrypt_decrypt_form_ubf');
		echo('</div>');
	}
}

/*GLOBAL FORM ENCRYPTION SWITCH*/
function gfef_global_encryption($form_id) {
	?>
	<script type='text/javascript'>
		function gfef_remove_report() {
			var e = document.getElementById('add-remove-global-encryption-report');
			e.style.display = 'none';
		 }
	</script>
	<?php
	if(get_option('gfe_global_encryption_on') && is_numeric(get_option('gfe_global_encryption_on')) && get_option('gfe_global_encryption_encrypt_hide') && get_option('gfe_global_encryption_on_off')) {
		$form_object = GFAPI::get_form($form_id);
		$fields		 = '';
		$report		 = null;
		foreach ($form_object['fields'] as $field) {
		   if ($field->type === 'text' || $field->type === 'textarea' || $field->type === 'date' || $field->type === 'name' || $field->type === 'number' || $field->type === 'email' || $field->type === 'phone' || $field->type === 'website' || $field->type === 'address' || $field->type === 'select' || $field->type === 'radio' || $field->type === 'multiselect' || $field->type === 'checkbox') {
			   if (get_option('gfe_global_encryption_encrypt_hide') == 1) {
				   if (get_option('gfe_global_encryption_on_off') == 1) {
					   $field->hidevalueField 	= false;
					   $field->encryptField		= true;
					   $report		 			= 1;
				   }
				   if (get_option('gfe_global_encryption_on_off') == 2) {
					   $field->encryptField 	= false;
					   $report		 			= 2;
				   }
			   }
			   if (get_option('gfe_global_encryption_encrypt_hide') == 2) {
				   if (get_option('gfe_global_encryption_on_off') == 1) {
					   $field->hidevalueField 	= true;
					   $field->encryptField 	= false;
					   $report		 			= 3;
				   }
				   if (get_option('gfe_global_encryption_on_off') == 2) {
					   $field->hidevalueField 	= false;
					   $report		 			= 4;
				   }
			   }
			   $fields .= $field->id . ', ';
		   }
		}
		$result = GFAPI::update_form($form_object);
		
		switch ($report) {
			case 1:
				echo('<div id="add-remove-global-encryption-report" style="max-width: 790px; background-color: #ffffff; border-left: 4px solid #4ecd33; padding: 15px; margin: 10px 10px 10px 0px; position: relative; -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1); box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);"><a href="#" style="position: absolute; top: 10px; right: 15px" onClick="gfef_remove_report();">close</a><p><b>ENCRYPTION TURNED ON FOR ALL SUPPORTED FIELDS<img src="' . plugin_dir_url(__FILE__) . 'images/locked.png" height="12" width="12"> :</b><p><p><b>Form ID:</b> ' . get_option('gfe_global_encryption_on') . '</p><p><b>Supported Field IDs:</b> ' . $fields . '</p></div>');
				break;
				
			case 2:
				echo('<div id="add-remove-global-encryption-report" style="max-width: 790px; background-color: #ffffff; border-left: 4px solid #4ecd33; padding: 15px; margin: 10px 10px 10px 0px; position: relative; -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1); box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);"><a href="#" style="position: absolute; top: 10px; right: 15px" onClick="gfef_remove_report();">close</a><p><b>ENCRYPTION TURNED OFF FOR ALL SUPPORTED FIELDS<img src="' . plugin_dir_url(__FILE__) . 'images/unlocked.png" height="12" width="12"> :</b><p><p><b>Form ID:</b> ' . get_option('gfe_global_encryption_on') . '</p><p><b>Supported Field IDs:</b> ' . $fields . '</p></div>');
				break;
				
			case 3:
				echo('<div id="add-remove-global-encryption-report" style="max-width: 790px; background-color: #ffffff; border-left: 4px solid #4ecd33; padding: 15px; margin: 10px 10px 10px 0px; position: relative; -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1); box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);"><a href="#" style="position: absolute; top: 10px; right: 15px" onClick="gfef_remove_report();">close</a><p><b>HIDE FIELD VALUE TURNED ON FOR ALL SUPPORTED FIELDS<img src="' . plugin_dir_url(__FILE__) . 'images/locked.png" height="12" width="12"> :</b><p><p><b>Form ID:</b> ' . get_option('gfe_global_encryption_on') . '</p><p><b>Supported Field IDs:</b> ' . $fields . '</p></div>');
				break;
				
			case 4:
				echo('<div id="add-remove-global-encryption-report" style="max-width: 790px; background-color: #ffffff; border-left: 4px solid #4ecd33; padding: 15px; margin: 10px 10px 10px 0px; position: relative; -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1); box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);"><a href="#" style="position: absolute; top: 10px; right: 15px" onClick="gfef_remove_report();">close</a><p><b>HIDE FIELD VALUE TURNED OFF FOR ALL SUPPORTED FIELDS<img src="' . plugin_dir_url(__FILE__) . 'images/unlocked.png" height="12" width="12"> :</b><p><p><b>Form ID:</b> ' . get_option('gfe_global_encryption_on') . '</p><p><b>Supported Field IDs:</b> ' . $fields . '</p></div>');
				break;
		}
		delete_option('gfe_global_encryption_on');
		delete_option('gfe_global_encryption_encrypt_hide');
		delete_option('gfe_global_encryption_on_off');
		delete_site_option('gfe_global_encryption_on');
		delete_site_option('gfe_global_encryption_encrypt_hide');
		delete_site_option('gfe_global_encryption_on_off');
	}
}

/*MERGE TAG FILTERING*/
add_filter("gform_merge_tag_filter", "gfe_merge_tag_filter", 11, 5);
function gfe_merge_tag_filter($value, $merge_tag, $options, $field, $raw_value) {
	$intitial_value		= $value;
	$decrypt_all_fields = false;
	$form_id 			= $field->formId;
	$field_id 			= $field->id;
	$field_user_owned 	= $field->gfedecryptownerField;
	if ($merge_tag == 'all_fields') {
		$include 	= preg_match("/include\[(.*?)\]/", $options, $included);
		$includes 	= explode(',', rgar( $included, 1 ));
		$exclude 	= preg_match("/exclude\[(.*?)\]/", $options, $excluded);
		$excludes 	= explode(',', rgar( $excluded, 1));
		$log 		= "gfe_merge_tag_filter(): {$field->label}({$field->id} - {$field->type}) - ";

		if ($include && in_array($field->id, $includes)) {
			switch ($field->type) {
				case 'html' :
					$value = $field->content;
					break;
				case 'section' :
					$value .= sprintf( '<tr bgcolor="#f6f6f6">
											<td width="20">&nbsp;</td>
											<td><font style="font-family:sans-serif; font-size:12px;">%s</font></td>
									   </tr>', $field->description);
					break;
				case 'signature' :
					$url 	= GFSignature::get_signature_url($raw_value);
					$value	= "<img alt='signature' src='{$url}'/>";
					break;
			}
			GFCommon::log_debug($log . 'included.');
		}
		
		if ($include && in_array($field->id, $includes)) {
			$value .= sprintf( '<tr bgcolor="#f6f6f6">
				<td width="20">&nbsp;</td>
				<td><font style="font-family:sans-serif; font-size:12px;">%s</font></td>
		   </tr>', $field->description);
			GFCommon::log_debug($log . 'included decrypted.');
		}
		
		if ($exclude && in_array($field->id, $excludes)) {
			GFCommon::log_debug($log . 'excluded.');
			return false;
		}
	} 

	if ($value || $value === 0 || $value === '0') {
	// Check if the field is encrypted or hidden and "merge tag filter" is on - currently passes only encrypted restricted display to conceal actual encryption or not
		if (!get_option('gfe_mergefilter')) {
			if ($field->encryptField == true) {
				$value = (esc_html__(get_option('gfe_restricted')));
			} 
			if ($field->hidevalueField == true) {
				$value = (esc_html__(get_option('gfe_restricted')));
			}
		}
		
		$field_mask = gfef_check_masks($form_id, $field_id, $intitial_value, 'M');
		if ($field_mask && !$field_user_owned) {
			$value = $field_mask;
		}
		return $value;
	}
}

/*CUSTOM MERGE TAG FULL DECRYPTED / ENCRYTPTED OUTPUT*/
add_filter( 'gform_replace_merge_tags', 'gfe_decrypted_merge_tag', 10, 7 );
function gfe_decrypted_merge_tag( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
	$gfef_decrypted_merge_tags 			= null;
	if (get_option('gfe_decrypt_merge_tags')) {
		$gfef_decrypted_merge_tags 		= get_option('gfe_decrypt_merge_tags');
		$gfef_decrypted_merge_tags 		= explode(',', $gfef_decrypted_merge_tags);
	}
	
	if ($gfef_decrypted_merge_tags) {
		foreach($gfef_decrypted_merge_tags as $gfef_decrypt_merge_tag) {
			$value 					= false;
			$gfef_decrypt_merge_tag	= trim($gfef_decrypt_merge_tag);
			$gfef_decrypt_merge_tag	= explode(':', $gfef_decrypt_merge_tag);
			$tag_form_id 			= trim($gfef_decrypt_merge_tag[0]);
			$tag_field_id 			= trim($gfef_decrypt_merge_tag[1]);
			$tag_encrypted_output	= trim($gfef_decrypt_merge_tag[2]);
			$form_id 				= rgar($form, 'id');
			$entry_id				= rgar($entry, 'id');
			
			if ($tag_form_id == $form_id) {
				if ($tag_field_id === "ALL") {
					$print_value   .= '<table style="width: 99%;
										border-top-width: 0px;
										border-right-width: 0px;
										border-bottom-width: 0px;
										border-left-width: 0px;
										-webkit-border-horizontal-spacing: 0px !important;
										-webkit-border-vertical-spacing: 1px;
										background-color: rgb(234, 234, 234);">
										<tbody style="display: table-row-group;
										vertical-align: middle;
										-webkit-border-horizontal-spacing: 0px !important;">';
					foreach($entry as $key => $raw_value) {
						$field 				= null;
						$raw_field_value 	= null;
						$label 				= null;
						$input_label		= null;
						$key_base			= null;
						
						if (strstr($key, '.')){
							$key_base 	= explode('.', $key);
							$key_base 	= $key_base[0];
							$field	 	= GFFormsModel::get_field($form, $key_base);
							$inputs 	= $field->inputs;
							
							if ($inputs) {
								foreach ($inputs as $input) {
									if ($input['id'] == $key) {
										$input_label 	= $input['label'];
										continue;
									}
								}
							}
							
							$label 					= $field->label ? $field->label : 'No Field Label';
							$label 					= $label . ' - ' . $input_label;
							$raw_field_value 		= gfef_get_raw_field_value($entry_id, $key);
							
						} else {
							$key_base				= $key;
							$field	 				= GFFormsModel::get_field($form, $key);
							$label 					= $field->label ? $field->label : 'No Field Label';
							$raw_field_value 		= gfef_get_raw_field_value($entry_id, $key);
						}

						if (substr($raw_field_value, 0, 11) === 'GFEncrypt: ' || $field->hidevalueField == true) {
							$value 			= gfef_decrypt($raw_field_value);
							$user_owned 	= substr($value, 0, 8);
							if ($user_owned === 'GFEFU[[[') {
								$value = gfef_get_user_owned_value($value);
							}
							if ($tag_encrypted_output === 'U') {
								$field 					= GFFormsModel::get_field($form, $key_base);
								$value					= gfef_decrypt_field($raw_field_value, $entry, $field, $key);
							}
							
							$print_value .= '<tr style="-webkit-border-vertical-spacing: 0px;
														padding-left: 5px;
														background-color: rgb(234, 242, 250);
														border: none;
														color: rgb(34, 34, 34);
														direction: ltr;
														display: table-row;
														font-family: arial, sans-serif;
														font-size: 13px;
														height: 25px;
														vertical-align: middle;">
											<td><font style="font-family:sans-serif; font-size:12px;"><strong>' . $label . '</strong></font></td></tr>
											<tr style="display: table-row;
														-webkit-border-horizontal-spacing: 0px;
														direction: ltr;
														display: table-cell;
														vertical-align: inherit;
														border: none;
														background-color: rgb(255, 255, 255);
														width: 100%;
														padding: 5px 5px 5px 25px;">
											<td> ' . $value . '</td></tr>
									   </tr>';
						}
					}
					
					$print_value .= '</tbody></table>';
					$gfef_merge_tag = '{gfef_decrypt_' . $tag_field_id . '}';
					if ($tag_encrypted_output === 'U') {
						$gfef_merge_tag = '{gfef_decrypt_' . $tag_field_id . '_USER}';
					}
					$text 			= str_replace($gfef_merge_tag, $print_value, $text );
					$print_value	= null;
				}

				$raw_field_value 	= gfef_get_raw_field_value($entry_id, $tag_field_id);
				
				if ($tag_encrypted_output && $tag_encrypted_output === 'X' && substr($raw_field_value, 0, 11) === 'GFEncrypt: ') {
					$value 			= $raw_field_value;
					$gfef_merge_tag = '{gfef_encrypt_' . $tag_field_id . '}';
					$text 			= str_replace($gfef_merge_tag, $value, $text );
				}
				
				if ($tag_encrypted_output && $tag_encrypted_output === 'X' && substr($raw_field_value, 0, 11) !== 'GFEncrypt: ') {
					$value 			= gfef_encrypt($raw_field_value, false);
					$gfef_merge_tag = '{gfef_encrypt_' . $tag_field_id . '}';
					$text 			= str_replace($gfef_merge_tag, $value, $text );
				}
				
				if ($tag_encrypted_output === 'U' && $tag_field_id !== "ALL") {
					$field 			= GFFormsModel::get_field($form, $tag_field_id);
					$value 			= gfef_decrypt_field($raw_field_value, $entry, $field, $tag_field_id);
					$gfef_merge_tag = '{gfef_decrypt_user_' . $tag_field_id . '}';
					$text 			= str_replace($gfef_merge_tag, $value, $text );
				}
				
				if (!$tag_encrypted_output) {
					$value 			= gfef_decrypt($raw_field_value);
					$user_owned 	= substr($value, 0, 8);
					if ($user_owned === 'GFEFU[[[') {
						$value = gfef_get_user_owned_value($value);
					}
					$gfef_merge_tag = '{gfef_decrypt_' . $tag_field_id . '}';
					$text 			= str_replace($gfef_merge_tag, $value, $text );
				}
			}
		}
	}
	return $text;
}


/*ADD GRAVITY FORMS FILE UPLOADS AS E-MAIL ATTACHMENTS -set any file upload fields class to "exclude" to remove standard link from notification*/
//Attach file uploads to notifications
add_filter('gform_notification', 'gfef_notification_attachments', 10, 3);
function gfef_notification_attachments( $notification, $form, $entry ) {
	$gfef_upload_attachments 	= get_option('gfe_attach_file_uploads');
	if ($gfef_upload_attachments) {
		$upload_attachments 		= explode(',', $gfef_upload_attachments);
		
		//target specified form:notification names in option
		foreach($upload_attachments as $uploads) {
			$trimmed_uploads = array();
			$uploads 		 = strtolower(trim($uploads));
			array_push($trimmed_uploads, $uploads);
			foreach($trimmed_uploads as $form_notifications) {
				$form_notifications = explode(':', $form_notifications);
				$notification_form	= $form_notifications[0];
				$form_id 		 	= rgar($form, 'id');
				$notification_name	= strtolower($notification["name"]);
				if(in_array($notification_name, $form_notifications) && $form_id == $notification_form) {

					$file_upload_fields = GFCommon::get_fields_by_type($form, array("fileupload"));

					if(!is_array($file_upload_fields)){
						return $notification;
					}
					$attachments = array();
					$upload_root = RGFormsModel::get_upload_root();

					foreach( $file_upload_fields as $field ) {
						$url = $entry[ $field['id'] ];
						if (empty($url)) {
							continue;
						} elseif ($field['multipleFiles']) {
							$uploaded_files = json_decode( stripslashes($url), true);
							foreach ($uploaded_files as $uploaded_file) {
								$attachment = preg_replace('|^(.*?)/gravity_forms/|', $upload_root, $uploaded_file);
								GFCommon::log_debug($log . 'attached: ' . print_r($attachment, true));
								$attachments[] = $attachment;
							}
						} else {
							$attachment = preg_replace('|^(.*?)/gravity_forms/|', $upload_root, $url);
							GFCommon::log_debug($log . 'attached: ' . print_r($attachment, true));
							$attachments[] = $attachment;
						}
					}
					$notification['attachments'] = $attachments;
				}
			}
		}
	}
	return $notification;
}

/*AUTO DELETE ENTRY OR FILES*/
function gfef_delete_form_entry_files($entry) {
	$form_id			 = $entry['form_id'];
	$delete_file_uploads = get_option('gfe_delete_file_uploads');
	$delete_entries		 = get_option('gfe_delete_entry');
	$delete_files		 = explode(',', $delete_file_uploads);
	$delete_entry		 = explode(',', $delete_entries);

	if (in_array($form_id, $delete_files) && !in_array($form_id, $delete_entry)) {
		$delete = GFFormsModel::delete_files($entry['id']);
		$result = ($delete) ? "entry {$entry['id']} files deleted." : $delete;
		GFCommon::log_debug("GFAPI::delete_files() - files - form #{$form['id']}: " . print_r($result, true));
	}
	if (in_array($form_id, $delete_entry)) {
		$delete = GFAPI::delete_entry($entry['id']);
		$result = ($delete) ? "entry {$entry['id']} deleted." : $delete;
		GFCommon::log_debug("GFAPI::delete_entry() - form #{$form['id']}: " . print_r($result, true));
	}
}
add_action('gform_after_submission', 'gfef_delete_active_form_entry_files', 15, 2);
function gfef_delete_active_form_entry_files($entry, $form) {
	if (class_exists('GFUser')) {
		$config = GFUser::get_active_config($form, $entry);
	}
	if (!$config['is_active']) {
		gfef_delete_form_entry_files($entry);
	}
}
add_action('gform_activate_user', 'gfef_delete_form_entry_files_after_activation', 15, 3);
function gfef_delete_form_entry_files_after_activation($user_id, $user_data, $signup_meta) {
	$entry = GFAPI::get_entry($signup_meta['lead_id']);
	gfef_delete_form_entry_files($entry);
}
add_action('gform_user_updated','gfef_delete_form_entry_files_after_update', 15, 3);
function gfef_delete_form_entry_files_after_update($user_id, $config, $lead) {
	gfef_delete_form_entry_files($lead);
}

/*ENTRY LIST NATIVE SEARCH*/
add_filter('gform_search_criteria_entry_list', 'gfef_encrypted_search');
function gfef_encrypted_search($search_criteria) {
	if ($search_criteria['field_filters']['0']['value']){
		$gfef_key 						= $search_criteria['field_filters']['0']['key'];
		$gfef_operator 					= $search_criteria['field_filters']['0']['operator'];
		$gfef_value 					= $search_criteria['field_filters']['0']['value'];
		$gfef_encryption_type			= get_option('gfe_encryption_method');
		$gfef_search_permission_list 	= get_option('gfe_user_search_permission_list');
		$gfef_search_permission_list	= explode(',', $gfef_search_permission_list);
		$gfe_current_user 				= wp_get_current_user();
		$gfe_current_username			= $gfe_current_user->user_login;
		$gfe_current_user_roles			= $gfe_current_user->roles;
		$gfe_user_role					= false;
		
		foreach ($gfef_search_permission_list as $role) {
			if (in_array($role, $gfe_current_user_roles, TRUE)) {
				$gfe_user_role = true;
			}
		}
		
		if (in_array($gfe_current_username, $gfef_search_permission_list) || $gfe_user_role && $gfef_encryption_type === 2) {
			$gfef_mcrypt_search_value 					= gfef_encrypt($gfef_value, false);
			$gfef_mcrypt_search_value_ucwords 			= gfef_encrypt(ucwords($gfef_value), false);			
			$gfef_mcrypt_search_value_ucfirst 			= gfef_encrypt(ucfirst($gfef_value), false);
			$gfef_mcrypt_search_value_strtoupper		= gfef_encrypt(strtoupper($gfef_value), false);
			$gfef_mcrypt_search_value_strtolower		= gfef_encrypt(strtolower($gfef_value), false);
			$mcrypt_cipher_name 						= MCRYPT_RIJNDAEL_128;
			$iv_size          							= mcrypt_get_iv_size($mcrypt_cipher_name, MCRYPT_MODE_CBC);
			$gfef_search_key							= substr($gfef_mcrypt_search_value, 11 + $iv_size, 10);
			$gfef_search_key_ucwords					= substr($gfef_mcrypt_search_value_ucwords, 11 + $iv_size, 10);
			$gfef_search_key_ucfirst					= substr($gfef_mcrypt_search_value_ucfirst, 11 + $iv_size, 10);
			$gfef_search_key_strtoupper					= substr($gfef_mcrypt_search_value_strtoupper, 11 + $iv_size, 10);
			$gfef_search_key_strtolower					= substr($gfef_mcrypt_search_value_strtolower, 11 + $iv_size, 10);
			$search_criteria['field_filters']['mode'] 	= 'any';
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key);
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key_ucwords);
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key_ucfirst);
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key_strtoupper);
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key_strtolower);
		}
		
		if (in_array($gfe_current_username, $gfef_search_permission_list) || $gfe_user_role && $gfef_encryption_type === 1) {
			$ssl_cipher_name 							= "AES-256-CBC";
			$iv_size 		 							= openssl_cipher_iv_length($ssl_cipher_name);
			$gfef_mcrypt_search_value 					= gfef_encrypt($gfef_value, false);
			$gfef_mcrypt_search_value_ucwords 			= gfef_encrypt(ucwords($gfef_value), false);			
			$gfef_mcrypt_search_value_ucfirst 			= gfef_encrypt(ucfirst($gfef_value), false);
			$gfef_mcrypt_search_value_strtoupper		= gfef_encrypt(strtoupper($gfef_value), false);
			$gfef_mcrypt_search_value_strtolower		= gfef_encrypt(strtolower($gfef_value), false);
			$gfef_search_key							= substr($gfef_mcrypt_search_value, 11 + $iv_size, 10);
			$gfef_search_key_ucwords					= substr($gfef_mcrypt_search_value_ucwords, 11 + $iv_size, 10);
			$gfef_search_key_ucfirst					= substr($gfef_mcrypt_search_value_ucfirst, 11 + $iv_size, 10);
			$gfef_search_key_strtoupper					= substr($gfef_mcrypt_search_value_strtoupper, 11 + $iv_size, 10);
			$gfef_search_key_strtolower					= substr($gfef_mcrypt_search_value_strtolower, 11 + $iv_size, 10);
			$search_criteria['field_filters']['mode'] 	= 'any';
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key);
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key_ucwords);
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key_ucfirst);
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key_strtoupper);
			$search_criteria['field_filters'][] 		= array('key' => $gfef_key, 'operator' => 'contains', 'value' => $gfef_search_key_strtolower);
		}
		
		return $search_criteria;
	}
	else {
		return $search_criteria;
	}
}

/*GFEF ENTRY LIST PAGE ENCRYPTION VERIFICATION NOTICE*/
add_action( 'gform_pre_entry_list', 'gfef_verification_notice' );
function gfef_verification_notice($form_id) {
	if (get_option('gfe_show_encryption')) {
		echo '<div style="width: auto; margin: 20px; background-color: #FFFFFF; border: 2px solid #00feff; border-radius: 7px; padding: 10px; min-height: 80px;"><img src="' . plugin_dir_url(__FILE__)  . 'images/GFEFicon80x80.jpg" height="80" width="80" style="display:inline-block; float:left; margin-right: 10px;"><p><b>Gravity Forms Encrypted Fields:</b></p>
		<p>"ENCRYPTION VERIFICATION MODE" is ACTIVE.<br/>
		Any encrypted data will display raw.<br/>Turn encryption verification mode off <a href="' . GFEF_ADMIN . '#gfefEVM">here</a> to resume normal operation.</p></div>';
	}
}

/*GFORMS ENCRYPTED FIELDS ADVANCED FIELD OPTIONS*/
add_action('gform_field_advanced_settings', 'gfe_encrypt_settings', 10, 2);
function gfe_encrypt_settings($position, $form_id) {

    //create settings on position 25 (right after Admin Field Label)
    if ($position == 50) {
        ?>
        <li class="encrypt_setting field_setting">
            <label for="field_encrypt_value" class="section_label">
                <?php
					_e('Encryption', 'gravityforms');
					echo ' <a href="https://codecanyon.net/user/pluginowl/portfolio" target="_blank"><img src="' . plugin_dir_url(__FILE__)  . 'images/owl-small.png" height="13px" width="auto" style="display:inline-block;"></a>';
				?>
            </label>
            <input type="radio" id="field_encrypt_value" name="encryptOption" onClick="SetFieldProperty('encryptField', this.checked);SetFieldProperty('hidevalueField', this.unchecked);SetFieldProperty('gfeoffvalueField', this.unchecked);" /> Encrypt field value <?php gform_tooltip( 'form_field_encrypt_value' ) ?>
        </li>
        <li class="hidevalue_setting field_setting">
            <input type="radio" id="field_hidevalue_value" name="encryptOption" onClick="SetFieldProperty('hidevalueField', this.checked);SetFieldProperty('encryptField', this.unchecked);SetFieldProperty('gfeoffvalueField', this.unchecked);SetFieldProperty('gfedecryptownerField', this.unchecked);SetFieldProperty('gfedecryptownerField', this.unchecked);" /> Hide field value <?php gform_tooltip( 'form_field_hidevalue_value' ) ?>
        </li>
        <li class="gfeoffvalue_setting field_setting">
            <input type="radio" id="field_gfeoffvalue_value" name="encryptOption" onClick="SetFieldProperty('hidevalueField', this.unchecked);SetFieldProperty('encryptField', this.unchecked);SetFieldProperty('gfedecryptownerField', this.unchecked);SetFieldProperty('gfedecryptownerField', this.unchecked);" /> Off <?php gform_tooltip( 'form_field_gfeoffvalue_value' ) ?>
        </li>
        <li class="decrypt_setting field_setting">
            <label for="field_decrypt_user_value">
                <?php _e('User/Role View Permission', 'gravityforms'); ?>
                <?php gform_tooltip('form_field_decrypt_user_value') ?>
            </label>
            <textarea rows="4" id="field_decrypt_user_value" style="width:100%; max-width: 278px" onKeyUp="SetFieldProperty('decryptFieldUsers', this.value);"/></textarea>
        </li>
        <li class="gfedecryptowner_setting field_setting">
            <input type="checkbox" id="field_gfedecryptowner_value" name="userOwnedOption" onClick="SetFieldProperty('gfedecryptownerField', this.checked);SetFieldProperty('encryptField', this.checked);SetFieldProperty('hidevalueField', this.unchecked);SetFieldProperty('gfeoffvalueField', this.unchecked);" /> User Owned Field <?php 			gform_tooltip( 'form_field_gfedecryptowner_value' ) ?>
        </li>
        <?php
    }
}

//Add setting to fields of type
add_action('gform_editor_js', 'gfe_editor_encrypt_script');
function gfe_editor_encrypt_script(){
    ?>
    <script type='text/javascript'>
        fieldSettings["text"] += ", .encrypt_setting";
		fieldSettings["textarea"] += ", .encrypt_setting";
		fieldSettings["date"] += ", .encrypt_setting";
		fieldSettings["name"] += ", .encrypt_setting";
		fieldSettings["number"] += ", .encrypt_setting";
		fieldSettings["email"] += ", .encrypt_setting";
		fieldSettings["phone"] += ", .encrypt_setting";
		fieldSettings["website"] += ", .encrypt_setting";
		fieldSettings["address"] += ", .encrypt_setting";
		fieldSettings["select"] += ", .encrypt_setting";
		fieldSettings["radio"] += ", .encrypt_setting";
		fieldSettings["multiselect"] += ", .encrypt_setting";
		fieldSettings["checkbox"] += ", .encrypt_setting";
		
		fieldSettings["text"] += ", .hidevalue_setting";
		fieldSettings["textarea"] += ", .hidevalue_setting";
		fieldSettings["date"] += ", .hidevalue_setting";
		fieldSettings["name"] += ", .hidevalue_setting";
		fieldSettings["number"] += ", .hidevalue_setting";
		fieldSettings["email"] += ", .hidevalue_setting";
		fieldSettings["phone"] += ", .hidevalue_setting";
		fieldSettings["website"] += ", .hidevalue_setting";
		fieldSettings["address"] += ", .hidevalue_setting";
		fieldSettings["select"] += ", .hidevalue_setting";
		fieldSettings["radio"] += ", .hidevalue_setting";
		fieldSettings["multiselect"] += ", .hidevalue_setting";
		fieldSettings["checkbox"] += ", .hidevalue_setting";
		
		fieldSettings["text"] += ", .gfeoffvalue_setting";
		fieldSettings["textarea"] += ", .gfeoffvalue_setting";
		fieldSettings["date"] += ", .gfeoffvalue_setting";
		fieldSettings["name"] += ", .gfeoffvalue_setting";
		fieldSettings["number"] += ", .gfeoffvalue_setting";
		fieldSettings["email"] += ", .gfeoffvalue_setting";
		fieldSettings["phone"] += ", .gfeoffvalue_setting";
		fieldSettings["website"] += ", .gfeoffvalue_setting";
		fieldSettings["address"] += ", .gfeoffvalue_setting";
		fieldSettings["select"] += ", .gfeoffvalue_setting";
		fieldSettings["radio"] += ", .gfeoffvalue_setting";
		fieldSettings["multiselect"] += ", .gfeoffvalue_setting";
		fieldSettings["checkbox"] += ", .gfeoffvalue_setting";
		
		fieldSettings["text"] += ", .decrypt_setting";
		fieldSettings["textarea"] += ", .decrypt_setting";
		fieldSettings["date"] += ", .decrypt_setting";
		fieldSettings["name"] += ", .decrypt_setting";
		fieldSettings["number"] += ", .decrypt_setting";
		fieldSettings["email"] += ", .decrypt_setting";
		fieldSettings["phone"] += ", .decrypt_setting";
		fieldSettings["website"] += ", .decrypt_setting";
		fieldSettings["address"] += ", .decrypt_setting";
		fieldSettings["select"] += ", .decrypt_setting";
		fieldSettings["radio"] += ", .decrypt_setting";
		fieldSettings["multiselect"] += ", .decrypt_setting";
		fieldSettings["checkbox"] += ", .decrypt_setting";
		
		fieldSettings["text"] += ", .gfedecryptowner_setting";
		fieldSettings["textarea"] += ", .gfedecryptowner_setting";
		fieldSettings["date"] += ", .gfedecryptowner_setting";
		fieldSettings["name"] += ", .gfedecryptowner_setting";
		fieldSettings["number"] += ", .gfedecryptowner_setting";
		fieldSettings["email"] += ", .gfedecryptowner_setting";
		fieldSettings["phone"] += ", .gfedecryptowner_setting";
		fieldSettings["website"] += ", .gfedecryptowner_setting";
		fieldSettings["address"] += ", .gfedecryptowner_setting";
		fieldSettings["select"] += ", .gfedecryptowner_setting";
		fieldSettings["radio"] += ", .gfedecryptowner_setting";
		fieldSettings["multiselect"] += ", .gfedecryptowner_setting";
		fieldSettings["checkbox"] += ", .gfedecryptowner_setting";

        //binding to the load field settings event to initialize options
        jQuery(document).bind("gform_load_field_settings", function(event, field, form){
            jQuery("#field_encrypt_value").attr("checked", field["encryptField"] == true);
			jQuery("#field_hidevalue_value").attr("checked", field["hidevalueField"] == true);
			jQuery("#field_gfeoffvalue_value").attr("checked", field["gfeoffvalueField"] == true);
			jQuery("#field_decrypt_user_value").val(field["decryptFieldUsers"]);
			jQuery("#field_gfedecryptowner_value").attr("checked", field["gfedecryptownerField"] == true);
        });
    </script>
    <?php
}

//GForm Field Settings Tooltips
add_filter('gform_tooltips', 'gfe_add_encryption_tooltips');
function gfe_add_encryption_tooltips($tooltips)  {
   $tooltips['form_field_encrypt_value'] = esc_html__("<h6>Encryption</h6><p>This option turns on database encryption for new submitted/updated values of this field, and allows for user/role based decrypted access through the admin interface.<br/>Turning this on will also hide existing unencrypted database values in admin from unauthorized users, but does not encrypt existing database field data. To encrypt existing database field values please use the encrypt/decrypt tool onthe options setings page.</p><p><b><span style='color:red'>CAUTION: YOU ARE RESPOSIBLE FOR ANY LOST OR DAMAGED DATA.</span></p><p>This requires encryption to be on in <a href=" . admin_url( 'options-general.php?page=gravity-forms-encrypted-fields' ) . ">settings->GF Encrypted Fields</a>. Full instructions are in settings page.</b></p><p>It is imperitive that you DO NOT change this setting when affected form entries are being edited / updated. Turning this option on or off when restricted form data is being updated may result in the submitted data being overwritten with the restricted data display if the editing user does not have view permissions when editing!</p>");
   return $tooltips;
}
add_filter('gform_tooltips', 'gfe_add_hidevalue_tooltips');
function gfe_add_hidevalue_tooltips($tooltips) {
   $tooltips['form_field_hidevalue_value'] = esc_html__("<h6>Hide Field Value</h6><p>This data safe option will turn off encryption of new submitted data for this field, gives user/role based view access and simply hides the field value in admin screen for unauthorized users.</p><p><b><span style='color:red'>CAUTION: YOU ARE RESPOSIBLE FOR ANY LOST OR DAMAGED DATA.</span></p><p>See <a href=" . admin_url( 'options-general.php?page=gravity-forms-encrypted-fields' ) . ">settings->GF Encrypted Fields</a> for full instructions.</b></p><p>It is imperitive that you DO NOT change this setting when affected form entries are being edited / updated. Turning this option on or off when restricted form data is being updated may result in the submitted data being overwritten with the restricted data display if the editing user does not have view permissions when editing!</p><p>Any previously submitted data that is already encrypted when this is turned on will still be encrypted and should be properly decrypted for users with permission, even though new submitted data will only be hidden, and not be encrypted when this is on.</p>");
   return $tooltips;
}
add_filter('gform_tooltips', 'gfe_add_gfeoffvalue_tooltips');
function gfe_add_gfeoffvalue_tooltips($tooltips) {
   $tooltips['form_field_gfeoffvalue_value'] = esc_html__("<h6>Off</h6><p>This turns encrypt and hide field value off.</p>");
   return $tooltips;
}
add_filter('gform_tooltips', 'gfe_add_encryption_user_tooltips');
function gfe_add_encryption_user_tooltips($tooltips) {
   $tooltips['form_field_decrypt_user_value'] = "<h6>User/Role View Permission</h6><p>Enter comma separated usernames/roles (use role slug) that should have access to view this encrypted field data as normally readable. If entering the slug for a Wordpress role all users of that role will have read permission for this field unless they are restricted by other permission settings. If left blank, all users can view the encrypted field data as readable. For no user access, enter 'lockdown' anywhere in the list.</p><p><b>See <a href=" . admin_url( 'options-general.php?page=gravity-forms-encrypted-fields' ) . ">settings->GF Encrypted Fields</a> for full instructions.</b></p><p>It is imperitive that you DO NOT change this setting when affected form entries are being edited / updated. Turning this option on or off when restricted form data is being updated may result in the submitted data being overwritten with the restricted data display if the editing user does not have view permissions when editing!</p><p>This is intended for users with only 'gravityforms_view_entries' permissions, and is NOT secure for users with gravityforms form editing permissions, as they can just add their own username here. To restrict form editing users, use the 'Limit User/Role View Permission Lists', 'User Lockout List', and 'User Access List' under settings->GF Encrypted Fields.</p>";
   return $tooltips;
}
add_filter('gform_tooltips', 'gfe_add_user_owned_tooltips');
function gfe_add_user_owned_tooltips($tooltips) {
   $tooltips['form_field_gfedecryptowner_value'] = "<h6>User Owned Field</h6><p>If you check this box, data encryption for this field will be turned on and only the logged in user who originally submitted the data while logged in will be able to view it as readable. This still applies even if this option is later turned off for a field. If a user who is not logged in submits data, it will still be encrypted and normal User View Permissions will apply, but non logged in users will NOT own the field data. This requires encryption to be on in <a href=" . admin_url( 'options-general.php?page=gravity-forms-encrypted-fields' ) . ">settings->GF Encrypted Fields</a>, and overrides all other User View Permissions.</p><p>If other users are able to update this data, the original user will still retain ownership.</p><p>If encryption is removed from this field for an entry, the user ownership is removed as well and normal view permissions will apply.</p>";
   return $tooltips;
}

/*FORM EDITOR AUGMENT*/
add_filter('gform_field_content', 'gfef_form_editor_augment', 10, 2);	
function gfef_form_editor_augment ($field_content, $field) {
	if ($field->is_form_editor()) {
		if ($field->encryptField == true){
			$field_content = '<img src="' . plugin_dir_url(__FILE__) . 'images/locked-blue.png" height="12" width="12" style="position:absolute; left:6px; top:31px;">' . $field_content;
			return $field_content;
		}
		if ($field->hidevalueField == true){
			$field_content = '<img src="' . plugin_dir_url(__FILE__) . 'images/hidden-blue.png" height="12" width="12" style="position:absolute; left:6px; top:31px;">' . $field_content;
			return $field_content;
		}
		else {
			return $field_content;
		}
	}
	else {
		return $field_content;
	}
}

/*CREATE SETTINGS MENU*/
//encrypt settings password
if (get_option('gfe_settings_lock') && strlen(get_option('gfe_settings_lock')) >= 8 && strlen(get_option('gfe_settings_lock')) <= 16) {
	$pass_hash = password_hash(get_option('gfe_settings_lock'), PASSWORD_BCRYPT);
	update_option('gfe_settings_lock', $pass_hash);
	//$gfef_lock = $pass_hash;
	add_action('admin_menu', 'gfe_gravity_forms_encrypted_fields_menulocked');
} 
if (get_option('gfe_settings_lock') && strlen(get_option('gfe_settings_lock')) <= 7) {
	delete_option('gfe_settings_lock');
	delete_site_option('gfe_settings_lock');
	remove_action('admin_menu', 'gfe_gravity_forms_encrypted_fields_menulocked', 9);
	add_action('admin_menu', 'gfe_gravity_forms_encrypted_fields_menu');
} 
if (strlen(get_option('gfe_settings_lock')) >= 32){
	$gfef_lock = get_option('gfe_settings_lock');
	$gfef_key  = get_option('gfe_settings_key');
	if (password_verify($gfef_key, $gfef_lock) || !get_option('gfe_settings_lock')) {
		add_action('admin_menu', 'gfe_gravity_forms_encrypted_fields_menu');
	} else {
		add_action('admin_menu', 'gfe_gravity_forms_encrypted_fields_menulocked');
	}
} 
if (!get_option('gfe_settings_lock')) {
	add_action('admin_menu', 'gfe_gravity_forms_encrypted_fields_menu');
}

//menu locked
function gfe_gravity_forms_encrypted_fields_menulocked() {
	//create new top-level menu
	add_options_page('gravity Forms Encrypted Fields Settings', 'GF Encrypted Fields', 'edit_users', 'gravity-forms-encrypted-fields', 'gravity_forms_encrypted_fields_settings_pagelocked');

	//call register settings function
	add_action( 'admin_init', 'register_gravity_forms_encrypted_fields_settings' );
}

//menu unlocked
function gfe_gravity_forms_encrypted_fields_menu() {
	//create new top-level menu
	add_options_page('gravity Forms Encrypted Fields Settings', 'GF Encrypted Fields', 'edit_users', 'gravity-forms-encrypted-fields', 'gravity_forms_encrypted_fields_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_gravity_forms_encrypted_fields_settings' );
}

function register_gravity_forms_encrypted_fields_settings() {
	//register our settings
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encryption_method');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_delete_options');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_admin_only');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encryption_bypass');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_restricted');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_hidevalue');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_custom_data_search');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_show_encryption');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_decrypt_merge_tags');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_masking');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_delete_entry');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_delete_file_uploads');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_attach_file_uploads');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_mergefilter');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_user_search_permission_list');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_limit_user_view_permission_list');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_user_lockout_list');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_user_lockout_override_list');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_website_key');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encryption_key');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encryption_key_override');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_settings_lock');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_norecover_pass');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_global_encryption_on');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_global_encryption_on_off');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_global_encryption_encrypt_hide');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encrypt_decrypt_form');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encrypt_decrypt_form_entries');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encrypt_decrypt_form_fields');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encrypt_decrypt_form_entry_paging');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encrypt_decrypt_form_paging_offset');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encrypt_decrypt_form_encrypt_all');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encrypt_decrypt_form_ubf');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encrypt_decrypt_user');
	register_setting('gravity_forms_encrypted_fields_settings-group', 'gfe_encrypt_decrypt');
	register_setting('gravity_forms_encrypted_fields_settings-key-group', 'gfe_settings_key');
}

//settings page locked
function gravity_forms_encrypted_fields_settings_pagelocked() {
?>
	<div class="wrap gfe-admin-page">
    <?php echo '<img src="' . plugin_dir_url(__FILE__)  . 'images/GFEFicon80x80.jpg" height="80" width="80" style="display:inline-block; float:left; margin-right: 10px;">'; ?>
    <h1>Gravity Forms Encrypted Fields</h1>
    <p style="margin-top:0px;">by <a href="https://codecanyon.net/user/pluginowl/portfolio" target="_blank">Plugin Owl</a></p><br/> 
		<form method="post" action="options.php" autocomplete="invalid">
        <?php settings_fields('gravity_forms_encrypted_fields_settings-key-group'); ?>
        <?php do_settings_sections('gravity_forms_encrypted_fields_settings-key-group'); ?>
        <table class="form-table">
            <tr valign="top">
                    <th scope="row">Settings Page LOCKOUT Password</th>
                    <td><input type="text" name="gfe_settings_key" value="" style="width:100%; max-width: 600px;" maxlength="32"/>
                        <div id="gfe-settings-page-instructions" style="max-width:600px;">
                            <p><?php echo ' <img src="' . plugin_dir_url(__FILE__) . 'images/locked.png" height="14" width="14">';?> Access to this page is restricted. Enter password.</p>
                        </div>
                    </td>
                </tr>
            </table>
            <?php submit_button();?>
        </form>
    </div>
<?php
}

//settings page unlocked
function gravity_forms_encrypted_fields_settings_page() {
	// begin create salt
	if (gfef_get_salt() === false) {
		gfef_create_salt(false);
	}
	// begin replace salt
	if (get_option('gfe_website_key')) {
		gfef_create_salt(esc_html__(get_option('gfe_website_key')));
	}
	//get gfe version 
	function gfe_get_plugin_version() {
		$plugin_data = get_plugin_data(__FILE__);
		$plugin_version = $plugin_data['Version'];
		return $plugin_version;
	}
    //test environment for encryption
    function gfe_check_encryption_function() {
        if(function_exists('mcrypt_encrypt') && extension_loaded('mcrypt') || function_exists('openssl_encrypt') && extension_loaded('openssl')) {
            echo esc_html__('YES');
        } else {
            echo esc_html__('NO');
        }
    }
	function gfe_check_encryption_type($checking) {
		if (get_option('gfe_encryption_method')) {
			$gfe_method = get_option('gfe_encryption_method');
			if ($checking === 1) {
				if ($gfe_method == 1 || $gfe_method == 2) {
					echo esc_html__('YES');
				}
			} 
			if ($checking === 2) {
				if ($gfe_method == 1) {
					echo '<img src="' . plugin_dir_url(__FILE__)  . 'images/check.png" height="14" width="14">' . esc_html__(' OpenSSL ON');
				} 
				if ($gfe_method == 2) {
					echo '<img src="' . plugin_dir_url(__FILE__)  . 'images/check.png" height="14" width="14">' . esc_html__(' Mcrypt ON');
				}
			} 
		} else {
			if ($checking === 1) {
				echo esc_html__('NONE');
			}
			if ($checking === 2) {
				echo '<img src="' . plugin_dir_url(__FILE__)  . 'images/x.png" height="14" width="14">' . esc_html__(' NO Encryption Type Selected');
			}
		}
	}
    function gfe_check_encryption() {
        $use_mcrypt 	= apply_filters('gform_use_mcrypt', function_exists('mcrypt_encrypt'));
		$use_openssl 	= function_exists('openssl_encrypt') && extension_loaded('openssl');
        
		if($use_openssl){
            echo '<img src="' . plugin_dir_url(__FILE__)  . 'images/check.png" height="14" width="14"> ' . esc_html__('OpenSSL Encryption Supported!');
        } else {
            echo '<img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> ' . esc_html__('OpenSSL Encryption NOT Supported! Requires OpenSSL install on server.');
        }
		echo '<br/>';
		if($use_mcrypt){
            echo '<img src="' . plugin_dir_url(__FILE__)  . 'images/check.png" height="14" width="14"> ' . esc_html__('Mcrypt Encryption Supported!');
        } else {
            echo '<img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> ' . esc_html__('Mcrypt Encryption NOT Supported! Requires mcrypt install on server.');
        }
	}
    function gfe_check_php() {
        if (version_compare( phpversion(), '5.6.0', '>=')) {
            echo '<img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> ' . esc_html__('PHP Version Supported!');
        } else {
            echo '<img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> ' . esc_html__('PHP Version Not Supported! Requires 5.6.0+');
        }
    }
    function gfe_check_wp_version() {
        if (version_compare(get_bloginfo('version'), '4.6.1', '>=')) {
            echo '<img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> ' . esc_html__('Wordpress Version Supported!');
        } else {
            echo '<img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> ' . esc_html__('Wordpress Version Not Supported! Requires 4.6.1+');
        }
    }
    function gfe_check_gf_version() {
        if (version_compare(GFCommon::$version, '2.0.7', '>=')) {
            echo '<img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> ' . esc_html__('Gravity Forms Version Supported!');
        } else {
            echo '<img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> ' . esc_html__('Gravity Forms Version Not Supported! Requires 2.0.7+');
        }
    }
	function get_website_key() {
        if (get_option('gfe_website_key')) {
            return esc_html__(get_option('gfe_website_key'));
        } else {
            return gfef_get_salt();
        }
    }
	function check_website_key($num) {
        if (gfef_get_salt() !== false && $num === 1) {
            return 'YES';
        } else if (gfef_get_salt() === false && $num === 1) {
            return 'NO';
        }
		if (gfef_get_salt() !== false && $num === 2) {
            return '<img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> ' . esc_html__('Website key generated.');
        } else if (gfef_get_salt() === false && $num === 2) {
            return '<img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> ' . esc_html__('Save settings to generate key.');
        }
    }
	function check_encryption_key($num) {
        if (get_option('gfe_encryption_key') && $num === 1) {
            return 'YES';
        } else if (!get_option('gfe_encryption_key') && $num === 1) {
            return 'NO';
        }
		if (get_option('gfe_encryption_key') && $num === 2  && !get_option('gfe_encryption_bypass')) {
            return '<img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> ' . esc_html__('Encryption password active.');
        } else if (!get_option('gfe_encryption_key') && $num === 2 && !get_option('gfe_encryption_bypass')) {
            return '<img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> ' . esc_html__('Enter an encryption password.');
        } else if ($num === 2 && get_option('gfe_encryption_bypass')) {
            return '<img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="12" width="12"> ' . esc_html__('Not used: Encryption off.');
        }
    }
	function gfe_encryption_on($num) {
		if (get_option('gfe_encryption_bypass') && $num === 1) { 
			$gfe_encryption_status = 'NO';
		} else if (!get_option('gfe_encryption_bypass') && $num === 1) {
			$gfe_encryption_status = 'YES';
		} 
		if (get_option('gfe_encryption_bypass') && $num === 2) { 
			$gfe_encryption_status = ' <p><img src="' . plugin_dir_url(__FILE__) . 'images/unlocked.png" height="14" width="14"> ENCRYPTION OFF</p>';
		} else if (!get_option('gfe_encryption_bypass') && $num === 2) {
			$gfe_encryption_status = ' <p><img src="' . plugin_dir_url(__FILE__) . 'images/locked.png" height="14" width="14"> ENCRYPTION ON</p></div>';
		} 
		return $gfe_encryption_status;
	}
	function gfe_merge_tag_filter_on($num) {
		if (get_option('gfe_mergefilter') && $num === 1) { 
			$gfe_merge_tag_filter_status = 'NO';
		} else if (!get_option('gfe_mergefilter') && $num === 1) {
			$gfe_merge_tag_filter_status = 'YES';
		} 
		if (get_option('gfe_mergefilter') && $num === 2) { 
			$gfe_merge_tag_filter_status = ' <p><img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="14" width="14"> MERGE TAG FILTER OFF</p>';
		} else if (!get_option('gfe_mergefilter') && $num === 2) {
			$gfe_merge_tag_filter_status = ' <p><img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> MERGE TAG FILTER ON</p></div>';
		} 
		return $gfe_merge_tag_filter_status;
	}
    
    ?>
    <style>
		input[type="text"], 
		textarea {
			border-radius: 7px;
			border: 2px;
		}
		input[type="text"] {
			width:100%;
			border: 2px solid #ddd;
		}
		button {
			border: 2px solid #00feff;
			background: none;
			background-color: #ffffff;
			border-radius: 7px;
			padding: 5px;
		}
		textarea.redtextarea {
			width: 100%;
			border: 2px solid red;
			border-radius: 7px;
		}
		input.redtext {
			width: 100%;			
			border: 2px solid red;
			border-radius: 7px;
		}
		table th.settings {
			background-color:#FFFFFF;
			padding: 10px;
			text-align: center;
		}
		table tr.settingstr {
			border-top: 2px solid #00feff;
		}
		table.form-table {
			max-width: 830px;
		}
		.center {
			 text-align: center;
		}
		table.encryptiontest tr:nth-child(even) {
			background-color: #eeeeee;
		}
		table.gfesystemcheck tr:nth-child(even) {
			background-color: #eeeeee;
		}
	</style>
    <div class="wrap gfe-admin-page" style="position:relative;">
    <?php echo '<img src="' . plugin_dir_url(__FILE__)  . 'images/GFEFicon80x80.jpg" height="80" width="80" style="display:inline-block; float:left; margin-right: 10px;">'; ?>
    <h1 style="display: inline;">Gravity Forms Encrypted Fields</h1><span> ver <?php echo gfe_get_plugin_version(); ?></span>
    <p style="margin-top:0px;">by <a href="https://codecanyon.net/user/pluginowl/portfolio" target="_blank">Plugin Owl</a></p>
    <p style="margin-top:0px;"><a href="https://codecanyon.net/user/pluginowl/portfolio" target="_blank">Check out our other plugins!</a></p><br/>
    <p><?php
	if (!get_option('gfe_encryption_method')) {
		echo '<div style="max-width:580px; border: 1px solid #000000; padding: 10px; background-color: #FFFFFF;">
			<p style="color: red; font-style: bold;"><span style="font-size: 40px;">!!! STOP !!!</span><br/>NOTICE: YOU MUST FOLLOW THIS PLUGINS README FILE INSTRUCTIONS IF UPGRADING FROM A VERSION PREVIOUS TO 3.0 TO VERSION 3.0+</p>
			<p>Version 3.0+ bring significant changes to the encryption used and also strengthens the data security over previous versions.</p>
			<p> If you did not decrypt ALL previous entries before upgrading from ver< 3.0 to ver 3.0+ according to this versions readme file upgrade instructions. Please reinstall your previous version according to its readme file upgrade instructions and then refer to the this versions readme file upgrade instructions to proceed with decrypting ALL old entries before upgrading to version 3.0+ after upgrading.  These entries will be re-encrypted under new encryption in ver 3.0+ </p><br/>
			<p>If this is a brand new install you do NOT need to do anything, and can simply select your encryption type and continue with setup</p>
		</div><br/>';
	} 			
	?></p>
    
    <?php
	// add/remove form encryption
	if (get_option('gfe_encrypt_decrypt') && get_option('gfe_encrypt_decrypt_form')) {
		$encrypt_decrypt				= get_option('gfe_encrypt_decrypt') ? esc_html__(get_option('gfe_encrypt_decrypt')) : false;
		$encrypt_decrypt_form 			= get_option('gfe_encrypt_decrypt_form') ? intval(esc_html__(get_option('gfe_encrypt_decrypt_form'))) : false;
		$encrypt_decrypt_entries		= get_option('gfe_encrypt_decrypt_form_entries') ? esc_html__(get_option('gfe_encrypt_decrypt_form_entries')) : false;
		$encrypt_decrypt_fields			= get_option('gfe_encrypt_decrypt_form_fields') ? esc_html__(get_option('gfe_encrypt_decrypt_form_fields')) : false;
		$encrypt_decrypt_paging			= get_option('gfe_encrypt_decrypt_form_entry_paging') ? intval(esc_html__(get_option('gfe_encrypt_decrypt_form_entry_paging'))) : false;
		$encrypt_decrypt_paging_offset	= get_option('gfe_encrypt_decrypt_form_paging_offset') ? intval(esc_html__(get_option('gfe_encrypt_decrypt_form_paging_offset'))) : false;
		$gfe_current_user 				= wp_get_current_user();
		$encrypt_decrypt_user			= $gfe_current_user->user_login;
		update_option('gfe_encrypt_decrypt_user', $encrypt_decrypt_user);
		gfef_add_remove_encryption($encrypt_decrypt, $encrypt_decrypt_form, $encrypt_decrypt_entries, $encrypt_decrypt_fields, $encrypt_decrypt_paging, $encrypt_decrypt_paging_offset);
	}
	// global form encryption process
	if(get_option('gfe_global_encryption_on')) {
		gfef_global_encryption(get_option('gfe_global_encryption_on'));
	}
	?>
 
    <form method="post" action="options.php" autocomplete="invalid">
        <?php settings_fields('gravity_forms_encrypted_fields_settings-group'); ?>
        <?php do_settings_sections('gravity_forms_encrypted_fields_settings-group'); ?>
    	<div class="gfef-sticky-save" style="position:fixed; top:20px; right:5px;"><?php submit_button();?></div>
        <table class="form-table">
        	<tr valign="top">
                <th scope="row">INSTRUCTIONS</th>
                <td>
                    <div id="gfe-instructions" style="
                    display: block;
                    width: 100%;
                    max-width: 600px;">
                        <p><b><span style="color:red;">NOTICE:</span> You are responsible for and accept full resposibility for any loss of data through use of this plugin software and accept all risk involved.<p>You accept full responsibility for any data security compliance, and any loss of any type incurred by yourself or any other persons or entities involved in the management and/or  ownership of this website or its related data.</p>
                        <p>Your use of this plugin software signifies your release and discharge of this software or any of its authors, owners, or representative entities from any and all liability, claim, demand or action relating to the use of this software.</p></b><p><br/></p>
                        <script type='text/javascript'>
							function toggle_visibility(id) {
								 var e = document.getElementById(id);
								 if(e.style.display == 'block')
									e.style.display = 'none';
								 else
									e.style.display = 'block';
							 }
						</script>
                        <p><button id="instructionsToggle" type="button" onClick="toggle_visibility('instructionsFull');">Show/Hide Full Setup Instructions</button></p>
                        <div id="instructionsFull" style="display:none;">
                            <p>This plugin encrypts data for storge in the database for selected fields at the time of form submission or on entry update if the field data is changed and  displays both encrypted and non encrypted submitted field data properly whether or not encryption is turned on for a field at any time. </p><br/>
                            <p>Switching encryption on or off AFTER data is submitted to a field will not encrypt or decrypt previously submitted data in the database, unless it is changed and updated after encryption is turned on or off, and could result some data being encrypted and other data left not encrypted for the same field. However when encryption is turned ON for a field, any previously unencrypted data submitted will still be blocked/hidden from users without view permissions using the "hide field value restricted display". This indicates that while the data value is being hidden in admin, it is NOT actually encrypted. Enter "encrypted" (or whatever youd like) into the "Encrypted Field Restricted Display" and enter "hidden" (or whatever youd like) into the "Hide Field Value Restricted Display" so you can see when values are being restricted from a users view instead of just being blank.<br/>To encrypt previously existing database field values please use the "Encrypt/Decrypt Form Entries" tool on this page.</p><br/>
                            <p>It is strongly recommended to decide on encrypting a field at its creation and leave it on permanently from there, while "hide field value" can be turned on or off at any time.</p><br/>
                            <p>To edit encrypted or hidden data, just edit the form entry as usual. Any users who can edit entries but cannot read hidden or encrypted data can still enter new data over it from the entry edit screen.</p><br/>
                            <p>To test on a field with data already entered you can backup your database and turn on encryption in the fields advanced tab and then thouroughly check all existing entries data for integrity before saving any new encrypted entries.</p><br/>
                            <p>Once submitted field data is saved encrypted in the database, the field data will be automatically decrypted and readable for users/roles with permissions when viewing in all admin interfaces and export options. This means that you should NOT ever expect to see encrypted stings such as "GFEncrypt: 7ef46193a17a23580e1019c054OURma215VFJuWVlyUGtCZkdnZmQ2dz09" in the admin interface (unless you either have "ENCRYPTION VERIFICATION MODE" on to verify encryption, or are using encrypted merge tags to display encrypted strings). The data should always either be returned as readable for users with view permissions, or should return the restricted display for the type of restriction occurring (encrypted/hidden).
                            After you have encrypted some data through form submission or update or the "Encrypt/Decrypt Form Entries" tool, if you can still read it as normal, it is because you have the view permissions to do so. If you want to verify it is actually encrypted you can use one of the following methods to do so:
                            <br/> Use "ENCRYPTION VERIFICATION MODE". (shows actual encrypted strings)
                            <br/> Log into your database and view the direct data. (shows actual encrypted strings)
							<br/> Remove your view permissions temporarily. (shows restricted display)
							<br/> Log in with a user without permissions. (shows restricted display)
                           </p><br/>
                            <p>This plugin can also optionally simply hide field data values in admin from individual users wthout permission. This option uses no encryption, but can be great for sites that just need a solution to hide form field data from some users.</p><br/><p><br/></p>
                            
                            <p><b>QUICK SETUP:</b>
                            <p><b>- </b>QUICK ENCRYPTION SETUP: After system check pass, follow "Encryption Type" setup instructions below, then to only allow the user "admin" access to all encrypted and/or hidden field values simply enter an encryption password, enter "lockdown" in the "User Lockout List", and enter "admin" in the "User Access List". Enter "encrypted" (or whatever youd like) into the "Encrypted Field Restricted Display" and enter "hidden" (or whatever youd like) into the "Hide Field Value Restricted Display" so you can see when values are being restricted from a users view instead of just being blank. Turn on "Encrypt field value" for fields in thier advanced options tab (or optionally use the "Global Form Encryption Switch" on this page to turn encryption on for all supported fields on a specified form at once), then exclude those fields in any of that forms {all_fields} merge tag uses using the "{all_fields} Merge Tag Exclude/Include Options" below.</p>
                            <br/>
                            <p><b>- </b>QUICK HIDDEN FIELD VALUES ONLY SETUP: After system check pass (Skip Encryption Password), to only allow the user "admin" access to all hidden field values simply turn on the "Encryption Bypass", enter "lockdown" in the "User Lockout List", and enter "admin" in the "User Access List".  Enter "encrypted" (or whatever youd like) into the "Encrypted Field Restricted Display" and enter "hidden" (or whatever youd like) into the "Hide Field Value Restricted Display" so you can see when values are being restricted from a users view instead of just being blank. Turn on "Hide field value" for fields in thier advanced options tab, then exclude those fields in any of that forms {all_fields} merge tag uses using the "{all_fields} Merge Tag Exclude/Include Options" below.</p>
                            </p>
                            <br/>
                            <p><b>FULL SETUP</b></p>
                            <p><b>1.</b> Ensure your system meets the below system check requirements.</p>
                            <p><b>2.</b> First follow "Encryption Type" setup instructions below, Enter "encrypted" (or whatever youd like) into the "Encrypted Field Restricted Display" and enter "hidden" (or whatever youd like) into the "Hide Field Value Restricted Display" so you can see when values are being restricted from a users view  instead of just being blank. Then enter a strong custom encrytption password and Save Changes again.</p>
                            <p><b>3.</b> Once the password is saved, your encrypted fields will begin to be saved under the given website key / password combination.</p>
                            <p><b>4.</b> If you are going to only hide admin field values and not use encryption at all, turn on the "Encryption Bypass" below.</p>
                            <p><b>5.</b> Use the form fields "Advanced" tab encryption options to turn on the database encryption or hide field value option per individual field (or optionally use the "Global Form Encryption Switch" on this page to turn encryption on for all supported fields on a specified form at once).</p>
                            <p><b>6.</b> By default, ALL users that can view form entries have view permissions to a field unless a field has anything entered into its "User View Permission" setting. Once any usernames/roles are entered into the "User View Permission" setting, all other users/roles will be restricted unless they have an overriding view permission. The "User View Permission" is per individual field and hovering over the help icon there will detail how it works as well as point to additional global permissions for more control. </p>
                            <p><b>7.</b> You can use the "Limit User/Role View Permission Lists" list below to enter comma separated user names to globally restrict what user names are valid when entered into any fields "User View Permission" list.</p>
                            <p><b>8.</b> You can use the "User Lockout List" below to enter comma separated user names to globally BLOCK individual user view permissions for ALL encrypted or hidden field values, regardless of individual fields "User View Permission" settings.</p>
                            <p><b>9.</b> You can use the "User Access List" below to enter comma separated user names to globally ALLOW individual user view permissions for ALL encrypted or hidden field values, regardless of individual fields "User View Permission" settings and "User Lockout List" settings.</p>
                            <p><b>10.</b> It is strongly reccommended that you use the "Merge Tag Restricted Display Filter" (this is on by default) and the "{all_fields} Merge Tag Exclude/Include Options" to prevent users with view permissions from sending out a forms encrypted or hidden field data in notifications, confirmations, or other gravity forms merge tag uses as readable. This will block all encrypted and hidden field data from being sent out in notifications/confirmations with the restricetd display. If you woul like to output field data as readable in notifications and/or confirmations, use the decrypted merge tags by allowing them per form/field with the setting below. They always output the readable decrypted field data.</p><br/>
                            <p><b>TEST.</b> When setup is complete, backup your database, and do some test entries in a test environment if possible. Check results with various dummy users to be sure your setup is functioning as intended before going live. Additional options available below may be used or disabled for your sites needs as well.</p><br/>
                            <p><b>NOTES:</b>
                            <p><b>- </b>Available Field Types: text, textarea, date, name, number, email, phone, website, address, dropdown, radio, multi select, checkbox.</p><br/>
                            <p><b>- </b>This plugin splits encryption keys storage methods, and attempts to block all unauthorized access to its files and this administration screen.<br/>The weakest areas for security are likely your database/ftp/wordpress login security and other wordpress users given high level permissions. Please understand that any other users given the ability to edit_users will be able to do whatever they like administratively since they can change roles and permissions, and can then if you have no lockout password set can access this page and any encrypted data.</p>
                            <br/>
                            <p><b>- </b>While this plugin contains many options for data view permissions that can work together to create more complex permission systems, you do not have to use ANY permission settings to encrypt data in the database. Just ready the system and turn on any fields encryption. However it is strongly reccommended that minimally you also use the "{all_fields} Merge Tag Exclude/Include Options" to prevent users with view permissions from sending out a forms encrypted or hidden field data in notifications, confirmations, or other gravity forms merge tag uses as readable.</p>
                            <br/>
                            <p><b>- </b>Please note that any data that needs to be passed forward from a form submission to use in other functionality such as payment or product information, user registration fields, or a mailchimp email address should NOT be encrypted. Most of those other types of plugins or add-ons NEED to have access to the data without any encryption to do their work properly. You can always test to see what exceptions there might be. Additionally, Gravity Forms itself can pass data forward to other forms from a submitted form through merge tag functionality and that data will be blocked by the merge tag filter, so using the custom decrypted and/or user decrypted merge tags can serve to pass forward form data when desired.</p>
                            <br/>
                            <p><b>- </b>If you change the key or password you will not be able to view data saved under them until the key and password are changed back.</p><br/>
                            <p><b>NOTICE:</b> This plugin fills one necessary component of data protection. The usage of other basic protections such as SSL, VPS, User capability restrictions, and strong admin user password enforcement alongside this plugin are strongly recommended. You may be subject to implementing additional data protection policies and procedures depending on the sensitivity level and type of the information you are collecting. Visit <a href="https://www.gravityhelp.com/documentation/article/security/" target="blank">here</a> for additional security practices using Gravity Forms in general. </p><br/>
                        </div>
                	</div>
                </td>
            </tr>
        	<tr valign="top">
                <th scope="row">SYSTEM CHECK</th>
                <td>
                    <div id="gfe-system-check" style="max-width:600px;">
                        <table class="gfesystemcheck"
                        style="
                        width: 100%;
                        max-width:600px;
                        background-color: #FFFFFF;
                        border: 2px solid #666666;
                        border-radius: 7px;
                        padding: 0px;
                        margin: 0;
                        font-size: 16px;
                        line-height: 30px">
                            <tr valign="top">
                                <td>Server Encryption Support:</td>
                                <td><strong> <?php echo gfe_check_encryption_function(); ?></strong></td>
                                <td><?php echo gfe_check_encryption(); ?></td>
                            </tr>
                            <tr valign="top">
                                <td>PHP Version:</td>
                                <td><strong><?php echo phpversion(); ?></strong></td>
                                <td><?php echo gfe_check_php(); ?></td>
                            </tr>
                            <tr valign="top">
                                <td>Wordpress Version:</td>
                                <td><strong><?php echo get_bloginfo( 'version' ); ?></strong></td>
                                <td><?php echo gfe_check_wp_version(); ?></td>
                            </tr>
                            <tr valign="top">
                                <td>Gravity Forms Version:</td>
                                <td><strong><?php echo GFCommon::$version; ?></strong></td>
                                <td><?php echo gfe_check_gf_version(); ?></td>
                            </tr>
                            <tr valign="top">
                                <td>Website Key:</td>
                                <td><strong><?php echo check_website_key(1); ?></strong></td>
                                <td><?php echo check_website_key(2); ?></td>
                            </tr>
                            <tr valign="top">
                                <td>Encryption Password:</td>
                                <td><strong><?php echo check_encryption_key(1); ?></strong></td>
                                <td><?php echo check_encryption_key(2); ?></td>
                            </tr>
                            <tr valign="top">
                                <td>Encryption Type:</td>
                                <td><strong><?php echo gfe_check_encryption_type(1); ?></strong></td>
                                <td><?php echo gfe_check_encryption_type(2); ?></td>
                            </tr>
                            <tr valign="top">
                                <td>Encryption Turned On:</td>
                                <td><strong><?php echo gfe_encryption_on(1); ?></strong></td>
                                <td><?php echo gfe_encryption_on(2); ?></td>
                            </tr>
                            <tr valign="top">
                                <td>Merge Tag Filter On:</td>
                                <td><strong><?php echo gfe_merge_tag_filter_on(1); ?></strong></td>
                                <td><?php echo gfe_merge_tag_filter_on(2); ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">ENCRYPTION TEST</th>
                <td>
                    <div id="gfe-test" style="max-width:600px;">
                        <p>This test uses current website key / encryption password config.</p>
                        <table class="encryptiontest"
                           style="
                            width: 100%;
                            max-width: 600px;
                            background-color: #FFFFFF;
                            border: 2px solid #666666;
                            border-radius: 7px;
                            padding: 0px;
                            margin: 0;
                            font-size: 12px;
                            line-height: 10px;">
                            <tr><td class="center">ENCRYPTION TYPE: </td><td><p><?php
							$gfe_type = get_option('gfe_encryption_method');
							if ($gfe_type == 1) {
								echo 'Open SSL';
							} else if ($gfe_type == 2) {
								echo 'Mcrypt';
							} else {
								echo 'Encryption type not selected'; 
							}
							?></p></td></tr>
                            <tr><td class="center">SUBMITTED DATA: </td><td><p><?php $gfe_test = 'my personal info 123-45-6789'; echo $gfe_test; ?></p></td></tr>
                            <tr><td class="center">CUSTOM DATA:<?php submit_button();?></td><td><input type="text" id="gfe_custom_data_search" name="gfe_custom_data_search" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_custom_data_search'));} ?>"/><p>Optionally enter custom data see resulting encrypted data below.</p></td></tr>	
                            <tr><td class="center">SAMPLE ENCRYPTED DATA: </td><td><p style="word-break:break-word;"><?php
								$gfe_test_encrypted = null;
								$use_mcrypt 		= apply_filters('gform_use_mcrypt', function_exists( 'mcrypt_encrypt'));
								$use_openssl		= function_exists('openssl_encrypt') && extension_loaded('openssl');
								$gfe_type			= get_option('gfe_encryption_method');
	
								if (get_option('gfe_custom_data_search') && is_admin() && current_user_can('edit_users')){
									 $gfe_test = get_option('gfe_custom_data_search');
								}
								$gfe_test_encrypted = gfef_encrypt($gfe_test, null);
                                 
                                if ($gfe_test_encrypted === $gfe_test && !get_option('gfe_encryption_bypass')) { 
                                    $gfe_test_encrypted = $gfe_test_encrypted . '<div style="background-color:#fed4d4; padding:0px 10px;"><p style="text-align: center;"><span style="color:#de0101;"><img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> NOT ENCRYPTED! VIEW SYSTEM CHECK</span></p></span>';
                                } else if ($gfe_test_encrypted === $gfe_test && get_option('gfe_encryption_bypass')) { 
                                    $gfe_test_encrypted = $gfe_test_encrypted . '<div style="background-color:#e6f8eb; padding:0px 10px;"><p style="text-align: center;"><span style="color:#4ecd33;"><img src="' . plugin_dir_url(__FILE__) . 'images/unlocked.png" height="12" width="12"> ENCRYPTION TURNED OFF</span></p></span>';
                                } else if (substr($gfe_test_encrypted, 0, 11) === 'GFEncrypt: '){
                                    $gfe_test_encrypted = $gfe_test_encrypted . '<div style="background-color:#e6f8eb; padding:0px 10px;"><p style="text-align: center;"><span style="color:#4ecd33;"><img src="' . plugin_dir_url(__FILE__) . 'images/locked.png" height="14" width="14"> ENCRYPTION SUCCESSFUL</span></p></div>';
                                }					 
                                echo $gfe_test_encrypted; 
                             ?></p</td>
                            </tr>
                            <tr><td class="center">DECRYPTED DATA: </td><td><p><?php
								$gfe_test_encrypted = null;
								$gfe_test_decrypted = null;
								$use_mcrypt 		= apply_filters('gform_use_mcrypt', function_exists( 'mcrypt_encrypt'));
								$use_openssl		= function_exists('openssl_encrypt') && extension_loaded('openssl');
								$gfe_type			= get_option('gfe_encryption_method');
								$gfe_test_encrypted = gfef_encrypt($gfe_test, null);
								$gfe_test_decrypted = gfef_decrypt($gfe_test_encrypted, null);
								
								if ($gfe_test_encrypted === $gfe_test && $gfe_test_decrypted === $gfe_test) { 
									$gfe_test_decrypted = $gfe_test_decrypted . '<div style="background-color:#e6f8eb; padding:0px 10px;"><p style="text-align: center;"><span style="color:#4ecd33;"><img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> ORIGINAL VALUE RETURNED</span></p></div>';
								} else if (substr($gfe_test_encrypted, 0, 11) === 'GFEncrypt: ' && $gfe_test_decrypted === $gfe_test){
									$gfe_test_decrypted = $gfe_test_decrypted . '<div style="background-color:#e6f8eb; padding:0px 10px;"><p style="text-align: center;"><span style="color:#4ecd33;"><img src="' . plugin_dir_url(__FILE__) . 'images/unlocked.png" height="14" width="14"> DECRYPTION SUCCESSFUL</span></p></div>';
								} else if (substr($gfe_test_encrypted, 0, 11) === 'GFEncrypt: ' && $gfe_test_decrypted !== $gfe_test && get_option('gfe_encryption_key_override')){
									$gfe_test_decrypted = '<div style="background-color:#fed4d4; padding:0px 10px;"><p style="text-align: center;"><span style="color:#de0101;"><img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> ENCRYPTION PASSWORD OVERRIDE IS ON.</span></p><p><span style="color:#de0101;">ENCRYPTION IS WORKING BUT TEST DATA CANNOT BE DECRYPTED UNDER THE CURRENT ENCRYPTION OVERRIDE PASSWORD.</span></p><p><span style="color:#de0101;">REMOVE THE OVERRIDE TO TEST DECRYPTION WITH THE CURRENT ACTIVE ENCRYPTION PASSWORD.</span></p></div>';
								} else {
									$gfe_test_decrypted = '<div style="background-color:#fed4d4; padding:0px 10px;"><p style="text-align: center;"><span style="color:#de0101;"><img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> DECRYPTION ERROR! REVIEW SYSTEM CHECK ABOVE</span></p></div>';
								}
								echo $gfe_test_decrypted; ?></p></td>
                            </tr>
                            <tr><td class="center">ENCRYPTED FIELD<br/>RESTRICTED DISPLAY: </td><td><p><?php
								if (get_option('gfe_restricted')) {
									echo esc_html__(get_option('gfe_restricted'));
								} else {
									echo '<div style="background-color:#fed4d4; padding:0px 10px;"><p style="text-align: center;"><span style="color:#de0101;"><img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> ENCRYPTED FIELD RESTRICTED DISPLAY is not set. Restricted encrypted field views will be blank.</span></p></span>';
								}	
							?></p></td></tr>
                            <tr><td class="center">HIDE FIELD VALUE<br/>RESTRICTED DISPLAY: </td><td><p><?php
								if (get_option('gfe_hidevalue')) {
									echo esc_html__(get_option('gfe_hidevalue'));
								} else {
									echo '<div style="background-color:#fed4d4; padding:0px 10px;"><p style="text-align: center;"><span style="color:#de0101;"><img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> HIDE FIELD VALUE RESTRICTED DISPLAY is not set. Restricted hidden field views will be blank.</span></p></span>';
								}	
							?></p></td></tr>
							<tr id="gfefEVM" style="background-color: #ffe4ee;"><td  class="center" style="border-radius: 7px;"><span style="color:red;">ENCRYPTION VERIFICATION MODE</span></td><td style="border-radius: 7px;"><p><input type="checkbox" name="gfe_show_encryption" value="1" <?php checked(1, get_option('gfe_show_encryption'), true); ?> /><p><span style="color:red;"><b>Only use when site is either not live or is in maintenance mode and any form entry or editing is not in session. Do not adjust any other settings while using this mode. Failure to adhere to these guidelines could result in possible data loss:</span><br/> Temporarily</b> turn this on to reveal any raw encrypted database data in ALL entry viewing interfaces and exports to quickly verify encryption. <span style="color:red;"><br/><b>DO NOT LEAVE THIS ON.</b></p></span></p><br/>		
                       		</td></tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr valign="top" style="border-top: 2px solid red;">
                <th class="settings" scope="row"><span style="color:red;">CAUTION !</span></th>
                <td>
                    <div id="gfe-administration-instructions" style="
                            width: 100%;
                            max-width: 570px;
                            background-color: #ffe4ee;
                            border: 2px solid red;
                            border-radius: 7px;
                            padding: 10px;
                            margin: 0;
                            font-size: 12px;
                            line-height: 10px;">
                        <p><span style="color:red;"><b>ADMINISTRATIVE AREA BELOW</b></span></p>
                        <p>Please use caution when editing the settings below, and BACK THESE SETTINGS UP so if you need to restore them you can easily.</p><p><b>It is imperitive that you DO NOT change any below settings or encryption / "hide field  data" options for a form field when any affected form entries are being edited / updated. Turning these options on or off when restricted form data is updated may result in the restricted data being overwritten with the restricted data display if the editing user does not have view permissions!</b></p><p>Any data written under a given key / password combination, can ONLY be read under the same combination, and may not be visible in form entries until the correct key / password combination is restored. It is HIGHLY reccommeded that you set your password and website key once and leave them alone forever, unless there should be some absolutely unavoidable need to change them.</p><br/>
                        <p>This page and its options are visible to ADMIN/SUPER ADMIN or users with edit_users permissions only.</p>
                        <p>Do not use any values equal to 0 for settings.</p>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row" >Encryption Type<?php if (!get_option('gfe_encryption_method')) {echo '<br/><p style="color: red; font-style: bold;">STOP! READ NOTICE</p>';} ?></th>
                <td>
                <?php
				if (!get_option('gfe_encryption_method')) {
					echo '<div style="max-width:580px; border: 2px solid #000000; padding: 10px; background-color: #FFFFFF;">
						<p style="color: red; font-style: bold;"><span style="font-size: 40px;">!!! STOP !!!</span><br/>NOTICE: YOU MUST FOLLOW THIS PLUGINS README FILE INSTRUCTIONS IF UPGRADING FROM A VERSION PREVIOUS TO 3.0 TO VERSION 3.0+</p>
						<p>Version 3.0+ bring significant changes to the encryption used and also strengthens the data security over previous versions.</p>
						<p> If you did not decrypt ALL previous entries before upgrading from ver <3.0 to ver 3.0+ according to this versions readme file upgrade instructions. Please reinstall your previous version according to its readme file upgrade instructions and then refer to the this versions readme file upgrade instructions to proceed with decrypting ALL old entries before upgrading to version 3.0+ after upgrading.  These entries will be re-encrypted under new encryption in ver 3.0+ </p><br/>
						<p>If this is a brand new install you do NOT need to do anything, and can simply select your encryption type and continue with setup</p>
					</div><br/>';
				} 
				?>
                	<?php if (!get_option('gfe_encryption_method')) {echo '<p style="color: red; font-style: bold;">STOP! READ NOTICE ABOVE</p>';} ?><input type="radio" name="gfe_encryption_method" value="1" <?php checked(1, get_option('gfe_encryption_method'), true); ?>> <b>OpenSSL</b>  (strongly recommended)<br/>
        			<?php if (!get_option('gfe_encryption_method')) {echo '<p style="color: red; font-style: bold;">STOP! READ NOTICE ABOVE</p>';} ?><input type="radio" name="gfe_encryption_method" value="2" <?php checked(2, get_option('gfe_encryption_method'), true); ?>> <b>Mcrypt</b>  (will be depricated from PHP core as of PHP 7.2+)
                    <div id="gfe-encryption-method-instructions" style="max-width:600px;">
                       	<p><?php echo gfe_check_encryption(); ?></p><br/>
                        <p>Select the Encryption type you would like to use.<br/>(OpenSSL is strongly recommended if it is available)</p>
                        <p>Data encrypted under one encryption type cannot be read under the other.</p>
                        <p><b>If switching encryption types it is strongly advised to use the "Encrypt/Decrypt Form Entries" tool to decrypt all past entries, and then switch encryption types and re encrypt them with the new encryption type.</b></p><br/>
                        <p>NOTES:<br/>
                        - Mcrypt is scheduled be depricated from PHP core as of ver 7.2+, but should still be available through add on installs.<br/>
                       	- OpenSSL is strongly recommended and may be required if you are currently running PHP 7.2+, or will be in the future.</p>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Delete Plugin Options on Removal</th>
                <td><input type="checkbox" name="gfe_delete_options" value="1" <?php checked(1, get_option('gfe_delete_options'), true); ?> />
                	<?php
					if (!get_option('gfe_delete_options')) { 
						$gfe_delete_options_status = ' <p><img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> Plugin options will currently be saved when you remove the plugin. This allows for safe removal and reinstallation of an upgraded or previous version without re-entering all setup options.</p><p> Be sure to back up your website key! This will have to be regenerated on update or removal and reinstallation and may change depending on changes you have made to your wordpress install. You can change it back with your copy if needed after update or reinstall.</p>';
					} else if (get_option('gfe_delete_options')){
						$gfe_delete_options_status = ' <p><img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="14" width="14"> <span style="color:red;">CAUTION!</span> All plugin options will currently be deleted when this plugin is removed. You should disable this if you are just removing to upgrade to a newer version.</p>';
					}?> 
					<div id="gfe_delete_options" style="max-width:600px;">
						<?php echo $gfe_delete_options_status; ?>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Allow Front End Display*<br/>*conditional option</th>
                <td><input type="checkbox" name="gfe_admin_only" value="1" <?php checked(1, get_option('gfe_admin_only'), true); ?> />
                	<?php
					if (!get_option('gfe_admin_only')) { 
						$gfe_admin_only_status = ' <p><img src="' . plugin_dir_url(__FILE__) . 'images/locked.png" height="14" width="14"> Encrypted and hidden fields can currently only be viewed as readable by logged in users in admin pages. To enable front end viewing for all users with permission, turn on this option.</p><p><b>NOTE:</b> This feature is considered "conditional" due to the large variety of ways that Gravity Forms data could possibly be shown on the front end of the site, and may not work with your site. Only plugins or code that uses the Gravity Forms get/save field API for front end display will be compatible. Please thouroughly test your system for functionality if you enable this option.</p>';
					} else if (get_option('gfe_admin_only')){
						$gfe_admin_only_status = ' <p><img src="' . plugin_dir_url(__FILE__) . 'images/unlocked.png" height="14" width="14"> Encrypted fields can currently be viewed as readable on any front end displays by users with permission. To restrict viewing to admin pages only, turn off this option.</p><p><b>NOTE:</b> This feature is considered "conditional" due to the large variety of ways that Gravity Forms data could possibly be shown on the front end of the site, and may not work with your site. Only plugins or code that uses the Gravity Forms get/save field API for front end display will be compatible. Please thouroughly test your system for functionality.</p>';
					}?> 
					<div id="gfe-admin-only-instructions" style="max-width:600px;">
						<?php echo $gfe_admin_only_status; ?>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Encryption Bypass</th>
                <td><input type="checkbox" name="gfe_encryption_bypass" value="1" <?php checked(1, get_option('gfe_encryption_bypass'), true); ?> />
                	<?php
					if (get_option('gfe_encryption_bypass')) { 
						$gfe_encryption_status = ' <p><span><img src="' . plugin_dir_url(__FILE__) . 'images/unlocked.png" height="14" width="14"> ENCRYPTION IS OFF</span></p>';
					} else if (!get_option('gfe_encryption_bypass')){
						$gfe_encryption_status = ' <p><span style="color:#4ecd33;"><img src="' . plugin_dir_url(__FILE__) . 'images/locked.png" height="14" width="14"> ENCRYPTION IS ON</span></p></div>';
					} 
					echo $gfe_encryption_status;
					?>
                    <div id="gfe-bypass-instructions" style="max-width:600px;">
                        <p>This data safe option will turn off / bypass ALL actual data encryption and still allow for hiding field values in admin with user viewing permission restrictions for fields with "hide field value" turned on.</p>
                        <p>If you are going to use this, it should ideally be turned on right after install so no encryption can be used.</p>
                        <p>If you want to only hide values on some fields while using encryption on others you can leave encryption on and select to either use encryption or hide values per individual field in the form field advanced options.</p>       
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Merge Tag Filter Bypass</th>
                <td><input type="checkbox" name="gfe_mergefilter" value="1" <?php checked(1, get_option('gfe_mergefilter'), true); ?> />
                	<div id="gfe-mergefilter-instructions" style="max-width:600px;">
                	<?php
						if (get_option('gfe_mergefilter')) { 
							$gfe_mergefilter_status = ' <p><span style="color:red;"><img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="14" width="14"> MERGE TAG RESTRICTED DISPLAY FILTER IS OFF</span></p><p>Turn this bypass option off to replace encrypted or hidden field values in merge tags with the encrypted field restricted display for ALL users in merge tag generated content such as confirmations or notifications.</p><p>If this is left on and a user with field viewing permission resends notifications or fills out a form that triggers notifications or confirmations or other merge tag results the encrypted and hidden data that the triggering user has permission to view will be readable to everyone in the resulting notifications and confirmations or other resulting merge tag output.</p><p>When the filter is not bypassed, this option requires that either encryption or hide field values is currently turned on for fields in order to hide output in merge tags. Any previously encrypted data for a field with encryption and hide field value currently turned off will still be decrypted and readable if user triggering the merge tag content has view permissions.<br/>To prevent this and/or exclude any fields from the {all_fields} merge tag entirely, use the "{all_fields} Merge Tag Exclude/Include Options" below.</p>';
						} else if (!get_option('gfe_mergefilter')){
							$gfe_mergefilter_status = ' <p><span style="color:#4ecd33;"><img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> MERGE TAG RESTRICTED DISPLAY FILTER IS ON </span></p><p>This filter replaces encrypted or hidden field values with the encrypted field restricted display in merge tags for ALL users, and is on by default to help prevent users with encrypted or hidden field view permissions from generating merge tag results (such as notifications and confirmations) with decrypted and unhidden field data.</p><br/>';
						} 
						echo $gfe_mergefilter_status;
					?>
                	<button id="gfe-mergefilter-instructions-idToggle" type="button" onClick="toggle_visibility('gfe-mergefilter-instructionsToggle');">SHOW/HIDE OPTION GUIDE</button></p>
						<div id="gfe-mergefilter-instructionsToggle" style="display:none;">
							<?php
								if (!get_option('gfe_mergefilter')){
									$gfe_mergefilter_status = '<p>If you want to pass certain encrypted or hidden field data through merge tags as readable all the time regardless of who fills out a form, please leave this filter on and use the "Decrypted Merge Tags" tool below and enter the corresponding custom merge tags that should be passed into your notifications and/or confirmations.</p><p>If you want to only allow <b>logged in users with view permissions</b> to send out field data as readable in resulting notiofications or confirmations when they fill out a form, turn on this bypass option to NOT use this filter.</p><br/><p>The merge tag filter requires that either encryption or hide field values is turned on for fields in order to hide the field value output in merge tags with restricted displays. Any previously encrypted data for a field with encryption or hide field value currently turned off will still be decrypted and readable by everyone in notifications and confirmations or wherever the merge tags are used if the user triggering the merge tag content generation(fills out form or resends notifications ect.) has view permissions to that field.<br/>To prevent this and/or exclude any fields from the {all_fields} merge tag entirely, use the "{all_fields} Merge Tag Exclude/Include Options" below.</p>';
								} 
								echo $gfe_mergefilter_status;
							?>
						</div>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">{all_fields} Merge Tag Exclude/Include Options</th>
                <td>
                    <div id="gfe-inc-exc-instructions" style="max-width:600px;">
                        <p>IN ADDITION to the above restricted display filter option you may use this plugin's "{all_fields} Merge Tag Exclude/Include Options" documented here to exclude encrypted or hidden or any other fields from your confirmations, notifications, or other gravity forms {all_fields} merge tag uses. You can also use these options to include HTML, Section Break, and Signatures in {all_fields} merge tag output.</p>
                        <br/>
                        <p><button id="gfe-inc-exc-instructions-idToggle" type="button" onClick="toggle_visibility('gfe-inc-exc-instructionsToggle');" >SHOW/HIDE OPTION GUIDE</button></p>
                        <div id="gfe-inc-exc-instructionsToggle" style="display:none;">
							<p>1. To exclude/remove a field(s) from {all_fields} mergetags add 'exclude[ID]' option to the {all_fields} tag. 'ID' is field's id.
							<br/>2. If you are not using the {all_fields} tag in confirmations or notifications or otherwise for a form, simply do not include merge tags for fields you do not want displayed.
							<br/>3. The 'include[ID]' option allows including HTML fields / Section Break field descriptions / Signature images  in {all_fields} merge tag output in notifications and confirmations ect.
							<br/>4. Visit <a href="http://www.gravityhelp.com/documentation/page/Merge_Tags" target="blank">http://www.gravityhelp.com/documentation/page/Merge_Tags</a> for a list of standard options.
							<br/>5. For gravity.pdf plugin exclusion use class "exclude" in field Custom CSS Class.
							</p>
							<p>
								<br/>Usage: {all_fields:include[ID],exclude[ID,ID]}
								<br/>Example: {all_fields:exclude[7,3]}
								<br/>Example: {all_fields:include[12]}
								<br/>Example: {all_fields:include[12],exclude[7,3]}
							</p>
						</div>              
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Decrypted Merge Tags<br/>{gfef_decrypt_FIELD ID}<br/>{gfef_decrypt_ALL}<br/> <br/>Encrypted Merge Tags<br/>{gfef_encrypt_FIELD ID}<br/>*developer tool<br/> <br/>User Decrypted Merge Tags<br/>{gfef_decrypt_user_FIELD ID}<br/>{gfef_decrypt_ALL_USER}</th>
                <td><textarea rows="4" style="width:100%; max-width:600px; border: 2px solid #ddd; border-radius: 7px;" name="gfe_decrypt_merge_tags" value="<?php echo esc_html__(get_option('gfe_decrypt_merge_tags')); ?>" /><?php echo  get_option('gfe_decrypt_merge_tags'); ?></textarea>
                    <div id="gfe-decrypt-merge-tags-instructions" style="max-width:600px;">
                        <p>Enter comma separated FORM ID:FIELD ID values to allow for using the specified fields decrypted merge tag in the specified forms merge tag output.
                        <br/>Enter comma separated FORM ID:FIELD ID:X values to allow for using the specified fields encrypted merge tag in the specified forms merge tag output. The only difference in entry is the additional 'X' to use the encrypted merge tag instead of the decrypted merge tag.
                        <br/>Enter comma separated FORM ID:FIELD ID:U values to allow for using the specified fields user view permisions decrypted merge tag in the specified forms merge tag output. The only difference in entry is the additional 'U' to use the user view permisions decrypted merge tag instead of the standard decrypted merge tag.
                        <br/>Enter comma separated FORM ID:ALL values to allow for outputing ALL encrypted and hidden fields as readable from the {gfef_decrypt_ALL} merge tag in the secified forms notifications or confirmations.
                        <br/>Enter comma separated FORM ID:ALL:U values to allow for outputing ALL encrypted and hidden fields the user has view permissions for as readable from the {gfef_decrypt_ALL_USER} merge tag in the secified forms notifications or confirmations.</p><br/>
                        <p><button id="gfe-decrypt-merge-tags-instructions-idToggle" type="button" onClick="toggle_visibility('gfe-decrypt-merge-tags-instructionsToggle');" >SHOW/HIDE OPTION GUIDE</button></p>
                        <div id="gfe-decrypt-merge-tags-instructionsToggle" style="display:none;">
							<p>The {gfef_decrypt_FIELD ID} merge tag outputs decrypted and human readable field data for the FIELD ID used.</p><br/>
							<p>The {gfef_decrypt_user_FIELD ID} merge tag outputs decrypted and human readable field data only for users generating the merge tags content that actually have view permissions to that field data. If you use this on a page then the viewing user would need permisions to the field data or they will get the retricted display. <span style="color:red;">Warning!</span> If you use this in a notification and the user triggering the notifications going out (by filling out form or resending notifications) has view permissions to the field(s) the decrypted data will be sent out in the notification e-mail. The decrypted output of the {gfef_decrypt_FIELD ID} merge tag bypasses ALL permissions and merge tag filtering and gives a constant decryted output. The {gfef_decrypt_FIELD ID} merge tags will not decrypt User Owned Fields unless the user generating the merge tag content owns the entries field data.</p>
							<br/>
							<p>The {gfef_encrypt_FIELD ID} merge tag is for developers to output an encrypted version of any field data in notifications and confirmations to be decrypted elsewhere by the data or email recipient. Data that is unencrypted will still have an encrypted version output by this merge tag. <b>Plugin Owl does NOT support or assist in the decryption of output encrypted fields on the recieving end of notifications or confirmations, but simply supplies this ability for developers to be able to pass encrypted field strings through email notifications and develop their own proper decryption for the other side.</b></p>
							<br/>
							<p>The {gfef_decrypt_ALL} merge tag outputs decrypted and human readable field data for ALL encrypted or hidden fields on a form (excluding user owned fields unless the owner triggers the merge tag results). The decrypted output of the {gfef_decrypt_ALL} merge tag bypasses ALL permissions and merge tag filtering and gives a constant decryted output. The {gfef_decrypt_ALL} merge tags will not decrypt User Owned Fields unless the user generating the merge tag content owns the entries field data.</p>
							<br/>
							<p>The {gfef_decrypt_ALL_USER} merge tag outputs decrypted and human readable field data for ALL encrypted or hidden fields on a form (excluding user owned fields unless the owner triggers the merge tag results) for users generating the merge tags content that actually have view permissions to that field data. The restricted display is shown for any fields they do not have access to. If you use this on a page then the viewing user would need permisions to the field data or they will get the retricted display. <span style="color:red;">Warning!</span> If you use this in a notification and the user triggering the notifications going out (by filling out form or resending notifications) has view permissions to the field(s) the decrypted data will be sent out in the notification e-mail.</p>
							<br/>
							<p>Using the decrypted merge tags ONLY decrypts for merge tag output and does not change permissions on the websites entry views.</p>
							<p>These custom merge tags are placed directly inline with your notification/confirmation content just like regular Gravity Forms merge tags where they are accepted. For information on how to use merge tags please refer to the Gravity Forms documentation.</p>
							<p>Before you can use these merge tags, you must "unlock" them by specifying any forms and fields here using the exact format provided in order for these merge tags to be valid for the fields when used in the specified forms. Using the merge tags for a forms notifications/confirmations without including it here will not function. This is to prevent user who can edit notifications/confirmations but cannot edit this plugins settings from sending out unauthorized decrypted data.</p>
							<br/>
							<p>Example: 22:1, 21:3, 22:4:X, 21:ALL, 24:7:U, 26:ALL:U</p>
							<p>The above example entered into this setting here will allow you to use the {gfef_decrypt_1} merge tag in form 22 notifications and confirmations (creates decrypted output of field 1), and the {gfef_decrypt_3} merge tag in form 21 notifications and confirmations (creates decrypted output of field 3), and the {gfef_encrypt_4} merge tag in form 22 notifications and confirmations (creates encrypted output of field 4), and the {gfef_decrypt_ALL} merge tag in form 21 notifications and confirmations (creates decrypted output of ALL encrypted and hidden fields in form 21), and the {gfef_decrypt_user_7} merge tag in form 24 notifications and confirmations (creates decrypted output of field 7 if the user has permission to the field), and the {gfef_decrypt_ALL_USER} merge tag in form 26 notifications and confirmations (creates decrypted output of ALL encrypted and hidden fields in form 26 if the user has permission to the fields)</p><br/>
							<p>.</p>
							<p>For multi part fields, you must specify which exact field id's you want returned.</p><br/>
							<p>Example: 22:9.3, 22:9.6</p><br/>
							<p>The above example entered into this setting here would allow for form ID 22 to use both {gfef_decrypt_9.3} and {gfef_decrypt_9.6} in its notifications/confirmatins to decrypt and output both the first and last name of the name field if field ID 9 is a "name". Please see documentation <a href="https://www.gravityhelp.com/documentation/article/field-object/" arget="blank">here</a> and <a href="https://www.gravityhelp.com/documentation/article/entry-object/#field-values" arget="blank">here</a> for starters on identifying multiple part fields IDs.</p>
							<br/>
							<p><button id="multi_part_field_idToggle" type="button" onClick="toggle_visibility('multi_part_field_id');"  style="border: 2px solid #00feff;
						background: none;background-color: #ffffff;border-radius: 7px;padding: 5px;">SHOW/HIDE MULTI PART FIELD ID GUIDE</button></p>
							<div id="multi_part_field_id" style="display:none;">
								<p>ADDRESS<br/>
								street  = FIELD ID.1<br/>
								city    = FIELD ID.3<br/>
								state   = FIELD ID.4<br/>
								zip     = FIELD ID.5<br/>
								country = FIELD ID.6<br/><br/>

								NAME<br/>
								first 	= FIELD ID.3<br/>
								last 	= FIELD ID.6<br/><br/>

								MULTI / CHECKBOXES<br/>
								box 1 = FIELD ID.1<br/>
								box 2 = FIELD ID.2<br/>
								ect.</p><br/>
							</div>
						</div>
					</div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Field Output Masking<br/>and Permissions Bypass</th>
                <td><textarea rows="4" style="width:100%; max-width:600px; border: 2px solid #ddd; border-radius: 7px;" name="gfe_masking" value="<?php echo esc_html__(get_option('gfe_masking')); ?>"  style="border: 2px; border-radius: 7px;"/><?php echo  get_option('gfe_masking'); ?></textarea>
                    <div id="gfe-masking-instructions" style="max-width:600px;">
                        <p>Enter formatted field masks to enable customized display output masking for specified forms and fields. This also overrides the standard restricted display and merge tag filter. Separate field masks with a comma. NO spaces in the mask.</p>
                        <p><button id="gfe-masking-instructions-idToggle" type="button" onClick="toggle_visibility('gfe-masking-instructionsToggle');" >SHOW/HIDE OPTION GUIDE</button></p>
                        <div id="gfe-masking-instructionsToggle" style="display:none;">
							<p><b>This feature is STRONGLY suggested to be used with <a href="https://www.gravityhelp.com/documentation/article/input-mask/" target="blank">input masked fields</a> that are required to be formatted a certain way so you can predict what the output mask will actually show. Using this without a field input mask could very possibly reveal encrypted or hiden data.</b></p>
							<p>Field data that a user has access to or is unencrypted and/or unhidden will just display normally. You CANNOT use a mask to show User Owned Field data.</p><br/>
							<p><b>Format:</b> Form ID : Field ID : beginnning charachter count : end charachter count : optional use in merge tags : optional permissions bypass to display full decrypted output.</p><br/>
							<p>Example: 12:4:0:4:M:F</p><br/>
							<p><b>The mask must complete the above first 4 sections in order. Enter '0' when no charachters are wanted. The last 2 sections for merge tags or full value are optional and can be in any order AFTER the mandatory first 4 sections.</b></p>
							<p>To also use this masked output in merge tags (this will override the merge tag filter display if its on), add a last mask section with "M" in it to use the mask on the field in merge tags. Example:   12:7:2:4:M</p>
							<p>Restricted display output from above example if data is "hippopotamus": hi***amus</p><br/>
							<p><b>Single Field Permissions Bypass</b></p>
							<p>To use the FULL decrypted output and bypass ALL view permissions, add a last mask section with "F" in it. (*if you only want to show full value in merge tags for notifications ect. but still have view restrictions on the site, please use the custom decrypted merge tags above.)    Example:   12:7:0:0:F</p>
							<p>Output from above example if data is "hippopotamus": hippopotamus</p><br/>
							<p>Example:   12:7:2:4:M  restricted displays will show beginning 2 charachters and last 4 charachters of form 22 for field 7 on the website and in merge tags</p><br/>
							<p>Example:   3:14:0:4  restricted displays will show no beginning charachters and last 4 charachters of form 3 for field 14 only on the website and NOT in merge tags</p>
							<p>Restricted display output from above example if data is "12-34-1234": ***1234</p><br/>
							<p>Example:   12:7:2:4:M, 3:14:0:4  will do BOTH of the above examples</p>
							<p>You can use 'M" and "F" together to output full decrypted display in merge tags too.</p>
							<p>Examples: 22:14:0:4:F, 22:3:3:0:F:M, 22:10:0:3:M:F</p>
						</div>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
            	<th class="settings" scope="row">Delete Entries After Submission</th>
                <td><textarea rows="4" style="width:100%; max-width:600px; border: 2px solid #ddd; border-radius: 7px;" name="gfe_delete_entry" value="<?php echo esc_html__(get_option('gfe_delete_entry')); ?>" /><?php echo  get_option('gfe_delete_entry'); ?></textarea>
                    <div id="gfe_delete_entry-instructions" style="max-width:600px;">
                        <p>Enter comma separated form ID's to have both the forms entries any uploaded files automatically deleted after notifications are sent on submission.</p>
                        <p>Use this to just send out notifications and not have to store the submitted entry data on your site while also keeping site size down and removing any uploaded files with potentially sensitive data such as a resume, from being stored on your website server.</p><br/>
                        <p>Example: 22,14,3</p>
                        <p>If the form has a User Registration feed the entry will be deleted after the user has been activated/updated.</p>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
            	<th class="settings" scope="row">Delete Only File Uploads After Submission</th>
                <td><textarea rows="4" style="width:100%; max-width:600px; border: 2px solid #ddd; border-radius: 7px;" name="gfe_delete_file_uploads" value="<?php echo esc_html__(get_option('gfe_delete_file_uploads')); ?>"  style="border: 2px solid #ddd; border-radius: 7px;"/><?php echo  get_option('gfe_delete_file_uploads'); ?></textarea>
                    <div id="gfe_delete_file_uploads-instructions" style="max-width:600px;">
                        <p>Enter comma separated form ID's to have only the forms uploaded files automatically deleted after notifications are sent on submission.</p>
                        <p>Use this to keep any sensitive data in uploaded files such as a resume, from being stored on your website server. It also works for just keeping total website size down instead of continually storing all uploaded files.</p><br/>
                        <p>Example: 22,14,3</p>
                        <p>If the form has a User Registration feed the uploaded files will be deleted after the user has been activated/updated.</p>
                        <p>Since the file will be deleted from the server, it NOT able to be viewed or downloaded by any created links to it. You can use the {all_fields} Merge Tag Exclude/Include Options to exlude the standard download link from showing up in {all_fields} notifications, and/or add the class "exclude" to the file upload field to have it not generate the download link in Gravity PDF plugin output.</p>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
            	<th class="settings" scope="row">Attach File Uploads to Notification Emails.</th>
                <td><textarea rows="4" style="width:100%; max-width:600px; border: 2px solid #ddd; border-radius: 7px;" name="gfe_attach_file_uploads" value="<?php echo esc_html__(get_option('gfe_attach_file_uploads')); ?>" /><?php echo  get_option('gfe_attach_file_uploads'); ?></textarea>
                    <div id="gfe_attach_file_uploads-instructions" style="max-width:600px;">
                        <p>Enter comma separated FORM ID:NOTIFICATION NAMEs to have the forms uploaded files automatically attached as physical file attachments to specified notifications that are sent on submission.</p><br/>
                        <p><button id="gfe_attach_file_uploads-instructions-idToggle" type="button" onClick="toggle_visibility('gfe_attach_file_uploads-instructionsToggle');" >SHOW/HIDE OPTION GUIDE</button></p>
                        <div id="gfe_attach_file_uploads-instructionsToggle" style="display:none;">                       
							<p>Example: 22:Admin Notification, 14:Johnny:Admin Notification, 3:UserNotification:Admin Notification:Email Sasha</p>
							<p>Explanation: The first item must always be the FORM ID then continue to add any notification names to have files attached to for that FORM ID separated by ':' and then separate each full sequence with a comma.</p>
							<p>Uploaded files will be attached and sent in notifications BEFORE any auto entry deletion or file deletion. If you auto delete file uploads or the entire entry the files will not be availabe to resend on any notification resends.</p>
							<p><b>You MUST be sure that the total size of the resulting email going out with attachments is smaller than your sending or the recipiants recieving e-mail allowed size, or the e-mail will NOT be delivered and files and entry could be auto deleted if using above "Delete File Uploads After Submission" or "Delete Entries After Submission" options. It is strongly reccommended that you constrain the upload size and total allowable uploads in any file upload fields to be sure that the total of the file size(s) in resulting email will be small enough to safely email every time to any recipients. You can check with your e-mail host and others as to a generally safe file size. We reccommend 7mb or less total email size including files.</b></p>
						</div>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Encrypted Field Restricted Display</th>
                <td><input type="text" name="gfe_restricted" style="border: 2px solid #ddd; border-radius: 7px;" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_restricted'));} ?>" style="width:100%; max-width: 600px;" maxlength="32"/>
                    <div id="gfe-restricted-instructions" style="max-width:600px;">
                        <p>Enter what you would like to display to users who DO NOT have access permissions to view <b>encrypted</b> values.<br/>
                        This will display for any data being stored encrypted, regardless of other settings even if encryption is later turned off for the field or "hide field data" is turned on instead. </p>
                        <p>Please also use the "Merge Tag Restricted Display Filter" and "{all_fields} Merge Tag Exclude/Include Options" above to ensure users with view permission do not send out encrypted and hidden field data as readable by triggering merge tag displays in notifications or confirmations ect. that use thier view permissions.</p>
                        <p>Using a unique value here will allow users without permission to know when data has been entered, and is actually encrypted vs just hidden when viewed without permission.</p>
                        <p>Default is to show nothing.</p>              
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Hide Field Value Restricted Display</th>
                <td><input type="text" name="gfe_hidevalue" style="border: 2px solid #ddd; border-radius: 7px;" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_hidevalue'));} ?>" style="width:100%; max-width: 600px;" maxlength="32"/>
                    <div id="gfe-hidevalue-instructions" style="max-width:600px;">
                        <p>Enter what you would like to display to users who DO NOT have access permissions to view <b>hidden</b> field values.</p>
                        <p>This will only display when the database value is simply hidden in admin, and IS NOT encrypted in the database.</p>
                        <p>Please also use the "Merge Tag Restricted Display Filter" and "{all_fields} Merge Tag Exclude/Include Options" above to ensure users with view permission do not send out encrypted and hidden field data as readable by triggering merge tag displays in notifications or confirmations ect. that use thier view permissions.</p>
                        <p>Using a unique value here will allow you to know what data is just hidden vs actually encrypted when viewed without permission. </p>  
                        <p>Default is to show nothing.</p>               
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">User/Role Encrypted Field Native Search Permission<br/></th>
                <td><textarea rows="4" style="width:100%; max-width:600px; border: 2px solid #ddd; border-radius: 7px;" name="gfe_user_search_permission_list" value="<?php echo esc_html__(get_option('gfe_user_search_permission_list')); ?>" /><?php echo  get_option('gfe_user_search_permission_list'); ?></textarea>
                    <div id="gfe-user-search-permission-list-instructions" style="max-width:600px;">
                        <p>Enter comma separated list of usernames/roles (use role slug) that can natively search entries of encrypted field data on the entries list page.</p>
                        <p>Native search finds exact values (not partial values contained within field data) only and this DOES INCLUDE finding values of multi part fields such as a first name/last name or city from a address.</p><br/>
                        <p><button id="gfe-user-search-permission-list-instructions-idToggle" type="button" onClick="toggle_visibility('gfe-user-search-permission-list-instructionsToggle');" >SHOW/HIDE OPTION GUIDE</button></p>
                      	<div id="gfe-user-search-permission-list-instructionsToggle" style="display:none;">
							<p>Users not on this list who have permission to view encrypted fields can still search for that data but return of data without native search is unreliable.</p> <p>Users who do not have permission to view encrypted field data cannot search based on its human readable value.</p>
							<p>You can only use native search to search for data submitted under the current website key/password.</p>
							<p><b>NOTICE: The ability to search for field data does not mean the data will display as readable if the user does not have permission to view the field data, but based on search criteria and results a user could determine what the data is regardless of its actuall display in results.</b></p>
						</div>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Limit User/Role View Permission Lists</th>
                <td><textarea rows="4" style="width:100%; max-width:600px; border: 2px solid #ddd; border-radius: 7px;" name="gfe_limit_user_view_permission_list" value="<?php echo esc_html__(get_option('gfe_limit_user_view_permission_list')); ?>" /><?php echo  get_option('gfe_limit_user_view_permission_list'); ?></textarea>
                    <div id="gfe-user-limit-permission-instructions" style="max-width:600px;">
                        <p>Enter comma separated list of usernames/roles (use role slug) to limit usernames and roles that <b>WILL BE VALID</b> when placed in ANY form fields advanced tab "User View Permission" list.</p>
                        <p><button id="gfe-user-limit-permission-instructions-idToggle" type="button" onClick="toggle_visibility('gfe-user-limit-permission-instructionsToggle');" >SHOW/HIDE OPTION GUIDE</button></p>
                        <div id="gfe-user-limit-permission-instructionsToggle" style="display:none;">
							<p>Use this to control users who have form editing permissions but DO NOT have access to this page's settings (edit_users permissions - this is normally ADMIN ONLY unless otherwise assigned to a user)  from adding unauthorized usernames or roles to fields "User View Permission" lists allowing unauthorized viewing of encryted or hidden data.</p>
							<p>If this is left blank, ALL usernames/roles in any fields "User View Permission" list will be accepted. If anything is entered here, only valid usernames/roles listed here AND in any fields "User View Permission" list will be given view permission for that field, and any unauthorized usernames or roles in the "User View Permission" lists will be ignored. If a fields "User View Permission" list is left blank it will still allow user permissions to ALL users regarless of validation restrictions here.</p>
							<p>This is a quick way to only ALLOW view permission for certain users/roles while still maintaning a level of per field control.</p>
						</div>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">User Lockout List</th>
                <td><textarea rows="4" style="width:100%; max-width:600px; border: 2px solid #ddd; border-radius: 7px;" name="gfe_user_lockout_list" value="<?php echo esc_html__(get_option('gfe_user_lockout_list')); ?>" /><?php echo  get_option('gfe_user_lockout_list'); ?></textarea>
                    <div id="gfe-user-lockout-instructions" style="max-width:600px;">
                        <p>Enter comma separated list of usernames that <b>CANNOT read ALL encrypted / hidden field data even if username is on "User View Permission" list</b> in form field.</p>
                        <p>Use this to globally lockout users who have form editing permissions but DO NOT have access to this page's settings (edit_users permissions - this is normally ADMIN ONLY unless otherwise assigned to a user)  from viewing any encryted data through any admin interface.</p>
                        <p>Enter "lockdown" anywhere in list to quickly lockout ALL users.</p>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">User Access List</th>
                <td><textarea rows="4" style="width:100%; max-width:600px; border: 2px solid #ddd; border-radius: 7px;" name="gfe_user_lockout_override_list" value="<?php echo esc_html__(get_option('gfe_user_lockout_override_list')); ?>" /><?php echo  get_option('gfe_user_lockout_override_list'); ?></textarea>
                    <div id="gfe-user-access-instructions" style="max-width:600px;">
                        <p>Enter comma separated list of usernames that <b>CAN read ALL encrypted / hidden field data even if username or lockdown is on "User Lockout List" list above, and/or username is not on "User View Permission" in field or lockdown is on "User View Permission" in field.</b></p>
                        <p>This access list globally gives individual users encrypted and hidden field access overriding every other view permission setting.</p>
                        <p><button id="gfe-user-access-instructions-idToggle" type="button" onClick="toggle_visibility('gfe-user-access-instructionsToggle');" >SHOW/HIDE OPTION GUIDE</button></p>
                        <div id="gfe-user-access-instructionsToggle" style="display:none;">
							<p>You can use this in combination with the "User Lockout List" to quickly globally allow certain users view permission to <b>ALL</b> encrypted or hidden fields While locking all others out, regardless of what is on any fields "User View Permission" list.</p>
							<p>For example: To quickly ONLY allow the user "User1" access to all hidden and encrypted fields simply enter "lockdown" in the "User Lockout List" and enter "User1" in this "User Access List". Or to keep individual fields "User View Permission" active and just allow "User1" access to all hidden and encrypted fields regardless of individual field "User View Permission" list settings just enter "User1" in this "User Access List".</p>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Website Key</th>
                <td><input type="text" name="gfe_website_key" style="border: 2px solid #ddd; border-radius: 7px;" value="" style="width:100%; max-width: 600px;" maxlength="32"/>
                    <div id="gfe-site-key-instructions" style="max-width:600px;">
                        <p><?php if (is_admin() && current_user_can('edit_users')){echo get_website_key();} ?><br/><?php echo check_website_key(2); ?></p><br/><b>COPY THIS KEY AND KEEP SAFE.</b><br/><p>This is your websites unique and secure auto generated key to VIEW AND WRITE data with. This should generate once you save changes, but will have to regenerate on plugin removal and reinstall. Be sure to keep a copy of your active key to replace the autogenerated one if you're using a custom key or have changed your WordPress installations security keys since initial install of this plugin.</p><p>Only the same website key / password combination can be used to transfer / recover data submitted / updated under them to Wordpress installations.<br/>You can enter a replacement website key if you like. Use normal ASCII charachters only.</p><br/>
                        <p><button id="gfe_website_key_manual_instructionsToggle" type="button" onClick="toggle_visibility('gfe_website_key_manual_instructions');"  style="border: 2px solid #00feff;
						background: none;background-color: #ffffff;border-radius: 7px;padding: 5px;">SHOW/HIDE HELP TO MANUALLY GENERATE</button></p>
							<div id="gfe_website_key_manual_instructions" style="display:none;">
								<p>If the website key will not automatically generate or save your custom key, you likely have a web server write permissions issues within your WordPress installation. This is most likely due to security restrictions on your web server. If you cannot resolve the permissions issue, you can save the website key manually by following these instructions.</p>
								<p>Using a <b>SECURE</b> FTP/SSH client, open the "salt.php" file within this plugin. The normal path for this file is /wp-content/plugins/gravity-forms-encrypted-fields/includes/. On line 7 where it reads<br/> $salt = "[%%SALT%%]";<br/>   change this to <br/>$salt = "<?php gfef_echo_salt(); ?>";</p>
								<p>Use copy and paste to avoid errors.</p></br/>
								<p>This manual entry above has been auto generated from your specific site, <b>is secure and unique to your WordPress installation only</b>, and does NOT need to be changed for security concerns. <b>If any changes must be made the entry must be of EQUAL CARACHTER LENGTH (32) and contain letters and numbers only ..NO SPECIAL CARACHTERS.</b> Remember to read the upgrade instructions in newer versions of the plugins .readme file and do not copy over this particular file/includes folder on upgrade to retain this setting.</p><br/>
							</div>
                    </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Encryption Password</th>
                <td><input type="text" name="gfe_encryption_key" style="border: 2px solid #ddd; border-radius: 7px;" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_encryption_key'));} ?>" style="width:100%; max-width: 600px;" maxlength="32"/>
                <div id="gfe-user-password-instructions" style="max-width:600px;">
                    <?php if (is_admin() && current_user_can('edit_users') && !get_option('gfe_encryption_key')){echo 
                    '<p><img src="' . plugin_dir_url(__FILE__) . 'images/x.png" height="12" width="12"> You must enter an encryption password to WRITE and VIEW data with before data encryption will begin.<br/><b>COPY THIS PASSWORD AND KEEP SAFE.</b><br/>Only the same password / website key can be used to transfer/recover/search data saved under them to Wordpress installations.<br/>USE NORMAL ASCII CHARACHTERS ONLY</p>' 
                    ;} else if (is_admin() && current_user_can('edit_users') && get_option('gfe_encryption_key')){echo 
                    '<p><img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> Encryption password active! </p><br/><b>COPY THIS PASSWORD AND KEEP SAFE.</b><br/><p>Encrypted fields will now be saved and viewable under this current website key / password combination. Only change the key if necessary! Use normal ASCII charachters only.</p>' ;} ?>
                </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Encryption Password Override</th>
                <td><input type="text" name="gfe_encryption_key_override" style="border: 2px solid #ddd; border-radius: 7px;" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_encryption_key_override'));} ?>" style="width:100%; max-width: 600px;"/>
                <div id="gfe-password-override-instructions" style="max-width:600px;">
                	<?php if (is_admin() && current_user_can('edit_users') && !get_option('gfe_encryption_key_override')){echo 
                    '<p>STOP! DO NOT USE THIS ON INITIAL SETUP!</p>
					<p>If you had to change your encryption password and did not re-encrypt old data under that password under your new password you can enter the decryption password for the old data to view it with without disrupting currently submitting data being written under your current password while you are using this VIEW ONLY OVERRIDE.
                    <br/>This key cannot actually change any field data (unless used in combination with the "Encrypt/Decrypt Form Entries" tool as the override decryption password) and is only used for VIEWING encrypted field data under old passwords.</p>
                    <p>The old data must have been encrypted using the website key currently being used. This is useful to view data only under a different password if the password had to be changed for any reason.<br/> Fields encrypted under your regular password will not be readable and may not show up while decrypting with a different password but will return to normal when the override is removed and decryption is using the normal password again.</p>' 
                    ;} else if (is_admin() && current_user_can('edit_users') && get_option('gfe_encryption_key_override')){echo 
                    '<p><img src="' . plugin_dir_url(__FILE__) . 'images/check.png" height="14" width="14"> Encryption Password Override Active! </p><p></p><p>Encrypted fields will now be viewed using this current website key / password override combination. Use normal ASCII charachters only.</p>' ;} ?>
                    <p>Any encrypted fields submitted or updated when this password override is active will still be encrypted using the current website key with the normal encryption password, and will be viewable under them once the override is removed.</p>
                </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Global Form Encryption Switch</th>
                <td><input type="text" name="gfe_global_encryption_on" style="border: 2px solid #ddd; border-radius: 7px;" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_global_encryption_on'));} ?>" style="width:100%; max-width: 600px;"/>
                <div id="gfe_global_encryption_on-encrypt-hide" style="max-width: 150px; border: 2px solid #ddd; border-radius: 7px; padding: 5px; margin-top: 5px">
                <input type="radio" name="gfe_global_encryption_encrypt_hide" value="1" <?php checked(1, get_option('gfe_global_encryption_encrypt_hide'), true); ?>> ENCRYPTION<br/>
				<input type="radio" name="gfe_global_encryption_encrypt_hide" value="2" <?php checked(2, get_option('gfe_global_encryption_encrypt_hide'), true); ?>> HIDE FIELD VALUE
                </div>
                <div id="gfe_global_encryption_on-on-off" style="max-width: 60px; border: 2px solid #ddd; border-radius: 7px; padding: 5px; margin-top: 5px">
                <input type="radio" name="gfe_global_encryption_on_off" value="1" <?php checked(1, get_option('gfe_global_encryption_on_off'), true); ?>> ON<br/>
				<input type="radio" name="gfe_global_encryption_on_off" value="2" <?php checked(2, get_option('gfe_global_encryption_on_off'), true); ?>> OFF
                </div>
                <div id="gfe_global_encryption_on-instructions" style="max-width:600px;">
                    <p>Enter the numeric FORM ID for a form and select ENCRYPTION or HIDE FIELD VALUE and ON or OFF to turn encryption or hide feld value on/off for ALL supported fields on the form. Save changes.</p>
                    <p>This only turns quickly encryption or hide field value on or off globally (for supported fields) for the singular form and DOES NOT encrypt or decrypt previous entries. After saving changes you may check the form in the form editor to confirm encryption or hide field value is turned on/off for all supported fields. Turning encryption on will turn off hide field value and turning hide field value on will turn off encryption the same as if you selected these options in the fields advanced tab within the form editor. However, turning off encryption will NOT turn off hide field value for fields, and turning off hide field value will NOT turn off encryption for fields.</p>
                </div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Encrypt/Decrypt Form Entries<p><span style="color:red;">!!! CAUTION !!!</span></p></th>
                <td>
                <p><button id="gfe-form-decryption-section-idToggle" type="button" onClick="toggle_visibility('gfe-form-decryption-sectionToggle');">SHOW/HIDE FORM ENCRYPT/DECRYPT TOOL</button></p>
                <br/>
				<div id="gfe-form-decryption-sectionToggle" style="max-width:600px; border: 2px solid red; border-radius: 7px; padding: 10px; background-color: #FFFFFF; display: none;">
					<p><span style="color:red;">BACKUP YOUR DATABASE FIRST!. DO NOT proceed without one or more immediate backups.</span></p>
					<p><span style="color:red;">When decrypting, You must be sure that any forms and entries and fields you enter here are decryptable under the CURRENT 'Website Key' and 'Encryption Password/Encryption Password Override'.</span></p>
					<p><span style="color:red;">If you attempt decryption with the wrong 'Website Key' or 'Encryption Password/Encryption Password Override' on encrypted data, your data will be LOST!</span></p><p><span style="color:red;">Adjust the 'Website Key' and 'Encryption Password/Encryption Password Override' FIRST and check that the specified form and entries are properly decrypted with the keys before running decryption!</span></p><br/>
					<p><button id="gfe-form-decryption-instructions-idToggle" type="button" onClick="toggle_visibility('gfe-form-decryption-instructionsToggle');">SHOW/HIDE ENCRYPTION TOOL GUIDE</button></p>
					<div id="gfe-form-decryption-instructionsToggle" style="display:none;">
						<p>Encryption always uses the current 'Website Key' and 'Encryption Password' and DOES NOT use the 'Encryption Password Override'.</p>
						<p>If you are decrypting from an old key/pass to encrypt under a new key/pass, you can set the key/pass/pass_override for proper decryption of selected form/entries/fields then change to new key/pass and run encryption on same entries/fields. If the old entries are under a different website key, you should  take available forms down temporarily if you have to briefly change the website key to decrypt entries under that old website key so that any entries submitted while they are changed will not be encrypted under the old website key. If only the encryption password is different, just use the encryption password override temporarily to use that old encryption password on decryption runs, and forms can stay active as they will still be encrypted under the current website key and encryption password.</p>
						<p>You can ONLY encrypt fields that have encryption available in the form editor, and by default only fields with encryption currently turned on in the form can be encrypted unless you select the "ALLOW ENCRYPTING FIELDS WITH ENCRYPTION CURRENTLY TURNED OFF IN FORM" option below. If field data is already encrypted it will be skipped. To change encryption keys you would have to decrypt first, then encrypt with a new key.</p>
						<p>- Available Field Types: text, textarea, date, name, number, email, phone, website, address, dropdown, radio, multi select, checkbox.</p>
						<p>Inputs with <span style="color:red;">*</span> are REQUIRED. others are optional.</p>
						<p>Note: Removing encryption from a 'User Owned Field' will remove their ownership and normal view permissions will apply. You can update user owned field data by simply typing over the field data in the entry editor and updatig the entry. The new data will still be owned by the orginal submitting user.</p><br/>
						<p>Once you have entered the proper information, Save Changes and this option will attempt to add or remove encryption from the specified Form, Entries, and Fields.</p><p>A status report will be generated at the top of this page after saving changes based on the settings processed.</p><br/><p></p>
					</div><br/>
					<p>ENCRYPT / DECRYPT</p>
					<input class="redtext" type="text" id="gfe_encrypt_decrypt" name="gfe_encrypt_decrypt" value="" maxlength="7"/>
					<p><b><span style="color:red;">*</span> Enter 'encrypt' to perform encryption. Enter 'decrypt' to perform decryption.</b></p><br/>
					<p>FORM ID</p>
					<input class="redtext" type="text" id="gfe_encrypt_decrypt_form" name="gfe_encrypt_decrypt_form" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_encrypt_decrypt_form'));} ?>" maxlength="32"/>
					<p><b><span style="color:red;">*</span> Enter the ID of the form to encrypt/decrypt. Only one form ID can be used at a time.</b></p>
					<p>example: 12</p>
					<p>(would get form with ID of 12 for processing).</p><br/>
					<p>ENTRY IDs</p>
					<textarea class="redtextarea" rows="4" id="gfe_encrypt_decrypt_form_entries" name="gfe_encrypt_decrypt_form_entries"/><?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_encrypt_decrypt_form_entries'));} ?></textarea>
					<p><b>Enter a comma separated list of entry IDs for the specified form to specify entries that should be encrypted/decrypted.</b> If left blank, ANY of the specified forms encrypted entries up to the 'MAX ENTRIES PER RUN' set will be decrypted from newest to last.</p>
					<p>example: 23,43,21   (would only get form entries with ID of 23, 43, and 21 for processing.</p>
					<p>If you use this option, the 'MAX ENTRIES PER RUN' and 'OFFSET RUN START POINT' section below is ignored, but you should NOT enter more than 200 individual entries here at a time to avoid server timeout issues.</p><br/>
					<p>FIELD IDs</p>
					<textarea class="redtextarea" rows="4" id="gfe_encrypt_decrypt_form_fields" name="gfe_encrypt_decrypt_form_fields"/><?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_encrypt_decrypt_form_fields'));} ?></textarea>
					<p><b>Enter a comma separated list of field IDs to limit which fields should be encrypted/decrypted.</b> If left blank, ANY of the specified forms fields (with encryption turned on in form - unless option to encrypt other fields with encryption turned off is on) will be encrypted during an encryption run, and any fields with Gravity Forms Encrypted Fields encrypted data (encryption can be turned on or off) will be decrypted during a decryption run.</p>
					<p>example: 23,43,21   (would only process form fields with IDs of 23, 43, or 21.</p>
					<p>This option will function to process only specific fields whether you are specifying entry IDs or using general processing runs with a 'max entry per run' and/or 'offset run start point' set.</p><br/>		
					<p><input type="checkbox" name="gfe_encrypt_decrypt_form_ubf" value="1" <?php checked(1, get_option('gfe_encrypt_decrypt_form_ubf'), true); ?> style="border:1px solid red;"/> ENCRYPT ASSIGNING USER OWNED FIELDS</p>
					<p><b>Enable to assign original logged in submitting user ownership for any current user owned fields on the form during encryption.</b> If disabled or the submitting user was not logged in, user owned fields will still be encrypted, but only regular (non user owned) field view permissions will apply.</p><br/>
					<p><input type="checkbox" name="gfe_encrypt_decrypt_form_encrypt_all" value="1" <?php checked(1, get_option('gfe_encrypt_decrypt_form_encrypt_all'), true); ?> style="border:1px solid red;"/> ALLOW ENCRYPTING FIELDS WITH ENCRYPTION CURRENTLY TURNED OFF IN FORM</p>
					<p style="color:red;">CAUTION !</p>
					<p><b>Enabling this option will allow encrypting fields for the given form that currently have encryption turned off in their advanced tab.</b> This will result in ALL encryptable fields types for the form being encrypted if no Field IDs are specified, and will also allow for specifying Field IDs to be encrypted that currently have encryption turned off. Fields must still be of encryptable type. </p><br/>
					<div class="processing runs" style="background-color: #fff1f6; padding: 2px; border: 2px solid #808080;  border-radius: 7px;">
						<p><span style="color:red;">*</span> If you are NOT specifying ENTRY IDs above you must enter a 'MAX ENTRIES PER RUN' number here to specify how many entries will be processed. If you ARE specifying ENTRY IDs above, this input section is not required and will be ignored.</p><br/>
						<p>MAX ENTRIES PER RUN</p>
						<input type="text" id="gfe_encrypt_decrypt_form_entry_paging" name="gfe_encrypt_decrypt_form_entry_paging" style="border-radius: 7px;" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_encrypt_decrypt_form_entry_paging'));} ?>" style="width:100%; max-width: 590px; border: 1px solid red;" maxlength="3"/>
						<p><b>Enter the max number of entries to attempt to encrypt/decrypt for the form at a time.</b>&nbsp(default is 0 for safety)</p>
						<p>To help prevent server timeout issues, this value cannot exceed 200. If you DO experience timeouts, lower the number of entries per run.</p>
						<p>Encryption/Decryption will start from the newest entry and work back through older entries this number of entries at a time.</p>
						<p>To start </p><br/>
						<p>OFFSET RUN START POINT</p>
						<input type="text" id="gfe_encrypt_decrypt_form_paging_offset" name="gfe_encrypt_decrypt_form_paging_offset" style="border-radius: 7px;" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_encrypt_decrypt_form_paging_offset'));} ?>" style="width:100%; max-width: 590px; border: 1px solid red;"/>
						<p><b>Enter the number of entries (from newest to oldest) that the run will start at.</b></p>
						<p>This is NOT the entry ID you wish to start at.</p>
						<p>Example: to start processing entries 100 entries back from the newest, enter: 100</p>
						<p>This is NOT required, and if left blank processing will start from the newest entry working back.</p>
						<p>Encryption/Decryption will start from the newest entry offset back by this number and work back through older entries at the 'MAX ENTRIES PER RUN' number of entries at a time.</p>
						<p>To target only specific entries, please use comma seperated ENTRY IDs above instead.</p><br/>
					</div>
				</div>
                </td>
            </tr>
            <tr class="settingstr" valign="top">
                <th class="settings" scope="row">Settings Page LOCKOUT Password<p><span style="color:red;">!!! CAUTION !!!</span></p></th>
                <td>
                   <p><button id="gfe-settings-lock-instructions-idToggle" type="button" onClick="toggle_visibility('gfe-settings-lock-instructionsToggle');">SHOW/HIDE LOCKOUT SETTINGS</button></p>
                   <div id="gfe-settings-lock-instructionsToggle" style="display:none;">
                   <br/>
					   <input class="redtext" type="text" id="gfe_settings_lock" name="gfe_settings_lock" value="<?php if (is_admin() && current_user_can('edit_users')){echo esc_html__(get_option('gfe_settings_key'));delete_option('gfe_settings_key');delete_site_option('gfe_settings_key');} ?>" maxlength="16"/>
						<div id="gfe-settings-lock-instructions" style="max-width:600px;">
							<p><b>Enter a password here to keep all current settings and remove this settings page from admin. MUST BE 8-16 normal ASCII charachters or page will NOT lock.</b><br/>
							<p>This settings page will be replaced with a page that will require this same Settings Page LOCKOUT Password to unlock the settings page again.</p><p>It is important that if you password lock the settings page you write down and store the Settings Page LOCKOUT Password safely.</p>
							<p>If you lose the password you will have to uninstall the plugin then reinstall to enter your encryption page settings again to re set-up the plugin and access encrypted data again This would require you to have both your Website Key and Encryption Password.</p>
							<p>Delete the Settings Page LOCKOUT Password and save changes to unlock page.</p><br/>
							<input type="checkbox" name="gfe_norecover_pass" value="1" <?php checked(1, get_option('gfe_norecover_pass'), true); ?> style="border:1px solid red;"/> <b>No password removal on plugin removal and reinstall?</b>
							<p>Check this box to KEEP this Settings Page LOCKOUT Password even if plugin is deleted and reinstalled. This will keep the Settings Page LOCKOUT Password even if 'Delete Plugin Options on Removal' is turned on. If this is not on the Settings Page LOCKOUT Password will be deleted on plugin removal and reinstall.</p>
							<p>This prevents any other admin level (with ability to manage plugins) users from getting around the Settings Page LOCKOUT Password but leaves NO recovery option for you should you lose your Settings Page LOCKOUT Password.</p>
							<p>BE SURE YOU STORE YOUR Settings Page LOCKOUT Password SAFELY BEFORE TURNING THIS ON.</p>
							<p><b>You are responsible for all password recovery.</b></p>
						</div>
					</div>
                </td>
            </tr>
        </table>  
        <?php submit_button();?>        
    </form>
    <?php echo '<img src="' . plugin_dir_url(__FILE__)  . 'images/owl-small.png" height="30" width="30" style="display:inline-block; float:left;">'; ?><p> <a href="https://codecanyon.net/user/pluginowl/portfolio" target="_blank">Plugin Owl</a></p>
    </div>
<?php }