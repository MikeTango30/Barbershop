<?php

use Barbershop\Core\Router;
use Barbershop\Core\Request;
use Barbershop\Core\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Barbershop\Utils\DependencyInjector;
use Barbershop\Reservations\AvailableTimes;
use Barbershop\Models\ReservationModel;

require_once __DIR__ . '/vendor/autoload.php';

$config = new Config();

$dbConfig = $config->get('db');

$db = new PDO(
            'mysql:host='.$dbConfig['host'].';dbname='.$dbConfig['name'].'',
            $dbConfig['user'],
            $dbConfig['password']
        );

$loader = new Twig_Loader_Filesystem(__DIR__ . '/src/Views');
$view = new Twig_Environment($loader);

$log = new Logger('barbershop');
$logFile = $config->get('log');
$log->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

$di = new DependencyInjector();
$di->set('PDO', $db);
$di->set('Utils\Config', $config);
$di->set('Twig_Environment', $view);
$di->set('Logger', $log);


$router = new Router($di);
$response = $router->route(new Request());
echo $response;



//TEST

$times = new AvailableTimes;
$reservationModel = new ReservationModel($db);
$times = $times->getTimes(14);

foreach($times as $day) {
    foreach($day as $dt) {
        echo $dt->format('Y-m-d -- H:i'), "</br>";
        var_dump($reservationModel->checkAvailability($dt->format('Y-m-d'), $dt->format('H:i')));
        
    }
}