<?php

use Symfony\Component\Dotenv\Dotenv;

if (file_exists(DRUPAL_ROOT . '/../.env')) {
  (new Dotenv())->usePutenv()->bootEnv(DRUPAL_ROOT . '/../.env', 'dev', ['test'], true);
}
