<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/** @var ClassLoader $loader */
$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('RethinkDB',__DIR__.'/../vendor/danielmewes/rdb/rdb.php');
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
