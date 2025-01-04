<?php

namespace Drupal\brimw\Normalizer;

use Drupal\config_pages\Entity\ConfigPages;

class DropdownSimulationNormalizer extends BaseParagraphNormalizer 
{
  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'dropdown_simulation';

  /**
   * @inheritDoc
   */
  public function normalize(
    $entity,
    $format = NULL,
    array $context = []
  ): array {
    $normalized = parent::normalize(
      $entity,
      $format,
      $context
    );

    $config = ConfigPages::config('bri_mw_simulation');
    if ($config instanceof ConfigPages) {
      if (!$config->field_simulation_url->isEmpty()) {
        $simulation_urls = [];
        foreach ($config->field_simulation_url as $url) {
          $simulation_urls[] = array_merge($url->getValue(), [
            'url' => $url->getUrl()->toString()
          ]);
        }
        $normalized['simulation_url'] = $simulation_urls;
      }
    }

    return $normalized;
  }
}
