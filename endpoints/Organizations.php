<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class Organizations
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all organizations
   *
   * @param array $params Query parameters
   * @return array List of organizations
   * @throws InvalidArgumentException
   */
  public function listOrganizations(array $params = []): array
  {
    $allowedParams = [
      'limit' => 'integer',
      'offset' => 'integer',
      'include_members_count' => 'boolean',
      'query' => 'string',
      'order_by' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', '/organizations', $params);
  }

  /**
   * Create an organization
   *
   * @param array $params Organization creation parameters
   * @return array Created organization object
   * @throws InvalidArgumentException
   */
  public function createOrganization(array $params): array
  {
    $allowedParams = [
      'name' => 'string',
      'created_by' => 'string',
      'private_metadata' => 'array',
      'public_metadata' => 'array',
      'slug' => 'string',
      'max_allowed_memberships' => 'integer',
      'created_at' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['name']) || !isset($params['created_by'])) {
      throw new InvalidArgumentException("Both 'name' and 'created_by' parameters are required");
    }

    return $this->client->request('POST', '/organizations', $params);
  }

  /**
   * Retrieve an organization
   *
   * @param string $organizationId The ID of the organization to retrieve
   * @return array Organization object
   * @throws InvalidArgumentException
   */
  public function getOrganization(string $organizationId): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    return $this->client->request('GET', "/organizations/{$organizationId}");
  }

  /**
   * Update an organization
   *
   * @param string $organizationId The ID of the organization to update
   * @param array $params Update parameters
   * @return array Updated organization object
   * @throws InvalidArgumentException
   */
  public function updateOrganization(string $organizationId, array $params): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    $allowedParams = [
      'public_metadata' => 'array',
      'private_metadata' => 'array',
      'name' => 'string',
      'slug' => 'string',
      'max_allowed_memberships' => 'integer',
      'admin_delete_enabled' => 'boolean',
      'created_at' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/organizations/{$organizationId}", $params);
  }

  /**
   * Delete an organization
   *
   * @param string $organizationId The ID of the organization to delete
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteOrganization(string $organizationId): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    return $this->client->request('DELETE', "/organizations/{$organizationId}");
  }

  /**
   * Update organization metadata
   *
   * @param string $organizationId The ID of the organization
   * @param array $params Metadata update parameters
   * @return array Updated organization object
   * @throws InvalidArgumentException
   */
  public function updateOrganizationMetadata(string $organizationId, array $params): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    $allowedParams = [
      'public_metadata' => 'array',
      'private_metadata' => 'array'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/organizations/{$organizationId}/metadata", $params);
  }

  /**
   * Upload organization logo
   *
   * @param string $organizationId The ID of the organization
   * @param string $imagePath Path to the image file
   * @param string $uploaderUserId ID of the user uploading the image
   * @return array Updated organization object
   * @throws InvalidArgumentException
   */
  public function uploadOrganizationLogo(string $organizationId, string $imagePath, string $uploaderUserId): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    Validator::validateId($uploaderUserId, 'Uploader User ID');
    if (!file_exists($imagePath)) {
      throw new InvalidArgumentException("Image file does not exist: {$imagePath}");
    }
    $data = [
      'file' => new \CURLFile($imagePath),
      'uploader_user_id' => $uploaderUserId
    ];
    return $this->client->request('PUT', "/organizations/{$organizationId}/logo", $data, true);
  }

  /**
   * Delete organization logo
   *
   * @param string $organizationId The ID of the organization
   * @return array Updated organization object
   * @throws InvalidArgumentException
   */
  public function deleteOrganizationLogo(string $organizationId): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    return $this->client->request('DELETE', "/organizations/{$organizationId}/logo");
  }
}

?>