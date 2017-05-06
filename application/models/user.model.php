<?php

/**
 * UserModel Class
 */
class UserModel {

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
  public function addUser($id, $competency_id) {
    try {
      $query = $this->db->prepare('INSERT INTO User(id, competency_id) VALUES (:id, :competency_id)');
      $query->execute(array(
        ':id' => $id,
        ':competency_id' => $competency_id
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
  public function exists($id) {
    $query = $this->db->prepare("SELECT id FROM User WHERE id = :id");
    $query->execute(array(':id' => $id));

    if ($query->rowCount() > 0) {
      return true; // exists!
    }

    return false;
  }

  /**
   *
   */
  public function getUser($id) {
    $query = $this->db->prepare("SELECT * FROM User WHERE id = :id");
    $query->execute(array(
      ':id' => $id
    ));

    return $query->fetchObject();
  }
}

?>
