<?php

namespace Drupal\bribe\Normalizer;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest_entity_recursive\Normalizer\ContentEntityNormalizer;

final class PromoContentNormalizer extends ContentEntityNormalizer
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
    protected $supportedParagraphTypes = ['promo_widget'];

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
    public function normalize($entity, $format = null, array $context = []): array|\ArrayObject|bool|float|int|string|null
    {
        $normalized = parent::normalize(
            $entity,
            $format,
            $context
        );

        $request = \Drupal::request();

        $limit = $request->query->get('limit', 10);
        $page = $request->query->get('page', 0);
        $offset = $page * $limit;

        $categoryIDs = $request->query->get('category_id', 'all');
        $locationIDs = $request->query->get('location_id', 'all');
        $productIDs = $request->query->get('product_id', 'all');

        $config = $entity->get('field_promo_configuration')->getValue();

        $getConfig = [];
        foreach ($config as $value) {
            $getConfig[] = $value['value'];
        }

        $configuration = [];


        $configuration['catID'] = $categoryIDs;
        $configuration['locID'] = $locationIDs;
        $configuration['prodID'] = $productIDs;

        if (in_array('with_sidebar', $getConfig)) {
            $configuration['with_sidebar'] = true;
        }
        if (in_array('hot_offers', $getConfig)) {
            $configuration['hotOffers'] = true;
        }
        if (in_array('popular_category', $getConfig)) {
            $configuration['popularCat'] = true;
        }
        $normalized['promo'] =[];

        $data['items'] = $this->promoList($configuration);
        $data['sidebar'] = $this->promoSidebar($configuration);
        $data['popular_category'] = $this->promoCategory($configuration);
        $data['configurations'] = $configuration;
        $data['config'] = $getConfig;

        $normalized['promo_data'] = $this->serializer->normalize($data, 'json_recursive');
        return $normalized;
    }

    /**
     * Load and display promo product terms.
     *
     * @return void
     */
    function promoList($configuration): array|\ArrayObject
    {
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $query = $node_storage->getQuery();
        $query->accessCheck(TRUE);
        $query->condition('type', 'promo');
        if(isset($configuration['hotOffers'])) {
            $query->condition('field_hot_offers', 1);
        }
        if($configuration['prodID'] !== 'all') {
            $query->condition('field_promo_product_type', $configuration['catID']);
        }
        if($configuration['locID'] !== 'all') {
            $query->condition('field_promo_location', $configuration['locID']);
        }
        if($configuration['catID'] !== 'all') {
            $query->condition('field_promo_category', $configuration['locID']);
        }
        $nids = $query->execute();

        $nodes = $node_storage->loadMultiple($nids);

        return $nodes;
    }

    function promoSidebar($configuration)
    {
        if(isset($configuration['with_sidebar']) && $configuration['with_sidebar'] === true ) {
            $data['category'] = $this->promoCategory($configuration);
            $data['product'] = $this->promoProduct($configuration);
            $data['location'] = $this->promoLocation($configuration);
            return $data;
        }

        return [];

    }

    function promoCategory($configuration)
    {
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $nids = $node_storage->getQuery()
            ->condition('type', 'promo_category')
            ->accessCheck(TRUE)
            ->execute();
        $nodes = $node_storage->loadMultiple($nids);

        return $nodes;
    }

    function promoProduct($configuration)
    {
        $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
        $vocabulary_name = 'promo_product'; // Replace with your vocabulary machine name.
        $tids = $term_storage->getQuery()
            ->condition('vid', $vocabulary_name)
            ->accessCheck(TRUE)
            ->execute();

        $terms = $term_storage->loadMultiple($tids);
        return $terms;
    }

    function promoLocation($configuration)
    {
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $nids = $node_storage->getQuery()
            ->condition('type', 'promo_location')
            ->accessCheck(TRUE)
            ->execute();
        $nodes = $node_storage->loadMultiple($nids);
        return $nodes;
    }

}
