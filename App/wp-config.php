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
define( 'DB_NAME', 'br6' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'CYj)H9oW/?f?u|mm;]|savtp0n/tqIg}y^bJGK_i<>D-s59ds)mGJEy`wMDI7n?+' );
define( 'SECURE_AUTH_KEY',  '+$_a.~80pCTr|zb:__#Rxhv#JMk1eDMy1cQ!1Sb(3>~RJ]!M=Uhgl-onb)O?JSRH' );
define( 'LOGGED_IN_KEY',    'EpgMEISYWnV9>R=LIBr8!v{UEc2~H-l%AVo$5.|J$,bQ?/T=)0k5+pr$?3!6do[1' );
define( 'NONCE_KEY',        'EA.>k28^p2~cIPlard_wx+C2vC7&bY~HW~u-mc@7M_srE-!*t:{21lAB@YT%[e{C' );
define( 'AUTH_SALT',        'zI+5k`l*M(zUwb[jt|h4o8TPqnBRS]E2pWNX.NyE+SOAJ4hPEU5;|-kk%DX*4)<h' );
define( 'SECURE_AUTH_SALT', 'UfwURJ5HFa{M)WDBwAQFTy?3_CQ9l/+-CSDM*Qw*p`l~rn*`,&6[!@_`@4Wc%2Dm' );
define( 'LOGGED_IN_SALT',   '9mi)c.8F8XiwStxFD]wNkBzytI#?a&1;F^J|0HNq,P_urF+:h;Zn,E(_j31~{MzI' );
define( 'NONCE_SALT',       '<|enhd.W3mudm/_@FK=]$l9V>;@@G!JNaV+/BTAQ`:7N+/<%|Dbo+:dG M<@[qP-' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
