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

    $survey_model = $this->loadModel('survey');
    $all_snippets = $survey_model->getAllSnippets();

    // read configurations
    $configurations = json_decode(file_get_contents(PATH_CONFS . "survey_config.json"), true);
    if (!isset($configurations) || $configurations == null) {
      Session::set('s_errors', array('survey_configuration' => 'It was not possible to read survey parameters.'));
      return;
    }

    if (!isset($configurations['type'])) {
      Session::set('s_errors', array('survey_configuration' => 'It was not possible to initialise all survey parameters.'));
      return;
    }

    $survey_configuration = $configurations["rate"][0];
    $rate_number_of_sets = (int) (count($all_snippets) / $survey_configuration['num_questions']) - 1;

    $survey_configuration = $configurations["forced_choice"][0];
    $forced_choice_number_of_sets = (int) (count($all_snippets) / 2 / $survey_configuration['num_questions']) - 1;

    $this->render('home/index', array(
      'rate_number_of_sets' => $rate_number_of_sets,
      'forced_choice_number_of_sets' => $forced_choice_number_of_sets
    ));
  }

  /**
   * Handles the actions performed on the homepage
   */
  public function action() {

    if ($_POST['submit'] == 'Begin') {
      $user_id = $_POST['user_id'];
      Session::set('user_id', $user_id);

      $type_of_survey = $_POST['type_of_survey'];
      Session::set('type_of_survey', $type_of_survey);

      $set_of_questions = $_POST['set_of_questions'];
      Session::set('set_of_questions', $set_of_questions);

      // load 'User' model
      $user_model = $this->loadModel('user');

      $str = '?user_id=' . $user_id . '&type_of_survey=' . $type_of_survey . '&set_of_questions=' . $set_of_questions;

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
