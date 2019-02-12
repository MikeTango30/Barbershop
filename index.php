<?php

use Barbershop\Core\Router;
use Barbershop\Core\Request;
use Barbershop\Core\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Barbershop\Utils\DependencyInjector;

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




















//FORCED one book
// $bookModel = new BookModel(Db::getInstance());
// $book = $bookModel->get(22);
// $params = ['book' => $book];
// echo $twig->loadTemplate('book.twig')->render($params);

//FORCED all books
// $bookModel = new BookModel(Db::getInstance());
// $books = $bookModel->getAll(1, 3);
// $params = ['books' => $books, 'currentPage' => 2];
// echo $twig->loadTemplate('books.twig')->render($params);


//FORCED sales
// $saleModel = new SaleModel(Db::getInstance());
// $sales = $saleModel->getByUser(1);
// $params = ['sales' => $sales];
// echo $twig->loadTemplate('sales.twig')->render($params);

//FORCED specific sale
// $saleModel = new SaleModel(Db::getInstance());
// $sale = $saleModel->get(1);
// $params = ['sale' => $sale];
// echo $twig->loadTemplate('sale.twig')->render($params);
