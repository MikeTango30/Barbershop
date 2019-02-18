<?php

namespace Barbershop\Reservations;

use Barbershop\Domain\Reservation;
use Barbershop\Models\ReservationModel;
use Barbershop\Reservations\Validators\ReservationValidator;
use Barbershop\Controllers\ErrorController;
use Barbershop\Reservations\CustomerManager;

class ReservationManager
{
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function identifyUser($param) {
        return (!empty($param["barber"])) ? "customer" : "barber";
        
    }
    
     public function manageReservation($formParameters) {
        $check = new ReservationValidator();
         if ($check->isReservationValid($formParameters)) {
             
            // create customer 
            $customerManager = new CustomerManager($this->db);
            $customerId = $customerManager->createCustomer($formParameters);
            
            //insert reservation
            $reservation = new Reservation();
            $reservation->setCustomerId($customerId);
            $reservation->setArrivalTime(date("H:i:s", strtotime($formParameters["arrival"])));
            $reservation->setReservationDate(date("Y-m-d", strtotime($formParameters["arrival"])));
            
            // create reservation 
            $reservationModel = new ReservationModel($this->db);
            if(!isset($_COOKIE["phone"])) {
                $reservationModel->createReservation($reservation);
            } else {
                $errors['reservationExist'] = "You cannot have more than one active reservation";
            }
         }
         
         $errors[] = $check->getErrors();    
         return $errors;
    } 
    
}