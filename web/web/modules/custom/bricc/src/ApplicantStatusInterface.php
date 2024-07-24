<?php

declare(strict_types=1);

namespace Drupal\bricc;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an applicant status entity type.
 */
interface ApplicantStatusInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
