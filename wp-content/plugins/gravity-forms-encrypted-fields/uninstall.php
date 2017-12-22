<?php
// if uninstall.php is not called by WordPress, die
if (	!defined( 'WP_UNINSTALL_PLUGIN' ) ||	!WP_UNINSTALL_PLUGIN ||	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) )) {
	status_header( 404 );
	die;
}

//normal options removal
if (get_option('gfe_delete_options')) {
	// for site options
	delete_option('gfe_encryption_method');
	delete_option('gfe_delete_options');
	delete_option('gfe_admin_only');
	delete_option('gfe_encryption_bypass');
	delete_option('gfe_restricted');
	delete_option('gfe_hidevalue');
	delete_option('gfe_custom_data_search');
	delete_option('gfe_show_encryption');
	delete_option('gfe_masking');
	delete_option('gfe_decrypt_merge_tags');
	delete_option('gfe_delete_entry');
	delete_option('gfe_delete_file_uploads');
	delete_option('gfe_attach_file_uploads');
	delete_option('gfe_mergefilter');
	delete_option('gfe_user_search_permission_list');
	delete_option('gfe_limit_user_view_permission_list');
	delete_option('gfe_user_lockout_list');
	delete_option('gfe_user_lockout_override_list');
	delete_option('gfe_website_key');
	delete_option('gfe_encryption_key');
	delete_option('gfe_encryption_key_override');
	delete_option('gfe_settings_key');
	delete_option('gfe_global_encryption_on');
	delete_option('gfe_global_encryption_on_off');
	delete_option('gfe_global_encryption_encrypt_hide');
	delete_option('gfe_encrypt_decrypt_form');
	delete_option('gfe_encrypt_decrypt_form_entries');
	delete_option('gfe_encrypt_decrypt_form_fields');
	delete_option('gfe_encrypt_decrypt_form_entry_paging');
	delete_option('gfe_encrypt_decrypt_form_ubf');
	delete_option('gfe_encrypt_decrypt_form_encrypt_all');
	delete_option('gfe_encrypt_decrypt_user');
	delete_option('gfe_encrypt_decrypt');
	
	// for site options in Multisite
	delete_site_option('gfe_encryption_method');
	delete_site_option('gfe_delete_options');
	delete_site_option('gfe_admin_only');
	delete_site_option('gfe_encryption_bypass');
	delete_site_option('gfe_restricted');
	delete_site_option('gfe_hidevalue');
	delete_site_option('gfe_custom_data_search');
	delete_site_option('gfe_show_encryption');
	delete_site_option('gfe_masking');
	delete_site_option('gfe_decrypt_merge_tags');
	delete_site_option('gfe_delete_entry');
	delete_site_option('gfe_delete_file_uploads');
	delete_site_option('gfe_attach_file_uploads');
	delete_site_option('gfe_mergefilter');
	delete_site_option('gfe_user_search_permission_list');
	delete_site_option('gfe_limit_user_view_permission_list');
	delete_site_option('gfe_user_lockout_list');
	delete_site_option('gfe_user_lockout_override_list');
	delete_site_option('gfe_website_key');
	delete_site_option('gfe_encryption_key');
	delete_site_option('gfe_encryption_key_override');
	delete_site_option('gfe_settings_key');
	delete_site_option('gfe_global_encryption_on');
	delete_site_option('gfe_global_encryption_on_off');
	delete_site_option('gfe_global_encryption_encrypt_hide');
	delete_site_option('gfe_encrypt_decrypt_form');
	delete_site_option('gfe_encrypt_decrypt_form_entries');
	delete_site_option('gfe_encrypt_decrypt_form_fields');
	delete_site_option('gfe_encrypt_decrypt_form_entry_paging');
	delete_site_option('gfe_encrypt_decrypt_form_encrypt_all');
	delete_site_option('gfe_encrypt_decrypt_form_ubf');
	delete_site_option('gfe_encrypt_decrypt_user');
	delete_site_option('gfe_encrypt_decrypt');
}

//no options screen password recovery options removal
if (!get_option('gfe_norecover_pass')){
	// for site options
	delete_option('gfe_settings_lock');
	delete_option('gfe_norecover_pass');
	
	// for site options in Multisite
	delete_site_option('gfe_settings_lock');
	delete_site_option('gfe_norecover_pass');
}

