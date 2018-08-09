<?php

namespace Subtext\Garbage;

use DI\ContainerBuilder;

require_once(dirname(__DIR__) . "/vendor/autoload.php");


try {
    $builder = new ContainerBuilder();
    $builder->addDefinitions('../config/di/config.php');
    $container = $builder->build();
    $app = $container->get(Application::class);
    $app->execute();
}catch (\Exception $e) {
    // TODO: handle exception
    echo "<h1>THERE WAS AN EXCEPTION</h1>";
    echo $e->getMessage();
}
