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
    
    public function manageReservation($firstname, $surname, $phone, $arrival) {
        $check = new ReservationValidator();
        $check->checkFieldsValidity($firstname, $surname, $phone);
        
        $customerManager = new CustomerManager($this->db);
        $customer = $customerManager->createCustomer($firstname, $surname, $phone);
        
         //insert reservation
        $reservation = new Reservation();
        $reservation->setCustomerId($customer);
        $reservation->setArrivalTime(date("H:i:s", strtotime($arrival)));
        $reservation->setReservationDate(date("Y-m-d", strtotime($arrival)));
        
        $reservationModel = new ReservationModel($this->db);
        $reservationModel->createReservation($reservation);
    }    
}