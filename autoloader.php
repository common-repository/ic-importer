<?php
defined( 'ABSPATH' ) || exit;

spl_autoload_register( 'ic_importer_autoloader' );

function ic_importer_autoloader( $class ) {
	$namespace = 'IC_Importer\\';
	if ( 0 !== strpos( $class, $namespace ) ) {
		return;
	}

	$main_class_name = substr( $class, strlen( $namespace ) );
	$class_file      = IC_IMPORTER_INC_DIR . str_replace( '\\', '/', $main_class_name ) . '.php';

	// if the file exists, require it
	if ( file_exists( $class_file ) ) {
		require_once $class_file;
	}
}