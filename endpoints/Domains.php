<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class Domains
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all instance domains
   *
   * @return array List of domains
   * @throws \Exception
   */
  public function listDomains(): array
  {
    return $this->client->request('GET', '/domains');
  }

  /**
   * Add a domain
   *
   * @param array $params Domain creation parameters
   * @return array Created domain object
   * @throws InvalidArgumentException
   */
  public function addDomain(array $params): array
  {
    $allowedParams = [
      'name' => 'string',
      'is_satellite' => 'boolean',
      'proxy_url' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['name']) || !isset($params['is_satellite'])) {
      throw new InvalidArgumentException("Both 'name' and 'is_satellite' parameters are required");
    }

    return $this->client->request('POST', '/domains', $params);
  }

  /**
   * Delete a satellite domain
   *
   * @param string $domainId The ID of the domain to delete
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteDomain(string $domainId): array
  {
    Validator::validateId($domainId, 'Domain ID');
    return $this->client->request('DELETE', "/domains/{$domainId}");
  }

  /**
   * Update a domain
   *
   * @param string $domainId The ID of the domain to update
   * @param array $params Update parameters
   * @return array Updated domain object
   * @throws InvalidArgumentException
   */
  public function updateDomain(string $domainId, array $params): array
  {
    Validator::validateId($domainId, 'Domain ID');
    $allowedParams = [
      'name' => 'string',
      'proxy_url' => 'string',
      'is_secondary' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/domains/{$domainId}", $params);
  }
}

?>