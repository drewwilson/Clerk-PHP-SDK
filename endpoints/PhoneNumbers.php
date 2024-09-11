<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class PhoneNumbers
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Create a phone number
   *
   * @param array $params Phone number creation parameters
   * @return array Created phone number object
   * @throws InvalidArgumentException
   */
  public function createPhoneNumber(array $params): array
  {
    $allowedParams = [
      'user_id' => 'string',
      'phone_number' => 'string',
      'verified' => 'boolean',
      'primary' => 'boolean',
      'reserved_for_second_factor' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['user_id']) || !isset($params['phone_number'])) {
      throw new InvalidArgumentException("Both 'user_id' and 'phone_number' parameters are required");
    }

    return $this->client->request('POST', '/phone_numbers', $params);
  }

  /**
   * Retrieve a phone number
   *
   * @param string $phoneNumberId The ID of the phone number to retrieve
   * @return array Phone number object
   * @throws InvalidArgumentException
   */
  public function getPhoneNumber(string $phoneNumberId): array
  {
    Validator::validateId($phoneNumberId, 'Phone Number ID');
    return $this->client->request('GET', "/phone_numbers/{$phoneNumberId}");
  }

  /**
   * Update a phone number
   *
   * @param string $phoneNumberId The ID of the phone number to update
   * @param array $params Update parameters
   * @return array Updated phone number object
   * @throws InvalidArgumentException
   */
  public function updatePhoneNumber(string $phoneNumberId, array $params): array
  {
    Validator::validateId($phoneNumberId, 'Phone Number ID');
    $allowedParams = [
      'verified' => 'boolean',
      'primary' => 'boolean',
      'reserved_for_second_factor' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/phone_numbers/{$phoneNumberId}", $params);
  }

  /**
   * Delete a phone number
   *
   * @param string $phoneNumberId The ID of the phone number to delete
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deletePhoneNumber(string $phoneNumberId): array
  {
    Validator::validateId($phoneNumberId, 'Phone Number ID');
    return $this->client->request('DELETE', "/phone_numbers/{$phoneNumberId}");
  }
}

?>