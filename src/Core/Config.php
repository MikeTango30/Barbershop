<?php
namespace Barbershop\Core;
    
use Barbershop\Exceptions\NotFoundException;
    

class Config 
{
    private $data;
    
    public function __construct() {
        $json = file_get_contents(__DIR__ . '/app.json');
        $this->data = json_decode($json, true);
    }

    public function get($key) {
        if (!isset($this->data[$key])) {
            throw new NotFoundException("Key $key not in config.");
        }
        return $this->data[$key];
    }
}    
