<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class OrganizationMemberships
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Create a new organization membership
   *
   * @param string $organizationId The ID of the organization
   * @param array $params Membership creation parameters
   * @return array Created organization membership object
   * @throws InvalidArgumentException
   */
  public function createOrganizationMembership(string $organizationId, array $params): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    $allowedParams = [
      'user_id' => 'string',
      'role' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['user_id']) || !isset($params['role'])) {
      throw new InvalidArgumentException("Both 'user_id' and 'role' parameters are required");
    }

    return $this->client->request('POST', "/organizations/{$organizationId}/memberships", $params);
  }

  /**
   * List all members of an organization
   *
   * @param string $organizationId The ID of the organization
   * @param array $params Query parameters
   * @return array List of organization memberships
   * @throws InvalidArgumentException
   */
  public function listOrganizationMemberships(string $organizationId, array $params = []): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    $allowedParams = [
      'limit' => 'integer',
      'offset' => 'integer',
      'order_by' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', "/organizations/{$organizationId}/memberships", $params);
  }

  /**
   * Update an organization membership
   *
   * @param string $organizationId The ID of the organization
   * @param string $userId The ID of the user
   * @param array $params Update parameters
   * @return array Updated organization membership object
   * @throws InvalidArgumentException
   */
  public function updateOrganizationMembership(string $organizationId, string $userId, array $params): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    Validator::validateId($userId, 'User ID');
    $allowedParams = [
      'role' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['role'])) {
      throw new InvalidArgumentException("The 'role' parameter is required");
    }

    return $this->client->request('PATCH', "/organizations/{$organizationId}/memberships/{$userId}", $params);
  }

  /**
   * Remove a member from an organization
   *
   * @param string $organizationId The ID of the organization
   * @param string $userId The ID of the user to remove
   * @return array Deleted organization membership object
   * @throws InvalidArgumentException
   */
  public function deleteOrganizationMembership(string $organizationId, string $userId): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    Validator::validateId($userId, 'User ID');
    return $this->client->request('DELETE', "/organizations/{$organizationId}/memberships/{$userId}");
  }

  /**
   * Update organization membership metadata
   *
   * @param string $organizationId The ID of the organization
   * @param string $userId The ID of the user
   * @param array $params Metadata update parameters
   * @return array Updated organization membership object
   * @throws InvalidArgumentException
   */
  public function updateOrganizationMembershipMetadata(string $organizationId, string $userId, array $params): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    Validator::validateId($userId, 'User ID');
    $allowedParams = [
      'public_metadata' => 'array',
      'private_metadata' => 'array'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/organizations/{$organizationId}/memberships/{$userId}/metadata", $params);
  }
}

?>