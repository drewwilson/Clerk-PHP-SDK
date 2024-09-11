<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class BetaFeatures
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
  public function updateInstanceAuthConfig(array $params): array
  {
    $allowedParams = [
      'restricted_to_allowlist' => 'boolean',
      'from_email_address' => 'string',
      'progressive_sign_up' => 'boolean',
      'session_token_template' => 'string',
      'enhanced_email_deliverability' => 'boolean',
      'test_mode' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', '/beta_features/instance_settings', $params);
  }

  /**
   * Update production instance domain
   *
   * @param array $params Domain update parameters
   * @return array Response indicating acceptance of the request
   * @throws InvalidArgumentException
   */
  public function changeProductionInstanceDomain(array $params): array
  {
    $allowedParams = [
      'home_url' => 'string',
      'is_secondary' => 'boolean'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['home_url'])) {
      throw new InvalidArgumentException("The 'home_url' parameter is required");
    }

    return $this->client->request('POST', '/instance/change_domain', $params);
  }
}

?>