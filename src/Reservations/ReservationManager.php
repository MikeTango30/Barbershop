<?php

namespace Barbershop\Reservations;

use Barbershop\Reservations\Validators\ReservationValidator;
use Barbershop\Controllers\ErrorController;

class ReservationManager
{
    public function manage() {
        $check = new ReservationValidator();
        
        if ($check->isValid($firstname == false)) {
            $errorController = new ErrorController($this->di, $this->request);
        
            return $errorController->requiredField();
        }
        
        elseif ($check->isValid($surname == false)) {
            $errorController = new ErrorController($this->di, $this->request);
        
            return $errorController->requiredField();
        }
        
        elseif ($check->isValid($phone == false)) {
            $errorController = new ErrorController($this->di, $this->request);
        
            return $errorController->requiredField();
        }
    }    
}