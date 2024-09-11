<?php
//
// Use this file only when you want to manually install Clerk, without using Composer.
//

require_once __DIR__ . '/ClerkClient.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/SessionVerifier.php';

// Endpoint files
$endpointFiles = [
  '/Endpoints/ActorTokens.php',
  '/Endpoints/AllowList.php',
  '/Endpoints/BetaFeatures.php',
  '/Endpoints/BlockList.php',
  '/Endpoints/Clients.php',
  '/Endpoints/Domains.php',
  '/Endpoints/EmailAddresses.php',
  '/Endpoints/InstanceSettings.php',
  '/Endpoints/Invitations.php',
  '/Endpoints/JWKS.php',
  '/Endpoints/JWTTemplates.php',
  '/Endpoints/Miscellaneous.php',
  '/Endpoints/OAuthApplications.php',
  '/Endpoints/OrganizationInvitations.php',
  '/Endpoints/OrganizationMemberships.php',
  '/Endpoints/Organizations.php',
  '/Endpoints/PhoneNumbers.php',
  '/Endpoints/ProxyChecks.php',
  '/Endpoints/RedirectURLs.php',
  '/Endpoints/SAMLConnections.php',
  '/Endpoints/Sessions.php',
  '/Endpoints/SignInTokens.php',
  '/Endpoints/SignUps.php',
  '/Endpoints/TestingTokens.php',
  '/Endpoints/User.php',
  '/Endpoints/Webhooks.php',
];

foreach ($endpointFiles as $file) {
  require_once __DIR__ . $file;
}

?>