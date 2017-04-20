<?php

/**
 * PHP-MVC starting point
 */

// load the (optional) Composer auto-loader
if (file_exists('vendor/autoload.php')) {
  require_once('vendor/autoload.php');
}

// load application config (error reporting etc.)
require_once('application/config/config.php');

// include the to-be-used language, english by default.
require_once('translations/en.php');

// load application class
require_once('application/libs/application.php');
require_once('application/libs/controller.php');

// session
require_once('application/libs/session.php');
Session::init();

// start the application
$app = new Application();

?>
