<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'bitnami_wordpress' );

/** Database username */
define( 'DB_USER', 'bn_wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'b09d39149d01195edeca8fe248b68060596362a60b369bb883e9d170dacccdf2' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'q(4B]iaN((h7<)Q`dm!cDTw@.Q:nhVjZ26uZv5V^>{2DPx`s[NgUvlVfvp0A>?EO' );
define( 'SECURE_AUTH_KEY',  'S>ICdwVAPW&A1n!A@]^T$20$3R;A;(B$T1,bC;O0+A~YPL$Jat9&2q,n^@G|iXu5' );
define( 'LOGGED_IN_KEY',    '<Luige>e{fsc*C`rDOoW`0[I#L+/@>6C<~$8UL*DdhSsOEdl:R=%S<NQ>FP`]3J1' );
define( 'NONCE_KEY',        'Q?q%(>IqKzNQm83=p#%Q=86/Rc .WckyS#R;{]$Dy>ir`%cdqcyCQChO^k_qLTR*' );
define( 'AUTH_SALT',        'yKa7[8bM/z1$KfSdC[/RV-b|g Mx@!`yR,{(V^^iB~#qB:.iu*kGk!}Qj_R&iS|q' );
define( 'SECURE_AUTH_SALT', 'js25PQ|OKAJ@uGh`hM[XVw- `fsby[l0`Z9UEpZ;tt)0 TbV@Is6Hq%&J28N$hzB' );
define( 'LOGGED_IN_SALT',   '|pwBJ+vBZp4EzIpOc#-5joG#7@It]@dT($fLR qz&jK!s?*8TSRv76-tnT[%>wS0' );
define( 'NONCE_SALT',       'Z/5,t^KUs^|grK2K]&OM#Q=*=0?cta3i|PA,q9U8q0q>nunwP.ow{?uBt{pi[R.^' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode. 
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define('WP_DEBUG', value: true);
define('WP_DEBUG_DISPLAY',false);
define('WP_DEBUG_LOG',true);

/* Add any custom values between this line and the "stop editing" line. */



define( 'FS_METHOD', 'direct' );
/**
 * The WP_SITEURL and WP_HOME options are configured to access from any hostname or IP address.
 * If you want to access only from an specific domain, you can modify them. For example:
 *  define('WP_HOME','http://example.com');
 *  define('WP_SITEURL','http://example.com');
 *
 */
if ( defined( 'WP_CLI' ) ) {
	$_SERVER['HTTP_HOST'] = '127.0.0.1';
}

define( 'WP_HOME', 'http://' . $_SERVER['HTTP_HOST'] . '/' );
define( 'WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

/**
 * Disable pingback.ping xmlrpc method to prevent WordPress from participating in DDoS attacks
 * More info at: https://docs.bitnami.com/general/apps/wordpress/troubleshooting/xmlrpc-and-pingback/
 */
if ( !defined( 'WP_CLI' ) ) {
	// remove x-pingback HTTP header
	add_filter("wp_headers", function($headers) {
		unset($headers["X-Pingback"]);
		return $headers;
	});
	// disable pingbacks
	add_filter( "xmlrpc_methods", function( $methods ) {
		unset( $methods["pingback.ping"] );
		return $methods;
	});
}

set_time_limit(3600);