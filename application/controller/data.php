<?php

/**
 * Data Class Controller
 */
class Data extends Controller {

  /**
   * Constructor
   */
  function __construct() {
    parent::__construct();
  }

  /**
   * PAGE: index
   */
  public function index() {
    print("<h1>Raw Data</h1>");

    print("<table>");
    print("  <tr>");
    print("    <th>Compentency Test</th>");
    print("    <th>Rate Survey</th>");
    print("    <th>Forced Choice Survey</th>");
    print("  </tr>");
    print("  <tr>");
    print("    <th><a href='" . URL . "public/competency-test-data.csv'>competency-test-data.csv</a></th>");
    print("    <th><a href='" . URL . "public/rate-survey-data.csv'>rate-survey-data.csv</a></th>");
    print("    <th><a href='" . URL . "public/forced_choice-survey-data.csv'>forced_choice-survey-data.csv</a></th>");
    print("  </tr>");
    print("</table>");

    $data_model = Controller::loadModel('data');

    /**
     * Competency Test
     */
    print("<h2>Competency Test</h2>");

    print("<table>");
    # user_id,competency_id,score,question_id,answer,time
    print("  <tr>");
    print("    <th>user_id</th>");
    print("    <th>competency_id</th>");
    print("    <th>score</th>");
    print("    <th>question_id</th>");
    print("    <th>answer</th>");
    print("    <th>time (seconds)</th>");
    print("  </tr>");
    $competency_file = fopen(dirname(__FILE__) . "/../../public/competency-test-data.csv", "w") or die("Unable to open '" . dirname(__FILE__) . "/../../public/competency-test-data.csv" . "' file!");
    fwrite($competency_file, "user_id,competency_id,score,question_id,answer,time\n");
    foreach ($data_model->getCompentencyTestData() as $competency_test_data) {
      print("  <tr>");
      print("    <td>" . $competency_test_data->user_id . "</td>");
      print("    <td>" . $competency_test_data->competency_id . "</td>");
      print("    <td>" . $competency_test_data->score . "</td>");
      print("    <td>" . $competency_test_data->question_id . "</td>");
      print("    <td>" . $competency_test_data->answer . "</td>");
      print("    <td>" . $competency_test_data->time . "</td>");
      print("  </tr>");
      fwrite($competency_file, "$competency_test_data->user_id,$competency_test_data->competency_id,$competency_test_data->score,$competency_test_data->question_id,$competency_test_data->answer,$competency_test_data->time\n");
    }
    fclose($competency_file);
    print("</table>");

    /**
     * Rate Survey
     */
    print("<h2>Rate Survey</h2>");
    # answer_id, answer_type, time, dont_know_answer, comments, num_stars, user_id, snippet_id, snippet_path, snippet_feature, tag_id, tag_value, container_type
    print("<table>");
    print("  <tr>");
    print("    <th>answer_id</th>");
    print("    <th>answer_type</th>");
    print("    <th>time (seconds)</th>");
    print("    <th>dont_know_answer</th>");
    print("    <th>comments</th>");
    print("    <th>num_stars</th>");
    print("    <th>user_id</th>");
    print("    <th>snippet_id</th>");
    print("    <th>snippet_path</th>");
    print("    <th>snippet_feature</th>");
    print("    <th>tag_id</th>");
    print("    <th>tag_value</th>");
    print("    <th>container_type</th>");
    print("  </tr>");
    $rate_survey_file = fopen(dirname(__FILE__) . "/../../public/rate-survey-data.csv", "w") or die("Unable to open '" . dirname(__FILE__) . "/../../public/rate-survey-data.csv" . "' file!");
    fwrite($rate_survey_file, "answer_id,answer_type,time,dont_know_answer,comments,num_stars,user_id,snippet_id,snippet_path,snippet_feature,tag_id,tag_value,container_type\n");
    foreach ($data_model->getRateSurveyData() as $rate_survey_data) {
      print("  <tr>");
      print("    <td>" . $rate_survey_data->answer_id . "</td>");
      print("    <td>" . $rate_survey_data->answer_type . "</td>");
      print("    <td>" . $rate_survey_data->time . "</td>");
      print("    <td>" . $this->escapeAndQuoteString($rate_survey_data->dont_know_answer) . "</td>");
      print("    <td>" . $this->escapeAndQuoteString($rate_survey_data->comments) . "</td>");
      print("    <td>" . $rate_survey_data->num_stars . "</td>");
      print("    <td>" . $rate_survey_data->user_id . "</td>");
      print("    <td>" . $rate_survey_data->snippet_id . "</td>");
      print("    <td><a href='" . URL . $rate_survey_data->snippet_path . "'>" . $rate_survey_data->snippet_path . "</a></td>");
      print("    <td>" . $rate_survey_data->snippet_feature . "</td>");
      print("    <td>" . $rate_survey_data->tag_id . "</td>");
      print("    <td>" . $rate_survey_data->tag_value . "</td>");
      print("    <td>" . $rate_survey_data->container_type . "</td>");
      print("  </tr>");
      fwrite($rate_survey_file, "$rate_survey_data->answer_id,$rate_survey_data->answer_type,$rate_survey_data->time," . $this->escapeAndQuoteString($rate_survey_data->dont_know_answer) . "," . $this->escapeAndQuoteString($rate_survey_data->comments) . ",$rate_survey_data->num_stars,$rate_survey_data->user_id,$rate_survey_data->snippet_id," . URL . "$rate_survey_data->snippet_path,$rate_survey_data->snippet_feature,$rate_survey_data->tag_id,$rate_survey_data->tag_value,$rate_survey_data->container_type" . "\n");
    }
    fclose($rate_survey_file);
    print("</table>");

    /**
     * Forced Choice Survey
     */
    print("<h2>Forced Choice Survey</h2>");

    print("<table>");
    # answer_id, answer_type, time, dont_know_answer, comments, chosen_snippet_id, user_id, snippet_id, snippet_path, snippet_feature, tag_id, tag_value, container_type
    print("  <tr>");
    print("    <th>answer_id</th>");
    print("    <th>answer_type</th>");
    print("    <th>time (seconds)</th>");
    print("    <th>dont_know_answer</th>");
    print("    <th>comments</th>");
    print("    <th>chosen_snippet_id</th>");
    print("    <th>user_id</th>");
    print("    <th>snippet_id</th>");
    print("    <th>snippet_path</th>");
    print("    <th>snippet_feature</th>");
    print("    <th>tag_id</th>");
    print("    <th>tag_value</th>");
    print("    <th>container_type</th>");
    print("  </tr>");
    $forced_choice_survey_file = fopen(dirname(__FILE__) . "/../../public/forced_choice-survey-data.csv", "w") or die("Unable to open '" . dirname(__FILE__) . "/../../public/forced_choice-survey-data.csv" . "' file!");
    fwrite($forced_choice_survey_file, "answer_id,answer_type,time,dont_know_answer,comments,chosen_snippet_id,user_id,snippet_id,snippet_path,snippet_feature,tag_id,tag_value,container_type\n");
    foreach ($data_model->getForcedChoiceSurveyData() as $forced_choice_survey_data) {
      print("  <tr>");
      print("    <td>" . $forced_choice_survey_data->answer_id . "</td>");
      print("    <td>" . $forced_choice_survey_data->answer_type . "</td>");
      print("    <td>" . $forced_choice_survey_data->time . "</td>");
      print("    <td>" . $this->escapeAndQuoteString($forced_choice_survey_data->dont_know_answer) . "</td>");
      print("    <td>" . $this->escapeAndQuoteString($forced_choice_survey_data->comments) . "</td>");
      print("    <td>" . $forced_choice_survey_data->chosen_snippet_id . "</td>");
      print("    <td>" . $forced_choice_survey_data->user_id . "</td>");
      print("    <td>" . $forced_choice_survey_data->snippet_id . "</td>");
      print("    <td><a href='" . URL . $forced_choice_survey_data->snippet_path . "'>" . $forced_choice_survey_data->snippet_path . "</a></td>");
      print("    <td>" . $forced_choice_survey_data->snippet_feature . "</td>");
      print("    <td>" . $forced_choice_survey_data->tag_id . "</td>");
      print("    <td>" . $forced_choice_survey_data->tag_value . "</td>");
      print("    <td>" . $forced_choice_survey_data->container_type . "</td>");
      print("  </tr>");
      fwrite($forced_choice_survey_file, "$forced_choice_survey_data->answer_id,$forced_choice_survey_data->answer_type,$forced_choice_survey_data->time," . $this->escapeAndQuoteString($forced_choice_survey_data->dont_know_answer) . "," . $this->escapeAndQuoteString($forced_choice_survey_data->comments) . ",$forced_choice_survey_data->chosen_snippet_id,$forced_choice_survey_data->user_id,$forced_choice_survey_data->snippet_id," . URL . "$forced_choice_survey_data->snippet_path,$forced_choice_survey_data->snippet_feature,$forced_choice_survey_data->tag_id,$forced_choice_survey_data->tag_value,$forced_choice_survey_data->container_type" . "\n");
    }
    fclose($forced_choice_survey_file);
    print("</table>");
  }

  /**
   *
   */
  public function competencyTestData() {
    $file = dirname(__FILE__) . "/../../public/competency-test-data.csv";
    header('Content-Description: CompetencyTestData');
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header("Content-Length: " . filesize($file));
    // IE
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    readfile($file);
    die();
  }

  /**
   *
   */
  public function rateSurveyData() {
    $file = dirname(__FILE__) . "/../../public/rate-survey-data.csv";
    header('Content-Description: CompetencyTestData');
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header("Content-Length: " . filesize($file));
    // IE
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    readfile($file);
    die();
  }

  /**
   *
   */
  public function forcedChoiceSurveyData() {
    $file = dirname(__FILE__) . "/../../public/forced_choice-survey-data.csv";
    header('Content-Description: CompetencyTestData');
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header("Content-Length: " . filesize($file));
    // IE
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    readfile($file);
    die();
  }

  /**
   *
   */
  private function escapeAndQuoteString($str) {
    // http://php.net/htmlspecialchars or http://php.net/manual/en/function.addslashes.php
    return str_replace(",", ';', "\"" . htmlspecialchars($str, ENT_COMPAT, 'UTF-8', true) . "\"");
  }
}

