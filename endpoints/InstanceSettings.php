<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class InstanceSettings
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Update instance settings
   *
   * @param array $params Update parameters
   * @return array Updated instance settings
   * @throws InvalidArgumentException
   */
  public function updateInstanceSettings(array $params): array
  {
    $allowedParams = [
      'test_mode' => 'boolean',
      'hibp' => 'boolean',
      'enhanced_email_deliverability' => 'boolean',
      'support_email' => 'string',
      'clerk_js_version' => 'string',
      'development_origin' => 'string',
      'allowed_origins' => 'array',
      'url_based_session_syncing' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', '/instance', $params);
  }

  /**
   * Update instance restrictions
   *
   * @param array $params Update parameters
   * @return array Updated instance restrictions
   * @throws InvalidArgumentException
   */
  public function updateInstanceRestrictions(array $params): array
  {
    $allowedParams = [
      'allowlist' => 'boolean',
      'blocklist' => 'boolean',
      'block_email_subaddresses' => 'boolean',
      'block_disposable_email_domains' => 'boolean',
      'ignore_dots_for_gmail_addresses' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', '/instance/restrictions', $params);
  }

  /**
   * Update instance organization settings
   *
   * @param array $params Update parameters
   * @return array Updated organization settings
   * @throws InvalidArgumentException
   */
  public function updateInstanceOrganizationSettings(array $params): array
  {
    $allowedParams = [
      'enabled' => 'boolean',
      'max_allowed_memberships' => 'integer',
      'admin_delete_enabled' => 'boolean',
      'domains_enabled' => 'boolean',
      'domains_enrollment_modes' => 'array',
      'creator_role_id' => 'string',
      'domains_default_role_id' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', '/instance/organization_settings', $params);
  }
}

?>