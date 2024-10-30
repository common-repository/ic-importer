<?php

namespace IC_Importer\Classes;

defined( 'ABSPATH' ) || exit;

class Core {

	private static $instance = null;

	/**
	 * Singleton instance
	 *
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'init', array( $this, 'ic_set_locale' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'ic_enqueue_admin_script' ), 99 );

		//Load Files
		$this->ic_importer_load_files();

		if ( is_admin() ) {
			Dashboard::instance();
			Import::instance();
		}
	}

	/**
	 * Load text domain
	 *
	 * @since 1.0.0
	 */
	public function ic_set_locale() {
		load_plugin_textdomain( 'ic-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Load files
	 *
	 * @since 1.0.0
	 */
	public function ic_importer_load_files() {
		require_once IC_IMPORTER_INC_DIR . 'lib/vendor/autoload.php';
	}

	/**
	 * enqueue scripts and styles
	 *
	 * @since 1.0.0
	 */
	public function ic_enqueue_admin_script( $hook ) {
		if ( isset( $hook ) && $hook == 'toplevel_page_ic-importer-settings' ) {
			wp_enqueue_style(
				'ic-importer-admin',
				IC_IMPORTER_URL . 'admin/css/admin.css',
				array(),
				false
			);
			wp_enqueue_style(
				'bootstrap',
				IC_IMPORTER_URL . 'admin/css/bootstrap.min.css',
				array(),
				false
			);

			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-droppable' );

			wp_enqueue_script(
				'bootstrap',
				IC_IMPORTER_URL . 'admin/js/bootstrap.min.js',
				array( 'jquery' ),
				false,
				true
			);
			wp_enqueue_script(
				'ic-importer-admin',
				IC_IMPORTER_URL . 'admin/js/admin.js',
				array( 'jquery' ),
				false,
				true
			);

			wp_localize_script( 'ic-importer-admin', 'ic_importer_ajax_object', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			) );
		}
	}

}


