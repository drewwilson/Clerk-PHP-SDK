<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;
use Clerk\Util\Validator;
use InvalidArgumentException;

class JWTTemplates
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * List all JWT templates
   *
   * @return array List of JWT templates
   * @throws \Exception
   */
  public function listJWTTemplates(): array
  {
    return $this->client->request('GET', '/jwt_templates');
  }

  /**
   * Create a JWT template
   *
   * @param array $params Template creation parameters
   * @return array Created JWT template object
   * @throws InvalidArgumentException
   */
  public function createJWTTemplate(array $params): array
  {
    $allowedParams = [
      'name' => 'string',
      'claims' => 'array',
      'lifetime' => 'integer',
      'allowed_clock_skew' => 'integer',
      'custom_signing_key' => 'boolean',
      'signing_algorithm' => 'string',
      'signing_key' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);

    if (!isset($params['name']) || !isset($params['claims'])) {
      throw new InvalidArgumentException("Both 'name' and 'claims' parameters are required");
    }

    return $this->client->request('POST', '/jwt_templates', $params);
  }

  /**
   * Retrieve a JWT template
   *
   * @param string $templateId The ID of the JWT template to retrieve
   * @return array JWT template object
   * @throws InvalidArgumentException
   */
  public function getJWTTemplate(string $templateId): array
  {
    Validator::validateId($templateId, 'Template ID');
    return $this->client->request('GET', "/jwt_templates/{$templateId}");
  }

  /**
   * Update a JWT template
   *
   * @param string $templateId The ID of the JWT template to update
   * @param array $params Update parameters
   * @return array Updated JWT template object
   * @throws InvalidArgumentException
   */
  public function updateJWTTemplate(string $templateId, array $params): array
  {
    Validator::validateId($templateId, 'Template ID');
    $allowedParams = [
      'name' => 'string',
      'claims' => 'array',
      'lifetime' => 'integer',
      'allowed_clock_skew' => 'integer',
      'custom_signing_key' => 'boolean',
      'signing_algorithm' => 'string',
      'signing_key' => 'string'
    ];
    Validator::validateParams($params, $allowedParams);
    return $this->client->request('PATCH', "/jwt_templates/{$templateId}", $params);
  }

  /**
   * Delete a JWT template
   *
   * @param string $templateId The ID of the JWT template to delete
   * @return array Deleted object response
   * @throws InvalidArgumentException
   */
  public function deleteJWTTemplate(string $templateId): array
  {
    Validator::validateId($templateId, 'Template ID');
    return $this->client->request('DELETE', "/jwt_templates/{$templateId}");
  }
}

?>