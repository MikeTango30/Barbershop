<?php

namespace Barbershop\Controllers;

use Barbershop\Core\Config;
use Barbershop\Core\Request;
use Monolog\Logger;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Monolog\Handler\StreamHandler;
use Barbershop\Utils\DependencyInjector;
use Barbershop\Reservations\ReservationManager;
use \DateTime;


abstract class AbstractController 
{
    protected $request;
    protected $db;
    protected $config;
    protected $view;
    protected $log;
    protected $di;
    
    public function __construct(DependencyInjector $di, Request $request) {
        $this->request = $request;
        $this->di = $di;
        $this->db = $di->get('PDO');
        $this->log = $di->get('Logger');
        $this->view = $di->get('Twig_Environment');
        $this->config = $di->get('Utils\Config');
    } 
    
    protected function render(string $template, array $params): string {
        return $this->view->loadTemplate($template)->render($params);
    }
    
    //check if user is a barber
    public function identifyUser(): string {
        $reservationManager = new ReservationManager($this->db);
        $identity = $reservationManager->identifyUser(
            $this->request->getParams()->getString("barber")
        );
        
        var_dump($this->request->getParams()->getString("barber"));
        
        return $identity;
    }
    
    //get today and tomorrw datetimes
    public function getTodayTomorrow(): array {
        $today = new DateTime;
        $tomorrow = new DateTime;
        $tomorrow->modify("+1 day");
        
        return $todayTomorrow = [
            "today"=>$today,
            "tomorrow"=>$tomorrow
        ];    
    }
}