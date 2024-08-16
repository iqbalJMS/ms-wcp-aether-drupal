<?php

namespace Drupal\brimw\Normalizer;

use Drupal\paragraphs\ParagraphInterface;
use Drupal\rest_entity_recursive\Normalizer\ContentEntityNormalizer;

class BaseParagraphNormalizer extends ContentEntityNormalizer 
{

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = ParagraphInterface::class;

  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'paragraph';

  /**
   * @inheritDoc
   */
  public function supportsNormalization(
    $data,
    ?string $format = NULL,
    array $context = []
  ): bool {
    return parent::supportsNormalization($data, $format) 
            && $data->bundle() === $this->supportedParagraphType;
  }
}