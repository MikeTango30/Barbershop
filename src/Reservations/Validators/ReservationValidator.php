<?php

namespace Barbershop\Reservations\Validators;
use Barbershop\Controllers\ErrorController;
use Barbershop\Controllers\AbstractController;


class ReservationValidator extends AbstractController
{
    public function isValid($name): bool {
       return !empty($name) ? true : false;
    }
    
    public function checkFieldsValidity($firstname, $surname, $phone) {
        if ($this->isValid($firstname == false)) {
            $errorController = new ErrorController($this->di, $this->request);
        
            return $errorController->requiredField();
        }
        elseif ($this->isValid($surname == false)) {
            $errorController = new ErrorController($this->di, $this->request);
        
            return $errorController->requiredField();
        }
        elseif ($this->isValid($phone == false)) {
            $errorController = new ErrorController($this->di, $this->request);
        
            return $errorController->requiredField();
        }        
    }
}