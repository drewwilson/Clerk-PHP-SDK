# Clerk PHP SDK

This PHP SDK provides a convenient way to interact with the Clerk API in your PHP applications. It covers the full range of [Clerk's Backend API](https://clerk.com/docs/reference/backend-api) (BAPI) capabilities, including user management, organization management, authentication, and more.

## Installation

### Option 1: Using Composer 

You can install the Clerk PHP SDK via Composer:

```bash
composer require clerk/clerk-sdk-php
```

Then, in your PHP script, you can include the autoloader:

```php
require 'vendor/autoload.php';
```

### Option 2: Manual Installation

If you prefer not to use Composer, you can manually include the SDK in your project:

1. Download the SDK files from the [GitHub repository](https://github.com/drewwilson/clerk-php-sdk).

2. Extract the files into your project directory. For example, you might put them in a `clerk-sdk-php` folder.

3. In your PHP script, include the `ClerkSDKLoader.php` file:

```php
require_once 'path/to/clerk-sdk-php/ClerkSDKLoader.php';
```

This loader file will handle including all necessary SDK files for you, making manual installation much simpler.

## Usage

Here's a basic example of how to use the SDK:

```php
<?php

// If using Composer:
require 'vendor/autoload.php';

// If using manual installation:
require_once 'path/to/clerk-sdk-php/ClerkSDKLoader.php';

////////

use Clerk\ClerkClient;

// Create the Clerk client instance
$clerk = ClerkClient::createInstance();

// Now you can use various methods provided by the SDK
```

## Configuration

Before using the SDK, you need to set up your Clerk Secret Key. You can do this by setting an environment variable:

```bash
export CLERK_SECRET_KEY=your_secret_key_here
```

Alternatively, you can set it in your PHP code (though this is less secure and not recommended for production):

```php
putenv("CLERK_SECRET_KEY=your_secret_key_here");
```

### Examples

#### 1. List Users

```php
try {
  $users = $clerk->users()->listUsers(['limit' => 10]);
  foreach ($users as $user) {
    echo "User ID: " . $user['id'] . ", Email: " . $user['email_address'] . "\n";
  }
} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}
```

#### 2. Create an Organization

```php
try {
  $organizationData = [
    'name' => 'My New Organization',
    'created_by' => 'user_1234567890'
  ];
  $newOrg = $clerk->organizations()->createOrganization($organizationData);
  echo "Created organization with ID: " . $newOrg['id'];
} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}
```

#### 3. Send an Invitation

```php
try {
  $invitationData = [
    'email_address' => 'newuser@example.com',
    'inviter_user_id' => 'user_1234567890',
    'role' => 'basic_member'
  ];
  $invitation = $clerk->organizationInvitations()->createOrganizationInvitation('org_1234567890', $invitationData);
  echo "Sent invitation to: " . $invitation['email_address'];
} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}
```

## Available Endpoints

The SDK provides access to various Clerk API endpoints. Here's a comprehensive list of available endpoints:

- `actorTokens()`
- `allowList()`
- `betaFeatures()`
- `blockList()`
- `clients()`
- `domains()`
- `emailAddresses()`
- `instanceSettings()`
- `invitations()`
- `jwks()`
- `jwtTemplates()`
- `miscellaneous()`
- `oauthApplications()`
- `organizationInvitations()`
- `organizationMemberships()`
- `organizations()`
- `phoneNumbers()`
- `proxyChecks()`
- `redirectURLs()`
- `samlConnections()`
- `sessions()`
- `signInTokens()`
- `signUps()`
- `testingTokens()`
- `users()`
- `webhooks()`

Each of these methods returns an object that provides further methods to interact with that specific endpoint.

For more detailed information about each endpoint and its available methods, please refer to the [Clerk API Documentation](https://clerk.com/docs/reference/backend-api).

## Environment Variables

The Clerk PHP SDK supports the following environment variables for configuration:

- `CLERK_SECRET_KEY`: Your Clerk Secret Key. This is required for authenticating requests to the Clerk API.

- `CLERK_API_URL`: The base URL for the Clerk API. Defaults to `https://api.clerk.com` if not set.

- `CLERK_API_VERSION`: The version of the Clerk API to use. Defaults to `v1` if not set.

- `CLERK_JWT_KEY`: The key used for JWT verification. This is optional and only needed if you're using custom JWT verification.

- `CLERK_PROXY_URL`: If you're using a proxy for Clerk API requests, set this to the proxy URL.

- `CLERK_DOMAIN`: Your Clerk domain. This is optional and used for certain operations.

- `CLERK_IS_SATELLITE`: Set to `1` if this is a satellite instance of Clerk. Defaults to `0`.

- `CLERK_TELEMETRY_DISABLED`: Set to `1` to disable telemetry. Defaults to `0`.

- `CLERK_TELEMETRY_DEBUG`: Set to `1` to enable debug mode for telemetry. Defaults to `0`.

You can set these environment variables in your system or include them in a `.env` file if you're using a package like `phpdotenv`.

Example of setting an environment variable:

```bash
export CLERK_SECRET_KEY=your_secret_key_here
```

Or in PHP (not recommended for production):

```php
putenv("CLERK_SECRET_KEY=your_secret_key_here");
```

Remember to never commit sensitive information like your Secret Key to version control. Always use environment variables or a secure secrets management system in production environments.

## Error Handling

The SDK uses exceptions to handle errors. Always wrap your API calls in try-catch blocks to handle potential errors gracefully.

## Contributing

Please do.

## License

This SDK is distributed under the MIT License. See the LICENSE file for more information.