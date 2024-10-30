<?php
/**
 * Plugin Name: IC Importer
 * Plugin URI:  https://itclanproducts.com/wp/plugins
 * Description: The most powerful solution for import posts and pages from spreadsheet
 * Version:     1.0.0
 * Author:      ITclan BD
 * Author URI:  https://www.itclanbd.com
 * License:     GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ic-importer
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

/**
 * Current plugin version.
 */
define( 'IC_IMPORTER_VERSION', '1.0.0' );

/* define plugin file */
if ( ! defined( 'IC_IMPORTER_PLUGIN_FILE' ) ) {
	define( 'IC_IMPORTER_PLUGIN_FILE', __FILE__ );
}

/* define plugin path */
if ( ! defined( 'IC_IMPORTER_BASENAME' ) ) {
	define( 'IC_IMPORTER_BASENAME', plugin_basename( __FILE__ ) );
}

/* define plugin path */
if ( ! defined( 'IC_IMPORTER_PATH' ) ) {
	define( 'IC_IMPORTER_PATH', plugin_dir_path( __FILE__ ) );
}

/* define plugin URL */
if ( ! defined( 'IC_IMPORTER_URL' ) ) {
	define( 'IC_IMPORTER_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
}

/* define inc URL */
if ( ! defined( 'IC_IMPORTER_INC_URL' ) ) {
	define( 'IC_IMPORTER_INC_URL', IC_IMPORTER_URL . 'inc' );
}

/* define inc path */
if ( ! defined( 'IC_IMPORTER_INC_DIR' ) ) {
	define( 'IC_IMPORTER_INC_DIR', trailingslashit( IC_IMPORTER_PATH . 'inc' ) );
}
/*
 * Autoloader
 *
 * load all plugin classes
 *
 * @since 1.0.0
 */
require_once IC_IMPORTER_PATH . 'autoloader.php';

function ic_importer_init() {
	\IC_Importer\Classes\Core::instance();
}

add_action( 'plugins_loaded', 'ic_importer_init' );