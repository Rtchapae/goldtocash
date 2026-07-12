<?php

namespace App\Domain\LandingPages\Support;

class LandingPagePathNormalizer
{
  /** @var array<int, string> */
  private const RESERVED_PREFIXES = [
    '/api',
    '/admin',
    '/storage',
    '/user',
    '/gold-info',
    '/sell-gold',
    '/sell',
    '/posts',
    '/login',
    '/sign-in',
  ];

  public static function normalize(?string $path): string
  {
    $path = trim((string) $path);
    $path = preg_replace('#/+#', '/', $path) ?? '';
    if ($path === '' || $path === '/') {
      return '/';
    }
    if (! str_starts_with($path, '/')) {
      $path = '/'.$path;
    }

    return rtrim($path, '/') ?: '/';
  }

  public static function isReserved(string $path): bool
  {
    $normalized = self::normalize($path);
    if ($normalized === '/') {
      return true;
    }

    foreach (self::RESERVED_PREFIXES as $prefix) {
      if ($normalized === $prefix || str_starts_with($normalized, $prefix.'/')) {
        return true;
      }
    }

    return false;
  }
}
