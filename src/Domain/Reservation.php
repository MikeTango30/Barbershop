<?php

namespace Barbershop\Domain;

use Babershop\Core\Customer;

class Reservation
{
    private $arrivalTime;
    private $reservationDate;
    private $customerId;
    //private $db;
   
    
    
    // public function __construct($db) {
    //     $this->db = $db;
    // }
    
    public function getArrivalTime() {
        return $this->arrivalTime;
    }
    
    public function getReservationDate() {
        return $this->reservationDate;
    }
    
    public function getCustomerId() {
        return $this->customerId;
    }
    
    public function setArrivalTime($arrivalTime) {
        $this->arrivalTime = $arrivalTime;
    }
    
    public function setReservationDate($reservationDate) {
        $this->reservationDate = $reservationDate;
    }
    
    public function setCustomerId($customerId) {
        $this->customerId = $customerId;
    }
}