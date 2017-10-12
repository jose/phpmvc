<?php

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
    return intval($keyAscii);
  }

  /**
   *
   */
  public static function encrypt($secret_data) {
    $key = Encryption::loadEncryptionKeyFromConfig();
    $ciphertext = "";

    // use something as simple as 'Caesar Cipher'. The Caesar cipher,
    // also known as a shift cipher, is one of the simplest forms of
    // encryption. It is a substitution cipher where each letter in the
    // original message (called the plaintext) is replaced with a letter
    // corresponding to a certain number of letters up or down in the
    // alphabet.
    for ($i = 0; $i < strlen($secret_data); $i++) {
      $ciphertext .= chr((ord($secret_data[$i]) + $key) % 255);
    }

    // reverse text
    $ciphertext = strrev($ciphertext);

    // convert from text to hexadecimal
    $ciphertext = implode(unpack("H*", $ciphertext));

    return $ciphertext;
  }

  /**
   *
   */
  public static function decrypt($ciphertext) {
    $key = Encryption::loadEncryptionKeyFromConfig();
    $secret_data = "";

    // convert from hexadecimal to text
    $secret_data = pack("H*", $ciphertext);

    // reverse text
    $secret_data = strrev($secret_data);

    // reverse 'Caesar Cipher'
    for ($i = 0; $i < strlen($secret_data); $i++) {
      $secret_data[$i] = chr((ord($secret_data[$i]) - $key) % 255);
    }

    return $secret_data;
  }
}

?>
