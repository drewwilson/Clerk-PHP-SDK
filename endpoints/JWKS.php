<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;

class JWKS
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Retrieve the JSON Web Key Set of the instance
   *
   * @return array The JSON Web Key Set
   * @throws \Exception
   */
  public function getJWKS(): array
  {
    return $this->client->request('GET', '/jwks');
  }
}

?>