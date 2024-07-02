<?php

declare(strict_types=1);

namespace Drupal\bricc\Entity;

use Drupal\bricc\BriccCategoryInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the category entity class.
 *
 * @ContentEntityType(
 *   id = "bricc_category",
 *   label = @Translation("Category"),
 *   label_collection = @Translation("Categories"),
 *   label_singular = @Translation("category"),
 *   label_plural = @Translation("categories"),
 *   label_count = @PluralTranslation(
 *     singular = "@count categories",
 *     plural = "@count categories",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\bricc\BriccCategoryListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\bricc\Form\BriccCategoryForm",
 *       "edit" = "Drupal\bricc\Form\BriccCategoryForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "bricc_category",
 *   admin_permission = "administer bricc_category",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/bricc/credit-card/category",
 *     "add-form" = "/admin/bricc/category/add",
 *     "canonical" = "/admin/bricc/category/{bricc_category}",
 *     "edit-form" = "/admin/bricc/category/{bricc_category}/edit",
 *     "delete-form" = "/admin/bricc/category/{bricc_category}/delete",
 *     "delete-multiple-form" = "/admin/content/category/delete-multiple",
 *   },
 *   field_ui_base_route = "entity.bricc_category.settings",
 * )
 */
final class BriccCategory extends ContentEntityBase implements BriccCategoryInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(self::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the category was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    // Card count
    $fields['card_count'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Card Count'))
      ->setDescription(t('How many card items in this category'))
      ->setDefaultValue(0)  // Set a default value if needed
      ->setSettings([
        'min' => 0,  // Minimum value constraint
        'max' => 2147483647,  // Maximum value constraint
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',  // Position of the label
        'type' => 'number_integer',  // Field type
        'weight' => 0,  // Position in the display
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',  // Form element type
        'weight' => 0,  // Position in the form
      ])
      ->setRequired(TRUE);  // Set to TRUE if the field is mandatory


    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the category was last edited.'));

    return $fields;
  }

}
