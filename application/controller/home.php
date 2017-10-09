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

    $ciphertext = "";

    if (isset($_GET['hash'])) {
      $ciphertext = $_GET['hash'];
      $secret_data = Utils::isHashValid($ciphertext);
      if ($secret_data == null) {
        $ciphertext = "";
      }
    }

    $this->render('home/index', array(
      'study_hash' => $ciphertext
    ));
  }

  /**
   * Handles the actions performed on the homepage
   */
  public function action() {

    if ($_POST['submit'] == 'Begin') {
      $user_id = $_POST['user_id'];
      Session::set('user_id', $user_id);

      $study_hash = $_POST['study_hash'];
      Session::set('study_hash', $study_hash);

      // load 'User' model
      $user_model = Controller::loadModel('user');

      $str = '?user_id=' . $user_id . '&study_hash=' . $study_hash;

      if (!$user_model->exists($user_id)) {
        // new competency java test
        header('location: ' . URL . 'competency' . $str);
        return;
      } else {
        // existing user
        header('location: ' . URL . 'survey' . $str);
        return;
      }
    }

    header('location: ' . URL);
    return;
  }
}

?>
