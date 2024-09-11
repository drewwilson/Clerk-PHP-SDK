<?php

namespace Clerk\Endpoints;

use Clerk\ClerkClient;

class TestingTokens
{
  private $client;

  public function __construct(ClerkClient $client)
  {
    $this->client = $client;
  }

  /**
   * Retrieve a new testing token
   *
   * @return array Testing token object
   * @throws \Exception
   */
  public function createTestingToken(): array
  {
    return $this->client->request('POST', '/testing_tokens');
  }
}

?>