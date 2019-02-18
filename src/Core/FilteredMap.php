<?php

namespace Barbershop\Core;

class FilteredMap
{
    private $map;
    
    public function __construct(array $baseMap)
    {
        $this->map = $baseMap;
    }
    
    public function has(string $name) : bool
    {
        return isset($this->map[$name]);
    }
    
    public function get(string $name)
    {
        return $this->map[$name] ?? null;
    }
    
    public function getInt(string $name)
    {
        return (int) $this->get($name);
    }
    
    public function getString(string $name, bool $filter = true)
    {
        $value = (string) $this->get($name);
        return $filter ? addslashes($value) : $value;
    }
    
    public function getAllParametersAsArray() {
        $params = [];
        foreach ($this->map as $field => $value) {
            $params[] = $field."=".$this->getString($field);
        }
        
        if(is_array($params) && count($params) >0) {
            $params = implode( "&",$params);
        } else {
            $params = array_shift($params);
        }
        
        return $params;
    }
}