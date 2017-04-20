<?php

/**
 * Class Home
 */
class Home extends Controller {

  /**
   * This method handles what happens when the homepage is presented
   * http://yourproject/home/ (which is the default page btw)
   */
  public function index() {
    // keep error/warning messages before destroying starting a new
    // session
    $s_errors = Session::get('s_errors');
    $s_messages = Session::get('s_messages');
    Session::destroy(); // making sure no previous data is used
    Session::set('s_errors', $s_errors);
    Session::set('s_messages', $s_messages);

    $this->render('home/index');
  }

  /**
   * Handles the actions performed on the homepage
   */
  public function action() {
    header('location: ' . URL);
    return;
  }
}

?>
