<?php

namespace Barbershop\Reservations\Validators;

use Barbershop\Controllers\ErrorController;

class ReservationValidator
{
    private $errors;
    
    public function isReservationValid(array $parameters): bool {
        
        foreach ($parameters as $field => $value) {
            if (!$this->isFieldNotEmpty($value)) {
                $this->errors[$field] = "Field is empty";
            }
        }
        
        return true;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function isFieldNotEmpty ($name): bool {
        return !empty($name) ? true : false;
    }
    
}