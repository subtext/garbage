<?php

require_once(__DIR__ . "/vendor/autoload.php");

use DI\ContainerBuilder;
use Subtext\Garbage\Application;

try {
    $builder = new ContainerBuilder();
    $container = $builder->build();
    $app = $container->get(Application::class);
    $app->execute();
}catch (\Exception $e) {
    // TODO: handle exception
    echo "THERE WAS AN EXCEPTION";
}
