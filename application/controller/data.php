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
    # UserID, CompetencyID, Score, QuestionID, Answer, Time
    print("  <tr>");
    print("    <th>UserID</th>");
    print("    <th>CompetencyID</th>");
    print("    <th>Score</th>");
    print("    <th>QuestionID</th>");
    print("    <th>Answer</th>");
    print("    <th>Time (seconds)</th>");
    print("  </tr>");
    $competency_file = fopen(dirname(__FILE__) . "/../../public/competency-test-data.csv", "w") or die("Unable to open '" . dirname(__FILE__) . "/../../public/competency-test-data.csv" . "' file!");
    fwrite($competency_file, "UserID,CompetencyID,Score,QuestionID,Answer,Time\n");
    foreach ($data_model->getCompentencyTestData() as $competency_test_data) {
      print("  <tr>");
      print("    <td>" . $competency_test_data->UserID . "</td>");
      print("    <td>" . $competency_test_data->CompetencyID . "</td>");
      print("    <td>" . $competency_test_data->Score . "</td>");
      print("    <td>" . $competency_test_data->QuestionID . "</td>");
      print("    <td>" . $competency_test_data->Answer . "</td>");
      print("    <td>" . $competency_test_data->Time . "</td>");
      print("  </tr>");
      fwrite($competency_file, "$competency_test_data->UserID,$competency_test_data->CompetencyID,$competency_test_data->Score,$competency_test_data->QuestionID,$competency_test_data->Answer,$competency_test_data->Time\n");
    }
    fclose($competency_file);
    print("</table>");

    /**
     * Rate Survey
     */
    print("<h2>Rate Survey</h2>");

    print("<table>");
    # AnswerID, AnswerType, UserID, Time, Skip, SnippetID, SnippetPath, Stars, Likes, Dislikes
    print("  <tr>");
    print("    <th>AnswerID</th>");
    print("    <th>AnswerType</th>");
    print("    <th>UserID</th>");
    print("    <th>Time (seconds)</th>");
    print("    <th>SkipMessage</th>");
    print("    <th>Comments</th>");
    print("    <th>SnippetID</th>");
    print("    <th>SnippetPath</th>");
    print("    <th>Stars</th>");
    print("    <th>Likes</th>");
    print("    <th>Dislikes</th>");
    print("  </tr>");
    $rate_survey_file = fopen(dirname(__FILE__) . "/../../public/rate-survey-data.csv", "w") or die("Unable to open '" . dirname(__FILE__) . "/../../public/rate-survey-data.csv" . "' file!");
    fwrite($rate_survey_file, "AnswerID,AnswerType,UserID,Time,SkipMessage,Comments,SnippetID,SnippetPath,Stars,Likes,Dislikes\n");
    foreach ($data_model->getRateSurveyData() as $rate_survey_data) {
      print("  <tr>");
      print("    <td>" . $rate_survey_data->AnswerID . "</td>");
      print("    <td>" . $rate_survey_data->AnswerType . "</td>");
      print("    <td>" . $rate_survey_data->UserID . "</td>");
      print("    <td>" . $rate_survey_data->Time . "</td>");
      print("    <td>" . $rate_survey_data->Skip . "</td>");
      print("    <td>" . $rate_survey_data->Comments . "</td>");
      print("    <td>" . $rate_survey_data->SnippetID . "</td>");
      print("    <td><a href='" . URL . $rate_survey_data->SnippetPath . "'>" . $rate_survey_data->SnippetPath . "</a></td>");
      print("    <td>" . $rate_survey_data->Stars . "</td>");
      print("    <td>" . implode(',', $rate_survey_data->Likes) . "</td>");
      print("    <td>" . implode(',', $rate_survey_data->Dislikes) . "</td>");
      print("  </tr>");
      fwrite($rate_survey_file, "$rate_survey_data->AnswerID,$rate_survey_data->AnswerType,$rate_survey_data->UserID,$rate_survey_data->Time,$rate_survey_data->Skip,$rate_survey_data->Comments,$rate_survey_data->SnippetID," . URL . $rate_survey_data->SnippetPath . ",$rate_survey_data->Stars," . implode(';', $rate_survey_data->Likes) . "," . implode(';', $rate_survey_data->Dislikes) . "\n");
    }
    fclose($rate_survey_file);
    print("</table>");

    /**
     * Forced Choice Survey
     */
    print("<h2>Forced Choice Survey</h2>");

    print("<table>");
    # AnswerID, AnswerType, UserID, Time, Skip, Snippet_A_ID, Snippet_A_Path, Snippet_B_ID, Snippet_B_Path, ChosenSnippetID, Likes_A, Dislikes_A, Likes_B, Dislikes_B
    print("  <tr>");
    print("    <th>AnswerID</th>");
    print("    <th>AnswerType</th>");
    print("    <th>UserID</th>");
    print("    <th>Time (seconds)</th>");
    print("    <th>SkipMessage</th>");
    print("    <th>Comments</th>");
    print("    <th>Snippet_A_ID</th>");
    print("    <th>Snippet_A_Path</th>");
    print("    <th>Snippet_B_ID</th>");
    print("    <th>Snippet_B_Path</th>");
    print("    <th>ChosenSnippetID</th>");
    print("    <th>Likes_A</th>");
    print("    <th>Dislikes_A</th>");
    print("    <th>Likes_B</th>");
    print("    <th>Dislikes_B</th>");
    print("  </tr>");
    $forced_choice_survey_file = fopen(dirname(__FILE__) . "/../../public/forced_choice-survey-data.csv", "w") or die("Unable to open '" . dirname(__FILE__) . "/../../public/forced_choice-survey-data.csv" . "' file!");
    fwrite($forced_choice_survey_file, "AnswerID,AnswerType,UserID,Time,SkipMessage,Comments,Snippet_A_ID,Snippet_A_Path,Snippet_B_ID,Snippet_B_Path,ChosenSnippetID,Likes_A,Dislikes_A,Likes_B,Dislikes_B\n");
    foreach ($data_model->getForcedChoiceSurveyData() as $forced_choice_survey_data) {
      print("  <tr>");
      print("    <td>" . $forced_choice_survey_data->AnswerID . "</td>");
      print("    <td>" . $forced_choice_survey_data->AnswerType . "</td>");
      print("    <td>" . $forced_choice_survey_data->UserID . "</td>");
      print("    <td>" . $forced_choice_survey_data->Time . "</td>");
      print("    <td>" . $forced_choice_survey_data->Skip . "</td>");
      print("    <td>" . $forced_choice_survey_data->Comments . "</td>");
      print("    <td>" . $forced_choice_survey_data->Snippet_A_ID . "</td>");
      print("    <td><a href='" . URL . $forced_choice_survey_data->Snippet_A_Path . "'>" . $forced_choice_survey_data->Snippet_A_Path . "</a></td>");
      print("    <td>" . $forced_choice_survey_data->Snippet_B_ID . "</td>");
      print("    <td><a href='" . URL . $forced_choice_survey_data->Snippet_B_Path . "'>" . $forced_choice_survey_data->Snippet_B_Path . "</a></td>");
      print("    <td>" . $forced_choice_survey_data->ChosenSnippetID . "</td>");
      print("    <td>" . implode(',', $forced_choice_survey_data->Likes_A) . "</td>");
      print("    <td>" . implode(',', $forced_choice_survey_data->Dislikes_A) . "</td>");
      print("    <td>" . implode(',', $forced_choice_survey_data->Likes_B) . "</td>");
      print("    <td>" . implode(',', $forced_choice_survey_data->Dislikes_B) . "</td>");
      print("  </tr>");
      fwrite($forced_choice_survey_file, "$forced_choice_survey_data->AnswerID,$forced_choice_survey_data->AnswerType,$forced_choice_survey_data->UserID,$forced_choice_survey_data->Time,$forced_choice_survey_data->Skip,$forced_choice_survey_data->Comments,$forced_choice_survey_data->Snippet_A_ID," . URL . $forced_choice_survey_data->Snippet_A_Path . ",$forced_choice_survey_data->Snippet_B_ID," . URL . $forced_choice_survey_data->Snippet_B_Path . ",$forced_choice_survey_data->ChosenSnippetID," . implode(';', $forced_choice_survey_data->Likes_A) . "," . implode(';', $forced_choice_survey_data->Dislikes_A) . "," . implode(';', $forced_choice_survey_data->Likes_B) . "," . implode(';', $forced_choice_survey_data->Dislikes_B) . "\n");
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
}

