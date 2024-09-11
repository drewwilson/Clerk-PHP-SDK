<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;

class Webhooks
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Create a Svix app
   *
   * @return array Svix URL object
   * @throws \Exception
   */
  public function createSvixApp(): array
  {
    return $this->client->request('POST', '/webhooks/svix');
  }

  /**
   * Delete a Svix app
   *
   * @return void
   * @throws \Exception
   */
  public function deleteSvixApp(): void
  {
    $this->client->request('DELETE', '/webhooks/svix');
  }

  /**
   * Create a Svix Dashboard URL
   *
   * @return array Svix URL object
   * @throws \Exception
   */
  public function generateSvixAuthURL(): array
  {
    return $this->client->request('POST', '/webhooks/svix_url');
  }
}

?>