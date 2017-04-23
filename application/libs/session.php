<?php

/**
 * Base Session Class
 */
class Session {

  /**
   * 
   */
  public static function init() {
    // create/read session
    session_set_cookie_params(0, '/');
    session_start();
  }

  /**
   *
   */
  public static function set($key, $value) {
    $_SESSION[$key] = $value;
  }

  /**
   *
   */
  public static function get($key) {
    if (isset($_SESSION[$key])) {
      return $_SESSION[$key];
    }
    return null;
  }

  /**
   * Unset all session values
   */
  public static function destroy() {
    unset($_SESSION);
    session_destroy();
  }
}

?>
