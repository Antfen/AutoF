<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'autoF');

/** MySQL database username */
define('DB_USER', 'autoFadmin');

/** MySQL database password */
define('DB_PASSWORD', 'Cjc6799ezRyZxNED');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'yhyonU0,Kz$-)XN#-`MHwI=EH;=%n>ZM]jrgkqe|2E=6O&)LsdBZ@qh<J(?a}7KQ');
define('SECURE_AUTH_KEY',  't./)xbJ>EU{zOyYz(>>SgByBm6N!R2=0yFtARac8:hW@@xyJ0Q|H+cfj`M,}:Ig[');
define('LOGGED_IN_KEY',    '4wG #qN,9Ic&P nj~oE HD;u4X{cBw3|(I^}yh|,M],gD/ j2&wBfYN#+acL6TUc');
define('NONCE_KEY',        '{03KesBC]]D{C+()p,:[?aoG%Pqh#-?$}Q(f_M;*WX{_T+2;H^HMb5->/5.;u+jS');
define('AUTH_SALT',        'j^2-owQi o0lR+i+-=%_kU=nf=N]{EmDIsi>p_wkwjsl5=a(Q4NU}1R;&[+3b*mw');
define('SECURE_AUTH_SALT', 'p1]Npg4t3-&qJ_+I]Ze@!G5-_|09jW^JI.pa`OTQKeAy-s;}Y@6),WH@n!Z dmu#');
define('LOGGED_IN_SALT',   ';ee<D}Gh9~|;vP&bufG5bg)p6~+Ym4}*!({zH2uS!1<g{nlb?jrv+b6 jKg0%zk8');
define('NONCE_SALT',       '#i.R|8n*8pnTb(f^1$ysU{Fs$N(-]^q/-j#?8vHxg-lO_9To##p7syV-NrPx^{:9');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
