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
    # user_id,competency_id,score,question_id,answer,time

    $query = $this->db->prepare("SELECT User.id AS 'user_id', Competency.id AS 'competency_id', Competency.score AS 'score', CompetencyAnswer.question_num AS 'question_id', CompetencyAnswer.choice AS 'answer', CompetencyAnswer.time_to_answer AS 'time' FROM User, Competency, CompetencyAnswer WHERE User.competency_id = Competency.id AND Competency.id = CompetencyAnswer.competency_id");

    $query->execute();
    return $query->fetchAll();
  }

  /**
   *
   */
  public function getRateSurveyData() {
    # answer_id, answer_type, time, dont_know_answer, comments, num_stars, user_id, snippet_id, snippet_path, snippet_feature, tag_id, tag_value, container_type

    $query = $this->db->prepare("SELECT Answer.id AS 'answer_id', Answer.type AS 'answer_type', Answer.time_to_answer AS 'time', Answer.dont_know_answer AS 'dont_know_answer', Answer.comments AS 'comments', Rate.num_stars AS 'num_stars', User.id as 'user_id', Snippet.id AS 'snippet_id', Snippet.path AS 'snippet_path', Snippet.feature AS 'snippet_feature', Tag.id AS 'tag_id', Tag.value AS 'tag_value', Container.type AS 'container_type' FROM Rate, Answer, User, AnswerSnippet, Snippet, AnswerSnippetContainer, Container, ContainerTag, Tag WHERE Rate.answer_id = Answer.id AND User.id = Answer.user_id AND Answer.id = AnswerSnippet.answer_id AND Snippet.id = AnswerSnippet.snippet_id AND AnswerSnippet.id = AnswerSnippetContainer.answer_snippet_id AND Container.id = AnswerSnippetContainer.container_id AND Container.id = ContainerTag.container_id AND Tag.id = ContainerTag.tag_id");
    $query->execute();

    return $query->fetchAll();
  }

  /**
   *
   */
  public function getForcedChoiceSurveyData() {
    # answer_id, answer_type, time, dont_know_answer, comments, chosen_snippet_id, user_id, snippet_id, snippet_path, snippet_feature, tag_id, tag_value, container_type

    $query = $this->db->prepare("SELECT Answer.id AS 'answer_id', Answer.type AS 'answer_type', Answer.time_to_answer AS 'time', Answer.dont_know_answer AS 'dont_know_answer', Answer.comments AS 'comments', ForcedChoice.chosen_snippet_id AS 'chosen_snippet_id', User.id as 'user_id', Snippet.id AS 'snippet_id', Snippet.path AS 'snippet_path', Snippet.feature AS 'snippet_feature', Tag.id AS 'tag_id', Tag.value AS 'tag_value', Container.type AS 'container_type' FROM ForcedChoice, Answer, User, AnswerSnippet, Snippet, AnswerSnippetContainer, Container, ContainerTag, Tag WHERE ForcedChoice.answer_id = Answer.id AND User.id = Answer.user_id AND Answer.id = AnswerSnippet.answer_id AND Snippet.id = AnswerSnippet.snippet_id AND AnswerSnippet.id = AnswerSnippetContainer.answer_snippet_id AND Container.id = AnswerSnippetContainer.container_id AND Container.id = ContainerTag.container_id AND Tag.id = ContainerTag.tag_id");
    $query->execute();

    return $query->fetchAll();
  }
}

?>
