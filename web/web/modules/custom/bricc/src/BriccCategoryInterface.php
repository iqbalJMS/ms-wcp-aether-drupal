<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a category entity type.
 */
interface BriccCategoryInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
