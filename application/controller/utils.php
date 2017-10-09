<?php

/**
 * Base Utils Class
 */
class Utils {

  public static function isHashValid($hash) {
    if (!isset($hash) || $hash == "") {
      Session::set('s_errors', array('hash' => 'In order to perform a study a valid hash key must be provided by URL, e.g., URL?hash=593563e23'));
      return null;
    }

    // decript hash
    $secret_data = json_decode(Encryption::decrypt($hash), true);
    if ($secret_data == null) {
      Session::set('s_errors', array('hash' => 'Invalid hash key. In order to perform a study a valid hash key must be provided.'));
      return null;
    }

    // check if it is well formed
    if (!isset($secret_data['type_of_survey']) || !isset($secret_data['set_of_questions']) || !isset($secret_data['prolific_token'])) {
      Session::set('s_errors', array('hash' => 'In order to perform a study a well formed hash key must be provided.'));
      return null;
    }

    $configurations = Utils::isConfigurationFileWellFormed();
    if ($configurations == null) {
      return null;
    }

    $type_of_survey = $secret_data['type_of_survey'];
    if (!isset($configurations[$type_of_survey])) {
      Session::set('s_errors', array('hash' => 'Invalid type of survey. In order to perform a study a well formed hash key must be provided.'));
      return null;
    }

    $set_of_questions = $secret_data['set_of_questions'];
    if (!is_int($set_of_questions)) {
      Session::set('s_errors', array('hash' => 'Invalid set of questions. In order to perform a study a well formed hash key must be provided.'));
      return null;
    }

    $survey_configuration = $configurations[$type_of_survey][0];
    $survey_model = Controller::loadModel('survey');
    $all_snippets = $survey_model->getAllSnippets();
    $number_of_sets = (int) (count($all_snippets) / $survey_configuration['num_questions']);

    if ($set_of_questions < 0 || $set_of_questions > $number_of_sets) {
      Session::set('s_errors', array('hash' => 'Invalid set of questions. In order to perform a study a well formed hash key must be provided.'));
      return null;
    }

    return $secret_data;
  }

  /**
   * Returns an JSON object in case of success, returns null otherwise
   */
  public static function isConfigurationFileWellFormed() {
    // read configurations
    $configurations = json_decode(file_get_contents(PATH_CONFS . "survey_config.json"), true);
    if (!isset($configurations) || $configurations == null) {
      Session::set('s_errors', array('survey_configuration' => 'It was not possible to read survey parameters.'));
      return null;
    }

    if (!isset($configurations['type'])) {
      Session::set('s_errors', array('survey_configuration' => 'It was not possible to initialise all survey parameters.'));
      return null;
    }

    return $configurations;
  }
}

?>
