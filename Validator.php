<?php

namespace Clerk\Util;

use InvalidArgumentException;

class Validator
{
  /**
   * Validate parameters against allowed parameters and their types
   *
   * @param array $params The parameters to validate
   * @param array $allowedParams The allowed parameters and their types
   * @throws InvalidArgumentException
   */
  public static function validateParams(array $params, array $allowedParams): void
  {
    foreach ($params as $key => $value) {
      if (!isset($allowedParams[$key])) {
        throw new InvalidArgumentException("Invalid parameter: {$key}");
      }
      self::validateType($value, $allowedParams[$key], $key);
    }
  }

  /**
   * Validate the type of a value
   *
   * @param mixed $value The value to validate
   * @param string $expectedType The expected type
   * @param string $paramName The name of the parameter (for error messages)
   * @throws InvalidArgumentException
   */
  public static function validateType($value, string $expectedType, string $paramName): void
  {
    switch ($expectedType) {
      case 'array':
        if (!is_array($value)) {
          throw new InvalidArgumentException("{$paramName} must be an array, " . gettype($value) . " given");
        }
        break;
      case 'integer':
        if (!is_int($value)) {
          throw new InvalidArgumentException("{$paramName} must be an integer, " . gettype($value) . " given");
        }
        break;
      case 'string':
        if (!is_string($value)) {
          throw new InvalidArgumentException("{$paramName} must be a string, " . gettype($value) . " given");
        }
        break;
      case 'boolean':
        if (!is_bool($value)) {
          throw new InvalidArgumentException("{$paramName} must be a boolean, " . gettype($value) . " given");
        }
        break;
      default:
        throw new InvalidArgumentException("Unsupported type check: {$expectedType}");
    }
  }

  /**
   * Validate an ID
   *
   * @param string $id The ID to validate
   * @param string $idName The name of the ID (for error messages)
   * @throws InvalidArgumentException
   */
  public static function validateId(string $id, string $idName = 'ID'): void
  {
    if (empty($id)) {
      throw new InvalidArgumentException("{$idName} cannot be empty");
    }
    if (!preg_match('/^[a-zA-Z0-9_.\-]+$/', $id)) {
      throw new InvalidArgumentException("{$idName} contains invalid characters");
    }
  }
}

?>