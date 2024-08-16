<?php

namespace Drupal\brimw\Normalizer;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\paragraphs\ParagraphInterface;

class SubscriptionNormalizer extends BaseParagraphNormalizer 
{
  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'subscription';

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private EntityTypeManagerInterface $em;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->em = $entityTypeManager;
  }

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

    $subscription = $this->em->getStorage('webform')
                                  ->load('subscription');

    if ($subscription) {
      $normalized['subscription_form'] = $this->serializer->normalize($subscription, 'json_recursive');
    }

    return $normalized;
  }

}
