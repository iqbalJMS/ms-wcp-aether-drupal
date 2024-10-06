<?php

use Symfony\Component\Dotenv\Dotenv;

if (file_exists(DRUPAL_ROOT . '/../../.env')) {
  // Find in project root
  (new Dotenv())->usePutenv()->bootEnv(DRUPAL_ROOT . '/../../.env', 'dev', ['test'], true);
}
elseif (file_exists(DRUPAL_ROOT . '/../.env')) {
  // Find in composer
  (new Dotenv())->usePutenv()->bootEnv(DRUPAL_ROOT . '/../.env', 'dev', ['test'], true);
}
