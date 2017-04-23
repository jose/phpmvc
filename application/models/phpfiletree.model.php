<?php

/**
 * Class phpFileTreeModel based on
 *
 * Cory S.N. LaViska
 * http://abeautifulsite.net/
 *
 * Documentation
 * http://abeautifulsite.net/notebook.php?article=21
 *
 */
class phpFileTreeModel {

  /**
   * Constructor
   */
  function __construct() {
    // empty
  }

#  /**
#   * Generates a valid XHTML list of all directories, sub-directories, and files in $directory
#   */
#  public function php_file_tree($directory, $extensions = array()) {
#    if (substr($directory, -1) == "/") {
#      // Remove trailing slash
#      $directory = substr($directory, 0, strlen($directory) - 1);
#    }

#    $code = $this->php_file_tree_dir($directory, $extensions);
#    return $code;
#  }

#  /**
#   * Recursive function called by php_file_tree() to list directories/files
#   */
#  private function php_file_tree_dir($directory, $extensions = array(), $first_call = true) {

#    // Get and sort directories/files
#    if (function_exists("scandir")) {
#      $file = scandir($directory);
#    } else {
#      $file = $this->php4_scandir($directory);
#    }

#    natcasesort($file);

#    // Make directories first
#    $files = $dirs = array();
#    foreach($file as $this_file) {
#      if (is_dir("$directory/$this_file")) {
#        $dirs[] = $this_file;
#      } else {
#        $files[] = $this_file;
#      }
#    }
#    $file = array_merge($dirs, $files);

#    // Filter unwanted extensions
#    if (!empty($extensions)) {
#      foreach(array_keys($file) as $key) {
#        if (!is_dir("$directory/$file[$key]")) {
#          $ext = substr($file[$key], strrpos($file[$key], ".") + 1);
#          if (!in_array($ext, $extensions)) {
#            unset($file[$key]);
#          }
#        }
#      }
#    }

#    $php_file_tree = "";
#    if (count($file) > 2) { // Use 2 instead of 0 to account for . and .. "directories"
#      $php_file_tree = "<ul";
#      if ($first_call) {
#        $php_file_tree .= " class=\"php-file-tree\"";
#        $first_call = false;
#      }
#      $php_file_tree .= ">";
#      foreach($file as $this_file) {
#        if ($this_file != "." && $this_file != "..") {
#          if (is_dir("$directory/$this_file")) {
#            // Directory
#            $php_file_tree .= "<li class=\"pft-directory\"><a href=\"#\">" . htmlspecialchars($this_file) . "</a>";
#            $php_file_tree .= $this->php_file_tree_dir("$directory/$this_file", $extensions, false);
#            $php_file_tree .= "</li>";
#          } else {
#            // File. Get extension (prepend 'ext-' to prevent invalid classes from extensions that begin with numbers)
#            $ext = "ext-" . substr($this_file, strrpos($this_file, ".") + 1);

#            $php_file_tree .= "<li class=\"pft-file " . strtolower($ext) . "\"><a class=\"composeButton\" path=\"$directory/$this_file\" href=\"#\">" . htmlspecialchars($this_file) . "</a></li>";
#          }
#        }
#      }
#      $php_file_tree .= "</ul>";
#    }

#    return $php_file_tree;
#  }

#  // For PHP4 compatibility
#  private function php4_scandir($dir) {
#    $dh  = opendir($dir);
#    while( false !== ($filename = readdir($dh)) ) {
#      $files[] = $filename;
#    }

#    sort($files);
#    return($files);
#  }

  /**
   * 
   *
   * $subjects dir
   * $snippets dir
   * $subject
   * $class
   * $type_of_snippet (e.g., pass, fail, oracle)
   * $generation_type (e.g., default, postprocessed)
   */
  public function php_file_tree_code($subject_dir, $snippets_dir,
                                      $subject_name, $class_name)
  {
    $base_dir = $subject_dir . "/" . $subject_name . "/" . $subject_name;
    $deps_file = $snippets_dir . "/" . $subject_name . "/" . $class_name . ".deps";

    // Root directory
    $php_file_tree = "<ul class=\"php-file-tree\">";
    $php_file_tree .= "<li class=\"pft-directory\">" .
                        "<a class=\"expand\" href=\"javascript:void(0)\">" .
                          "<span class=\"pft-directory-img\" aria-hidden=\"true\"></span> " .
                          htmlspecialchars($subject_name) .
                        "</a>";

    // 
    $php_file_tree .= "<ul>";

    $handle = fopen($deps_file, "r");
    if ($handle) {
      // 1. get all deps
      $deps = (array) null;
      while (($line = fgets($handle)) !== false) {
        $class_name_dep = trim(str_replace($subject_name . ".", "", $line));
        if (strpos($class_name_dep, '$') !== false) {
          continue ; // do not consider inner classes
        }
        $deps = array_merge($deps, array($class_name_dep));
      }

      // 2. natural sort
      natcasesort($deps);
      foreach ($deps as $dep) {
        $elem_name = htmlspecialchars($dep);

        $class_path = $base_dir . "/" . str_replace(".", "/", $dep) . ".java";
        $total_lines = intval(exec("wc -l '".str_replace(URL, "", $class_path)."'"));

        #$php_file_tree .= "<li class=\"pft-file ext-java\"> .
        $php_file_tree .= "<li class=\"pft-file\">" .  
                            "<a class=\"expand\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-triangle-right\" aria-hidden=\"true\"></span>".
                            "<span class=\"pft-file-img ext-java\" aria-hidden=\"true\"></span></a>" .
                            "<a class=\"openJavaFile\" path=\"".$class_path."\" line=\"-1\" total_lines=\"".$total_lines."\" href=\"#\">" .
                              $elem_name .
                            "</a>";
        $ast_file = str_replace(".java", ".ast", $class_path);
        $php_file_tree .= $this->parseJavaFile($ast_file, $class_path, $total_lines, $elem_name);
        $php_file_tree .= "</li>";
      }

      fclose($handle);
    }

    $php_file_tree .= "</ul></li></ul>";
    return $php_file_tree;
  }

  /**
   *
   */
  private function parseJavaFile($ast, $class_path, $total_lines, $elem_name) {
    $json_decoded = json_decode(file_get_contents($ast), true);
    return $this->toList($json_decoded, $class_path, $total_lines, $elem_name);
  }

  /**
   * Iterate over a class
   */
  private function _printClass($json, $class_path, $total_lines, $elem_name) {
    $str = "<li class=\"ptf-class\">" .
            "<a class=\"expand\" href=\"javascript:void(0)\"><span class=\"glyphicon glyphicon-triangle-right\" aria-hidden=\"true\"></span>";
    if ($json['type'] === 'interface') {
      $str .= "<span class=\"pft-interface-img\" aria-hidden=\"true\"></span></a>";
    } else {
      $str .= "<span class=\"pft-class-img\" aria-hidden=\"true\"></span></a>";
    }
    $str .= "<a class=\"openJavaFile\" parent_name=\"".$elem_name."\" path=\"".$class_path."\" line=\"".$json['line_number']."\" total_lines=\"".$total_lines."\" href=\"#\">".$json['name']."</a>";

    // classes
    foreach ($json['classes'] as $class) {
      $str = $str."<ul>".$this->_printClass($class, $class_path, $total_lines, $elem_name)."</ul>";
    }
    // methods
    $str_methods = "<ul>";
    foreach ($json['methods'] as $method) {
      $str_methods .= "<li class=\"ptf-method\">";

      if ($method['visibility'] === 'default') {
        $str_methods .= "<span class=\"pft-method-default-img\" aria-hidden=\"true\"></span>";
      } else if ($method['visibility'] === 'public') {
        $str_methods .= "<span class=\"pft-method-public-img\" aria-hidden=\"true\"></span>";
      } else if ($method['visibility'] === 'private') {
        $str_methods .= "<span class=\"pft-method-private-img\" aria-hidden=\"true\"></span>";
      } else if ($method['visibility'] === 'protected') {
        $str_methods .= "<span class=\"pft-method-protected-img\" aria-hidden=\"true\"></span>";
      }

      $str_methods .= "<a class=\"openJavaFile\" parent_name=\"".$elem_name."\" path=\"".$class_path."\" line=\"".$method['line_number']."\" total_lines=\"".$total_lines."\" href=\"#\">".
            $method['name'] .
            '('.htmlspecialchars($method['parameters']).') ';

      if (!empty($method['return'])) {
        $str_methods .= ' : '.htmlspecialchars($method['return']);
      }

      $str_methods .= "</a></li>";
    }
    $str_methods .= "</ul></li>";
    $str .= $str_methods;

    return $str;
  }

  /**
   * Iterate over the root element (i.e., a file)
   */
  private function toList($json, $class_path, $total_lines, $elem_name) {
    $str = "<ul>";
    if (!empty($json)) {
      foreach ($json['classes'] as $class) {
        $str .= $this->_printClass($class, $class_path, $total_lines, $elem_name);
      }
    }
    $str .= "</ul>";

    return $str;
  }
}

?>
