<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'strendio_wp_huzas' );

/** Database username */
define( 'DB_USER', 'strendio_wp_0do1n' );

/** Database password */
define( 'DB_PASSWORD', 'X8*#!RHDDc90K?mK' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

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
define('AUTH_KEY', 'PoMSfXup(g1_N[s[35oqg2v/-a72]56F41+BvU253A6fZ1&2dDdvY170s2ZGp|p6');
define('SECURE_AUTH_KEY', 'P6wRm6P869#k1b1sc]@pk@96Z339@9kD#600LD6Im4)(_G:AT(iM&f-@;4701gG7');
define('LOGGED_IN_KEY', '4e5e1j]S|]+&!33f4*TADi(HDa-#~f~!#9p%57]410&:y([us9RSb;J1FYG6w027');
define('NONCE_KEY', '6-@@@~&]6XA:7mQyxT(|6YRzQ-P4&/Dor82&F73Hk5K-@z(42HIK]~mS+-22uXB4');
define('AUTH_SALT', 'mR79G|xgw-@9#Mp&|51M!243y9-00F2FxngAAEJY8*o:1K3nMH6|R|#onw5:NVkW');
define('SECURE_AUTH_SALT', 'g#JR(3/8uSe](42w@4zA41HIM6(tqC|eZ4NGLe4&2B#HhuP[J43h#1~p7X+J6qvd');
define('LOGGED_IN_SALT', '+2_wvXv8zLN:[(6+a5aE%(4687gGl5:iN|&24M%~ZG!&HO4y6H97]cnG-G@-CtlJ');
define('NONCE_SALT', 'a2@GD3qF![il8[9(p7)]y_sbWQ]f)36c:m51H|@SkOV05iIQA1*%f#l3802eN73x');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'iR4Jxr1_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);
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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'DISABLE_WP_CRON', true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
