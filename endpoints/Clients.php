<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class Clients
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Verify a client
   *
   * @param array $params Verification parameters
   * @return array Verified client object
   * @throws InvalidArgumentException
   */
  public function verifyClient(array $params): array
  {
    $allowedParams = [
      'token' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['token'])) {
      throw new InvalidArgumentException("The 'token' parameter is required");
    }

    return $this->client->request('POST', '/clients/verify', $params);
  }

  /**
   * Get a client
   *
   * @param string $clientId The ID of the client to retrieve
   * @return array Client object
   * @throws InvalidArgumentException
   */
  public function getClient(string $clientId): array
  {
    Validator::validateId($clientId, 'Client ID');
    return $this->client->request('GET', "/clients/{$clientId}");
  }
}

?>