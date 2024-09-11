<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class ProxyChecks
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Verify the proxy configuration for your domain
   *
   * @param array $params Proxy check parameters
   * @return array Proxy check result
   * @throws InvalidArgumentException
   */
  public function verifyDomainProxy(array $params): array
  {
    $allowedParams = [
      'domain_id' => 'string',
      'proxy_url' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['domain_id']) || !isset($params['proxy_url'])) {
      throw new InvalidArgumentException("Both 'domain_id' and 'proxy_url' parameters are required");
    }

    return $this->client->request('POST', '/proxy_checks', $params);
  }
}

?>