<?php

namespace Clerk;
use Exception;

use Clerk\Endpoints\ActorTokens;
use Clerk\Endpoints\AllowList;
use Clerk\Endpoints\BetaFeatures;
use Clerk\Endpoints\BlockList;
use Clerk\Endpoints\Clients;
use Clerk\Endpoints\Domains;
use Clerk\Endpoints\EmailAddresses;
use Clerk\Endpoints\InstanceSettings;
use Clerk\Endpoints\Invitations;
use Clerk\Endpoints\JWKS;
use Clerk\Endpoints\JWTTemplates;
use Clerk\Endpoints\Miscellaneous;
use Clerk\Endpoints\OAuthApplications;
use Clerk\Endpoints\OrganizationInvitations;
use Clerk\Endpoints\OrganizationMemberships;
use Clerk\Endpoints\Organizations;
use Clerk\Endpoints\PhoneNumbers;
use Clerk\Endpoints\ProxyChecks;
use Clerk\Endpoints\RedirectURLs;
use Clerk\Endpoints\SAMLConnections;
use Clerk\Endpoints\Sessions;
use Clerk\Endpoints\SignInTokens;
use Clerk\Endpoints\SignUps;
use Clerk\Endpoints\TestingTokens;
use Clerk\Endpoints\User;
use Clerk\Endpoints\Webhooks;

class ClerkClient
{
  private static $instance = null;
  private $secretKey;
  private $baseUrl;
  private $apiVersion;
  private $jwtKey;
  private $proxyUrl;
  private $domain;
  private $isSatellite;
  private $telemetryDisabled;
  private $telemetryDebug;
  private $rateLimitRemaining;
  private $rateLimitReset;

  private $actorTokens;
  private $allowList;
  private $betaFeatures;
  private $blockList;
  private $clients;
  private $domains;
  private $emailAddresses;
  private $instanceSettings;
  private $invitations;
  private $jwks;
  private $jwtTemplates;
  private $miscellaneous;
  private $oauthApplications;
  private $organizationInvitations;
  private $organizationMemberships;
  private $organizations;
  private $phoneNumbers;
  private $proxyChecks;
  private $redirectURLs;
  private $samlConnections;
  private $sessions;
  private $signInTokens;
  private $signUps;
  private $testingTokens;
  private $user;
  private $webhooks;

  private function __construct() {
    $this->secretKey = $this->getEnvVar('CLERK_SECRET_KEY', true);
    $this->validateSecretKey();

    $this->baseUrl = $this->getEnvVar('CLERK_API_URL', false, 'https://api.clerk.com');
    $this->apiVersion = $this->getEnvVar('CLERK_API_VERSION', false, 'v1');
    $this->jwtKey = $this->getEnvVar('CLERK_JWT_KEY');
    $this->proxyUrl = $this->getEnvVar('CLERK_PROXY_URL');
    $this->domain = $this->getEnvVar('CLERK_DOMAIN');
    $this->isSatellite = $this->getEnvVar('CLERK_IS_SATELLITE') === '1';
    $this->telemetryDisabled = $this->getEnvVar('CLERK_TELEMETRY_DISABLED') === '1';
    $this->telemetryDebug = $this->getEnvVar('CLERK_TELEMETRY_DEBUG') === '1';

    $this->rateLimitRemaining = null;
    $this->rateLimitReset = null;

    $this->actorTokens = new ActorTokens($this);
    $this->allowList = new AllowList($this);
    $this->betaFeatures = new BetaFeatures($this);
    $this->blockList = new BlockList($this);
    $this->clients = new Clients($this);
    $this->domains = new Domains($this);
    $this->emailAddresses = new EmailAddresses($this);
    $this->instanceSettings = new InstanceSettings($this);
    $this->invitations = new Invitations($this);
    $this->jwks = new JWKS($this);
    $this->jwtTemplates = new JWTTemplates($this);
    $this->miscellaneous = new Miscellaneous($this);
    $this->oauthApplications = new OAuthApplications($this);
    $this->organizationInvitations = new OrganizationInvitations($this);
    $this->organizationMemberships = new OrganizationMemberships($this);
    $this->organizations = new Organizations($this);
    $this->phoneNumbers = new PhoneNumbers($this);
    $this->proxyChecks = new ProxyChecks($this);
    $this->redirectURLs = new RedirectURLs($this);
    $this->samlConnections = new SAMLConnections($this);
    $this->sessions = new Sessions($this);
    $this->signInTokens = new SignInTokens($this);
    $this->signUps = new SignUps($this);
    $this->testingTokens = new TestingTokens($this);
    $this->user = new User($this);
    $this->webhooks = new Webhooks($this);
  }

  public static function createInstance(): ClerkClient {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function getEnvVar(string $name, bool $required = false, $default = null) {
    $value = getenv($name);
    if ($required && $value === false) {
      throw new Exception("Required environment variable $name is not set");
    }
    return $value !== false ? $value : $default;
  }

  private function validateSecretKey(): void {
    if (!preg_match('/^sk_[a-zA-Z0-9]+_[a-zA-Z0-9]+$/', $this->secretKey)) {
      throw new Exception('Invalid secret key format. It should be in the format: sk_<environment>_<secret value>');
    }
  }

  public function request(string $method, string $endpoint, array $data = []) {
    $this->handleRateLimit();

    $url = $this->baseUrl . '/' . $this->apiVersion . $endpoint;
    if ($this->proxyUrl) {
      $url = $this->proxyUrl . $url;
    }

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . $this->secretKey,
      'Content-Type: application/json'
      ],
      CURLOPT_CUSTOMREQUEST => $method
    ];

    if (!empty($data)) {
      $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $headers = curl_getinfo($curl, CURLINFO_HEADER_OUT);
    curl_close($curl);

    $this->updateRateLimitInfo($headers);
    $this->sendTelemetry($method, $endpoint, $statusCode);

    if ($statusCode >= 400) {
      throw new Exception("API request failed with status code: $statusCode");
    }

    return json_decode($response, true);
  }

  private function handleRateLimit() {
    if ($this->rateLimitRemaining !== null && $this->rateLimitRemaining <= 0) {
      $sleepTime = max(0, $this->rateLimitReset - time());
      sleep($sleepTime);
    }
  }

  private function updateRateLimitInfo($headers) {
    $this->rateLimitRemaining = $headers['X-RateLimit-Remaining'] ?? null;
    $this->rateLimitReset = $headers['X-RateLimit-Reset'] ?? null;
  }

  private function sendTelemetry($method, $endpoint, $statusCode) {
    if ($this->telemetryDisabled) {
      return;
    }

    $telemetryData = [
      'method' => $method,
      'endpoint' => $endpoint,
      'statusCode' => $statusCode,
      'isSatellite' => $this->isSatellite,
      // other relevant telemetry data
    ];

    if ($this->telemetryDebug) {
      error_log('Clerk Telemetry Data: ' . json_encode($telemetryData));
    } else {
      // TODO
      // Ssend this data to Clerk's telemetry endpoint
      // $this->request('POST', '/telemetry', $telemetryData);
    }
  }

  public function verifyToken($token, $customJwtKey = null) {
    $jwtKey = $customJwtKey ?? $this->jwtKey;

    if (!$jwtKey) {
      throw new Exception('JWT key is not set. Unable to verify token.');
    }

    // TODO
    // Implement JWT verification logic here

    // Placeholder implementation
    try {
      // JWT verification logic goes here
      // For example, using firebase/php-jwt:
      // $decoded = \Firebase\JWT\JWT::decode($token, $jwtKey, array('HS256'));
      $decoded = "Placeholder for decoded token";
      return $decoded;
    } catch (\Exception $e) {
      throw new Exception("Token verification failed: " . $e->getMessage());
    }
  }

  public function actorTokens(): ActorTokens { return $this->actorTokens; }
  public function allowList(): AllowList { return $this->allowList; }
  public function betaFeatures(): BetaFeatures { return $this->betaFeatures; }
  public function blockList(): BlockList { return $this->blockList; }
  public function clients(): Clients { return $this->clients; }
  public function domains(): Domains { return $this->domains; }
  public function emailAddresses(): EmailAddresses { return $this->emailAddresses; }
  public function instanceSettings(): InstanceSettings { return $this->instanceSettings; }
  public function invitations(): Invitations { return $this->invitations; }
  public function jwks(): JWKS { return $this->jwks; }
  public function jwtTemplates(): JWTTemplates { return $this->jwtTemplates; }
  public function miscellaneous(): Miscellaneous { return $this->miscellaneous; }
  public function oauthApplications(): OAuthApplications { return $this->oauthApplications; }
  public function organizationInvitations(): OrganizationInvitations { return $this->organizationInvitations; }
  public function organizationMemberships(): OrganizationMemberships { return $this->organizationMemberships; }
  public function organizations(): Organizations { return $this->organizations; }
  public function phoneNumbers(): PhoneNumbers { return $this->phoneNumbers; }
  public function proxyChecks(): ProxyChecks { return $this->proxyChecks; }
  public function redirectURLs(): RedirectURLs { return $this->redirectURLs; }
  public function samlConnections(): SAMLConnections { return $this->samlConnections; }
  public function sessions(): Sessions { return $this->sessions; }
  public function signInTokens(): SignInTokens { return $this->signInTokens; }
  public function signUps(): SignUps { return $this->signUps; }
  public function testingTokens(): TestingTokens { return $this->testingTokens; }
  public function users(): User { return $this->user; }
  public function webhooks(): Webhooks { return $this->webhooks; }

}

?>