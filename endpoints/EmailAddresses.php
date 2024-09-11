<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class EmailAddresses
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Create an email address
   *
   * @param array $params Email address creation parameters
   * @return array Created email address object
   * @throws InvalidArgumentException
   */
  public function createEmailAddress(array $params): array
  {
    $allowedParams = [
      'user_id' => 'string',
      'email_address' => 'string',
      'verified' => 'boolean',
      'primary' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['user_id']) || !isset($params['email_address'])) {
      throw new InvalidArgumentException("Both 'user_id' and 'email_address' parameters are required");
    }

    return $this->client->request('POST', '/email_addresses', $params);
  }

  /**
   * Retrieve an email address
   *
   * @param string $emailAddressId The ID of the email address to retrieve
   * @return array Email address object
   * @throws InvalidArgumentException
   */
  public function getEmailAddress(string $emailAddressId): array
  {
    Validator::validateId($emailAddressId, 'Email Address ID');
    return $this->client->request('GET', "/email_addresses/{$emailAddressId}");
  }

  /**
   * Update an email address
   *
   * @param string $emailAddressId The ID of the email address to update
   * @param array $params Update parameters
   * @return array Updated email address object
   * @throws InvalidArgumentException
   */
  public function updateEmailAddress(string $emailAddressId, array $params): array
  {
    Validator::validateId($emailAddressId, 'Email Address ID');
    $allowedParams = [
      'verified' => 'boolean',
      'primary' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/email_addresses/{$emailAddressId}", $params);
  }

  /**
   * Delete an email address
   *
   * @param string $emailAddressId The ID of the email address to delete
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteEmailAddress(string $emailAddressId): array
  {
    Validator::validateId($emailAddressId, 'Email Address ID');
    return $this->client->request('DELETE', "/email_addresses/{$emailAddressId}");
  }
}

?>