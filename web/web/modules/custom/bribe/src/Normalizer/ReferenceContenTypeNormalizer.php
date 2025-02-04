<?php

namespace Drupal\bribe\Normalizer;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest_entity_recursive\Normalizer\ContentEntityNormalizer;

class ReferenceContenTypeNormalizer extends ContentEntityNormalizer
{
    /**
     * The interface or class that this Normalizer supports.
     *
     * @var string
     */
    protected $supportedInterfaceOrClass = 'Drupal\paragraphs\ParagraphInterface';

    /**
     * Array of supported paragraph types.
     *
     * @var array
     */
    protected $supportedParagraphTypes = ['content_type'];

    /**
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    private EntityTypeManagerInterface $em;

    public function __construct(EntityTypeManagerInterface $entityTypeManager)
    {
        $this->em = $entityTypeManager;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization(
        $data,
        ?string $format = NULL,
        array $context = []
    ): bool {
        if (parent::supportsNormalization($data, $format)) {
            if (empty($this->supportedParagraphTypes)) {
                return TRUE;
            }
            if (in_array($data->getType(), $this->supportedParagraphTypes)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * @inheritDoc
     */

    public function normalize($entity, $format = null, array $context = []): array|\ArrayObject|bool|float|int|string|null
    {
        $normalized = parent::normalize(
            $entity,
            $format,
            $context
        );

        $getContentType = $entity->get('field_content_type')->target_id;
        $categories = $entity->get('field_category_product')->getValue();
        $sites = $entity->get('field_site')->getValue();
        $cats = [];
        foreach ($categories as $cat) {
            $cats[] = $cat['value'];
        }
        $sitelist = [];
        foreach ($sites as $site) {
            $sitelist[] = $site['value'];
        }

        // dump($categories);
        // exit;

        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $query = $node_storage->getQuery();
        $query->accessCheck(false);
        $query->condition('type', $getContentType);
        if($getContentType == 'product'){
            if(!empty($cats)) {
                $query->condition('field_category', $cats, 'IN');
            }
            if(!empty($sitelist)){
                $query->condition('field_site', $sitelist, 'IN');
            }
            // $query->condition('field_category', $sites, 'IN');
        }
        $nids = $query->execute();


        $nodes = $node_storage->loadMultiple($nids);

        // $limit = $request->query->get('limit', 10); // Default limit
        //$page = $request->query->get('page', 0); // Default page
        // $offset = $page * $limit;

        // $node_datas = $node_storage->loadByProperties(['type' => $entity->get('field_content_type')->target_id]);
        // $node_datas = array_slice($node_datas, $offset, $limit);

        $data = [];
        foreach ($nodes as $node) {
            // Get the details of each node
            $fields_data = [];

            // Get all field definitions for the node type
            $field_definitions = $node->getFieldDefinitions();

            // Loop through each field definition and get its value
            foreach ($field_definitions as $field_name => $field_definition) {
                if (strpos($field_definition->getType(), 'entity_reference') !== false) {
                    // Get the referenced entities
                    $referenced_entities = $node->get($field_name)->referencedEntities();
                    $fields_data[$field_name] = $this->serializer->normalize($referenced_entities, 'json');

                    // dump($referenced_entities);

                } else {
                    // Get the field value
                    $fields_data[$field_name] = $node->get($field_name)->getValue();
                }
            }

            $data[] = $fields_data;
        }
        $normalized['field_content_type'] = $data ?: [];

        return $normalized;
    }
}
