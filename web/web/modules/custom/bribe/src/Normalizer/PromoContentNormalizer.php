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
        
        $configuration['title'] = $request->query->get('search', '');
        
        $normalized['promo'] =[];

        $getItems = $this->promoList($configuration);

        $data['items'] = [];
        foreach ($getItems as $keyItems => $valueItems) {
            $data['items'][] = $valueItems;
        }

        $data['sidebar'] = $this->promoSidebar($configuration);

        $getPopularCategory = $this->promoPopularCategory($configuration);
        
        $data['popular_category'] = [];
        foreach ($getPopularCategory as $keySPopularCategory => $valuePopularCategory) {
            $data['popular_category'][] = $valuePopularCategory;
        }

        $data['configurations'] = $configuration;
        $data['config'] = $getConfig;

        // $normalized['promo_data'] = $this->serializer->normalize($data, 'json_recursive');
        $normalized['promo_data'] = $data;
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
        if($configuration['title'] != ''){
            $query->condition('title', '%' . $configuration['title'] . '%', 'LIKE');
        }
        $nids = $query->execute();

        $nodes = $node_storage->loadMultiple($nids);

        $getNodes = $this->serializer->normalize($nodes, 'json_recursive');

        return $getNodes;
    }

    function promoSidebar($configuration)
    {
        if(isset($configuration['with_sidebar']) && $configuration['with_sidebar'] === true ) {
            $getCategory = $this->promoCategory($configuration);
            $data['category'] = [];
            foreach ($getCategory as $keyCategory => $valueCategory) {
                $data['category'][] = $valueCategory;
            }
            $getProduct = $this->promoProduct($configuration);
            $data['product'] = [];
            foreach ($getProduct as $keyProduct => $valueProduct) {
                $data['product'][] = $valueProduct;
            }
            $getLocation = $this->promoLocation($configuration);
            $data['location'] = [];
            foreach ($getLocation as $keyLocation => $valueLocation) {
                $data['location'][] = $valueLocation;
            }
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

        $getNodes = $this->serializer->normalize($nodes, 'json_recursive');

        return $getNodes;
    }

    function promoPopularCategory($configuration)
    {
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $query = $node_storage->getQuery()
            ->condition('type', 'promo_category')
            ->accessCheck(TRUE)
            ->sort('changed', 'DESC')
            ->range(0,4);
        $or_group = $query->orConditionGroup();
        $or_group->condition('status', 1); // Condition for published nodes.
        $or_group->condition('title', 'Others');
        $query->condition($or_group);
        $nids = $query->execute();
        $nodes = $node_storage->loadMultiple($nids);

        $getNodes = $this->serializer->normalize($nodes, 'json_recursive');

        return $getNodes;
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

        $getTerms = $this->serializer->normalize($terms, 'json_recursive');

        return $getTerms;
    }

    function promoLocation($configuration)
    {
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $nids = $node_storage->getQuery()
            ->condition('type', 'promo_location')
            ->accessCheck(TRUE)
            ->execute();
        $nodes = $node_storage->loadMultiple($nids);

        $getNodes = $this->serializer->normalize($nodes, 'json_recursive');

        return $getNodes;
    }

}
