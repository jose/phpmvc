<?php

/**
 * DataModel Class
 */
class DataModel {

  /**
   * Constructor
   *
   * Every model needs a database connection, passed to the model
   * @param object $db A PDO database connection
   */
  function __construct($db) {
    try {
      $this->db = $db;
    } catch (PDOException $e) {
      Session::set('s_errors', array('database' => MESSAGE_DATABASE_ERROR));
    }
  }

  /**
   *
   */
  public function getCompentencyTestData() {
    $query = $this->db->prepare('SELECT User.id AS UserID, Competency.id AS CompetencyID, Competency.score AS Score, CompetencyAnswer.question_num AS QuestionID, CompetencyAnswer.choice AS Answer, CompetencyAnswer.time_to_answer AS Time FROM User, Competency, CompetencyAnswer WHERE User.competency_id = Competency.id AND Competency.id = CompetencyAnswer.competency_id');

    $query->execute();
    return $query->fetchAll();
  }

  /**
   *
   */
  private function getSnippetsIDsOfAnAnswer($answer_id) {
    $query = $this->db->prepare('SELECT AnswerSnippet.snippet_id AS SnippetID, Snippet.path AS SnippetPath FROM Answer, AnswerSnippet, Snippet WHERE Answer.id = :answer_id AND Answer.id = AnswerSnippet.answer_id AND Snippet.id = AnswerSnippet.snippet_id');
    $query->execute(array(
      ':answer_id' => $answer_id
    ));
    return $query->fetchAll();
  }

  /**
   *
   */
  private function getTagsOfAnAnswer($tag_type, $answer_id, $snippet_id) {
    // get answer/snippet ID
    $query = $this->db->prepare('SELECT * FROM AnswerSnippet WHERE AnswerSnippet.answer_id = :answer_id AND AnswerSnippet.snippet_id = :snippet_id');
    $query->execute(array(
      ':answer_id' => $answer_id,
      ':snippet_id' => $snippet_id
    ));
    $answer_snippet_id = $query->fetchObject()->id;

    // get tags
    $query = $this->db->prepare('SELECT Tag.value AS Tag FROM Container, ContainerTag, Tag, AnswerSnippetContainer WHERE Container.type = :tag_type AND Container.id = ContainerTag.container_id AND Tag.id = ContainerTag.tag_id AND Container.id = AnswerSnippetContainer.container_id AND AnswerSnippetContainer.answer_snippet_id = :answer_snippet_id');
    $query->execute(array(
      ':tag_type' => $tag_type,
      ':answer_snippet_id' => $answer_snippet_id
    ));

    $tags = array();
    foreach ($query->fetchAll() as $tag) {
      $tags[] = $tag->Tag;
    }

    return $tags;
  }

  /**
   *
   */
  public function getRateSurveyData() {
    # AnswerID, AnswerType, UserID, Time, Skip, SnippetID, SnippetPath, Stars, Likes, Dislikes
    $data = array();

    $query = $this->db->prepare('SELECT Answer.id AS AnswerID, Answer.type AS AnswerType, Answer.user_id AS UserID, Answer.time_to_answer AS Time, Answer.dont_know_answer AS Skip, Answer.comments AS Comments, Rate.num_stars AS Stars, Snippet.id AS SnippetID, Snippet.path AS SnippetPath FROM Answer, Rate, AnswerSnippet, Snippet WHERE Answer.type = \'rate\' AND Answer.id = Rate.answer_id AND Answer.id = AnswerSnippet.answer_id AND Snippet.id = AnswerSnippet.snippet_id');
    $query->execute();

    foreach ($query->fetchAll() as $answer) {
      $data_point = new stdClass;
      $data_point->AnswerID = $answer->AnswerID;
      $data_point->AnswerType = $answer->AnswerType;
      $data_point->UserID = $answer->UserID;
      $data_point->Time = $answer->Time;
      $data_point->Skip = escapeAndQuoteString($answer->Skip);
      $data_point->Comments = escapeAndQuoteString($answer->Comments);
      $data_point->SnippetID = $answer->SnippetID;
      $data_point->SnippetPath = $answer->SnippetPath;
      $data_point->Stars = $answer->Stars;
      $data_point->Likes = $answer->Skip != '' ? array() : $this->getTagsOfAnAnswer('like', $answer->AnswerID, $answer->SnippetID);
      $data_point->Dislikes = $answer->Skip != '' ? array() : $this->getTagsOfAnAnswer('dislike', $answer->AnswerID, $answer->SnippetID);

      $data[] = $data_point;
    }

    return $data;
  }

  /**
   *
   */
  public function getForcedChoiceSurveyData() {
    # AnswerID, AnswerType, UserID, Time, Skip, Snippet_A_ID, Snippet_A_Path, Snippet_B_ID, Snippet_B_Path, ChosenSnippetID, Likes_A, Dislikes_A, Likes_B, Dislikes_B
    $data = array();

    $query = $this->db->prepare('SELECT Answer.id AS AnswerID, Answer.type AS AnswerType, Answer.user_id AS UserID, Answer.time_to_answer AS Time, Answer.dont_know_answer AS Skip, Answer.comments AS Comments, ForcedChoice.chosen_snippet_id AS ChosenSnippetID FROM Answer, ForcedChoice, AnswerSnippet, Snippet WHERE Answer.type = \'forced_choice\' AND Answer.id = ForcedChoice.answer_id AND Answer.id = AnswerSnippet.answer_id AND Snippet.id = AnswerSnippet.snippet_id GROUP BY Answer.id');
    $query->execute();

    foreach ($query->fetchAll() as $answer) {
      $data_point = new stdClass;
      $data_point->AnswerID = $answer->AnswerID;
      $data_point->AnswerType = $answer->AnswerType;
      $data_point->UserID = $answer->UserID;
      $data_point->Time = $answer->Time;
      $data_point->Skip = escapeAndQuoteString($answer->Skip);
      $data_point->Comments = escapeAndQuoteString($answer->Comments);
      $data_point->ChosenSnippetID = $answer->ChosenSnippetID;

      $snippets = $this->getSnippetsIDsOfAnAnswer($answer->AnswerID);

      $snippet = $snippets[0];
      $data_point->Snippet_A_ID = $snippet->SnippetID;
      $data_point->Snippet_A_Path = $snippet->SnippetPath;
      $data_point->Likes_A = $answer->Skip != '' ? array() : $this->getTagsOfAnAnswer('like', $answer->AnswerID, $snippet->SnippetID);
      $data_point->Dislikes_A = $answer->Skip != '' ? array() : $this->getTagsOfAnAnswer('dislike', $answer->AnswerID, $snippet->SnippetID);

      $snippet = $snippets[1];
      $data_point->Snippet_B_ID = $snippet->SnippetID;
      $data_point->Snippet_B_Path = $snippet->SnippetPath;
      $data_point->Likes_B = $answer->Skip != '' ? array() : $this->getTagsOfAnAnswer('like', $answer->AnswerID, $snippet->SnippetID);
      $data_point->Dislikes_B = $answer->Skip != '' ? array() : $this->getTagsOfAnAnswer('dislike', $answer->AnswerID, $snippet->SnippetID);

      $data[] = $data_point;
    }

    return $data;
  }

  /**
   *
   */
  private function escapeAndQuoteString($str) {
    //return "\"" . str_replace(",", ';', $str) . "\"";
    // http://php.net/htmlspecialchars or http://php.net/manual/en/function.addslashes.php
    return "\"" . htmlspecialchars($str, ENT_COMPAT | ENT_HTML401, 'UTF-8', true) . "\"";
  }
}

?>
