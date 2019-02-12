<?php

namespace Barbershop\Domain;

use Babershop\Core\Customer;

class Reservation
{
    private $bookTime;
    private $bookDate;
    private $customer;
    
    
    public function getTime() {
        return $this->bookTime;
    }
    
    public function getDate() {
        return $this->bookDate;
    }
    
    public function getCustomer() {
        return $this->customer;
    }
    
    public function setTime($bookTime) {
        $this->bookTime = $bookTime;
    }
    
    public function setDate($bookDate) {
        $this->bookDate = $bookDate;
    }
    
    public function setCustomer($customer) {
        $this->customer = $customer;
    }
}