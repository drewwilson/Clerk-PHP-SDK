<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class AllowList
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all identifiers on the allow-list
   *
   * @return array List of allow-list identifiers
   * @throws \Exception
   */
  public function listAllowlistIdentifiers(): array
  {
    return $this->client->request('GET', '/allowlist_identifiers');
  }

  /**
   * Add identifier to the allow-list
   *
   * @param array $params Creation parameters
   * @return array Created allow-list identifier object
   * @throws InvalidArgumentException
   */
  public function createAllowlistIdentifier(array $params): array
  {
    $allowedParams = [
      'identifier' => 'string',
      'notify' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['identifier'])) {
      throw new InvalidArgumentException("The 'identifier' parameter is required");
    }

    return $this->client->request('POST', '/allowlist_identifiers', $params);
  }

  /**
   * Delete identifier from allow-list
   *
   * @param string $identifierId The ID of the identifier to delete from the allow-list
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteAllowlistIdentifier(string $identifierId): array
  {
    Validator::validateId($identifierId, 'Identifier ID');
    return $this->client->request('DELETE', "/allowlist_identifiers/{$identifierId}");
  }
}

?>