<?php

/**
 * Class StudyModel
 */
class StudyModel {

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
  public function hasUserCompletedStudy($type, $user_id) {
    $query = $this->db->prepare('SELECT COUNT(*) FROM Study WHERE type = :type AND user_id = :user_id');
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
  public function getAllTags() {
    $query = $this->db->prepare('SELECT * FROM Tag');
    $query->execute();
    return $query->fetchAll();
  }

  /**
   *
   */
  public function addTagsToContainer($container_id, $tag_id) {
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
  public function getAllSnippets() {
    $query = $this->db->prepare('SELECT * FROM Snippet');
    $query->execute();
    return $query->fetchAll();
  }

  /**
   *
   */
  public function createStudy($type, $user_id, $time_to_answer) {
    try {
      $query = $this->db->prepare('INSERT INTO Study(type, user_id, time_to_answer) VALUES (:type, :user_id, :time_to_answer)');
      $query->execute(array(
        ':type' => $type,
        ':user_id' => $user_id,
        ':time_to_answer' => $time_to_answer
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
  public function createIndividualStudy($study_id, $snippet_id, $num_stars, $like_container_id, $dislike_container_id) {
    try {
      $query = $this->db->prepare('INSERT INTO IndividualStudy(study_id, snippet_id, num_stars, like_id, dislike_id) VALUES (:study_id, :snippet_id, :num_stars, :like_id, :dislike_id)');
      $query->execute(array(
        ':study_id' => $study_id,
        ':snippet_id' => $snippet_id,
        ':num_stars' => $num_stars,
        ':like_id' => $like_container_id,
        ':dislike_id' => $dislike_container_id
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
  public function createPairStudy($study_id, $snippet_a_id, $like_container_a_id, $dislike_container_a_id, $snippet_b_id, $like_container_b_id, $dislike_container_b_id, $chosen_snippet_id) {
    try {
      $query = $this->db->prepare('INSERT INTO PairStudy(study_id, snippet_a_id, like_a_id, dislike_a_id, snippet_b_id, like_b_id, dislike_b_id, chosen_snippet_id) VALUES (:study_id, :snippet_a_id, :like_a_id, :dislike_a_id, :snippet_b_id, :like_b_id, :dislike_b_id, :chosen_snippet_id)');
      $query->execute(array(
        ':study_id' => $study_id,
        ':snippet_a_id' => $snippet_a_id,
        ':like_a_id' => $like_container_a_id,
        ':dislike_a_id' => $dislike_container_a_id,
        ':snippet_b_id' => $snippet_b_id,
        ':like_b_id' => $like_container_b_id,
        ':dislike_b_id' => $dislike_container_b_id,
        ':chosen_snippet_id' => $chosen_snippet_id
      ));
    } catch(PDOExecption $e) {
      $this->db->rollback();
      return false;
    }

    return true;
  }
}

?>
