<?php
	// load  all classes
	spl_autoload_register('az_get_all_classes');

	function az_get_all_classes($class_name) {
		$path = dirname(__FILE__) . '/';
		$extension = ".class.php"; // file extsnsion for classes

		$file_name = $path . $class_name . $extension;

		if ( file_exists($file_name) ) {
			// include alll files
			include_once $file_name;
			//echo $file_name;
		}
		else {
			// clear all errors
		}
	}