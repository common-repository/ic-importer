<?php

namespace IC_Importer\Classes;

defined( 'ABSPATH' ) || exit;

class Dashboard {

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

	function __construct() {
		add_action( 'admin_menu', array( $this, 'ic_importer_admin_menu' ) );
	}

	/**
	 * Importer admin menu.
	 *
	 * @since 1.0.0
	 */
	public function ic_importer_admin_menu() {
		add_menu_page(
			__( 'IC Importer', 'ic-importer' ),
			__( 'IC Importer', 'ic-importer' ),
			'manage_options',
			'ic-importer-settings',
			array( $this, 'ic_admin_settings_page' ),
			'dashicons-download',
			'20'
		);
	}

	/**
	 * Importer settings page.
	 *
	 * @since 1.0.0
	 */
	public function ic_admin_settings_page() {
		?>
        <div class="ic-importer-main">
            <div class="ic-importer-header my-4">
                <h2 class="ic-header-title"><?php _e( 'IC Importer - Import posts and pages from google spreadsheet', 'ic-importer' ); ?></h2>
            </div>
            <div class="ic-importer-inner">
                <form id="ic_importer_form" action="#" method="post" class="ic_importer_form" enctype="multipart/form-data">
                    <div class="ic-section-file-upload">
                        <!-- alert message -->
                        <div class="alert alert-danger" role="alert"></div>
                        <div class="alert alert-success" role="alert"></div>

                        <!-- Step One -->
                        <div class="ic-importer-step ic-importer-step-one">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="ic-importer-step-title"><?php _e( 'File upload', 'ic-importer' ); ?></h5>
                                    <div class="ic-section-wrap">
                                        <input class="form-control" type="file" name="ic-importer-file" id="ic-importer-file" accept=".xls,.xlsx" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="ic-importer-step-title"><?php _e( 'Select Post Type', 'ic-importer' ); ?></h5>
                                    <div class="ic-section-wrap">
                                        <select name="ic-importer-post-type" class="form-control mw-100" required>
                                            <option value=""><?php _e( 'Select Post Type', 'ic-importer' ); ?></option>
                                            <option value="post"><?php _e( 'Post', 'ic-importer' ); ?></option>
                                            <option value="page"><?php _e( 'Page', 'ic-importer' ); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <p class="submit d-flex justify-content-end">
                                <button id="ic_importer_step1_btn" class="button button-primary"><?php _e( 'Upload File', 'ic-importer' ); ?></button>
                            </p>
                        </div>

                        <!-- Step Two -->
                        <div class="ic-importer-step ic-importer-step-two">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5 class="ic-importer-step-title"><?php _e( 'Excel Columns', 'ic-importer' ); ?></h5>
                                    <table class="table table-bordered ic-excel-column-table">
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="col-md-2">
                                    <div class="instruction">
                                        <img src="<?php echo esc_url( IC_IMPORTER_URL . 'admin/images/mouse-drag.png' ); ?>" alt="drag-and-drop">
                                        <p><?php _e( 'Drag and drop excel columns on the right to your desired fields.', 'ic-importer' ) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="ic-post-fields"></div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="yes" id="update_existing_data" name="update_existing_data">
                                        <label class="form-check-label" for="update_existing_data">
											<?php _e( 'Update Existing Items Fields', 'ic-importer' ); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <p class="submit d-flex justify-content-end">
                                <span id="submit" class="button button-primary ic-importer-prev-btn"><?php _e( 'Previous Step', 'ic-importer' ); ?></span>
                                <button type="submit" id="submit" class="button button-primary"><?php _e( 'Import Post', 'ic-importer' ); ?></button>
                            </p>
                        </div>
                    </div>
					<?php wp_nonce_field( 'ic_importer_action', 'ic_importer_nonce' ); ?>
                </form>

                <div class="ic-info-wrapper">
                    <div class="ic-info-box">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center">
                                <img src="<?php echo esc_url( IC_IMPORTER_URL . 'admin/images/support.png' ); ?>" alt="support">
                            </div>
                            <div class="col-lg-4 col-md-6 ic-info-box-content">
                                <h3 class="ic-box-header"><?php _e( 'Need Any Help?', 'ic-importer' ); ?></h3>
                                <p><?php _e( 'Stuck with something? We are always ready to help you 24/7. Feel free to contact with our support team.', 'ic-importer' ); ?></p>
                                <a href="mailto:<?php echo esc_attr( 'info@itclanbd.com' ) ?>" class="ic-btn-primary"><?php _e( 'Contact with us', 'ic-importer' ) ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="ic-info-box">
                        <div class="row align-items-center ic-col-reverse">
                            <div class="col-md-6 col-lg-4 ic-info-box-content">
                                <h3 class="ic-box-header"><?php _e( 'Missing Any Feature?', 'ic-importer' ); ?></h3>
                                <p><?php _e( 'Did we miss any feature that you need badly? Feel free to do a feature request to our support team.', 'ic-importer' ); ?></p>
                                <a href="mailto:<?php echo esc_attr( 'info@itclanbd.com' ) ?>" class="ic-btn-primary"><?php _e( 'Request Feature', 'ic-importer' ) ?></a>
                            </div>
                            <div class="col-md-6 offset-lg-2 text-center">
                                <img src="<?php echo esc_url( IC_IMPORTER_URL . 'admin/images/missing-feature.png' ); ?>" alt="missing-feature">
                            </div>
                        </div>
                    </div>
                    <div class="ic-info-box">
                        <h3 class="ic-box-header"><?php _e( 'Frequently Asked Questions', 'ic-importer' ); ?></h3>
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                                            aria-controls="collapseOne"><?php _e( 'Do I have to add an author name?', 'ic-importer' ); ?></button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body"><?php _e( 'No, It\'s not required. You can add an author name or if you leave the author field empty, then the posts will import under the current user', 'ic-importer' ); ?></div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                            aria-controls="collapseTwo"><?php _e( 'Do I have to add a Date?', 'ic-importer' ); ?></button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                    <div class="accordion-body"><?php _e( 'No, It\'s not required. You can add a Date or if you leave the date field empty, then the posts will import at the current date.', 'ic-importer' ); ?></div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                            aria-controls="collapseThree"><?php _e( 'Can I add Post Categories/Tags?', 'ic-importer' ); ?></button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body"><?php _e( 'Yes, You can add them comma-separated in an excel column.', 'ic-importer' ); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}