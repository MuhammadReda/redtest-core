<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 3/25/15
 * Time: 12:16 PM
 */

namespace RedTest\core\fields;

use RedTest\core\forms\Form;
use RedTest\core\Utils;

class ListBoolean extends Field {

  /**
   * Fill field is specified form with default values.
   *
   * @param Form $formObject
   *   Form object.
   * @param string $field_name
   *   Field name.
   *
   * @return array
   *   An array with 2 values:
   *   (1) $success: Whether default values could be filled in the field.
   *   (2) $msg: Message in case there is an error. This will be empty if
   *   $success is TRUE.
   */
  public static function fillDefaultValues(Form $formObject, $field_name) {
    if (method_exists($formObject, 'getEntityObject')) {
      // This is an entity form.
      list($field, $instance, $num) = $formObject->getFieldDetails($field_name);
      $function = 'fillDefault' . Utils::makeTitleCase(
          $instance['widget']['type']
        ) . 'Values';

      return self::$function($formObject, $field_name);
    }
  }

  /**
   * Fill checkbox field with default values.
   *
   * @param Form $formObject
   *   Form object.
   * @param string $field_name
   *   Field name.
   *
   * @return array
   *   An array with 3 values:
   *   (1) $success: Whether default values could be filled in the field.
   *   (2) $values: Values that were filled for the field.
   *   (3) $msg: Message in case there is an error. This will be empty if
   *   $success is TRUE.
   */
  public static function fillDefaultOptionsButtonsValues(
    Form $formObject,
    $field_name
  ) {
    $num = 1;
    $required = FALSE;
    $allowed_values = array();
    if (method_exists($formObject, 'getEntityObject')) {
      // This is an entity form.
      list($field, $instance, $num) = $formObject->getFieldDetails($field_name);
      $allowed_values = array_keys($field['settings']['allowed_values']);
      $required = $instance['required'];
    }

    $values = array();
    foreach ($allowed_values as $allowed_value) {
      if (Utils::getRandomInt(0, 1)) {
        $values[] = $allowed_value;
      }
    }

    if ($required && !sizeof($values)) {
      // This field is required and no checkbox has been selected. Select one
      // randomly.
      $key = array_rand($allowed_values);
      $values[] = $allowed_values[$key];
    }

    return self::fillOptionsButtonsValues($formObject, $field_name, $values);
  }


  public static function fillDefaultOptionsOnOffValues(
    Form $formObject,
    $field_name
  ) {
    $required = FALSE;
    if (method_exists($formObject, 'getEntityObject')) {
      // This is an entity form.
      list($field, $instance, $num) = $formObject->getFieldDetails($field_name);
      $required = $instance['required'];
    }

    $values = ($required || Utils::getRandomInt(0, 1)) ? array(1) : array();

    return self::fillOptionsButtonsValues($formObject, $field_name, $values);
  }

  public static function fillOptionsOnOffValues(
    Form $formObject,
    $field_name,
    $values
  ) {
    $formObject->emptyField($field_name);

    if (is_string($values)) {
      $values = array($values);
    }

    $input = array();
    if (sizeof($values)) {
      foreach ($values as $key => $value) {
        if (is_string($value) || is_numeric($value)) {
          $input[$value] = $value;
        }
      }

      $formObject->setValues($field_name, array(LANGUAGE_NONE => $input));
    }

    return array(TRUE, $input, "");
  }
}