<?php

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->usePutenv()->bootEnv(__DIR__ . '/../config.txt', 'dev', ['test'], true);
