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

        $limit = $request->query->get('limit', 0);
        $page = $request->query->get('page', 0);
        $offset = ($page - 1) * $limit;

        $categoryIDs = $request->query->get('category_id', 'all');
        $locationIDs = $request->query->get('location_id', 'all');
        $productIDs = $request->query->get('product_id', 'all');
        $micrositeIDs = $request->query->get('microsite_id', 'all');

        $config = $entity->get('field_promo_configuration')->getValue();

        $filterMicrosite = $entity->get('field_promo_microsite')->getValue();

        $getConfig = [];
        foreach ($config as $value) {
            $getConfig[] = $value['value'];
        }

        $configuration = [];


        $configuration['offset'] = $offset;
        $configuration['limit'] = $limit;
        $configuration['catID'] = $categoryIDs;
        $configuration['locID'] = $locationIDs;
        $configuration['prodID'] = $productIDs;
        $configuration['micrositeID'] = $micrositeIDs;
        if(!empty($filterMicrosite)) {
            foreach ($filterMicrosite as $value) {
                $configuration['filter_microsite'] = $value['target_id'];
            }
        }
        if (in_array('with_sidebar', $getConfig)) {
            $configuration['with_sidebar'] = true;
        }
        if (in_array('hot_offers', $getConfig)) {
            $configuration['hotOffers'] = true;
        }
        if (in_array('popular_category', $getConfig)) {
            $configuration['popularCat'] = true;
        }
        if (in_array('latest_four', $getConfig)) {
            $configuration['latest_four'] = 4;
        }
        if (in_array('latest_seven', $getConfig)) {
            $configuration['latest_seven'] = 7;
        }
        if (in_array('microsite', $getConfig)) {
            $configuration['microsite'] = true;
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
        foreach ($getPopularCategory as $keyPopularCategory => $valuePopularCategory) {
            $data['popular_category'][] = $valuePopularCategory;
        }

        $getMicrosite = $this->promoMicrosite($configuration);

        $data['promo_microsite'] = [];
        foreach ($getMicrosite as $keyMicrosite => $valueMicrosite) {
            $data['promo_microsite'][] = $valueMicrosite;
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
        $query->accessCheck(false);
        $query->condition('type', 'promo');
        if(isset($configuration['hotOffers'])) {
            $query->condition('field_hot_offers', 1);
        }
        if($configuration['prodID'] !== 'all') {
            $query->condition('field_promo_product_type', (int) $configuration['prodID']);
        }
        if($configuration['locID'] !== 'all') {
            $query->condition('field_promo_location', (int) $configuration['locID']);
        }
        if($configuration['catID'] !== 'all') {
            $query->condition('field_promo_category', (int) $configuration['catID']);
        }
        if($configuration['micrositeID'] !== 'all'){
            $query->condition('field_promo_microsite_owner', (int) $configuration['micrositeID']);
        }
        if(!empty($configuration['microsite'])){
            $query->condition('field_promo_microsite_owner', (int) $configuration['microsite']);
        }
        if(!empty($configuration['filter_microsite']) && $configuration['micrositeID'] == 'all'){
            $query->condition('field_promo_microsite_owner', (int) $configuration['filter_microsite']);
        }
        if($configuration['title'] != ''){
            $query->condition('title', '%' . $configuration['title'] . '%', 'LIKE');
        }
        if(!empty($configuration['limit'])){
            $query->range(0,$configuration['limit']);
        }
        if(!empty($configuration['latest_four']) && empty($configuration['limit']) && empty($configuration['latest_seven'])){
            $query->range(0,4);
        }
        if(!empty($configuration['latest_seven']) && empty($configuration['limit']) && empty($configuration['latest_four'])){
            $query->range(0,5);
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

    function promoMicrosite($configuration)
    {
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $nids = $node_storage->getQuery()
            ->condition('type', 'promo_microsite_owner')
            ->accessCheck(TRUE)
            ->execute();
        $nodes = $node_storage->loadMultiple($nids);

        $getNodes = $this->serializer->normalize($nodes, 'json_recursive');

        return $getNodes;
    }

}
