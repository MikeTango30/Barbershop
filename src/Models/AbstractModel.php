<?php

namespace Barbershop\Models;

use PDO;

abstract class AbstractModel 
{
    protected $db;
    protected $di;
    protected $request;
    
    public function __construct(DependencyInjector $di, Request $request) {
        $this->db = $di->db;
        $this->di = $di;
        $this->request = $request;
    }
}