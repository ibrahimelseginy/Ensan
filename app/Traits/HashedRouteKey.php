<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Uses a stable, signed and URL-safe representation of the primary key in routes.
 *
 * Numeric route keys remain resolvable so existing bookmarks keep working, while
 * every newly generated URL uses the opaque hash.
 */
trait HashedRouteKey
{
    public function getRouteKey(): mixed
    {
        $key = $this->getKey();

        if ($key === null) {
            return null;
        }

        return $this->encodeRouteKey((string) $key);
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        if ($field !== null && $field !== $this->getRouteKeyName()) {
            return $query->where($field, $value);
        }

        $routeKey = $this->decodeRouteKey((string) $value);

        // Backwards compatibility for links generated before hashed URLs.
        if ($routeKey === null && ctype_digit((string) $value)) {
            $routeKey = (string) $value;
        }

        return $query->where($this->getRouteKeyName(), $routeKey ?? 0);
    }

    private function encodeRouteKey(string $key): string
    {
        $plaintext = str_repeat("\0", 8) . pack('J', (int) $key);
        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-ecb',
            $this->routeEncryptionKey(),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('Unable to encode the route key.');
        }

        $signature = substr(hash_hmac('sha256', $ciphertext, $this->routeHashSecret(), true), 0, 8);
        $payload = $ciphertext . $signature;

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    private function decodeRouteKey(string $hash): ?string
    {
        $encoded = strtr($hash, '-_', '+/');
        $padding = strlen($encoded) % 4;

        if ($padding !== 0) {
            $encoded .= str_repeat('=', 4 - $padding);
        }

        $payload = base64_decode($encoded, true);

        if ($payload === false || strlen($payload) !== 24) {
            return null;
        }

        $ciphertext = substr($payload, 0, 16);
        $signature = substr($payload, 16, 8);
        $expected = substr(hash_hmac('sha256', $ciphertext, $this->routeHashSecret(), true), 0, 8);

        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-ecb',
            $this->routeEncryptionKey(),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
        );

        if ($plaintext === false || strlen($plaintext) !== 16 || substr($plaintext, 0, 8) !== str_repeat("\0", 8)) {
            return null;
        }

        $decoded = unpack('Jkey', substr($plaintext, 8));
        $key = $decoded['key'] ?? 0;

        return $key > 0 ? (string) $key : null;
    }

    private function routeEncryptionKey(): string
    {
        return hash('sha256', $this->routeHashSecret() . '|' . static::class, true);
    }

    private function routeHashSecret(): string
    {
        return (string) config('app.key', 'ensan-route-key');
    }
}
