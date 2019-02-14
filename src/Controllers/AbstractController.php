<?php

namespace Barbershop\Controllers;

use Barbershop\Core\Config;
use Barbershop\Core\Request;
use Monolog\Logger;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Monolog\Handler\StreamHandler;
use Barbershop\Utils\DependencyInjector;


abstract class AbstractController 
{
    protected $request;
    protected $db;
    protected $config;
    protected $view;
    protected $log;
    protected $di;
    protected $customerId;
    
    public function __construct(DependencyInjector $di, Request $request) {
        $this->request = $request;
        $this->di = $di;
        $this->db = $di->get('PDO');
        $this->log = $di->get('Logger');
        $this->view = $di->get('Twig_Environment');
        $this->config = $di->get('Utils\Config');
        $this->customerId = $_COOKIE['user'];
    } 

    
    public function setCustomerId($customerId) {
        $this->customerId = $customerId;
    }
    
    protected function render(string $template, array $params): string {
        return $this->view->loadTemplate($template)->render($params);
    }

}