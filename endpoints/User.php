<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class User
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all users
   *
   * @param array $params Query parameters
   * @return array List of users
   * @throws InvalidArgumentException
   */
  public function listUsers(array $params = []): array
  {
    $allowedParams = [
      'limit' => 'integer',
      'offset' => 'integer',
      'order_by' => 'string',
      'email_address' => 'array',
      'phone_number' => 'array',
      'external_id' => 'array',
      'username' => 'array',
      'web3_wallet' => 'array',
      'user_id' => 'array',
      'organization_id' => 'array',
      'query' => 'string',
      'last_active_at_since' => 'integer'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', '/users', $params);
  }

  /**
   * Create a new user
   *
   * @param array $params User creation parameters
   * @return array Created user object
   * @throws InvalidArgumentException
   */
  public function createUser(array $params): array
  {
    $allowedParams = [
      'external_id' => 'string',
      'first_name' => 'string',
      'last_name' => 'string',
      'email_address' => 'array',
      'phone_number' => 'array',
      'web3_wallet' => 'array',
      'username' => 'string',
      'password' => 'string',
      'password_digest' => 'string',
      'password_hasher' => 'string',
      'skip_password_checks' => 'boolean',
      'skip_password_requirement' => 'boolean',
      'totp_secret' => 'string',
      'backup_codes' => 'array',
      'public_metadata' => 'array',
      'private_metadata' => 'array',
      'unsafe_metadata' => 'array',
      'created_at' => 'string',
      'delete_self_enabled' => 'boolean',
      'create_organization_enabled' => 'boolean',
      'create_organizations_limit' => 'integer'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('POST', '/users', $params);
  }

  /**
   * Retrieve a user
   *
   * @param string $userId The ID of the user to retrieve
   * @return array User object
   * @throws InvalidArgumentException
   */
  public function getUser(string $userId): array
  {
    Validator::validateId($userId);
    return $this->client->request('GET', "/users/{$userId}");
  }

  /**
   * Update a user
   *
   * @param string $userId The ID of the user to update
   * @param array $params Update parameters
   * @return array Updated user object
   * @throws InvalidArgumentException
   */
  public function updateUser(string $userId, array $params): array
  {
    Validator::validateId($userId);
    $allowedParams = [
      'external_id' => 'string',
      'first_name' => 'string',
      'last_name' => 'string',
      'primary_email_address_id' => 'string',
      'primary_phone_number_id' => 'string',
      'primary_web3_wallet_id' => 'string',
      'username' => 'string',
      'profile_image_id' => 'string',
      'password' => 'string',
      'password_digest' => 'string',
      'password_hasher' => 'string',
      'totp_secret' => 'string',
      'backup_codes' => 'array',
      'public_metadata' => 'array',
      'private_metadata' => 'array',
      'unsafe_metadata' => 'array',
      'created_at' => 'string',
      'skip_password_checks' => 'boolean',
      'skip_password_requirement' => 'boolean',
      'sign_out_of_other_sessions' => 'boolean',
      'delete_self_enabled' => 'boolean',
      'create_organization_enabled' => 'boolean',
      'create_organizations_limit' => 'integer'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/users/{$userId}", $params);
  }

  /**
   * Delete a user
   *
   * @param string $userId The ID of the user to delete
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteUser(string $userId): array
  {
    Validator::validateId($userId);
    return $this->client->request('DELETE', "/users/{$userId}");
  }

  /**
   * Count users
   *
   * @param array $params Query parameters
   * @return array Total count object
   * @throws InvalidArgumentException
   */
  public function getUsersCount(array $params = []): array
  {
    $allowedParams = [
      'email_address' => 'array',
      'phone_number' => 'array',
      'external_id' => 'array',
      'username' => 'array',
      'web3_wallet' => 'array',
      'user_id' => 'array',
      'query' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', '/users/count', $params);
  }

  /**
   * Ban a user
   *
   * @param string $userId The ID of the user to ban
   * @return array Updated user object
   * @throws InvalidArgumentException
   */
  public function banUser(string $userId): array
  {
    Validator::validateId($userId);
    return $this->client->request('POST', "/users/{$userId}/ban");
  }

  /**
   * Unban a user
   *
   * @param string $userId The ID of the user to unban
   * @return array Updated user object
   * @throws InvalidArgumentException
   */
  public function unbanUser(string $userId): array
  {
    Validator::validateId($userId);
    return $this->client->request('POST', "/users/{$userId}/unban");
  }

  /**
   * Lock a user
   *
   * @param string $userId The ID of the user to lock
   * @return array Updated user object
   * @throws InvalidArgumentException
   */
  public function lockUser(string $userId): array
  {
    Validator::validateId($userId);
    return $this->client->request('POST', "/users/{$userId}/lock");
  }

  /**
   * Unlock a user
   *
   * @param string $userId The ID of the user to unlock
   * @return array Updated user object
   * @throws InvalidArgumentException
   */
  public function unlockUser(string $userId): array
  {
    Validator::validateId($userId);
    return $this->client->request('POST', "/users/{$userId}/unlock");
  }

  /**
   * Set user profile image
   *
   * @param string $userId The ID of the user
   * @param string $imagePath Path to the image file
   * @return array Updated user object
   * @throws InvalidArgumentException
   */
  public function setUserProfileImage(string $userId, string $imagePath): array
  {
    Validator::validateId($userId);
    if (!file_exists($imagePath)) {
      throw new InvalidArgumentException("Image file does not exist: {$imagePath}");
    }
    $data = [
      'file' => new \CURLFile($imagePath)
    ];
    return $this->client->request('POST', "/users/{$userId}/profile_image", $data, true);
  }

  /**
   * Delete user profile image
   *
   * @param string $userId The ID of the user
   * @return array Updated user object
   * @throws InvalidArgumentException
   */
  public function deleteUserProfileImage(string $userId): array
  {
    Validator::validateId($userId);
    return $this->client->request('DELETE', "/users/{$userId}/profile_image");
  }

  /**
   * Merge and update a user's metadata
   *
   * @param string $userId The ID of the user
   * @param array $metadata Metadata to merge and update
   * @return array Updated user object
   * @throws InvalidArgumentException
   */
  public function updateUserMetadata(string $userId, array $metadata): array
  {
    Validator::validateId($userId);
    $allowedMetadata = [
      'public_metadata' => 'array',
      'private_metadata' => 'array',
      'unsafe_metadata' => 'array'
    ];
    Validator::validateParams($metadata, $allowedMetadata);
    return $this->client->request('PATCH', "/users/{$userId}/metadata", $metadata);
  }

  /**
   * Retrieve the OAuth access token of a user
   *
   * @param string $userId The ID of the user
   * @param string $provider The ID of the OAuth provider
   * @return array OAuth access token information
   * @throws InvalidArgumentException
   */
  public function getOAuthAccessToken(string $userId, string $provider): array
  {
    Validator::validateId($userId);
    if (empty($provider)) {
      throw new InvalidArgumentException("Provider cannot be empty");
    }
    return $this->client->request('GET', "/users/{$userId}/oauth_access_tokens/{$provider}");
  }

  /**
   * Retrieve all memberships for a user
   *
   * @param string $userId The ID of the user
   * @param array $params Query parameters
   * @return array List of organization memberships
   * @throws InvalidArgumentException
   */
  public function getUserOrganizationMemberships(string $userId, array $params = []): array
  {
    Validator::validateId($userId);
    $allowedParams = [
      'limit' => 'integer',
      'offset' => 'integer'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('GET', "/users/{$userId}/organization_memberships", $params);
  }

  /**
   * Verify the password of a user
   *
   * @param string $userId The ID of the user
   * @param string $password The password to verify
   * @return array Verification result
   * @throws InvalidArgumentException
   */
  public function verifyPassword(string $userId, string $password): array
  {
    Validator::validateId($userId);
    if (empty($password)) {
      throw new InvalidArgumentException("Password cannot be empty");
    }
    return $this->client->request('POST', "/users/{$userId}/verify_password", ['password' => $password]);
  }

  /**
   * Verify a TOTP or backup code for a user
   *
   * @param string $userId The ID of the user
   * @param string $code The TOTP or backup code to verify
   * @return array Verification result
   * @throws InvalidArgumentException
   */
  public function verifyTOTP(string $userId, string $code): array
  {
    Validator::validateId($userId);
    if (empty($code)) {
      throw new InvalidArgumentException("Code cannot be empty");
    }
    return $this->client->request('POST', "/users/{$userId}/verify_totp", ['code' => $code]);
  }

  /**
   * Disable a user's MFA methods
   *
   * @param string $userId The ID of the user
   * @return array Operation result
   * @throws InvalidArgumentException
   */
  public function disableMFA(string $userId): array
  {
    Validator::validateId($userId);
    return $this->client->request('DELETE', "/users/{$userId}/mfa");
  }

  /**
   * Delete a user passkey
   *
   * @param string $userId The ID of the user
   * @param string $passkeyId The ID of the passkey to delete
   * @return array Operation result
   * @throws InvalidArgumentException
   */
  public function deleteUserPasskey(string $userId, string $passkeyId): array
  {
    Validator::validateId($userId);
    Validator::validateId($passkeyId, 'Passkey ID');
    return $this->client->request('DELETE', "/users/{$userId}/passkeys/{$passkeyId}");
  }

  /**
   * Delete a user web3 wallet
   *
   * @param string $userId The ID of the user
   * @param string $walletId The ID of the web3 wallet to delete
   * @return array Operation result
   * @throws InvalidArgumentException
   */
  public function deleteUserWeb3Wallet(string $userId, string $walletId): array
  {
    Validator::validateId($userId);
    Validator::validateId($walletId, 'Wallet ID');
    return $this->client->request('DELETE', "/users/{$userId}/web3_wallets/{$walletId}");
  }

  /**
   * Create a TOTP for a user
   *
   * @param string $userId The ID of the user
   * @return array TOTP creation result
   * @throws InvalidArgumentException
   */
  public function createUserTOTP(string $userId): array
  {
    Validator::validateId($userId);
    return $this->client->request('POST', "/users/{$userId}/totp");
  }

}