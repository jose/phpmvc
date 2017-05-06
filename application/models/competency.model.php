<?php

/**
 * Class CompetencyModel
 */
class CompetencyModel {

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
  public function addCompetency($score) {
    try {
      $query = $this->db->prepare('INSERT INTO Competency(score) VALUES (:score)');
      $query->execute(array(
        ':score' => $score
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
  public function addAnswer($competency_id, $question_num, $choice, $time_to_answer) {
    try {
      $query = $this->db->prepare('INSERT INTO CompetencyAnswer(competency_id, question_num, choice, time_to_answer) VALUES (:competency_id, :question_num, :choice, :time_to_answer)');
      $query->execute(array(
        ':competency_id' => $competency_id,
        ':question_num' => $question_num,
        ':choice' => $choice,
        ':time_to_answer' => $time_to_answer
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
  public function getCompetencyAnswers($competency_id) {
    $query = $this->db->prepare('SELECT * FROM AnswerCompetency WHERE competency_id = :competency_id');
    $query->execute(array(
      ':competency_id' => $competency_id
    ));

    return $query->fetchAll();
  }

  /**
   *
   */
  public function getAllCompetencyTests() {
    $query = $this->db->prepare('SELECT * FROM Competency');
    $query->execute();
    return $query->fetchAll();
  }

}

?>
