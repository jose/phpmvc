<?php

/**
 * Class SurveyModel
 */
class SurveyModel {

  /**
   * Constructor
   *
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
  public function hasUserCompletedSurvey($type, $user_id) {
    $query = $this->db->prepare('SELECT COUNT(*) FROM Answer WHERE type = :type AND user_id = :user_id');
    $query->execute(array(
      ':type' => $type,
      ':user_id' => $user_id
    ));

    if ($query->fetchColumn() > 0) {
      return true;
    }

    return false;
  }

  /**
   *
   */
  public function getAllTags() {
    $query = $this->db->prepare('SELECT * FROM Tag');
    $query->execute();
    return $query->fetchAll();
  }

  /**
   *
   */
  public function getTagID($value) {
    $query = $this->db->prepare('SELECT * FROM Tag WHERE value = :value');
    $query->execute(array(
      ':value' => $value
    ));

    return $query->fetchObject()->id;
  }

  /**
   *
   */
  public function getAllSnippets() {
    $query = $this->db->prepare('SELECT * FROM Snippet');
    $query->execute();
    return $query->fetchAll();
  }

  /**
   *
   */
  public function getPairSnippet($snippet) {
    $feature = explode("_", $snippet->path)[0] . "_%";
    $query = $this->db->prepare('SELECT * FROM Snippet WHERE Snippet.path LIKE :feature AND Snippet.id != :id_of_snippet_already_selected');

    try {
      $query->execute(array(
        ':feature' => $feature,
        ':id_of_snippet_already_selected' => $snippet->id
      ));
    } catch(PDOExecption $e) {
      return NULL;
    }

    return $query->fetchObject();
  }

  /**
   *
   */
  public function createContainer($type) {
    try {
      $query = $this->db->prepare('INSERT INTO Container(type) VALUES (:type)');
      $query->execute(array(
        ':type' => $type
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return -1;
    }

    return $this->db->lastInsertId();
  }

  /**
   *
   */
  public function addTagToContainer($container_id, $tag_id) {
    try {
      $query = $this->db->prepare('INSERT INTO ContainerTag(container_id, tag_id) VALUES (:container_id, :tag_id)');
      $query->execute(array(
        ':container_id' => $container_id,
        ':tag_id' => $tag_id
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }

    return true;
  }

  /**
   *
   */
  private function addTagsToContainer($container_id, $tags) {
    foreach ($tags as $tag) {
      $tag_id = $this->getTagID($tag);
      if (! $this->addTagToContainer($container_id, $tag_id)) {
        return false;
      }
    }

    return true;
  }

  /**
   *
   */
  public function createRateAnswer(
    // Answer
    $type, $user_id, $time_to_answer, $dont_know_answer, $comments,
    // Rate
    $num_stars,
    // AnswerSnipper
    $snippet_id,
    // Tags
    $likes, $dislikes
    ) {

    // create two containers: like and dislike
    $likes_container_id = $this->createContainer('like');
    $dislikes_container_id = $this->createContainer('dislike');

    if ($likes_container_id == -1 || $dislikes_container_id == -1) {
      return false;
    }

    // add tags to each respective container
    if (! $this->addTagsToContainer($likes_container_id, $likes)) {
      return false;
    }
    if (! $this->addTagsToContainer($dislikes_container_id, $dislikes)) {
      return false;
    }

    // create a new answer
    try {
      $query = $this->db->prepare('INSERT INTO Answer(type, user_id, time_to_answer, dont_know_answer, comments) VALUES (:type, :user_id, :time_to_answer, :dont_know_answer, :comments)');
      $query->execute(array(
        ':type' => $type,
        ':user_id' => $user_id,
        ':time_to_answer' => $time_to_answer,
        ':dont_know_answer' => $dont_know_answer,
        ':comments' => $comments
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }
    $answer_id = $this->db->lastInsertId();

    // create a new 'rate' answer
    try {
      $query = $this->db->prepare('INSERT INTO Rate(answer_id, num_stars) VALUES (:answer_id, :num_stars)');
      $query->execute(array(
        ':answer_id' => $answer_id,
        ':num_stars' => $num_stars
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }

    // add snippet to answer
    try {
      $query = $this->db->prepare('INSERT INTO AnswerSnippet(answer_id, snippet_id) VALUES (:answer_id, :snippet_id)');
      $query->execute(array(
        ':answer_id' => $answer_id,
        ':snippet_id' => $snippet_id
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }
    $answer_snippet_id = $this->db->lastInsertId();

    // connect answer and tags, first container of likes and then container of
    // dislikes
    try {
      $query = $this->db->prepare('INSERT INTO AnswerSnippetContainer(answer_snippet_id, container_id) VALUES (:answer_snippet_id, :container_id)');

      // likes
      $query->execute(array(
        ':answer_snippet_id' => $answer_snippet_id,
        ':container_id' => $likes_container_id
      ));

      // dislikes
      $query->execute(array(
        ':answer_snippet_id' => $answer_snippet_id,
        ':container_id' => $dislikes_container_id
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }

    return true;    
  }

  /**
   *
   */
  public function createForcedChoiceAnswer(
    // Answer
    $type, $user_id, $time_to_answer, $dont_know_answer, $comments,
    // Chosen snippet
    $chosen_snippet_id,
    // AnswerSnipper
    $snippet_a_id, $snippet_b_id,
    // Tags
    $likes_snippet_a, $dislikes_snippet_a, $likes_snippet_b, $dislikes_snippet_b
    ) {

    // create two containers (like and dislike) per snippet
    $likes_container_a_id = $this->createContainer('like');
    $dislikes_container_a_id = $this->createContainer('dislike');
    $likes_container_b_id = $this->createContainer('like');
    $dislikes_container_b_id = $this->createContainer('dislike');

    if ($likes_container_a_id == -1 || $dislikes_container_a_id == -1 ||
        $likes_container_b_id == -1 || $dislikes_container_b_id == -1) {
      return false;
    }

    // add tags to each respective container
    if (! $this->addTagsToContainer($likes_container_a_id, $likes_snippet_a)) {
      return false;
    }
    if (! $this->addTagsToContainer($dislikes_container_a_id, $dislikes_snippet_a)) {
      return false;
    }
    if (! $this->addTagsToContainer($likes_container_b_id, $likes_snippet_b)) {
      return false;
    }
    if (! $this->addTagsToContainer($dislikes_container_b_id, $dislikes_snippet_b)) {
      return false;
    }

    // create a new answer
    try {
      $query = $this->db->prepare('INSERT INTO Answer(type, user_id, time_to_answer, dont_know_answer, comments) VALUES (:type, :user_id, :time_to_answer, :dont_know_answer, :comments)');
      $query->execute(array(
        ':type' => $type,
        ':user_id' => $user_id,
        ':time_to_answer' => $time_to_answer,
        ':dont_know_answer' => $dont_know_answer,
        ':comments' => $comments
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }
    $answer_id = $this->db->lastInsertId();

    // create a new 'forced_choice' answer
    try {
      $query = $this->db->prepare('INSERT INTO ForcedChoice(answer_id, chosen_snippet_id) VALUES (:answer_id, :chosen_snippet_id)');
      $query->execute(array(
        ':answer_id' => $answer_id,
        ':chosen_snippet_id' => $chosen_snippet_id
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }

    // add snippet A to answer
    try {
      $query = $this->db->prepare('INSERT INTO AnswerSnippet(answer_id, snippet_id) VALUES (:answer_id, :snippet_id)');
      $query->execute(array(
        ':answer_id' => $answer_id,
        ':snippet_id' => $snippet_a_id
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }
    $answer_snippet_id = $this->db->lastInsertId();

    // connect answer and tags, first container of likes and then container of
    // dislikes
    try {
      $query = $this->db->prepare('INSERT INTO AnswerSnippetContainer(answer_snippet_id, container_id) VALUES (:answer_snippet_id, :container_id)');

      // likes
      $query->execute(array(
        ':answer_snippet_id' => $answer_snippet_id,
        ':container_id' => $likes_container_a_id
      ));

      // dislikes
      $query->execute(array(
        ':answer_snippet_id' => $answer_snippet_id,
        ':container_id' => $dislikes_container_a_id
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }

    // add snippet B to answer
    try {
      $query = $this->db->prepare('INSERT INTO AnswerSnippet(answer_id, snippet_id) VALUES (:answer_id, :snippet_id)');
      $query->execute(array(
        ':answer_id' => $answer_id,
        ':snippet_id' => $snippet_b_id
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }
    $answer_snippet_id = $this->db->lastInsertId();

    // connect answer and tags, first container of likes and then container of
    // dislikes
    try {
      $query = $this->db->prepare('INSERT INTO AnswerSnippetContainer(answer_snippet_id, container_id) VALUES (:answer_snippet_id, :container_id)');

      // likes
      $query->execute(array(
        ':answer_snippet_id' => $answer_snippet_id,
        ':container_id' => $likes_container_b_id
      ));

      // dislikes
      $query->execute(array(
        ':answer_snippet_id' => $answer_snippet_id,
        ':container_id' => $dislikes_container_b_id
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }

    return true;
  }

}

?>
