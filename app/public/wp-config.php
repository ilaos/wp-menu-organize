  <?php
    define( 'WP_CACHE', false ); // By Speed Optimizer by SiteGround

    define('SFB_PRO_DEV', true); // Enable Pro features for testing

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
    define( 'DB_NAME', 'local' );

    /** Database username */
    define( 'DB_USER', 'root' );

    /** Database password */
    define( 'DB_PASSWORD', 'root' );

    /** Database hostname */
    define( 'DB_HOST', 'localhost' );

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
    define( 'AUTH_KEY',          '3NVReQYRo<;*^wC7yINfB}hkfz{{tcJ@`,? kAoUUBE*<?6#`[|5MU5_K!@,<F c' );
    define( 'SECURE_AUTH_KEY',   '.ftwu#Uk&YP5NXtJGkznK>GAEmYyuCO*u#V x9oWIQqdD+TW~bi)5WD*sR8g#L+3' );
    define( 'LOGGED_IN_KEY',     'XG0EV,!t-;% VujJHiajNWhm._?x}I?bX08M|)6,3^q*gyZZ(#pdRP -=z3xD{Gz' );
    define( 'NONCE_KEY',         'nvYFL,;f[K K9opN?DY={kqO(s;g+ O0kq=jAy;u#!0]rb.O+[]q.XHwnHi?EOIa' );
    define( 'AUTH_SALT',         '.h{&qv`E:3nzEQiOknl.*WV;w.FNFtv3;X2uu>OKPDF#veP*@3QlB}AGZj~>dBfO' );
    define( 'SECURE_AUTH_SALT',  'JIWyO<;K{Un9g0)-Q!qtJxKhNps)CX_Sc;AiW4]/CcKISWHV,Ouo0.n9;eyRRV6d' );
    define( 'LOGGED_IN_SALT',    'oI&=!/Jh-5A8gq{ueE=(rD8gy,,5d1EZor^`W$BsJ>tKnGz6jIER;R0( ]p3)a{a' );
    define( 'NONCE_SALT',        'LitBd4V~88a}(+3g=I>,P?F:#DHr1CYH]z,6MNi9&r16G{)|mvIAjF)q_Xq>,V+r' );
    define( 'WP_CACHE_KEY_SALT', '8:XW@QtOBoeu}PUq/C9WY-7}6uj#F@).t+||n8qbeoq%q`5nS8UVh95B`[/+KoXO' );


    /**#@-*/

    /**
     * WordPress database table prefix.
     *
     * You can have multiple installations in one database if you give each
     * a unique prefix. Only numbers, letters, and underscores please!
     */
    $table_prefix = 'wp_';


    /* Add any custom values between this line and the "stop editing" line. */

    // Enable WP debug logging
    define( 'WP_DEBUG', true );
    define( 'WP_DEBUG_LOG', true );
    define( 'WP_DEBUG_DISPLAY', true );

    // Enable Submittal Builder Dev Mode (shows Demo Tools menu)
    define( 'SFB_DEV_MODE', true );

    // Enable Agency tier features for testing (Brand Presets)
    define( 'SFB_AGENCY_DEV', true ); // Enable Agency features for testing

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
    define( 'WP_ENVIRONMENT_TYPE', 'local' );
    /* That's all, stop editing! Happy publishing. */

    /** Absolute path to the WordPress directory. */
    if ( ! defined( 'ABSPATH' ) ) {
        define( 'ABSPATH', __DIR__ . '/' );
    }

    /** Sets up WordPress vars and included files. */
    require_once ABSPATH . 'wp-settings.php';