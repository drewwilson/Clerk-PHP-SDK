<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class SAMLConnections
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all SAML Connections
   *
   * @param array $params Query parameters
   * @return array List of SAML Connections
   * @throws InvalidArgumentException
   */
  public function listSAMLConnections(array $params = []): array
  {
    $allowedParams = [
      'limit' => 'integer',
      'offset' => 'integer'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', '/saml_connections', $params);
  }

  /**
   * Create a SAML Connection
   *
   * @param array $params SAML Connection creation parameters
   * @return array Created SAML Connection object
   * @throws InvalidArgumentException
   */
  public function createSAMLConnection(array $params): array
  {
    $allowedParams = [
      'name' => 'string',
      'domain' => 'string',
      'provider' => 'string',
      'idp_entity_id' => 'string',
      'idp_sso_url' => 'string',
      'idp_certificate' => 'string',
      'idp_metadata_url' => 'string',
      'idp_metadata' => 'string',
      'attribute_mapping' => 'array'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['name']) || !isset($params['domain']) || !isset($params['provider'])) {
      throw new InvalidArgumentException("'name', 'domain', and 'provider' parameters are required");
    }

    return $this->client->request('POST', '/saml_connections', $params);
  }

  /**
   * Retrieve a SAML Connection
   *
   * @param string $samlConnectionId The ID of the SAML Connection to retrieve
   * @return array SAML Connection object
   * @throws InvalidArgumentException
   */
  public function getSAMLConnection(string $samlConnectionId): array
  {
    Validator::validateId($samlConnectionId, 'SAML Connection ID');
    return $this->client->request('GET', "/saml_connections/{$samlConnectionId}");
  }

  /**
   * Update a SAML Connection
   *
   * @param string $samlConnectionId The ID of the SAML Connection to update
   * @param array $params Update parameters
   * @return array Updated SAML Connection object
   * @throws InvalidArgumentException
   */
  public function updateSAMLConnection(string $samlConnectionId, array $params): array
  {
    Validator::validateId($samlConnectionId, 'SAML Connection ID');
    $allowedParams = [
      'name' => 'string',
      'domain' => 'string',
      'idp_entity_id' => 'string',
      'idp_sso_url' => 'string',
      'idp_certificate' => 'string',
      'idp_metadata_url' => 'string',
      'idp_metadata' => 'string',
      'attribute_mapping' => 'array',
      'active' => 'boolean',
      'sync_user_attributes' => 'boolean',
      'allow_subdomains' => 'boolean',
      'allow_idp_initiated' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/saml_connections/{$samlConnectionId}", $params);
  }

  /**
   * Delete a SAML Connection
   *
   * @param string $samlConnectionId The ID of the SAML Connection to delete
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteSAMLConnection(string $samlConnectionId): array
  {
    Validator::validateId($samlConnectionId, 'SAML Connection ID');
    return $this->client->request('DELETE', "/saml_connections/{$samlConnectionId}");
  }
}

?>