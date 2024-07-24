<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a card item entity type.
 */
interface BriccCardItemInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
