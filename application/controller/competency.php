<?php

/**
 * Competency Class Controller
 */
class Competency extends Controller {

  // each user would have to answer correctly to X% of all questions
  public $threshold_score;

  private $num_questions;

  private $correct_answers;

  /**
   * Constructor
   */
  function __construct() {
    parent::__construct();

    // read configurations
    $configurations = json_decode(file_get_contents(PATH_CONFS . "competency_config.json"), true);
    if (!isset($configurations) || $configurations == null) {
      Session::set('s_errors', array('competency_configuration' => 'It was not possible to read competency parameters.'));
      return;
    }

    if (!isset($configurations['threshold_score'])
          || !isset($configurations['num_questions'])
          || !isset($configurations['questions_answers'])) {
      Session::set('s_errors', array('competency_configuration' => 'It was not possible to initialise all competency parameters.'));
      return;
    }

    $this->threshold_score = $configurations['threshold_score'];
    $this->num_questions = $configurations['num_questions'];

    $this->correct_answers = array();
    foreach ($configurations['questions_answers'] as $question_answer) {
      $this->correct_answers[$question_answer['question_id']] = $question_answer['answer'];
    }
  }

  /**
   * PAGE: index
   */
  public function index() {
    // Set initial session values
    Session::set('question_index', 1);
    Session::set('progress', 0);

    if (isset($_GET['user_id']) && isset($_GET['study_hash'])) {
      $user_id = preg_replace('/\s+/', '', $_GET['user_id']);
      Session::set('user_id', $user_id);

      $ciphertext = $_GET['study_hash'];
      $secret_data = Utils::isHashValid($ciphertext);
      if ($secret_data == null) {
        header('location: ' . URL);
        return;
      }
      Session::set('study_hash', $ciphertext);

      $this->render('competency/index', array(
        'total_num_questions' => $this->num_questions
      ));
    } else {
      header('location: ' . URL);
    }
  }

  /**
   * Handle all actions of this controler
   */
  public function action() {
    $question_index = Session::get('question_index');
    $action_to_perform = ''; // invalid action by default!

    if ($_POST['submit'] == 'Begin') {
      $action_to_perform = 'competency/question/' . $question_index;
    } else if ($_POST['submit'] == 'Previous') {
      $this->trackAnswer($question_index);

      $question_index--;
      Session::set('question_index', $question_index);

      $this->progressBar();

      $action_to_perform = 'competency/question/' . $question_index;
    } else if ($_POST['submit'] == 'Next') {
      $this->trackAnswer($question_index);

      $question_index++;
      Session::set('question_index', $question_index);

      $this->progressBar();

      $action_to_perform = 'competency/question/' . $question_index;
    } else if ($_POST['submit'] == 'Submit') {
      $this->trackAnswer($question_index);
      $action_to_perform = 'competency/submit/';
    }

    header('location: ' . URL . $action_to_perform);
  }

  /**
   *
   */
  public function question($question_index) {
    // if there is no user_id, user should not have access to any question
    $user_id = Session::get('user_id');
    if (!isset($user_id)) {
      header('location: ' . URL);
      return;
    }

    if ($question_index > $this->num_questions) {
      header('location: ' . URL . 'competency/question/' . Session::get('question_index'));
      return;
    }

    // if user use back button
    Session::set('question_index', $question_index);

    $answers = Session::get('answers');

    $selected = -1;
    if (isset($answers[$question_index]) && !empty($answers[$question_index])) {
      $selected = $answers[$question_index]['_option'];
    }

    $this->render('competency/' . $question_index, array(
      'question_index' => $question_index,
      'option_selected' => $selected,
      'progress' => Session::get('progress'),
      'total_num_questions' => ($this->num_questions)
    ));
  }

  /**
   *
   */
  private function trackAnswer($question_index) {
    $time_to_answer = $_POST['time_to_answer'];
    $answers = Session::get('answers');

    for ($i = 1; $i <= count($answers); $i++) {
      $qi = $answers[$i]['question_index'];
      if ($question_index == $qi) {
        $answers[$i]['time_to_answer'] = $time_to_answer;
        $answers[$i]['_option'] = $_POST['_option'];

        // update answer
        Session::set('answers', $answers);
        return;
      }
    }

    // new answer
    $answer = array(
      'question_index' => $question_index,
      '_option' => $_POST['_option'],
      'time_to_answer' => $time_to_answer
    );

    $answers[$question_index] = $answer;
    Session::set('answers', $answers);
  }

  /**
   *
   */
  private function progressBar() {
    $answers = Session::get('answers');

    $user_answers = 0;
    foreach ($answers as $answer) {
      if ($answer['_option'] != NULL) {
        $user_answers++;
      }
    }

    $progress = $user_answers * round(100.0 / $this->num_questions);
    Session::set('progress', $progress);
  }

  /**
   *
   */
  public function submit() {
    // if there is no user_id, user should not have access to any question
    $user_id = Session::get('user_id');
    if (!isset($user_id)) {
      header('location: ' . URL);
      return;
    }

    $score = 0;
    $answers = Session::get('answers');
    // trying to submit without having answering any question
    if (empty($answers)) {
      Session::set('s_errors', array(INCOMPLETE_COMPETENCY));
      Session::set('question_index', 0);
      Session::set('progress', 0);

      header('location: ' . URL . 'competency/question/' . 0);
      return;
    } else if (count($answers) < $this->num_questions) {
      // trying to submit after answering only some (but not all) questions
      header('location: ' . URL . 'competency/question/' . Session::get('question_index'));
      return;
    } else {
      // sanity check if the user has answered all questions
      foreach ($answers as $answer) {
        if ($answer['_option'] != NULL) {
          // check if answer is correct and increase score
          if ($this->correct_answers[$answer['question_index']] == $answer['_option']) {
            $score = $score + 1;
          }
        } else {
          Session::set('s_errors', array(INCOMPLETE_COMPETENCY));

          $question_index = $answer['question_index'];
          Session::set('question_index', $question_index);
          header('location: ' . URL . 'competency/question/' . $question_index);

          return;
        }
      }
    }

    // calculate the final score
    $score = round(($score / $this->num_questions) * 100);
    // save score as a session variable
    Session::set('score', $score);

    $this->submitDB($user_id, $answers, $score);
    header('location: ' . URL . 'competency/thanks/');
  }

  /**
   *
   */
  private function submitDB($user_id, $answers, $score) {
    // create a new competency
    $competency_model = Controller::loadModel('competency');
    $competency_id = $competency_model->addCompetency($score);

    // add user
    $user_model = Controller::loadModel('user');
    $user_model->addUser($user_id, $competency_id);

    // add all answers
    foreach ($answers as $answer) {
      $user_answer = $answer['_option'];

      if (!$competency_model->addAnswer($competency_id, $answer['question_index'], $user_answer, $answer['time_to_answer'])) {      
        echo "<p>--- Something wrong ---</p>";
        echo "<p>user_id: " . $user_id . "</p>";
        echo "<p>competency_id: " . $competency_id . "</p>";
        echo "<p>answer['question_index']: " . $answer['question_index'] . "</p>";
        echo "<p>user_answer: " . $user_answer . "</p>";
        echo "<p>answer['time_to_answer']: " . $answer['time_to_answer'] . "</p>";
        echo "<p></p>";

        die;
      }
    }
  }

  /**
   *
   */
  public function thanks() {
    // if there is no user_id, user should not have access to any question
    $user_id = Session::get('user_id');
    if (!isset($user_id)) {
      header('location: ' . URL);
      return;
    }

    // get users' score
    $score = Session::get('score');
    if (!isset($score)) {
      header('location: ' . URL . 'competency/question/' . Session::get('question_index'));
      return;
    }

    $study_hash = Session::get('study_hash');

    // clean session
    Session::destroy();

    // say thanks and goodbye
    $this->render('competency/thanks', array(
      'user_id' => $user_id,
      'study_hash' => $study_hash,
      'score' => $score,
      'threshold_score' => $this->threshold_score
    ));
  }
}

?>
