<?php

require_once(dirname(__DIR__) . "/vendor/autoload.php");

use DI\ContainerBuilder;
use Subtext\Garbage\Application;

try {
    $builder = new ContainerBuilder();
    $builder->addDefinitions('../config/di/config.php');
    $container = $builder->build();
    $app = $container->get(Application::class);
    $app->execute();
}catch (\Exception $e) {
    // TODO: handle exception
    echo "THERE WAS AN EXCEPTION\n";
    echo $e->getMessage();
}
