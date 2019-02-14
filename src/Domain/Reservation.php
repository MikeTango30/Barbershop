<?php

namespace Barbershop\Domain;

use Babershop\Core\Customer;

class Reservation
{
    private $arrivalTime;
    private $reservationDate;
    private $customer_id;
   
    
    
    public function getArrivalTime() {
        return $this->arrivalTime;
    }
    
    public function getReservationDate() {
        return $this->reservationDate;
    }
    
    public function getCustomer_id() {
        return $this->customer_id;
    }
    
    public function setArrivalTime($arrivalTime) {
        $this->arrivalTime = $arrivalTime;
    }
    
    public function setReservationDate($reservationDate) {
        $this->reservationDate = $reservationDate;
    }
    
    public function setCustomerId($customer_id) {
        $this->customer_id = $customer_id;
    }
}