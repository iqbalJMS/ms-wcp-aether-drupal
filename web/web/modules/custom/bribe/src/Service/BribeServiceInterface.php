<?php

namespace Drupal\bribe\Service;  
  
interface BribeServiceInterface {  
  /**  
  * Retrieves data from external GraphQL API.  
  *  
  * @param string $category  
  *  The category to search for.  
  *  
  * @return array  
  */  
  public function getData($category = NULL);  
  
  /**  
  * Creates new data on external GraphQL API.  
  *  
  * @param array $data  
  *  The data to create.  
  *  
  * @return array  
  */  
  public function createData($data);  
  
  /**  
  * Updates existing data on external GraphQL API.  
  *  
  * @param string $id  
  *  The ID of the data to update.  
  * @param array $data  
  *  The data to update.  
  *  
  * @return array  
  */  
  public function updateData($id, $data);  
  
  /**  
  * Retrieves promo data by ID from external GraphQL API.  
  *  
  * @param string $id  
  *  The ID of the promo to retrieve.  
  *  
  * @return array  
  */  
  public function getPromoById($id);  
  
  /**  
  * Deletes a promo from external GraphQL API.  
  *  
  * @param string $id  
  *  The ID of the promo to delete.  
  *  
  * @return bool  
  */  
  public function deletePromo($id);  
}