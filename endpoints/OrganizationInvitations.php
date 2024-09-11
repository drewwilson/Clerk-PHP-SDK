<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class OrganizationInvitations
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Create and send an organization invitation
   *
   * @param string $organizationId The ID of the organization
   * @param array $params Invitation creation parameters
   * @return array Created organization invitation object
   * @throws InvalidArgumentException
   */
  public function createOrganizationInvitation(string $organizationId, array $params): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    $allowedParams = [
      'email_address' => 'string',
      'inviter_user_id' => 'string',
      'role' => 'string',
      'public_metadata' => 'array',
      'private_metadata' => 'array',
      'redirect_url' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['email_address']) || !isset($params['inviter_user_id']) || !isset($params['role'])) {
      throw new InvalidArgumentException("'email_address', 'inviter_user_id', and 'role' parameters are required");
    }

    return $this->client->request('POST', "/organizations/{$organizationId}/invitations", $params);
  }

  /**
   * Bulk create and send organization invitations
   *
   * @param string $organizationId The ID of the organization
   * @param array $invitations Array of invitation creation parameters
   * @return array Array of created organization invitation objects
   * @throws InvalidArgumentException
   */
  public function createOrganizationInvitationBulk(string $organizationId, array $invitations): array
  {
    Validator::validateId($organizationId, 'Organization ID');

    foreach ($invitations as $invitation) {
      $allowedParams = [
        'email_address' => 'string',
        'inviter_user_id' => 'string',
        'role' => 'string',
        'public_metadata' => 'array',
        'private_metadata' => 'array',
        'redirect_url' => 'string'
      ];
      Validator::validateParams($invitation, $allowedParams);

      if (!isset($invitation['email_address']) || !isset($invitation['inviter_user_id']) || !isset($invitation['role'])) {
        throw new InvalidArgumentException("'email_address', 'inviter_user_id', and 'role' parameters are required for each invitation");
      }
    }

    return $this->client->request('POST', "/organizations/{$organizationId}/invitations/bulk", $invitations);
  }

  /**
   * List organization invitations
   *
   * @param string $organizationId The ID of the organization
   * @param array $params Query parameters
   * @return array List of organization invitations
   * @throws InvalidArgumentException
   */
  public function listOrganizationInvitations(string $organizationId, array $params = []): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    $allowedParams = [
      'limit' => 'integer',
      'offset' => 'integer',
      'status' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', "/organizations/{$organizationId}/invitations", $params);
  }

  /**
   * Retrieve an organization invitation
   *
   * @param string $organizationId The ID of the organization
   * @param string $invitationId The ID of the invitation to retrieve
   * @return array Organization invitation object
   * @throws InvalidArgumentException
   */
  public function getOrganizationInvitation(string $organizationId, string $invitationId): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    Validator::validateId($invitationId, 'Invitation ID');
    return $this->client->request('GET', "/organizations/{$organizationId}/invitations/{$invitationId}");
  }

  /**
   * Revoke a pending organization invitation
   *
   * @param string $organizationId The ID of the organization
   * @param string $invitationId The ID of the invitation to revoke
   * @param array $params Revocation parameters
   * @return array Revoked organization invitation object
   * @throws InvalidArgumentException
   */
  public function revokeOrganizationInvitation(string $organizationId, string $invitationId, array $params): array
  {
    Validator::validateId($organizationId, 'Organization ID');
    Validator::validateId($invitationId, 'Invitation ID');
    $allowedParams = [
      'requesting_user_id' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['requesting_user_id'])) {
      throw new InvalidArgumentException("The 'requesting_user_id' parameter is required");
    }

    return $this->client->request('POST', "/organizations/{$organizationId}/invitations/{$invitationId}/revoke", $params);
  }
}

?>