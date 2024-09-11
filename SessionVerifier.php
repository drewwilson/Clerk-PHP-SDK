<?php

namespace Clerk\Util;

use Clerk\ClerkClient;
use Exception;

class SessionVerifier
{
  /**
   * Verify a session token
   *
   * @param string $sessionToken The session token to verify
   * @param ClerkClient $clerk The Clerk client instance
   * @return array The decoded claims if the token is valid
   * @throws Exception If the token is invalid or verification fails
   */
  public static function verifySession(string $sessionToken, ClerkClient $clerk): array {
    try {
      $jwtKey = $clerk->getJwtKey();
      if ($jwtKey) {
        $claims = self::verifyJWTWithKey($sessionToken, $jwtKey);
      } else {
        $claims = self::verifyJWTWithJWKS($sessionToken, $clerk);
      }

      // Get user details
      $user = $clerk->users()->getUser($claims['sub']);

      return [
        'user_id' => $user['id'],
        'user_banned' => $user['banned'] ?? false,
        'claims' => $claims,
      ];
    } catch (Exception $e) {
      throw new Exception("Session verification failed: " . $e->getMessage());
    }
  }

  /**
   * Verify a JWT token using a provided key
   *
   * @param string $token The JWT token
   * @param string $key The verification key
   * @return array The decoded claims
   * @throws Exception If verification fails
   */
  private static function verifyJWTWithKey(string $token, string $key): array {
    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
      throw new Exception('Invalid token format');
    }

    $header = json_decode(base64_decode($tokenParts[0]), true);
    $payload = json_decode(base64_decode($tokenParts[1]), true);
    $signature = $tokenParts[2];

    // Verify token hasn't expired
    if (isset($payload['exp']) && $payload['exp'] < time()) {
      throw new Exception('Token has expired');
    }

    // Verify signature
    $signatureValid = self::verifySignatureWithKey($token, $key);
    if (!$signatureValid) {
      throw new Exception('Invalid token signature');
    }

    return $payload;
  }

  /**
   * Verify a JWT token using JWKS
   *
   * @param string $token The JWT token
   * @param ClerkClient $clerk The Clerk client instance
   * @return array The decoded claims
   * @throws Exception If verification fails
   */
  private static function verifyJWTWithJWKS(string $token, ClerkClient $clerk): array {
    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
      throw new Exception('Invalid token format');
    }

    $header = json_decode(base64_decode($tokenParts[0]), true);
    $payload = json_decode(base64_decode($tokenParts[1]), true);
    $signature = $tokenParts[2];

    $keyId = $header['kid'] ?? null;
    if (!$keyId) {
      throw new Exception('Unable to find key ID in token header');
    }

    // Fetch the JSON Web Key Set
    $jwks = $clerk->jwks()->getJWKS();
    $jwk = self::findJWK($jwks, $keyId);

    if (!$jwk) {
      throw new Exception('Unable to find matching JWK');
    }

    // Verify token hasn't expired
    if (isset($payload['exp']) && $payload['exp'] < time()) {
      throw new Exception('Token has expired');
    }

    // Verify signature
    $signatureValid = self::verifySignatureWithJWK($token, $jwk);
    if (!$signatureValid) {
      throw new Exception('Invalid token signature');
    }

    return $payload;
  }

  /**
   * Find a specific JWK in the JWKS by key ID
   *
   * @param array $jwks The JSON Web Key Set
   * @param string $keyId The key ID to find
   * @return array|null The found JWK or null if not found
   */
  private static function findJWK(array $jwks, string $keyId): ?array {
    foreach ($jwks['keys'] as $key) {
      if ($key['kid'] === $keyId) {
        return $key;
      }
    }
    return null;
  }

  /**
   * Verify the signature of a JWT token using a provided key
   *
   * @param string $token The JWT token
   * @param string $key The verification key
   * @return bool True if the signature is valid, false otherwise
   */
  private static function verifySignatureWithKey(string $token, string $key): bool {
    $tokenParts = explode('.', $token);
    $signature = self::base64UrlDecode($tokenParts[2]);

    return openssl_verify(
      $tokenParts[0] . '.' . $tokenParts[1],
      $signature,
      $key,
      self::getOpenSSLAlgorithm()
    ) === 1;
  }

  /**
   * Verify the signature of a JWT token using a JWK
   *
   * @param string $token The JWT token
   * @param array $jwk The JSON Web Key
   * @return bool True if the signature is valid, false otherwise
   */
  private static function verifySignatureWithJWK(string $token, array $jwk): bool {
    $tokenParts = explode('.', $token);
    $signature = self::base64UrlDecode($tokenParts[2]);

    $publicKey = self::jwkToPublicKey($jwk);

    return openssl_verify(
      $tokenParts[0] . '.' . $tokenParts[1],
      $signature,
      $publicKey,
      self::getOpenSSLAlgorithm()
    ) === 1;
  }

  /**
   * Get the appropriate OpenSSL algorithm identifier
   *
   * @return int|string The OpenSSL algorithm identifier
   */
  private static function getOpenSSLAlgorithm() {
    return defined('OPENSSL_ALGO_SHA256') ? OPENSSL_ALGO_SHA256 : 'SHA256';
  }

  /**
   * Convert a JWK to a public key
   *
   * @param array $jwk The JSON Web Key
   * @return string The PEM formatted public key
   */
  private static function jwkToPublicKey(array $jwk): string {
    $modulus = self::base64UrlDecode($jwk['n']);
    $exponent = self::base64UrlDecode($jwk['e']);

    $modulus = self::addLeadingZero($modulus);
    $exponent = self::addLeadingZero($exponent);

    $der = self::createRSAPublicKeyDER($modulus, $exponent);
    $pem = '-----BEGIN PUBLIC KEY-----' . PHP_EOL;
    $pem .= chunk_split(base64_encode($der), 64, PHP_EOL);
    $pem .= '-----END PUBLIC KEY-----' . PHP_EOL;

    return $pem;
  }

  /**
   * Create a DER encoded RSA public key
   *
   * @param string $modulus The RSA modulus
   * @param string $exponent The RSA exponent
   * @return string The DER encoded RSA public key
   */
  private static function createRSAPublicKeyDER(string $modulus, string $exponent): string {
    $modulus = pack('Ca*a*', 2, self::encodeLength(strlen($modulus)), $modulus);
    $exponent = pack('Ca*a*', 2, self::encodeLength(strlen($exponent)), $exponent);

    $pubKey = pack(
      'Ca*a*a*',
      48,
      self::encodeLength(strlen($modulus) + strlen($exponent) + 2),
      $modulus,
      $exponent
    );

    $algorithmIdentifier = pack('H*', '300d06092a864886f70d0101010500');
    $der = pack(
      'Ca*a*',
      48,
      self::encodeLength(strlen($algorithmIdentifier) + strlen($pubKey) + 1),
      $algorithmIdentifier . chr(3) . self::encodeLength(strlen($pubKey)) . $pubKey
    );

    return $der;
  }

  /**
   * Encode the length for DER encoding
   *
   * @param int $length The length to encode
   * @return string The encoded length
   */
  private static function encodeLength(int $length): string {
    if ($length <= 127) {
      return chr($length);
    }

    $temp = ltrim(pack('N', $length), chr(0));
    return pack('Ca*', 0x80 | strlen($temp), $temp);
  }

  /**
   * Add a leading zero byte if the most significant bit is set
   *
   * @param string $data The input data
   * @return string The data with a leading zero if necessary
   */
  private static function addLeadingZero(string $data): string {
    if (ord($data[0]) > 127) {
      return "\0" . $data;
    }
    return $data;
  }

  /**
   * Decode a base64 URL-safe string
   *
   * @param string $input The input string
   * @return string The decoded string
   */
  private static function base64UrlDecode(string $input): string {
    $remainder = strlen($input) % 4;
    if ($remainder) {
      $input .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($input, '-_', '+/'));
  }
}

?>