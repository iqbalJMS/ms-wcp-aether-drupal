<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a city entity type.
 */
interface BriccCityInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
