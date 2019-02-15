<?php

namespace Barbershop\Reservations\Validators;

class ReservationValidator
{
    public function isValid($name): bool {
       return !empty($name) ? true : false;
    }
}