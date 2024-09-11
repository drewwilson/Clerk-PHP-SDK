<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class OAuthApplications
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all OAuth applications
   *
   * @param array $params Query parameters
   * @return array List of OAuth applications
   * @throws InvalidArgumentException
   */
  public function listOAuthApplications(array $params = []): array
  {
    $allowedParams = [
      'limit' => 'integer',
      'offset' => 'integer'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', '/oauth_applications', $params);
  }

  /**
   * Create an OAuth application
   *
   * @param array $params Application creation parameters
   * @return array Created OAuth application object
   * @throws InvalidArgumentException
   */
  public function createOAuthApplication(array $params): array
  {
    $allowedParams = [
      'name' => 'string',
      'callback_url' => 'string',
      'scopes' => 'string',
      'public' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['name']) || !isset($params['callback_url'])) {
      throw new InvalidArgumentException("Both 'name' and 'callback_url' parameters are required");
    }

    return $this->client->request('POST', '/oauth_applications', $params);
  }

  /**
   * Retrieve an OAuth application
   *
   * @param string $oauthApplicationId The ID of the OAuth application to retrieve
   * @return array OAuth application object
   * @throws InvalidArgumentException
   */
  public function getOAuthApplication(string $oauthApplicationId): array
  {
    Validator::validateId($oauthApplicationId, 'OAuth Application ID');
    return $this->client->request('GET', "/oauth_applications/{$oauthApplicationId}");
  }

  /**
   * Update an OAuth application
   *
   * @param string $oauthApplicationId The ID of the OAuth application to update
   * @param array $params Update parameters
   * @return array Updated OAuth application object
   * @throws InvalidArgumentException
   */
  public function updateOAuthApplication(string $oauthApplicationId, array $params): array
  {
    Validator::validateId($oauthApplicationId, 'OAuth Application ID');
    $allowedParams = [
      'name' => 'string',
      'callback_url' => 'string',
      'scopes' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/oauth_applications/{$oauthApplicationId}", $params);
  }

  /**
   * Delete an OAuth application
   *
   * @param string $oauthApplicationId The ID of the OAuth application to delete
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteOAuthApplication(string $oauthApplicationId): array
  {
    Validator::validateId($oauthApplicationId, 'OAuth Application ID');
    return $this->client->request('DELETE', "/oauth_applications/{$oauthApplicationId}");
  }

  /**
   * Rotate the client secret of an OAuth application
   *
   * @param string $oauthApplicationId The ID of the OAuth application
   * @return array Updated OAuth application object with new client secret
   * @throws InvalidArgumentException
   */
  public function rotateOAuthApplicationSecret(string $oauthApplicationId): array
  {
    Validator::validateId($oauthApplicationId, 'OAuth Application ID');
    return $this->client->request('POST', "/oauth_applications/{$oauthApplicationId}/rotate_secret");
  }
}

?>