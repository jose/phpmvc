<?php

/**
 * Home Class Controller
 */
class Example extends Controller {

  private $snippets_dir;
  private $subjects_dir;

  /**
   *
   */
  function __construct() {
    parent::__construct();

    $this->subjects_dir = URL . "public/subjects";
    $this->snippets_dir = URL . "public/snippets";
  }

  /**
   * 
   */
  public function index() {

    $user_id = Session::get('user_id');
    if ($user_id == null) {
      // do not allow 'example' page if there is not any user defined
      header('location: ' . URL);
      return;
    }

    $this->render('example/index');
    return;
  }

  /**
   *
   */
  public function progress_bar() {
    // Progress bar
    $progress = 35;

    $this->render('example/progress_bar', array(
      'progress' => $progress
    ));
    return;
  }

  /**
   *
   */
  public function multiple_choice_question() {
    $this->render('example/multiple_choice_question');
    return;
  }

  /**
   *
   */
  public function filetree() {
    // PHP file tree
    $phpFileTree_model = $this->loadModel('phpFileTree');

    // get file tree
    $subject_name = "org.apache.commons.math3";
    $class_name = "org.apache.commons.math3.complex.Complex";
    $tree = $phpFileTree_model->php_file_tree_code(
              $this->subjects_dir, $this->snippets_dir,
              $subject_name, $class_name);
    // snippet
    $snippet_source_code = file_get_contents($this->snippets_dir . "/" . $subject_name . "/" . $class_name . ".java");

    $this->render('example/filetree', array(
      'tree' => $tree,
      'snippet' => $snippet_source_code,
    ));
    return;
  }

  /**
   *
   */
  public function tags() {
    // TODO get this list of tags from a database
    $tags = ["Length", "Width", "Variables", "Indentation", "ConditionalStatements", "Numbers", "Comments", "Methods", "Parameters", "ArrayLength", "Spaces", "Parentheses", "ArithmeticOperators", "Comparisons", "Assertions", "Classes", "Characters", "Strings", "Exceptions", "Nulls", "Casts", "Booleans", "Arrays", "Fields"];

    $this->render('example/tags', array(
      'tags' => $tags,
    ));
    return;
  }

  /**
   *
   */
  public function pair() {
    // TODO get this list of tags from a database
    $tags = ["Length", "Width", "Variables", "Indentation", "ConditionalStatements", "Numbers", "Comments", "Methods", "Parameters", "ArrayLength", "Spaces", "Parentheses", "ArithmeticOperators", "Comparisons", "Assertions", "Classes", "Characters", "Strings", "Exceptions", "Nulls", "Casts", "Booleans", "Arrays", "Fields"];

    $this->render('example/pair', array(
      'tags' => $tags,
    ));
    return;
  }

  /**
   *
   */
  public function action() {
    // TODO
    //header('location: ' . URL);
    print_r($_POST);
    die(); // so that previous print could be read
  }
}

?>
