<?php

/**
 * JSON helper class to handle some functions related to parsing and outputing JSON response.
 */
class JsonHelper {

  /**
   * Set JSON Error.
   *
   * @param string $message
   *   Error message to print.
   * @param int $error_code
   *   Error code related to current request.
   *
   * @return string
   *   Domain name.
   */
  public function setError($message, $error_code = NULL) {
    $data = [];
    $data['status'] = 'error';
    $data['message'] = $message;
    if ($error_code) {
      $data['code'] = $error_code;
    }
    return json_encode($data, JSON_PRETTY_PRINT);
  }

  /**
   * Indents a flat JSON string to make it more human-readable.
   *
   * @param string $json
   *   The original JSON string to process.
   *
   * @return string
   *   Indented version of the original JSON string.
   */
  public function indent($json) {
    $result = '';
    $pos = 0;
    $strLen = strlen($json);
    $indentStr = "\t";
    $newLine = "\n";

    for ($i = 0; $i < $strLen; $i++) {
      // Grab the next character in the string.
      $char = $json[$i];

      // Are we inside a quoted string?
      if ($char == '"') {
        // Search for the end of the string (keeping in mind of the escape sequences)
        if (!preg_match('`"(\\\\\\\\|\\\\"|.)*?"`s', $json, $m, NULL, $i)) {
          return $json;
        }

        // Add extracted string to the result and move ahead.
        $result .= $m[0];
        $i += strlen($m[0]) - 1;
        continue;
      }
      elseif ($char == '}' || $char == ']') {
        $result .= $newLine;
        $pos--;
        $result .= str_repeat($indentStr, $pos);
      }

      // Add the character to the result string.
      $result .= $char;

      // If the last character was the beginning of an element,
      // output a new line and indent the next line.
      if ($char == ',' || $char == '{' || $char == '[') {
        $result .= $newLine;
        if ($char == '{' || $char == '[') {
          $pos++;
        }

        $result .= str_repeat($indentStr, $pos);
      }
    }

    return $result;
  }

}
