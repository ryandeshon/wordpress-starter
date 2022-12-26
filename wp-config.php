<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_wpstarterlocal_db' );

/** MySQL database username */
define( 'DB_USER', 'wp_wpstarterlocal_user' );

/** MySQL database password */
define( 'DB_PASSWORD', 'wp_wpstarterlocal_pw' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '_ CtYWVU`^|$uZ*$uFR:<QgCB0#Tpjz[%*W90}ksBVEJK>TuSzi6Du>LG@([EHMm' );
define( 'SECURE_AUTH_KEY',   'gwB$x[.PO54CihnzAq&!GHAOtoE(k; seezsFsnw/LXvI&@|wWC$iSF#$7^z?,.,' );
define( 'LOGGED_IN_KEY',     '<7G+,P#o1b@/r-Us<+^{BXJv <]PG+9j6Hgauk}w4qe|FsRg?9$Z`WV=!`-:hbGd' );
define( 'NONCE_KEY',         'OL^P jXj@xXwA/}ScsEmp/5bypsqxIu0&~xJ!5fC?TUdLty1Z=@yNuWrjman4+LY' );
define( 'AUTH_SALT',         'Ic8vLSA)2h!q4IIMvNh73y2Y8ONrQ /daNe,EfVlp#{twv:my>`S9>|^R}L!14eo' );
define( 'SECURE_AUTH_SALT',  '{?&n3Zj#CWq%,Il}L[T-&:|{e`-:H_E3?]8e4Of1`%{dXKWA53~lp_bVv~|T1m>x' );
define( 'LOGGED_IN_SALT',    'YB#d8=$+{?D~)lyX uLZ-FF]8JsLuQDJlT*VA#0{I(9dPiMlpU<j/ll0&8I$(~K ' );
define( 'NONCE_SALT',        'AV[V[<OkPe2OOVuY_~BB^-WSqOsi(?od_UaVaI64oC+|3Fm.]67uq<<%qf-Z4_qL' );
define( 'WP_CACHE_KEY_SALT', 'nVNIWK!wh(Zb|!J GIa&IE7%*NF]0/jFADHR_Ez},jL#F7*_Ne`@JD-K4cfuS|,r' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

define( 'WP_HOME', 'https://wp-starter.local/' );
define( 'WP_SITEURL', 'https://wp-starter.local/' );