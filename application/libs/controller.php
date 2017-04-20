<?php

/**
 * Base Controller Class
 */
class Controller {

  /**
   * @var null Database Connection
   */
  protected static $db = null;

  /**
   * Constructor
   */
  function __construct() {
    // just open a connection once
    if (self::$db == null) {
      $this->databaseConnection();
    }
  }

  /**
   * Open the database connection with the credentials from application/config/config.php
   */
  private function databaseConnection() {
    // create a database connection, using the constants from config/config.php
    try {
      // set the (optional) options of the PDO connection. in this case, we set the fetch mode to
      // "objects", which means all results will be objects, like this: $result->user_name !
      // For example, fetch mode FETCH_ASSOC would return results like this: $result["user_name] !
      // @see http://www.php.net/manual/en/pdostatement.fetch.php
      $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                       PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

      // generate a database connection, using the PDO connector
      // @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
      self::$db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, $options);
    }
    // if an error is catched, database connection failed
    catch (PDOException $e) {
      Session::set('s_errors', array('database' => MESSAGE_DATABASE_ERROR . ' ' . $e->getMessage()));
    }
  }

  /**
   * Load the model with the given name.
   * loadModel("AModel") would include models/amodel.php and create the object in the controller, like this:
   * $a_model = $this->loadModel('AModel');
   * Note that the model class name is written in "CamelCase", the model's filename is the same in lowercase letters
   * @param string $model_name The name of the model
   * @return object model
   */
  public function loadModel($model_name) {
    require 'application/models/' . strtolower($model_name) . '.model.php';
    $model_name = $model_name . "Model";
    return new $model_name(self::$db); // return new model
  }

  /**
   *
   */
  public function render($view, $data_array = array()) {
    // load Twig, the template engine
    // @see http://twig.sensiolabs.org
    $twig_loader = new Twig_Loader_Filesystem(PATH_VIEWS);
    $twig = new Twig_Environment($twig_loader, array('debug' => true));
    $twig->addExtension(new Twig_Extension_Debug());

    $twig->addGlobal('s_errors', Session::get('s_errors'));
    $twig->addGlobal('s_messages', Session::get('s_messages'));

    // render a view while passing the to-be-rendered data
    echo $twig->render($view . PATH_VIEW_FILE_TYPE, $data_array);

    // clear warnings/messages
    Session::set('s_errors', null);
    Session::set('s_messages', null);
  }
}

?>
