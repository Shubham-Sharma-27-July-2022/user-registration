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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'user' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '5aj1O$z(p2uPbqhuf/[`P(/vk*.EF#2uufGV22QpM;SxUo9Qea=Wz?t(:6Y!4fkk' );
define( 'SECURE_AUTH_KEY',  'CT#:rAY!hZ[:Dk{n.5Rv=uC=xGhe4iLN<2,wkr88x$%&8P[a`EMIpr7jwTw:jYtD' );
define( 'LOGGED_IN_KEY',    'd-NcUqTdUi1^)i^{9^$DcP)aLyz&;hhL8y{a1G@4;JSdHEwf%R)O-+X]9. #ecrP' );
define( 'NONCE_KEY',        'j?4W4*R<,LLaa<3~|7.`Sati&uUZl])8_ ubf]0^J;)*,@NW1iAt5k[6MGF%Ye&~' );
define( 'AUTH_SALT',        '9v7DBo-*j+#F9U}Sn/!`+jVox.yia3.Ut4TrOeSsQ&F@1|D: 040&4eA~,6AT@ER' );
define( 'SECURE_AUTH_SALT', 'sF87O3m1NB{c|w^#unM_@0iN$}/kb(wgCPhdFEV@C?,8GdLp^ u-}ykg)9w^-2DP' );
define( 'LOGGED_IN_SALT',   'x]0,e0ffa[5zGk5.Jfx8+Q`&<7+O$&)@9[8NYd4;FC0B1caFG$0Bo -<uE[i6,Fb' );
define( 'NONCE_SALT',       '}eoT{{#)03Ifc%>U<:Ho&)cVx|{]|Hzy2?DhzvVy>u_jZ1~vM>LBmtJ[iUy}{}gy' );

/**#@-*/
@ini_set('upload_max_size' , '256M' );
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
