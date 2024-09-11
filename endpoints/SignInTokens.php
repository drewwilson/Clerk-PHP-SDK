<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class SignInTokens
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Create a sign-in token
   *
   * @param array $params Token creation parameters
   * @return array Created sign-in token object
   * @throws InvalidArgumentException
   */
  public function createSignInToken(array $params): array
  {
    $allowedParams = [
      'user_id' => 'string',
      'expires_in_seconds' => 'integer'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['user_id'])) {
      throw new InvalidArgumentException("The 'user_id' parameter is required");
    }

    return $this->client->request('POST', '/sign_in_tokens', $params);
  }

  /**
   * Revoke a sign-in token
   *
   * @param string $signInTokenId The ID of the sign-in token to revoke
   * @return array Revoked sign-in token object
   * @throws InvalidArgumentException
   */
  public function revokeSignInToken(string $signInTokenId): array
  {
    Validator::validateId($signInTokenId, 'Sign-in Token ID');
    return $this->client->request('POST', "/sign_in_tokens/{$signInTokenId}/revoke");
  }
}

?>