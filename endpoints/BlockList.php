<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class BlockList
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all identifiers on the block-list
   *
   * @return array List of block-list identifiers
   * @throws \Exception
   */
  public function listBlocklistIdentifiers(): array
  {
    return $this->client->request('GET', '/blocklist_identifiers');
  }

  /**
   * Add identifier to the block-list
   *
   * @param array $params Creation parameters
   * @return array Created block-list identifier object
   * @throws InvalidArgumentException
   */
  public function createBlocklistIdentifier(array $params): array
  {
    $allowedParams = [
      'identifier' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['identifier'])) {
      throw new InvalidArgumentException("The 'identifier' parameter is required");
    }

    return $this->client->request('POST', '/blocklist_identifiers', $params);
  }

  /**
   * Delete identifier from block-list
   *
   * @param string $identifierId The ID of the identifier to delete from the block-list
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteBlocklistIdentifier(string $identifierId): array
  {
    Validator::validateId($identifierId, 'Identifier ID');
    return $this->client->request('DELETE', "/blocklist_identifiers/{$identifierId}");
  }
}

?>