<?php

/**
 * Study Class Controller
 */
class Study extends Controller {

  // a score < than will not allow user to perform the study
  private $threshold_score;

  private $study_type;

  private $num_questions;

  // can same user perform same study several times?
  private $allow_multiple_attempts;

  /**
   * Constructor
   */
  function __construct() {
    parent::__construct();

    if (Session::get('token') !== null) {
      // to avoid reading parameters over and over
      $this->study_type = Session::get('study_type');
      $this->num_questions = Session::get('num_questions');
      $this->allow_multiple_attempts = Session::get('allow_multiple_attempts');
      $this->threshold_score = Session::get('threshold_score');

      return;
    }

    // read configurations
    $configurations = json_decode(file_get_contents(PATH_CONFS . "study_config.json"), true);
    if (!isset($configurations) || $configurations == null) {
      Session::set('s_errors', array('study_configuration' => 'It was not possible to read study parameters.'));
      return;
    }

    if (!isset($configurations['type'])) {
      Session::set('s_errors', array('study_configuration' => 'It was not possible to initialise all study parameters.'));
      return;
    }

    $type_of_studies = array();
    foreach ($configurations['type'] as $type) {
      array_push($type_of_studies, $type['name']);
    }

    $this->study_type = $type_of_studies[array_rand($configurations['type'])];
    Session::set('study_type', $this->study_type);

    $this->num_questions = $configurations[$this->study_type][0]['num_questions'];
    Session::set('num_questions', $this->num_questions);

    $this->allow_multiple_attempts = (strtolower($configurations[$this->study_type][0]['allow_multiple_attempts']) == "no" ? false : true);
    Session::set('allow_multiple_attempts', $this->allow_multiple_attempts);

    if (!isset($this->study_type) || !isset($this->num_questions) || !isset($this->allow_multiple_attempts)) {
      Session::set('s_errors', array('study_configuration' => 'Configuration file is not well formed.'));
      return;
    }

    // read competency's configurations
    $configurations = json_decode(file_get_contents(PATH_CONFS . "competency_config.json"), true);
    if (!isset($configurations) || $configurations == null) {
      Session::set('s_errors', array('competency_configuration' => 'It was not possible to read competency parameters.'));
      return;
    }

    $this->threshold_score = $configurations['threshold_score'];
    Session::set('threshold_score', $this->threshold_score);
  }

  /**
   * PAGE: index
   */
  public function index() {

    // Unique token
    $token = uniqid(mt_rand(), true);

    Session::set('token', $token);
    Session::set('question_index', 0);
    Session::set('progress', 0);

    if (isset($_GET['user_id'])) {
      $user_id = $_GET['user_id'];

      // has he/she got a score > threshold?
      $user_model = $this->loadModel('user');
      $score = $user_model->getCompetencyScore($user_id);
      if ($score < $this->threshold_score) {
        Session::set('s_errors', array(str_replace('$score', $score, STUDY_NOT_AVAILABLE_SCORE)));
        header('location: ' . URL);
        return;
      }

      $study_model = $this->loadModel('study');
      if (!$this->allow_multiple_attempts && $study_model->hasUserCompletedStudy($this->study_type, $user_id)) {
        Session::set('s_errors', array(str_replace('$user_id', $user_id, ALREADY_DONE_STUDY)));
        header('location: ' . URL);
        return;
      }

      $this->render('study/index', array(
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
      // select all questions for this study
      $questions = $this->selectQuestions();
      if (!isset($questions) || count($questions) == 0) {
        header('location: ' . URL);
        return;
      }

      Session::set('questions', $questions);

      $action_to_perform = 'study/question/' . $question_index;
    } else if ($_POST['submit'] == 'Next') {
      if ($this->registerAnswer($question_index)) {
        $question_index++; // next question number
        Session::set('question_index', $question_index);
        $this->progressBar();
      }
      $action_to_perform = 'study/question/' . $question_index;
    } else if ($_POST['submit'] == 'Submit') {
      if ($this->registerAnswer($question_index)) {
        $action_to_perform = 'study/submit/';
      } else  {
        $action_to_perform = 'study/question/' . $question_index;
      }
    }

    header('location: ' . URL . $action_to_perform);
  }

  /**
   *
   */
  private function selectQuestions() {
    if (strtolower($this->study_type) == "individual") {
      return $this->selectQuestionsForIndividualStudy();
    } else if (strtolower($this->study_type) == "pair") {
      return $this->selectQuestionsForPairStudy();
    }

    Session::set('s_errors', array('study' => 'Type of study not supported.'));
    return null;
  }

  /**
   *
   */
  private function selectQuestionsForIndividualStudy() {
    $questions = (array) null;

    $study_model = $this->loadModel('study');

    // get all tags from DB
    $all_tags = $study_model->getAllTags();
    $tags_names = array();
    foreach ($all_tags as $tag) {
      array_push($tags_names, $tag->value);
    }

    // get all snippets from DB and randomly pick N, i.e., $this->num_questions
    $all_snippets = $study_model->getAllSnippets();
    $rand_keys = array_rand($all_snippets, $this->num_questions);
    if (!is_array($rand_keys)) {
      // When picking only one entry, array_rand() returns the key for
      // a random entry. Otherwise, an array of keys for the random
      // entries is returned. 
      $rand_keys = array($rand_keys);
    }

    // prepare questions for the study
    $question_index = 0;
    foreach ($rand_keys as $rand_key) {
      $selected_snippet = $all_snippets[$rand_key];

      $question = array(
        $question_index => array(
          'snippet_id' => $selected_snippet->id,
          'snippet_path' => $selected_snippet->path,
          'snippet_source_code' => file_get_contents(URL . $selected_snippet->path),
          'time_to_answer' => 0,
          'num_stars' => 0.0,
          'dont_know' => '',
          'tags' => $tags_names,
          'likes' => array(),
          'dislikes' => array(),
          'question_type' => 'individual'
        )
      );

      $questions = array_merge($questions, $question);
      $question_index++;
    }

    return $questions;
  }

  /**
   *
   */
  private function selectQuestionsForPairStudy() {
    $questions = (array) null;

    $study_model = $this->loadModel('study');

    // get all tags from DB
    $all_tags = $study_model->getAllTags();
    $tags_names = array();
    foreach ($all_tags as $tag) {
      array_push($tags_names, $tag->value);
    }

    // get all snippets from DB and randomly pick N, i.e., $this->num_questions * 2
    $all_snippets = $study_model->getAllSnippets();
    $rand_keys = array_rand($all_snippets, $this->num_questions * 2);
    if (!is_array($rand_keys)) {
      // When picking only one entry, array_rand() returns the key for
      // a random entry. Otherwise, an array of keys for the random
      // entries is returned. 
      $rand_keys = array($rand_keys);
    }

    // prepare questions for the study
    $question_index = 0;
    for ($i = 0; $i < count($rand_keys); $i = $i + 2) {
      $selected_snippet_a = $all_snippets[$rand_keys[$i]];
      $selected_snippet_b = $all_snippets[$rand_keys[$i + 1]];

      $question = array(
        $question_index => array(
          'snippet_a_id' => $selected_snippet_a->id,
          'snippet_a_path' => $selected_snippet_a->path,
          'snippet_a_source_code' => file_get_contents(URL . $selected_snippet_a->path),
          'snippet_a_likes' => array(),
          'snippet_a_dislikes' => array(),
          'snippet_b_id' => $selected_snippet_b->id,
          'snippet_b_path' => $selected_snippet_b->path,
          'snippet_b_source_code' => file_get_contents(URL . $selected_snippet_b->path),
          'snippet_b_likes' => array(),
          'snippet_b_dislikes' => array(),
          'chosen_snippet_id' => '',
          'dont_know' => '',
          'tags' => $tags_names,
          'time_to_answer' => 0,
          'question_type' => 'pair'
        )
      );

      $questions = array_merge($questions, $question);
      $question_index++;
    }

    return $questions;
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

    $it_should_be_at_question_index = Session::get('question_index');
    if ($question_index != $it_should_be_at_question_index) {
      header('location: ' . URL . 'study/question/' . $it_should_be_at_question_index);
      return;
    }

    $questions = Session::get('questions');
    $question = $questions[$question_index];

    // render question based on its type

    if ($question['question_type'] == "individual") {
      $this->render('individual/index', array(
        'question_index' => $question_index,
        'progress' => Session::get('progress'),
        'snippet_source_code' => $question['snippet_source_code'],
        'tags' => $question['tags'],
        'likes' => $question['likes'],
        'dislikes' => $question['dislikes'],
        'num_stars' => $question['num_stars'],
        'dont_know' => $question['dont_know'],
        'total_num_questions' => $this->num_questions-1
      ));
    } else if ($question['question_type'] == "pair") {
      $this->render('pair/index', array(
        'question_index' => $question_index,
        'progress' => Session::get('progress'),
        'tags' => $question['tags'],
        'snippet_a_id' => $question['snippet_a_id'],
        'snippet_a_source_code' => $question['snippet_a_source_code'],
        'snippet_a_likes' => $question['snippet_a_likes'],
        'snippet_a_dislikes' => $question['snippet_a_dislikes'],
        'snippet_b_id' => $question['snippet_b_id'],
        'snippet_b_source_code' => $question['snippet_b_source_code'],
        'snippet_b_likes' => $question['snippet_b_likes'],
        'snippet_b_dislikes' => $question['snippet_b_dislikes'],
        'chosen_snippet_id' => $question['chosen_snippet_id'],
        'dont_know' => $question['dont_know'],
        'total_num_questions' => $this->num_questions-1
      ));
    }
  }

  /**
   *
   */
  private function registerAnswer($question_index) {
    $questions = Session::get('questions');
    $question = $questions[$question_index];

    // tracking time
    $question['time_to_answer'] = $_POST['time_to_answer'];

    // no answer?
    $dont_know = isset($_POST['dont_know_textarea']) ? $_POST['dont_know_textarea'] : '';
    if (str_word_count($dont_know) == 0) {
      $dont_know = '';
    }

    $is_it_complete = true;
    if ($dont_know != '') {
      $question['dont_know'] = $dont_know;
    } else {
      if ($question['question_type'] == "individual") {
        $question['likes'] = ($_POST['like-container'] == "" ? array() : explode(',', $_POST['like-container']));
        $question['dislikes'] = ($_POST['dislike-container'] == "" ? array() : explode(',', $_POST['dislike-container']));
        $question['num_stars'] = $_POST['star-rating'];

        if ($question['num_stars'] == 0 || (count($question['likes']) == 0 && count($question['dislikes']) == 0)) {
          Session::set('s_errors', array(INCOMPLETE_ANSWER));
          $is_it_complete = false;
        }
      } else if ($question['question_type'] == "pair") {
        $question['snippet_a_likes'] = ($_POST['test_case_a_like-container'] == "" ? array() : explode(',', $_POST['test_case_a_like-container']));
        $question['snippet_a_dislikes'] = ($_POST['test_case_a_dislike-container'] == "" ? array() : explode(',', $_POST['test_case_a_dislike-container']));
        $question['snippet_b_likes'] = ($_POST['test_case_b_like-container'] == "" ? array() : explode(',', $_POST['test_case_b_like-container']));
        $question['snippet_b_dislikes'] = ($_POST['test_case_b_dislike-container'] == "" ? array() : explode(',', $_POST['test_case_b_dislike-container']));
        $question['chosen_snippet_id'] = $_POST['chosen_snippet_id'];

        if ((count($question['snippet_a_likes']) == 0 && count($question['snippet_a_dislikes']) == 0)
            || (count($question['snippet_b_likes']) == 0 && count($question['snippet_b_dislikes']) == 0)
            || $question['chosen_snippet_id'] == "") {
          Session::set('s_errors', array(INCOMPLETE_ANSWER));
          $is_it_complete = false;
        }
      }
    }

    // update questions
    $questions[$question_index] = $question;
    Session::set('questions', $questions);

    return $is_it_complete;
  }

  /**
   *
   */
  private function progressBar() {
    $questions = Session::get('questions');

    $how_many_answered_so_far = 0;
    foreach ($questions as $question) {
      if ($question['dont_know'] != '') {
        $how_many_answered_so_far++;
      } else {
        if ($question['question_type'] == "individual") {
          if (count($question['likes']) > 0 || count($question['dislikes']) > 0) {
            $how_many_answered_so_far++;
          }
        } else if ($question['question_type'] == "pair") {
          if ((count($question['snippet_a_likes']) > 0 || count($question['snippet_a_dislikes']) > 0)
              && (count($question['snippet_b_likes']) > 0 || count($question['snippet_b_dislikes']) > 0)
              && $question['chosen_snippet_id'] != "") {
            $how_many_answered_so_far++;
          }
        }
      }
    }

    $progress = $how_many_answered_so_far * round(100.0 / $this->num_questions);
    Session::set('progress', $progress);
  }

  /**
   *
   */
  public function submit() {
    // if there is no user_id, user should not have access to this function
    $user_id = Session::get('user_id');
    if (!isset($user_id)) {
      header('location: ' . URL);
      return;
    }

    $questions = Session::get('questions');

    // sanity check if user has answered all questions
    for ($question_index = 0; $question_index < count($questions); $question_index++) {
      $question = $questions[$question_index];
      $completed = true;

      if ($question['dont_know'] != '') {
        continue;
      }

      if ($question['question_type'] == "individual") {
        if (count($question['likes']) == 0 && count($question['dislikes']) == 0) {
          $completed = false;
        }
      } else if ($question['question_type'] == "pair") {
        if ((count($question['snippet_a_likes']) == 0 && count($question['snippet_a_dislikes']) == 0)
            || (count($question['snippet_b_likes']) == 0 && count($question['snippet_b_dislikes']) == 0)
            || $question['chosen_snippet_id'] == "") {
          $completed = false;
        }
      }

      if (! $completed) {
        Session::set('s_errors', array(INCOMPLETE_STUDY));

        Session::set('question_index', $question_index);
        header('location: ' . URL . 'study/question/' . $question_index);

        return;
      }
    }

    if (! $this->submitDB($user_id, $questions)) {
      var_dump(Session::get('s_errors'));
      print("<br />");
      $this->prettyPrintQuestions($questions);
      die(); // TODO are you sure? how about writing everything to a file and send it to me by email?
    }

    Session::set('submitted', "submitted");
    header('location: ' . URL . 'study/thanks/');
  }

  /**
   *
   */
  private function submitDB($user_id, $questions) {
    // create a new study
    $study_model = $this->loadModel('study');

    foreach ($questions as $question) {

      if ($question['question_type'] == "individual") {
        // create two containers: one for likes and another for dislikes
        $likes_container_id = $study_model->createContainer('like');
        $dislikes_container_id = $study_model->createContainer('dislike');

        if ($likes_container_id == -1 || $dislikes_container_id == -1) {
          Session::set('s_errors', array("It was not possible to create containers!"));
          // TODO revert DB
          return false;
        }

        // add each tag to a specific container

        if (! $this->addTagsToContainer($study_model, $likes_container_id, $question['likes'])) {
          Session::set('s_errors', array("Adding like-tags to its container failed!"));
          // TODO revert DB
          return false;
        }
        
        if (! $this->addTagsToContainer($study_model, $dislikes_container_id, $question['dislikes'])) {
          Session::set('s_errors', array("Adding dislikes-tags to its container failed!"));
          // TODO revert DB
          return false;
        }

        // create a general study
        $study_id = $study_model->createStudy($question['question_type'], $user_id, $question['time_to_answer'], $question['dont_know']);
        if ($study_id == -1) {
          Session::set('s_errors', array("It was not possible to create a general study!"));
          // TODO revert DB
          return false;
        }

        // create a specific study
        if (! $study_model->createIndividualStudy($study_id, $question['snippet_id'], $question['num_stars'], $likes_container_id, $dislikes_container_id)) {
          Session::set('s_errors', array("It was not possible to create a specific study!"));
          // TODO revert DB
          return false;
        }

      } else if ($question['question_type'] == "pair") {

        // create four containers: two for likes and another two for dislikes
        $likes_container_a_id = $study_model->createContainer('like');
        $dislikes_container_a_id = $study_model->createContainer('dislike');
        $likes_container_b_id = $study_model->createContainer('like');
        $dislikes_container_b_id = $study_model->createContainer('dislike');

        if ($likes_container_a_id == -1 || $dislikes_container_a_id == -1
            || $likes_container_b_id == -1 || $dislikes_container_b_id == -1) {
          Session::set('s_errors', array("It was not possible to create containers!"));
          // TODO revert DB
          return false;
        }

        // add each tag to a specific container

        if (! $this->addTagsToContainer($study_model, $likes_container_a_id, $question['snippet_a_likes'])) {
          Session::set('s_errors', array("Adding like-tags (of snippet A) to its container failed!"));
          // TODO revert DB
          return false;
        }
        if (! $this->addTagsToContainer($study_model, $dislikes_container_a_id, $question['snippet_a_dislikes'])) {
          Session::set('s_errors', array("Adding dislikes-tags (of snippet A) to its container failed!"));
          // TODO revert DB
          return false;
        }

        if (! $this->addTagsToContainer($study_model, $likes_container_b_id, $question['snippet_b_likes'])) {
          Session::set('s_errors', array("Adding like-tags (of snippet B) to its container failed!"));
          // TODO revert DB
          return false;
        }
        if (! $this->addTagsToContainer($study_model, $dislikes_container_b_id, $question['snippet_b_dislikes'])) {
          Session::set('s_errors', array("Adding dislikes-tags (of snippet B) to its container failed!"));
          // TODO revert DB
          return false;
        }

        // create a general study
        $study_id = $study_model->createStudy($question['question_type'], $user_id, $question['time_to_answer'], $question['dont_know']);
        if ($study_id == -1) {
          Session::set('s_errors', array("It was not possible to create a general study!"));
          // TODO revert DB
          return false;
        }

        // create a specific study
        if (! $study_model->createPairStudy($study_id, $question['snippet_a_id'], $likes_container_a_id, $dislikes_container_a_id, $question['snippet_b_id'], $likes_container_b_id, $dislikes_container_b_id, $question['chosen_snippet_id'])) {
          Session::set('s_errors', array("It was not possible to create a specific study!"));
          // TODO revert DB
          return false;
        }
      }
    }

    return true;
  }

  /**
   *
   */
  private function prettyPrintQuestions($questions) {
    foreach ($questions as $question) {
      if ($question['question_type'] == "individual") {
        print("<table style=\"width:100%;\">");
          print("<tr>");
            print("<th>question_type</th>");
            print("<th>snippet_id</th>");
            print("<th>time_to_answer</th>");
            print("<th>num_stars</th>");
            print("<th>likes</th>");
            print("<th>dislikes</th>");
            print("<th>don't know</th>");
          print("</tr>");
          print("<tr>");
            print("<td>" . $question['question_type'] . "</td>");
            print("<td>" . $question['snippet_id'] . "</td>");
            print("<td>" . $question['time_to_answer'] . "</td>");
            print("<td>" . $question['num_stars'] . "</td>");
            print("<td>" . implode(',', $question['likes']) . "</td>");
            print("<td>" . implode(',', $question['dislikes']) . "</td>");
            print("<td>" . $question['dont_know'] . "</td>");
          print("</tr>");
        print("</table>");
        print("<br />");
      } else if ($question['question_type'] == "pair") {
        print("<table style=\"width:100%;\">");
          print("<tr>");
            print("<th>question_type</th>");
            print("<th>snippet_a_id</th>");
            print("<th>snippet_b_id</th>");
            print("<th>time_to_answer</th>");
            print("<th>chosen_snippet_id</th>");
            print("<th>snippet_a_likes</th>");
            print("<th>snippet_a_dislikes</th>");
            print("<th>snippet_b_likes</th>");
            print("<th>snippet_b_dislikes</th>");
            print("<th>don't know</th>");
          print("</tr>");
          print("<tr>");
            print("<td>" . $question['question_type'] . "</td>");
            print("<td>" . $question['snippet_a_id'] . "</td>");
            print("<td>" . $question['snippet_b_id'] . "</td>");
            print("<td>" . $question['time_to_answer'] . "</td>");
            print("<td>" . $question['chosen_snippet_id'] . "</td>");
            print("<td>" . implode(',', $question['snippet_a_likes']) . "</td>");
            print("<td>" . implode(',', $question['snippet_a_dislikes']) . "</td>");
            print("<td>" . implode(',', $question['snippet_b_likes']) . "</td>");
            print("<td>" . implode(',', $question['snippet_b_dislikes']) . "</td>");
            print("<td>" . $question['dont_know'] . "</td>");
          print("</tr>");
        print("</table>");
        print("<br />");
      }
    }
  }

  /**
   *
   */
  private function addTagsToContainer($model, $container_id, $tags) {
    foreach ($tags as $tag) {
      $tag_id = $model->getTagID($tag);
      if (! $model->addTagsToContainer($container_id, $tag_id)) {
        return false;
      }
    }

    return true;
  }

  /**
   *
   */
  public function thanks() {
    // if there is no user_id, user should not have access to 'thanks' option
    $user_id = Session::get('user_id');
    if (!isset($user_id)) {
      header('location: ' . URL);
      return;
    }

    $submitted = Session::get('submitted');
    if (!isset($submitted)) {
      header('location: ' . URL . 'study/question/' . Session::get('question_index'));
      return;
    }

    $token = Session::get('token');

    // clean session
    Session::destroy();

    // say thanks and show the token
    $this->render('study/thanks', array(
      'token' => $token
    ));
  }
}

?>
