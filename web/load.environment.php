<?php

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->usePutenv()->bootEnv(__DIR__ . '/../.env', 'dev', ['test'], true);
