<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Twig extension.
 */
final class BriccTwigExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFilters(): array {
    $filters[] = new TwigFilter(
      'maskdata',
      [$this, 'maskdata'],
    );
    return $filters;
  }

  /**
   * Find & Replace characters in a string for masking & hiding.
   */
  public function maskdata(string $string, int $charactersToUnmask = 4, $replacement = '*') {
    // Check if the string length is greater than 4
    if (strlen($string) > $charactersToUnmask) {
      // Get the last 4 characters
      $offset = $charactersToUnmask * -1;
      $lastChars = substr($string, $offset);
      // Mask the rest of the string with asterisks
      $masked = str_repeat('*', strlen($string) - $charactersToUnmask) . $lastChars;
    } else {
      // If the string length is 4 or less, return the string as it is
      $masked = $string;
    }

    return $masked;
  }

}
