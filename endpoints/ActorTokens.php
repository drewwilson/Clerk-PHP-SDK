<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class ActorTokens
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Create an actor token
   *
   * @param array $params Creation parameters
   * @return array Created actor token object
   * @throws InvalidArgumentException
   */
  public function createActorToken(array $params): array
  {
    $allowedParams = [
      'user_id' => 'string',
      'actor' => 'array',
      'expires_in_seconds' => 'integer',
      'session_max_duration_in_seconds' => 'integer'
    ];
    Validator::validateParams($params, $allowedParams);

    // Ensure required parameters are present
    if (!isset($params['user_id']) || !isset($params['actor'])) {
      throw new InvalidArgumentException("Both 'user_id' and 'actor' are required parameters");
    }

    // Ensure actor has a 'sub' property
    if (!isset($params['actor']['sub'])) {
      throw new InvalidArgumentException("The 'actor' parameter must include a 'sub' property");
    }

    return $this->client->request('POST', '/actor_tokens', $params);
  }

  /**
   * Revoke an actor token
   *
   * @param string $actorTokenId The ID of the actor token to revoke
   * @return array Revoked actor token object
   * @throws InvalidArgumentException
   */
  public function revokeActorToken(string $actorTokenId): array
  {
    Validator::validateId($actorTokenId, 'Actor Token ID');
    return $this->client->request('POST', "/actor_tokens/{$actorTokenId}/revoke");
  }
}

?>