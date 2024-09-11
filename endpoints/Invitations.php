<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class Invitations
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Create an invitation
   *
   * @param array $params Invitation creation parameters
   * @return array Created invitation object
   * @throws InvalidArgumentException
   */
  public function createInvitation(array $params): array
  {
    $allowedParams = [
      'email_address' => 'string',
      'public_metadata' => 'array',
      'redirect_url' => 'string',
      'notify' => 'boolean',
      'ignore_existing' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['email_address'])) {
      throw new InvalidArgumentException("The 'email_address' parameter is required");
    }

    return $this->client->request('POST', '/invitations', $params);
  }

  /**
   * List all invitations
   *
   * @param array $params Query parameters
   * @return array List of invitations
   * @throws InvalidArgumentException
   */
  public function listInvitations(array $params = []): array
  {
    $allowedParams = [
      'limit' => 'integer',
      'offset' => 'integer',
      'status' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', '/invitations', $params);
  }

  /**
   * Revoke an invitation
   *
   * @param string $invitationId The ID of the invitation to revoke
   * @return array Revoked invitation object
   * @throws InvalidArgumentException
   */
  public function revokeInvitation(string $invitationId): array
  {
    Validator::validateId($invitationId, 'Invitation ID');
    return $this->client->request('POST', "/invitations/{$invitationId}/revoke");
  }
}

?>