<?php

/**
 * Error reporting
 * Useful to show every little problem during development, but only
 * show hard errors in production
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * Project URL (for local development either "http://127.0.0.1" or
 * "http://localhost")
 */
define('URL', 'http://localhost/phpmvc/');

/**
 * Database name and credentials
 */
define('DB_TYPE', 'mysql');
define('DB_HOST', '__host__');
define('DB_NAME', '__db_name__');
define('DB_USER', '__db_user__');
define('DB_PASS', '__db_pass__');

/**
 * Views 
 */
define('PATH_VIEWS', 'application/views/'); // path to views
define('PATH_VIEW_FILE_TYPE', '.twig'); // ending of your view files, like .php, .twig or similar.

/**
 * Custom configurations
 */
define('PATH_CONFS', 'application/config/');

/**
 * Upload
 */
define('UPLOAD_SIZE', 1000000);
define('PATH_UPLOAD_IMG', 'public/img/');

?>
