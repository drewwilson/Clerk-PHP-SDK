<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class SignUps
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Update a sign-up
   *
   * @param string $signUpId The ID of the sign-up to update
   * @param array $params Update parameters
   * @return array Updated sign-up object
   * @throws InvalidArgumentException
   */
  public function updateSignUp(string $signUpId, array $params): array
  {
    Validator::validateId($signUpId, 'Sign-up ID');
    $allowedParams = [
      'custom_action' => 'boolean',
      'external_id' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/sign_ups/{$signUpId}", $params);
  }
}

?>