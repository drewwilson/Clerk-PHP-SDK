<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class Sessions
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all sessions
   *
   * @param array $params Query parameters
   * @return array List of sessions
   * @throws InvalidArgumentException
   */
  public function listSessions(array $params = []): array
  {
    $allowedParams = [
      'client_id' => 'string',
      'user_id' => 'string',
      'status' => 'string',
      'limit' => 'integer',
      'offset' => 'integer'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', '/sessions', $params);
  }

  /**
   * Retrieve a session
   *
   * @param string $sessionId The ID of the session to retrieve
   * @return array Session object
   * @throws InvalidArgumentException
   */
  public function getSession(string $sessionId): array
  {
    Validator::validateId($sessionId, 'Session ID');
    return $this->client->request('GET', "/sessions/{$sessionId}");
  }

  /**
   * Revoke a session
   *
   * @param string $sessionId The ID of the session to revoke
   * @return array Revoked session object
   * @throws InvalidArgumentException
   */
  public function revokeSession(string $sessionId): array
  {
    Validator::validateId($sessionId, 'Session ID');
    return $this->client->request('POST', "/sessions/{$sessionId}/revoke");
  }

  /**
   * Create a session token from a JWT template
   *
   * @param string $sessionId The ID of the session
   * @param string $templateName The name of the JWT Template
   * @return array Created token object
   * @throws InvalidArgumentException
   */
  public function createSessionTokenFromTemplate(string $sessionId, string $templateName): array
  {
    Validator::validateId($sessionId, 'Session ID');
    if (empty($templateName)) {
      throw new InvalidArgumentException("Template name cannot be empty");
    }
    return $this->client->request('POST', "/sessions/{$sessionId}/tokens/{$templateName}");
  }
}

?>