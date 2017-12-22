<?php


// ** MySQL settings ** //
/** The name of the database for WordPress */
define('DB_NAME', 'starter');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('AUTH_KEY',         '=y7fyG>bz+TRm]%wSXG5<J_#~k<-qwj=ql*~ ^r5b!hY/0|mbqIM4C,IslSZ$YTE');
define('SECURE_AUTH_KEY',  'jto(=}@No7I|6D[+rK#<j~j6|NY.)mA F>g(cJ`uG,$+DBXfGyGB+/(MqcXV,Y+?');
define('LOGGED_IN_KEY',    'XgEO?b,P$1=z|sG5-y;R@+6|:tXPY-rd}H)a4LCD;fL8p0I--E`4cT>]OWJ&7-5@');
define('NONCE_KEY',        'HTsm-NT+6=0(g{KZpp1~[0||zYPy4kN27hgclIJssg`?J HPkOjPbxJJxE&QS~eo');
define('AUTH_SALT',        'A^J(h*`RSe,*+4L+$r{FPm?Z5WI[&), LDk|7Za;A;s|,)w?IZ7UsT(VNhRkYj9M');
define('SECURE_AUTH_SALT', '$nDyYjW?&l99fC|M^VL3G,7AsRoxw0XeWoUw$`%4KHV6})PUp6JFec+/p!glC+K?');
define('LOGGED_IN_SALT',   'Hk{.kb0-Ao~hEN)zjZYw`m$HvyVUo^&ZJq;]DF9y,D8f1;R[-<K |?QN /s3Ng]/');
define('NONCE_SALT',       '2e3k8t^sY7ipNWs&*^7*m:]-knSvd!.y*Z$K-)<6sw@IEqE4La<2mbN_s57kVk)U');


$table_prefix = 'wp_';


global $memcached_servers;
$memcached_servers = array(
    array(
        '127.0.0.1', // Memcached server IP address
        11211        // Memcached server port
    )
);


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
