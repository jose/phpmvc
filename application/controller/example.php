<?php

/**
 * Home Class Controller
 */
class Example extends Controller {

  /**
   *
   */
  function __construct() {
    parent::__construct();
  }

  /**
   * 
   */
  public function index() {

    $user_id = Session::get('user_id');
    if ($user_id == null) {
      // do not allow 'example' page if there is not any user defined
      header('location: ' . URL);
      return;
    }

    $this->render('example/index');
    return;
  }
}

?>
