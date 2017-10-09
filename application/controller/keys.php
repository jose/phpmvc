<?php

/**
 * Survey Keys Controller
 */
class Keys extends Controller {

  /**
   * Constructor
   */
  function __construct() {
    parent::__construct();
  }

  public function index() {
    header('location: ' . URL);
  }

  /**
   *
   */
  public function obfuscate() {
    $survey_model = Controller::loadModel('survey');
    $all_snippets = $survey_model->getAllSnippets();

    $configurations = Utils::isConfigurationFileWellFormed();
    if ($configurations == null) {
      return;
    }

    $survey_configuration = $configurations["rate"][0];
    $rate_number_of_sets = (int) (count($all_snippets) / $survey_configuration['num_questions']);

    $survey_configuration = $configurations["forced_choice"][0];
    $forced_choice_number_of_sets = (int) (count($all_snippets) / $survey_configuration['num_questions']);

    $type_of_survey = "";
    $set_of_questions = "";
    $prolific_token = "";
    $ciphertext = "";

    if (isset($_POST['submit']) && $_POST['submit'] == 'Obfuscate') {
      $type_of_survey = $_POST['type_of_survey'];
      $set_of_questions = $_POST['set_of_questions'];
      $prolific_token = $_POST['prolific_token'];
      $ciphertext = URL . "?hash=" . Encryption::encrypt("{\"type_of_survey\": \"" . $type_of_survey . "\", \"set_of_questions\": " . $set_of_questions . ", \"prolific_token\": \"" . $prolific_token . "\"}");
    }

    $this->render('keys/obfuscate', array(
      'rate_number_of_sets' => $rate_number_of_sets,
      'forced_choice_number_of_sets' => $forced_choice_number_of_sets,
      'type_of_survey' => $type_of_survey,
      'set_of_questions' => $set_of_questions,
      'prolific_token' => $prolific_token,
      'ciphertext' => $ciphertext
    ));
  }

  /**
   *
   */
  public function deobfuscate() {
    if (isset($_POST['submit']) && $_POST['submit'] == 'Deobfuscate') {
      $ciphertext = $_POST['ciphertext'];
      $ciphertext = str_replace(URL . "?hash=", "", $ciphertext);
      $secret_data = Encryption::decrypt($ciphertext);
      if ($secret_data == null) {
        $secret_data = "Error: invalid hash!";
      }

      $this->render('keys/deobfuscate', array(
        'ciphertext' => $ciphertext,
        'secret_data' => $secret_data
      ));
      return;
    }

    $this->render('keys/deobfuscate');
  }
}

?>
