<?php

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

/**
 * Base Encryption Class
 */
class Encryption {

  /**
   * 
   */
  private static function loadEncryptionKeyFromConfig() {
    // load the content of /application/config/key.txt
    $keyAscii = file_get_contents(PATH_CONFS . "key.txt");
    return Key::loadFromAsciiSafeString($keyAscii);
  }

  /**
   *
   */
  public static function encrypt($secret_data) {
    $key = Encryption::loadEncryptionKeyFromConfig();
    $ciphertext = Crypto::encrypt($secret_data, $key);
    return $ciphertext;
  }

  /**
   *
   */
  public static function decrypt($ciphertext) {
    $key = Encryption::loadEncryptionKeyFromConfig();

    $secret_data = null;
    try {
      $secret_data = Crypto::decrypt($ciphertext, $key);
    } catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
      // An attack! Either the wrong key was loaded, or the ciphertext has
      // changed since it was created -- either corrupted in the database or
      // intentionally modified by someone trying to carry out an attack.
    }

    return $secret_data;
  }
}

?>
