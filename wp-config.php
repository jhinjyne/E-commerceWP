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
define( 'DB_NAME', 'learningwordpress' );

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
define( 'AUTH_KEY',         '9d.iHyXDUXR;KVn:{w`{7?$f4!=,{`RXav=fNBRcOLW1XG@{ybjUf;+zL-mH.(y`' );
define( 'SECURE_AUTH_KEY',  'Q`/1D2s]h:m7H{n3/w2_p7)`EI~HX*6_UkYdnM}1@$=ns>W6Az6B|Xk>54-#V{)$' );
define( 'LOGGED_IN_KEY',    '*;F]Ir>F* sB{9,|:B<W]R0v.o(rX4a@-$kM`Z8IZJ0Xl[K%W}/0k4m+LiJK,xJZ' );
define( 'NONCE_KEY',        'm>m<xC{>/aS5r_6TA(tB@Vv-a!E:ec:-lRh}V1]6Vxm&Cf$5&3ma`bQhK_]e4$4d' );
define( 'AUTH_SALT',        '3p|#75m</robQeXt(//Ex:_B$r7Q$(bd:fp&-{o*2t6!3|u)^CDSl3of3&k&Fyfh' );
define( 'SECURE_AUTH_SALT', '_IPoj@fo+56^O&BXsoy4iZImO[nbD>haP]@NAtEl(%wT|:&XO.Jb>Xz_whiSW,:[' );
define( 'LOGGED_IN_SALT',   '1<lo8 j|jz/[V_D?hCS300(nLrnox2sk:xFA)r[pr3{;+2oj@yb_Z;{FoG]H$/l!' );
define( 'NONCE_SALT',       'u^)ibQBs5<=yNjrXeRsoH78V<tQ>/)QrU6#U4ku,bDz.M~r^E<goO}?7w4k{Iue[' );

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
