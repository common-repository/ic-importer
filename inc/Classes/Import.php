<?php

namespace IC_Importer\Classes;

defined( 'ABSPATH' ) || exit;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class Import {

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

	/**
	 * Import constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( is_admin() || current_user_can( 'manage_options' ) ) {
			add_action( 'wp_ajax_ic_importer_file_upload', array( $this, 'ic_importer_file_upload_ajax' ) );
			add_action( 'wp_ajax_ic_importer_post_import', array( $this, 'ic_importer_post_import_ajax' ) );
		}
	}

	/**
	 * Post fields.
	 *
	 * @since 1.0.0
	 */
	public function ic_importer_post_fields() {
		$post_fields = array(
			'post_title'     => __( 'Post Title', 'ic-importer' ),
			'post_thumbnail' => __( 'Post Thumbnail', 'ic-importer' ),
			'post_content'   => __( 'Post Content', 'ic-importer' ),
			'post_excerpt'   => __( 'Post Excerpt', 'ic-importer' ),
			'post_cat'       => __( 'Post Category', 'ic-importer' ),
			'post_tag'       => __( 'Post Tag', 'ic-importer' ),
			'post_author'    => __( 'Post Author', 'ic-importer' ),
			'post_date'      => __( 'Post Date', 'ic-importer' ),
		);
		$post_fields = apply_filters( 'ic_post_fields', $post_fields );

		$fields = '';
		$fields .= apply_filters( 'ic_post_fields_title', '<h5 class="ic-importer-step-title">' . __( 'Post Fields', 'ic-importer' ) . '</h5>' );
		foreach ( $post_fields as $key => $post_field ) {
			$fields .= '<div class="form-group">';
			$fields .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $post_field ) . '</label>';
			$fields .= '<input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" class="form-control ic-post-field" placeholder="' . esc_attr( $post_field ) . '"/>';
			$fields .= '</div>';
		}

		return $fields;
	}

	/**
	 * Page fields.
	 *
	 * @since 1.0.0
	 */
	public function ic_importer_page_fields() {
		$page_fields = array(
			'post_title'     => __( 'Page Title', 'ic-importer' ),
			'post_thumbnail' => __( 'Page Thumbnail', 'ic-importer' ),
			'post_content'   => __( 'Page Content', 'ic-importer' ),
			'post_author'    => __( 'Page Author', 'ic-importer' ),
			'post_date'      => __( 'Page Date', 'ic-importer' ),
		);
		$page_fields = apply_filters( 'ic_page_fields', $page_fields );

		$fields = '';
		$fields .= apply_filters( 'ic_page_fields_title', '<h5 class="ic-importer-step-title">' . __( 'Page Fields', 'ic-importer' ) . '</h5>' );
		foreach ( $page_fields as $key => $page_field ) {
			$fields .= '<div class="form-group">';
			$fields .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $page_field ) . '</label>';
			$fields .= '<input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" class="form-control ic-post-field" placeholder="' . esc_attr( $page_field ) . '"/>';
			$fields .= '</div>';
		}

		return $fields;
	}

	/**
	 * importer file upload ajax callback
	 *
	 * @since 1.0.0
	 */
	public function ic_importer_file_upload_ajax() {
		$response  = array(
			'status' => 'error',
		);
		$post_type = isset( $_POST['postType'] ) && ! empty( $_POST['postType'] ) ? sanitize_text_field( $_POST['postType'] ) : '';

		if ( ! isset( $_POST['ic_importer_nonce'] ) || ! wp_verify_nonce( $_POST['ic_importer_nonce'], 'ic_importer_action' ) ) {
			$response['message'] = __( 'Sorry, your nonce did not verify.', 'ic-importer' );
		} else {
			$allowedFileType = array(
				'application/vnd.ms-excel',
				'text/xls',
				'text/xlsx',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			);

			if ( in_array( $_FILES["file"]["type"], $allowedFileType ) && $_FILES["file"]["size"] > 0 ) {

				$filename     = $_FILES["file"]["tmp_name"];
				$objPHPExcel  = IOFactory::load( $filename );
				$row          = $objPHPExcel->getActiveSheet()->getRowIterator( 1 )->current();
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells( false );

				$response['excelColumns'] = '';
				foreach ( $cellIterator as $key => $cell ) {
					$column    = sanitize_text_field( $cell->getColumn() );
					$label     = sanitize_text_field( $cell->getValue() );
					$drag_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-grip-horizontal" viewBox="0 0 16 16"><path d="M2 8a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>';
					if ( ! empty( $cell->getValue() ) ) {
						$response['excelColumns'] .= '<tr><td>';
						$response['excelColumns'] .= '<div class="ic-column" data-value="' . esc_attr( $column ) . '" data-label="{' . esc_attr( sanitize_title( $label ) . '[' . $column . ']' ) . '}"><span>' . esc_html( $label ) . '</span>' . $drag_icon . '</div>';
						$response['excelColumns'] .= '</td></tr>';
					}
				}

				if ( $post_type == 'page' ) {
					$response['post_fields'] = $this->ic_importer_page_fields();
				} else {
					$response['post_fields'] = $this->ic_importer_post_fields();
				}

				$response['status'] = 'success';
			} else {
				$response['message'] = __( 'Invalid File Type. Upload Excel File.', 'ic-importer' );
			}


			if ( empty( $_POST['postType'] ) ) {
				$response['message'] = __( 'Please select post type.', 'ic-importer' );
				$response['status']  = 'error';
			}
			if ( empty( $_FILES["file"] ) ) {
				$response['message'] = __( 'Please upload excel file first.', 'ic-importer' );
				$response['status']  = 'error';
			}
		}

		echo json_encode( $response );
		die();
	}

	/**
	 * importer post import ajax callback
	 *
	 * @since 1.0.0
	 */
	public function ic_importer_post_import_ajax() {
		$response  = array(
			'status' => 'error',
		);
		$post_type = isset( $_POST['ic-importer-post-type'] ) && ! empty( $_POST['ic-importer-post-type'] ) ? sanitize_text_field( $_POST['ic-importer-post-type'] ) : '';

		if ( ! isset( $_POST['ic_importer_nonce'] ) || ! wp_verify_nonce( $_POST['ic_importer_nonce'], 'ic_importer_action' ) ) {
			$response['message'] = __( 'Sorry, your nonce did not verify.', 'ic-importer' );
		} else {
			if ( ! empty( $_POST['post_title'] ) ) {
				$allowedFileType = array(
					'application/vnd.ms-excel',
					'text/xls',
					'text/xlsx',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				);

				if ( in_array( $_FILES["file"]["type"], $allowedFileType ) && $_FILES["file"]["size"] > 0 ) {

					$filename        = $_FILES["file"]["tmp_name"];
					$objPHPExcel     = IOFactory::load( $filename );
					$rows            = $objPHPExcel->getActiveSheet()->toArray( null, true, true, true );
					$total_insert    = 0;
					$total_update    = 0;
					$total_duplicate = 0;
					foreach ( $rows as $key => $row ) {
						//title
						$title = isset( $_POST['post_title'] ) && ! empty( $_POST['post_title'] ) ? sanitize_text_field( $row[ $this->ic_get_column( $_POST['post_title'] ) ] ) : '';

						//Thumbnail
						$thumbnail = isset( $_POST['post_thumbnail'] ) && ! empty( $_POST['post_thumbnail'] ) ? esc_url_raw( $row[ $this->ic_get_column( $_POST['post_thumbnail'] ) ] ) : '';

						//excerpt
						$excerpt = isset( $_POST['post_excerpt'] ) && ! empty( $_POST['post_excerpt'] ) ? wp_specialchars_decode( $row[ $this->ic_get_column( $_POST['post_excerpt'] ) ], ENT_QUOTES ) : '';
						$excerpt = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $excerpt );

						//content
						$content = isset( $_POST['post_content'] ) && ! empty( $_POST['post_content'] ) ? wp_specialchars_decode( $row[ $this->ic_get_column( $_POST['post_content'] ) ], ENT_QUOTES ) : '';
						$content = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $content );

						//author
						$author    = isset( $_POST['post_author'] ) && ! empty( $_POST['post_author'] ) ? sanitize_text_field( $row[ $this->ic_get_column( $_POST['post_author'] ) ] ) : '';
						$author    = get_user_by( 'login', $author );
						$author_id = $author->ID;

						if ( empty( $_POST['post_date'] ) ) {
							$date = current_time( 'mysql' );
						} else {
							$date_string = strtotime( sanitize_text_field( $row[ $this->ic_get_column( $_POST['post_date'] ) ] ) );
							$date        = date( "Y-m-d H:i:s", $date_string );
						}

						if ( $post_type === 'post' ) {
							//category
							$post_cat = isset( $_POST['post_cat'] ) && ! empty( $_POST['post_cat'] ) ? sanitize_text_field( $row[ $this->ic_get_column( $_POST['post_cat'] ) ] ) : '';
							//tag
							$post_tag = isset( $_POST['post_tag'] ) && ! empty( $_POST['post_tag'] ) ? sanitize_text_field( $row[ $this->ic_get_column( $_POST['post_tag'] ) ] ) : '';
						}

						if ( $key == 1 || empty( $title ) ) {
							continue;
						}

						$is_post_exist = get_page_by_title( $title, 'OBJECT', $post_type );
						$post_args     = array(
							'post_type'    => $post_type,
							'post_status'  => 'publish',
							'post_title'   => wp_strip_all_tags( $title ),
							'post_author'  => absint($author_id),
							'post_excerpt' => $excerpt,
							'post_content' => $content,
							'post_date'    => $date,
						);

						if ( isset( $is_post_exist ) && isset( $_POST['update_existing_data'] ) && $_POST['update_existing_data'] === 'yes' ) {
							$post_args['ID'] = $is_post_exist->ID;
							$post_id         = wp_update_post( $post_args );
							$total_update ++;
						} elseif ( ! isset( $is_post_exist ) ) {
							$post_id = wp_insert_post( $post_args );
							$total_insert ++;
						} else {
							$total_duplicate ++;
						}

						if ( ! is_wp_error( $post_id ) ) {
							if ( $thumbnail ) {
								$this->generate_post_thumbnail( $thumbnail, $post_id );
							}
							if ( $post_cat ) {
								$this->insert_taxonomies( 'category', $post_cat, $post_id );
							}
							if ( $post_tag ) {
								$this->insert_taxonomies( 'post_tag', $post_tag, $post_id );
							}
						} else {
							$response['message'] = $post_id->get_error_message();
						}

						$response['message'] = sprintf(
							'%1$s %2$s <br> %3$s %4$s <br> %5$s %6$s',
							sprintf( __( 'Total %s imported:', 'ic-importer' ), $post_type ),
							$total_insert,
							sprintf( __( 'Total %s Updated:', 'ic-importer' ), $post_type ),
							$total_update,
							sprintf( __( 'Total %s skipped:', 'ic-importer' ), $post_type ),
							$total_duplicate
						);
						$response['status']  = 'success';
					}

				} else {
					$response['message'] = __( 'Invalid File Type. Upload Excel File.', 'ic-importer' );
				}
			} else {
				$response['message'] = __( 'Title field is required.', 'ic-importer' );
			}
		}

		echo json_encode( $response );
		wp_die();
	}

	public function generate_post_thumbnail( $image_url, $post_id ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents( $image_url );
		$filename   = basename( $image_url );
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		file_put_contents( $file, $image_data );

		$wp_filetype = wp_check_filetype( $filename, null );
		$attachment  = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		$attach_id   = wp_insert_attachment( $attachment, $file, $post_id );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
		$res2        = set_post_thumbnail( $post_id, $attach_id );
	}

	public function insert_taxonomies( $taxonomy, $terms, $post_id = 0 ) {
		$terms = explode( ',', sanitize_text_field( $terms ) );

		if ( is_array( $terms ) && ! empty( $terms ) ) {

			$term_list = array();
			foreach ( $terms as $term ) {

				if ( empty( $term ) ) {
					continue;
				}

				// Check if the term exists
				$_term = term_exists( $term, $taxonomy, 0 );

				$term_id = false;
				if ( ! is_array( $_term ) ) {
					$_term = wp_insert_term( $term, $taxonomy, array(
						'parent' => 0
					) );

					if ( ! is_wp_error( $_term ) ) {
						$term_id = isset( $_term['term_id'] ) ? absint( $_term['term_id'] ) : false;
					}
				} elseif ( isset( $_term['term_id'] ) && absint( $_term['term_id'] ) > 0 ) {
					$term_id = absint( $_term['term_id'] );
				}

				if ( $term_id !== false && absint( $term_id ) > 0 ) {
					$term_list[] = apply_filters( 'ic_importer_add_post_term', $term_id );
				}
				unset( $term, $_term );
			}


			if ( ! empty( $term_list ) ) {
				wp_set_object_terms( $post_id, $term_list, $taxonomy, true );
			}

			unset( $term_list );
		}
	}

	public function ic_get_column( $column = '' ) {
		if ( isset( $column ) && ! empty( $column ) ) {
			$column = ' ' . $column;
			$ini    = strpos( $column, '[' );
			if ( $ini == 0 ) {
				return false;
			}
			$ini += strlen( '[' );
			$len = strpos( $column, ']', $ini ) - $ini;

			return substr( $column, $ini, $len );
		}

		return false;
	}
}