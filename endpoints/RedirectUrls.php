<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class RedirectURLs
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all redirect URLs
   *
   * @return array List of redirect URLs
   * @throws \Exception
   */
  public function listRedirectURLs(): array
  {
    return $this->client->request('GET', '/redirect_urls');
  }

  /**
   * Create a redirect URL
   *
   * @param array $params Redirect URL creation parameters
   * @return array Created redirect URL object
   * @throws InvalidArgumentException
   */
  public function createRedirectURL(array $params): array
  {
    $allowedParams = [
      'url' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['url'])) {
      throw new InvalidArgumentException("The 'url' parameter is required");
    }

    return $this->client->request('POST', '/redirect_urls', $params);
  }

  /**
   * Retrieve a redirect URL
   *
   * @param string $redirectUrlId The ID of the redirect URL to retrieve
   * @return array Redirect URL object
   * @throws InvalidArgumentException
   */
  public function getRedirectURL(string $redirectUrlId): array
  {
    Validator::validateId($redirectUrlId, 'Redirect URL ID');
    return $this->client->request('GET', "/redirect_urls/{$redirectUrlId}");
  }

  /**
   * Delete a redirect URL
   *
   * @param string $redirectUrlId The ID of the redirect URL to delete
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteRedirectURL(string $redirectUrlId): array
  {
    Validator::validateId($redirectUrlId, 'Redirect URL ID');
    return $this->client->request('DELETE', "/redirect_urls/{$redirectUrlId}");
  }
}

?>