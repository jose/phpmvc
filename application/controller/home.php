<?php

/**
 * Home Class Controller
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

    if ($_POST['submit'] == 'Begin') {
      $user_id = $_POST['user_id'];
      Session::set('user_id', $user_id);

      // load 'User' model
      $user_model = $this->loadModel('user');

      if (!$user_model->exists($user_id)) {
        // new user
        Session::set('s_messages', array("User '$user_id' is not in the database"));
        // new competency java test
        header('location: ' . URL . 'competency?user_id=' . $user_id);
        return;
      } else {
        // existing user
        Session::set('s_messages', array("User '$user_id' is in the database"));

        // go to 'examples'
        header('location: ' . URL . 'example');
        return;
      }
    }

    header('location: ' . URL);
    return;
  }
}

?>
