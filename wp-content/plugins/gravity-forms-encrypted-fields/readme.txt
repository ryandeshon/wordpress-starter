=== Gravity Forms Encrypted Fields ===

Contributors: Neil Rowe
Tags: gravity, forms, encrypted, fields, encryption, encrypted fields, data encryption, encryption, sensitive data, encrypt data, encrypt submission, hide fields, hide, hide data, hidden, hidden fields, merge tag, merge tag exclude, merge tag include
Requires at least: 4.4
Tested up to: 4.7.2
Stable tag: 3.7
License: copyright 2016 Neil Rowe % DBA Plugin Owl

This plugin extends Gravity Forms with option to use AES-256 level database storage encryption or simply hide field values for individual Gravity Forms fields with individual user based view permission options. The plugin also has global user view permissions and merge tag output controls.



== Description ==

Gravity Forms Encrypted Fields allows you to easily select individual text and textarea fields in individual forms to have their data stored encrypted/scrambled in the database as basic protection against private or sensitive data loss/breach. Gravity Forms Encrypted Fields also allows for per field user access to the decrypted/readable data in the admin interface to limit users who can read the data through viewing form results in admin. The plugin also allows for simply hiding field data in the admin interface without using encryption. The Plugin also allows for setting user based viewing restrictions on encrypted fields without actually using any encryption. The plugin also has an admin option settings for global access or restriction lists to limit certain or all users from viewing encrypted data as readable in admin form entries regardless or permissions on the individual fields.

Once submitted field data is saved encrypted in the database, the field data will be automatically decrypted and readable when viewing in all admin interfaces and export options as long as this box remains checked on form.

Available Field Types: text, textarea, date, name, number, email, phone, website, address, dropdown, radio, multi select, checkbox.

To limit users who can read encrypted data in admin and export options for a field use 'User Decryption Permission' in the fields advanced options, the 'User Lockout List' , 'User Access List' and '{all_fields} Merge Tag Exclude/Include Options' under settings->GF Encrypt Field.

This plugin can be used to lock users out from reading submitted field data while viewing entries in admin which helps protect the data in the event of a login breach. The plugin also offers some basic protection for submitted field data being stored in the database by rendering it unreadable to anyone without proper decryption. 

NOTICE: This plugin fills one necessary component of data protection. The usage of other basic protections such as SSL, VPS, User capability restrictions, and strong admin user password enforcement alongside this plugin are strongly recommended. You may be subject to implementing additional data protection policies and procedures depending on the sensitivity level and type of the information you are collecting. Visit the following link for additional security practices using Gravity Forms in general: https://www.gravityhelp.com/documentation/article/security/

CAUTION: BACKUP YOUR DB AND RUN TESTS IN TEST ENVIRONMENT BEFORE TURNING ON THIS FEATURE. YOU ARE RESPOSIBLE FOR ANY LOST, STOLEN, OR DAMAGED DATA.
IF YOU USE ENCRYPTION IT IS STRONGLY RECCOMMENDED TO SET IT UP ON A NEW(or newly copied) FIELD WITH NO PREVIOUSLY SUBMITTED UNENCRYPTED DATA AND LEAVE IT ON FOR THE FIELD PERMANENTLY.

This plugin attempts to show both encrypted and non encrypted submitted field data at all times, but switching encryption on or off AFTER data is submitted to field could result in you having some data encrypted and some not in the same field. To test on a field with data already entered you can turn on and thouroughly check all existing entries data for integrity.



= Requirements =

-WordPress 4.6+
-PHP 5.6+ (5.5 and 5.4 should also function but are NOT SUPPORTED)
-Gravity Forms Version 2.0.7+
-Server must support one of the following encryption methods:
:: OpenSSL Encryption Enabled -ver 3.0+
:: Mcrypt Encryption Enabled -required for ver 2.9.3 or previous

-It is important to test the plugin for reliable data encryption and decryption within a test site on non-essential data before running in any live production environment with sensitive and/or essential data. 



== Installation ==

First Install:
Always backup your WordPress database and files before installing any plugins or updates!

You can install Gravity Forms Encrypted Fields through the "Add Plugins" page of the "Plugins" menu in WordPress (Plugins => Add New => Upload Plugin). Upload the plugin file without unzipping it. Or follow directions below for FTP install.

1. Unzip the plugin file.
2. Upload with your FTP software the "gravity-forms-encrypted-fields" folder, and only this folder, to your plugins directory (it should be "wp-content/plugins").
3. Activate the plugin through the "Plugins" menu in WordPress.
4. Follow setup instructions to configure the plugin through the "Settings->GF Encrypted Fields" options page in WordPress.


UPGRADE:
1. IMPORTANT: (If upgrading from a version prior to 3.0, before installing 3.0 you must first use the encrypt/decrypt tool (see below for quick instructions) to decrypt ALL previous entries with encryption and then upgrade to ver 3.0 following below instructions and then select encryption type on settings options page and save changes, then encrypt ALL previous entries again to resume functionality) Previously encrypted entries in prior versions CANNOT be decrypted/read using new encryption methods in ver 3.0+.

To use the settings options page "Encrypt/Decrypt Form Entries" tool you should back up your Wordpress installation and database and read and follow the tools instructions first. Then you only have to specify "decrypt" , enter a form ID, make sure "ENCRYPT ASSIGNING USER OWNED FIELDS" is on,  and enter 200 for max entries per run. Save changes, and then if you have more than 200 entries for that form specify "decrypt" again and set the "OFFSET RUN START POINT" to 200 (where first run left off) and run again. ..just repeat for each form and change the "OFFSET RUN START POINT" to where last run left off in increments of 200 until complete. Then once upgraded to new version, repeat this process using "encrypt" to re-encrypt the entries with new encryption.


2. Backup your database and WordPress installation, and copy your sites current Encryption Password and Website Key. The Website Key will have to be regenerated on update or removal and reinstallation and may change depending on changes you have made to your wordpress install. You can change it back with your copy if needed. If you want to keep your settings page password active please check the box by it for 'No password removal on plugin removal and reinstall'.
3. Save an offline backup copy of the current version gravity-forms-encrypted-fields folder in your wp-content/plugins directory. 
4. Put your site into offline/maintenace mode and deactivate the "Gravity Forms Encrypted Fields" plugin. Unzip the installation file and use FTP to replace your sites gravity-forms-encrypted-fields folder contents (Do NOT replace the "includes" folder or you will have to regenerate your website key or enter in your custom one again) in your wp-content/plugins directory with the new versions folder contents. The folder contents are normally located in wp-content->plugins->gravity-forms-encrypted-fields.
5. Re-activate the "Gravity Forms Encrypted Fields" plugin. Go to the "Settings->GF Encrypted Fields" options page and save settings to regenerate your site key if needed. If the site key was changed since the autogenerated one, replace this with the one you copied in step 1 and save settings.
6. If upgrading from version < 3.0 to version 3.0+ YOU MUST SELECT YOUR ENCRYPTION TYPE ON SETTINGS SCREEN ON UPGRADE TO RESUME ENCRYPTION FUNCTIONALITY.
7. If you encounter errors, please switch this folder back to the earlier version you saved a backup copy of.
8. Check the Changelog below and be sure to read the current versions additions/changes and make any neccessary configuration adjustments then take the site out of maintenance mode.



== Screenshots ==

1. Admin Entries View
2. Options Page
3. Field Encryption Options

== Changelog ==
Version 3.7
	* Added Hide Field Value controls to the "Global Form Encryption Switch"
	* Fixed "Global Form Encryption Switch" to turn off hide field value when encryption turned on and turn off encryption when hide field value turned on.
	* IMMEDIATE BUG FIX for 3.6: form fields not rendering
Version 3.6
	* Added encrypted/hidden indicators by field labels for quick reference of encrypted/hidden fields in the form editor without having to open the field options.
	* Added "Global Form Encryption Switch" to turn on/off encryption for all supported fields on a specified form to settings page.
	* Updated instructions.
Version 3.5
	* Improved multi input field labels in {gfef_decrypt_ALL} merge tags.
	* Code improvements/bug fix for {gfef_decrypt_ALL} merge tags not rendering all encrypted fields.
	* Fixed {gfef_decrypt_ALL} merge tags to also include hidden field output.
	* Added {gfef_decrypt_user_FIELD ID} merge tags to allow for specific field output through merge tags that checks the users view permissions to the field for use in "Gravity View" and other use cases where the merge tag is generating site content.
	* Added {gfef_decrypt_ALL_USER} merge tag to allow for ALL encrypted and hidden field output through a single merge tag that checks the users view permissions to the fields for use in "Gravity View" and other use cases where the merge tag is generating site content.
	* Bug fix for "undefined variable" when using the "encrypt/decrypt" tool.
Version 3.4
	* Added instructions for manual website key generation for installations on web servers with security restrictions preventing auto generation.
	* Code improvements.
Version 3.3
	* Added plugin version reporting to unlocked settings page.
	* Update author links.
	* Added explanation for 0 entries processed if no ENTRY IDs are specified and MAX ENTRIES PER RUN is left blank to the encryption/decryption tool report.
Version 3.2
	* Added {gfef_decrypt_ALL} merge tag for ability to include decrypted output of ALL encrypted and hidden fields thorugh Decrypted Merge Tag tool output.
	* Added Encryption Verification Mode Option to encryption test section of setting page to reveal raw encrypted data on entries pages for verification of encryption.
	* Adjusted decrypted merge tag tool to no longer allow output of user owned fields unless original logged in submitting owner generates merge tag results.
	* Form Encrypt/Decrypt Tool and Settings Page Lockout Password are now hidden by default to prevent accidental entries.
	* Cleaned up options page with subtle visual improvements and Guide materials visibility toggles.
	* Minor code improvements and CSS style streamlining.
	NOTICE: (If upgrading from version prior to 3.0, before installing 3.0 you must first use the encrypt/decrypt tool to decrypt ALL previous entries with encryption and then upgrade to ver 3.0 and select encryption type and save changes, then encrypt previous entries again to resume functionality) Previously encrypted entries in prior versions CANNOT be decrypted/read using new encryption methods in ver 3.0+. IMPORTANT: Please always refer to the plugin’s readme file for detailed instructions on upgrading between versions.
Version 3.1
	* Expanded Native Search Feature to attempt finding the entered search term(s) with varying capitalization automatically.
	* Fixed Encryption Test to also warn users when encryption password override is on while using Open SSL encryption.
	* Clarified Encryption Password Override instructions.
	* Subtle visual changes to settings options page for more clear section breaks.
	NOTICE: (If upgrading from version prior to 3.0, before installing 3.0 you must first use the encrypt/decrypt tool to decrypt ALL previous entries with encryption and then upgrade to ver 3.0 and select encryption type and save changes, then encrypt previous entries again to resume functionality) Previously encrypted entries in prior versions CANNOT be decrypted/read using new encryption methods in ver 3.0+
Version 3.0
	* Added OpenSSL encryption with ability to select and switch encryption type. 
	* Changed Mcrypt encryption to add additional level of security. 
	* Added ability to search user based fields with “native search”.
	* Changed "Encrypt/Decrypt Form Entries" tool to ONLY encrypt fields with encryption currently turned ON in form by default.
	* Added option to "Encrypt/Decrypt Form Entries" tool to allow for encryption of fields with encryption currently turned OFF in form.
	* Added subtle visual improvements to settings page.
	NOTICE: (If upgrading from version prior to 3.0, before installing 3.0 you must first use the encrypt/decrypt tool to decrypt ALL previous entries with encryption and then upgrade to ver 3.0 and select encryption type and save changes, then encrypt previous entries again to resume functionality) Previously encrypted entries in prior versions CANNOT be decrypted/read using new encryption methods in ver 3.0+
Version 2.9.3
	* Added native search functionality of encrypted field data (does not include user owned fields).
	* Added "Search Permission" option to limit users/roles ability to search based on encrypted field data.
	* Added Encrypted Merge Tags {gfef_encrypt_FIELD ID} for developers to output an encrypted version of any field data in notifications and confirmations to be decrypted elsewhere by data or email recipient. Data that is unencrypted will still have an encrypted version output by this merge tag.
	* Added floating "Save Changes" button to settings options page to assist quicker settings modifications.
Version 2.9.1
	* Added "Search Data" option to setting screen to allow for searching entry field data based on stored encrypted strings. ..This allows for stable search of encrypted fields.
Version 2.9
	* Added ability to enter roles to fields user view permissions and the limit user view permissions list. Using role slugs in these locations now controls permissions for all user of a certain role per field without the need to add all the user names under that role. Individual users can be restricted elsewhere such as the lockout list and the finer grain individual user permissions will override the role locking the individual users out.
	* Fixed the "Limit User/Role View Permission Lists" functionality so that when users/roles are in here, any field with a blank "User/Role View Permission List" still gives access to ALL users, but only users/roles in the limiting list will be valid if using the fields "User/Role View Permission List" to limit an individual fields view permissions.   !!!!!! Please check setup on update. This will give access where there previously wasn't if a fields "User/Role View Permission List" was blank but was restricting access still !!!!! 
Version 2.7
	* Added ability to auto delete specified form entries after submission/user registration.
	* Added ability to auto delete specified form file uploads after submission/user registration.
	* Added ability to attach specified form file uploads to specified notifications after submission/user registration before entry or file uploads are deleted.
Version 2.5
	* Added custom output preview masking for fields including hidden/encrypted fields to use for entry view, and optionally also in merge tags.
	* Added complete permissions bypass for full decrypted output (optionally including merge tags) for user specified form fields.
	* Added admin controlled decrypted merge tags to allow for full decrypted output of field data in merge tags (confirmations and notifications) while still keeping all website view permissions.
	* Changed the Merge Tag Filter to allow for both the filtering of individual field merge tags as well as the "all Fields" merge tag.
	* Removed browser autocompletion from settings page lockout password field and settings page. (Please update your lockout password, or clear browsers autocomplete cache/storage after upgrading to this version. Nothing is reccommended on new installs).
	* Added notice to failed encryption test for when encryption override password is on.
Version 2.3
	* Added Form, Entry, and Field specific encryption and decryption with user based field control through admin options settings page.
	* Added Encryption/Decryption Reporting to admin settings options page to report status of manual encryption/decryption runs.
	* Improoved priority of encryption function to allow for other hooked functions to be pre-processed.
	* Added encryption to admin option settings page password for database storage.
	* Fixed display of 0 value data when encrypted.
	* Code cleanup and improvements
Version 2.0
	* Added 'User Owned Field' advanced field option to allow ONLY the logged in user who originally submitted the data to be able to view it as readable even if another user updates the data. -This overrides ALL other user permissions.
	* Added *conditional* option to allow front end display of encrypted/hidden data for users with permission
	* Added option to save settings when deleting plugin to install updated version
	* Added option to save admin options page password regardless of deleting other settings to prevent password bypass when uninstalling and reinstalling plugin
	* Code cleanup and improvements
Version 1.7.2
	* Added Feature to optionally password protect admin options screen
Version 1.7.1 (current available version)
	* Bug fix for salt creation and zero value data
Version 1.7
	* Added Merge Tag Filter and {all_fields} Merge Tag Exclude/Include Options
	* Expanded the system check to include encryption bypass and merge tag filter for quick overview of system settings
Version 1.6
	* Added Feature to 'Limit User View Permission Lists'
Version 1.5
	* Added Feature 'User Access List'
	* Fixed encryption of zero value data.
	* Added Instructions and in depth option descriptions
Version 1.2
	* Added Hide Field data safe option
	* Changed decryption to no longer require field encryption turned on for users with permission
	* Changed restricted displays to respond to whether or not data is actually encrypted or just hidden
Version 1.0
	* Initial version