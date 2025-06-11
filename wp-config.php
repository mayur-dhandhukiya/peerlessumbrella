<?php
define( 'WP_CACHE', false ); // Added by WP Rocket

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
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'peerlessumbrella_new' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Webby@#$2022' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'AxzQD<2fK}~;7LMb>oE(ne#2gMi3X&IKE.iJxx5l|Z?Fl*)W~1sZeh>~vH>L>t$1' );
define( 'SECURE_AUTH_KEY',  'qW(iF9ovs}U-~CHsn~lB2d|UY3;S@|T8 E@,0II)uGAD#Lz>?o(&[Yjd}qIkUyvM' );
define( 'LOGGED_IN_KEY',    'cmE9199M)`,-G-Pu>4qfK}E3`z(4JAJiC[K<Sw@`v|+2Kip)d.-L+BFx{U1*TfZU' );
define( 'NONCE_KEY',        '5<u?KXoacmvLOS6OqJsB!yl=xkfs;]idRYX.UwBK]7<v]9y$kd*lx:cYNS7pt&Mm' );
define( 'AUTH_SALT',        '(gbR =+!KC##x+NvT@yW9=e=xaGWQZSyJ;0b|V@ywz8o5hWf&V.h^^&.jX|x&hQ3' );
define( 'SECURE_AUTH_SALT', '+x 9VRV~GR4J-4:HO?#v%&~<*NT-8K1cvC&DGo%Kh]<LM8D;l%/{UC@k!d=:7J%_' );
define( 'LOGGED_IN_SALT',   'nXM;./$0R(]~/<+CNN^#`p?X5W[&4}?1{x|YxmHd&x|Xq:;V0_gL:;DEYTvSA}EB' );
define( 'NONCE_SALT',       '9[B`)VvxVCX]tG@2TA1.3+*l%V7C^(T|c[cI>K/DLXy?GabnT-kc67h^=4%GP/})' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
